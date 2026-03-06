<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use Exception;

class ImportJtlOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-jtl-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronisiert Aufträge aus allen JTL-Wawi-Mandanten in die lokale Datenbank';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starte JTL-Auftrags-Import...");
        $startTime = microtime(true);

        // 1. Lookup-Daten laden (Pre-Caching wie bei Offers)
        // Wir laden alle Projekte und gruppieren sie nach Name (kleingeschrieben)
        $projects = DB::table('auftrag_projekt')
            ->leftJoin('auftrag_projekt_firma', 'auftrag_projekt.id', '=', 'auftrag_projekt_firma.projekt_id')
            ->select('auftrag_projekt.*', 'auftrag_projekt_firma.firma_id', 'auftrag_projekt_firma.bg', 'auftrag_projekt_firma.name_kuerzel')
            ->get();
        $firmenMap = [];
        foreach ($projects as $p) {
            $nameLower = strtolower($p->firmenname);
            if (!isset($firmenMap[$nameLower])) {
                $firmenMap[$nameLower] = [];
            }
            $firmenMap[$nameLower][] = $p;
        }
        
        $aliasMap = DB::table('auftrag_projekt_firma_namen')
            ->join('auftrag_projekt_firma', 'auftrag_projekt_firma.id', '=', 'auftrag_projekt_firma_namen.name_id')
            ->select('auftrag_projekt_firma_namen.begriff', 'auftrag_projekt_firma.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [strtolower($item->begriff) => $item->name];
            })
            ->toArray();

        $userMap = DB::table('user')->get()->keyBy('name_komplett')->toArray();

        // Bestehende Aufträge laden (wir brauchen projekt_id und auftrag_id für den Key)
        $existingOrders = DB::table('auftrag_tabelle')
            ->select(DB::raw("CONCAT(projekt_id, '_', auftrag_id) as key_id"), 'letzter_status', 'id')
            ->get()
            ->keyBy('key_id')
            ->toArray();

        // Hilfe-Map: kAuftrag -> Liste von projekt_ids (um Duplikate über Projekte hinweg zu finden)
        $orderProjMap = [];
        foreach ($existingOrders as $key => $eo) {
            $parts = explode('_', $key);
            $pId = $parts[0];
            $aId = $parts[1];
            if (!isset($orderProjMap[$aId])) $orderProjMap[$aId] = [];
            $orderProjMap[$aId][] = (int)$pId;
        }

        // 2. WAWI Mandanten abrufen
        $wawiConnections = DB::table('auftrag_projekt_wawi')->get();
        
        $totalInserted = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($wawiConnections as $wawi) {
            $this->info("Verarbeite Mandant: {$wawi->dataname} ({$wawi->host})");
            
            try {
                $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
                $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                // Aufträge abrufen (lvAuftragsverwaltung)
                $orders = $wawi_db->query("
                    SELECT t1.kAuftrag, t1.cAuftragsnummer, t1.cBenutzername, t1.cFirmenname, t1.cStatustext,
                           t1.dErstellt, t1.dVoraussichtlichesLieferdatum, t1.fAuftragswertNetto,
                           t1.cRechnungsadresseFirma, t1.cRechnungsadresseAnrede, t1.cRechnungsadresseTitel,
                           t1.cRechnungsadresseVorname, t1.cRechnungsadresseNachname,
                           t1.cRechnungsadresseStrasse, t1.cRechnungsadressePlz, t1.cRechnungsadresseOrt, 
                           t1.cRechnungsadresseLand, t1.cRechnungsadresseMail, t1.cRechnungsadresseTelefon, 
                           t1.cRechnungsadresseMobilTelefon, t1.cKundeNr,
                           t1.cLieferadresseFirma, t1.cLieferadresseAnrede, t1.cLieferadresseTitel,
                           t1.cLieferadresseVorname, t1.cLieferadresseNachname,
                           t1.cLieferadresseStrasse, t1.cLieferadressePlz, t1.cLieferadresseOrt,
                           t1.cLieferadresseLand,
                           t1.cKundengruppe, kk.cName as cKundenKategorieName
                    FROM Verkauf.lvAuftragsverwaltung t1
                    LEFT JOIN dbo.tKunde k ON k.kKunde = t1.kKunde
                    LEFT JOIN dbo.tKundenKategorie kk ON kk.kKundenKategorie = k.kKundenKategorie
                    WHERE t1.nStorniert = 0 AND t1.dErstellt >= DATEADD(month, -12, GETDATE())
                    ORDER BY t1.dErstellt DESC
                ")->fetchAll();

                $activeJtlIds = [];

                foreach ($orders as $obj) {
                    $auftrag_id = $obj['kAuftrag'];
                    $activeJtlIds[] = $auftrag_id;
                    $projekt_firmenname = trim($obj['cFirmenname'] ?? '');
                    $projekt_firmenname_lower = strtolower($projekt_firmenname);

                    // Firma auflösen
                    if (!isset($firmenMap[$projekt_firmenname_lower]) && isset($aliasMap[$projekt_firmenname_lower])) {
                        $projekt_firmenname = $aliasMap[$projekt_firmenname_lower];
                        $projekt_firmenname_lower = strtolower($projekt_firmenname);
                    }

                    if (empty($projekt_firmenname) || !isset($firmenMap[$projekt_firmenname_lower])) {
                        $totalSkipped++;
                        continue;
                    }

                    // Richtige Projekt-ID finden
                    $matches = $firmenMap[$projekt_firmenname_lower];
                    $firma = $matches[0]; // Fallback auf den ersten Treffer
                    
                    // Wenn es mehrere Projekte mit gleichem Namen gibt, prüfen wir:
                    // 1. Hat die aktuelle Wawi-Verbindung eine bevorzugte Projekt-ID?
                    // 2. Existiert der Auftrag bereits unter einer dieser Projekt-IDs?
                    if (count($matches) > 1) {
                        foreach ($matches as $m) {
                            if ($m->id == $wawi->auftrag_projekt_id) {
                                $firma = $m;
                                break;
                            }
                            // Wenn der Auftrag bereits unter dieser Projekt-ID existiert, bleiben wir dabei
                            if (isset($orderProjMap[$auftrag_id]) && in_array($m->id, $orderProjMap[$auftrag_id])) {
                                $firma = $m;
                                break;
                            }
                        }
                    }

                    $projekt_id = $firma->id;
                    $lookupKey = "{$projekt_id}_{$auftrag_id}";

                    $benutzer = !empty($obj['cBenutzername']) ? $obj['cBenutzername'] : 'Fabian Frank';
                    $userData = $userMap[$benutzer] ?? $userMap['Fabian Frank'] ?? null;
                    
                    $data = [
                        'auftrag_id' => $auftrag_id,
                        'projekt_id' => $projekt_id,
                        'firmen_id' => $firma->firma_id,
                        'benutzer' => $benutzer,
                        'benutzer_kuerzel' => $userData->kuerzel ?? '',
                        'auftragsnummer' => $obj['cAuftragsnummer'] ?? '',
                        'firmenname' => $obj['cRechnungsadresseFirma'] ?? '',
                        'projektname' => $obj['cStatustext'] ?? '',
                        'erstelldatum' => !empty($obj['dErstellt']) ? date("Y-m-d H:i:s", strtotime($obj['dErstellt'])) : now(),
                        'lieferdatum' => !empty($obj['dVoraussichtlichesLieferdatum']) ? date("Y-m-d H:i:s", strtotime($obj['dVoraussichtlichesLieferdatum'])) : null,
                        'auftragssumme' => $obj['fAuftragswertNetto'] ?? 0,
                        'projekt_firmenname' => $projekt_firmenname,
                        'kundennummer' => $obj['cKundeNr'] ?? '',
                        'ansprechpartner_anrede' => $obj['cRechnungsadresseAnrede'] ?? '',
                        'ansprechpartner_titel' => $obj['cRechnungsadresseTitel'] ?? '',
                        'ansprechpartner_vorname' => $obj['cRechnungsadresseVorname'] ?? '',
                        'ansprechpartner_nachname' => $obj['cRechnungsadresseNachname'] ?? '',
                        'kunde_strasse' => $obj['cRechnungsadresseStrasse'] ?? '',
                        'kunde_plz' => $obj['cRechnungsadressePlz'] ?? '',
                        'kunde_ort' => $obj['cRechnungsadresseOrt'] ?? '',
                        'kunde_land' => $obj['cRechnungsadresseLand'] ?? '',
                        'kunde_mail' => $obj['cRechnungsadresseMail'] ?? '',
                        'kunde_telefon' => $obj['cRechnungsadresseTelefon'] ?? '',
                        'ansprechpartner_mobil' => $obj['cRechnungsadresseMobilTelefon'] ?? '',
                        'lieferadresse_firma' => $obj['cLieferadresseFirma'] ?? '',
                        'lieferadresse_anrede' => $obj['cLieferadresseAnrede'] ?? '',
                        'lieferadresse_titel' => $obj['cLieferadresseTitel'] ?? '',
                        'lieferadresse_vorname' => $obj['cLieferadresseVorname'] ?? '',
                        'lieferadresse_nachname' => $obj['cLieferadresseNachname'] ?? '',
                        'lieferadresse_strasse' => $obj['cLieferadresseStrasse'] ?? '',
                        'lieferadresse_plz' => $obj['cLieferadressePlz'] ?? '',
                        'lieferadresse_ort' => $obj['cLieferadresseOrt'] ?? '',
                        'lieferadresse_land' => $obj['cLieferadresseLand'] ?? '',
                        'kundengruppe' => $obj['cKundengruppe'] ?? '',
                        'kundenkategorie' => $obj['cKundenKategorieName'] ?? '',
                        'projekt_firmenname_kuerzel' => $firma->name_kuerzel ?? '',
                        'projekt_farbe_hex' => $firma->bg ?? '',
                    ];

                    try {
                        if (array_key_exists($lookupKey, $existingOrders)) {
                            $existingOrder = (object)$existingOrders[$lookupKey];
                            // Wenn der Auftrag bereits abgeschlossen ist, sparen wir uns das Update
                            if ($existingOrder->letzter_status === 'A') {
                                $totalSkipped++;
                                continue;
                            }

                            DB::table('auftrag_tabelle')
                                ->where('auftrag_id', $auftrag_id)
                                ->where('projekt_id', $projekt_id)
                                ->update($data);
                            $totalUpdated++;
                        } else {
                            // Initialer Status für neue Aufträge
                            $data['letzter_status'] = 'NEU';
                            $data['letzter_status_name'] = 'Neu';
                            $data['letzter_status_bg_hex'] = '3b82f6'; // Blau für Neu
                            $data['letzter_status_farbe_hex'] = 'fff';
                            $data['abgeschlossen_status'] = 'Angebot nicht abgeschlossen'; // Standard für aktive
                            
                            // Felder ohne Default-Wert, die beim Update NICHT geleert/überschrieben werden dürfen
                            $data['bestellnummer'] = '';
                            $data['hersteller'] = '';
                            $data['plz'] = '';
                            // kundengruppe und kundenkategorie werden bereits oben in $data gesetzt
                            $data['istbezahlt'] = '';
                            $data['storniert'] = '';
                            $data['timestamp'] = date("Y-m-d H:i:s");
                            
                            DB::table('auftrag_tabelle')->insert($data);
                            $existingOrders[$lookupKey] = 'NEU';
                            $totalInserted++;
                        }
                    } catch (Exception $rowEx) {
                        $this->error("Fehler bei Auftrag #{$auftrag_id}: " . $rowEx->getMessage());
                        $totalErrors++;
                    }
                }

                // Löschen von explizit stornierten Aufträgen
                // Wir fragen die Wawi gezielt nach stornierten Aufträgen und löschen diese lokal.
                $stornierteOrders = $wawi_db->query("
                    SELECT kAuftrag
                    FROM Verkauf.lvAuftragsverwaltung
                    WHERE nStorniert = 1 AND dErstellt >= DATEADD(month, -12, GETDATE())
                ")->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($stornierteOrders)) {
                    $deletedCount = DB::table('auftrag_tabelle')
                        // Wichtig: Wir löschen nur Aufträge, die auch diesem Mandanten zugeordnet waren.
                        // Um ganz sicher zu gehen, löschen wir einfach anhand der kAuftrag Liste.
                        // Da die auftrag_id eindeutig für den JTL Mandanten ist, reicht das meistens,
                        // aber zur Sicherheit prüfen wir, ob die order in Olgav2 existiert.
                        ->whereIn('auftrag_id', $stornierteOrders)
                        ->where('firmen_id', $wawi->auftrag_projekt_id) // Sicherstellen, dass es der richtige Firmen-Scope ist
                        ->delete();
                    
                    if ($deletedCount > 0) {
                        $this->warn("{$deletedCount} explizit stornierte Aufträge entfernt für Mandant {$wawi->dataname}.");
                    }
                }

            } catch (Exception $e) {
                $this->error("Fehler bei Mandant {$wawi->dataname}: " . $e->getMessage());
                Log::error("JTL Auftrags-Import Error ({$wawi->dataname}): " . $e->getMessage());
                $totalErrors++;
            }
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Auftrags-Import abgeschlossen in {$duration}s.");
        $this->info("Neu: {$totalInserted} | Aktualisiert: {$totalUpdated} | Übersprungen: {$totalSkipped} | Fehler: {$totalErrors}");
        
        Log::info("JTL Auftrags-Import abgeschlossen: {$totalInserted} neu, {$totalUpdated} aktualisiert, {$totalErrors} Fehler.");
    }
}
