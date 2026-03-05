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
        $firmenMap = DB::table('auftrag_projekt_firma')->get()->mapWithKeys(function ($item) {
            return [strtolower($item->name) => $item];
        })->toArray();
        
        $aliasMap = DB::table('auftrag_projekt_firma_namen')
            ->join('auftrag_projekt_firma', 'auftrag_projekt_firma.id', '=', 'auftrag_projekt_firma_namen.name_id')
            ->select('auftrag_projekt_firma_namen.begriff', 'auftrag_projekt_firma.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [strtolower($item->begriff) => $item->name];
            })
            ->toArray();

        $userMap = DB::table('user')->get()->keyBy('name_komplett')->toArray();

        $existingOrders = DB::table('auftrag_tabelle')
            ->select(DB::raw("CONCAT(projekt_id, '_', auftrag_id) as key_id"))
            ->pluck('key_id')
            ->flip()
            ->toArray();

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
                    SELECT kAuftrag, cAuftragsnummer, cBenutzername, cFirmenname, cStatustext,
                           dErstellt, dVoraussichtlichesLieferdatum, fAuftragswertNetto,
                           cRechnungsadresseFirma, cRechnungsadresseAnrede, cRechnungsadresseTitel,
                           cRechnungsadresseVorname, cRechnungsadresseNachname,
                           cRechnungsadresseStrasse, cRechnungsadressePlz, cRechnungsadresseOrt, 
                           cRechnungsadresseLand, cRechnungsadresseMail, cRechnungsadresseTelefon, 
                           cRechnungsadresseMobilTelefon, cKundeNr
                    FROM Verkauf.lvAuftragsverwaltung
                    WHERE nStorniert = 0
                    ORDER BY dErstellt DESC
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

                    $firma = $firmenMap[$projekt_firmenname_lower];
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
                        'ansprechpartner_anrede' => $obj['cRechnungsadresseAnrede'] ?? null,
                        'ansprechpartner_titel' => $obj['cRechnungsadresseTitel'] ?? null,
                        'ansprechpartner_vorname' => $obj['cRechnungsadresseVorname'] ?? null,
                        'ansprechpartner_nachname' => $obj['cRechnungsadresseNachname'] ?? null,
                        'kunde_strasse' => $obj['cRechnungsadresseStrasse'] ?? null,
                        'kunde_plz' => $obj['cRechnungsadressePlz'] ?? null,
                        'kunde_ort' => $obj['cRechnungsadresseOrt'] ?? null,
                        'kunde_land' => $obj['cRechnungsadresseLand'] ?? null,
                        'kunde_mail' => $obj['cRechnungsadresseMail'] ?? null,
                        'kunde_telefon' => $obj['cRechnungsadresseTelefon'] ?? null,
                        'ansprechpartner_mobil' => $obj['cRechnungsadresseMobilTelefon'] ?? null,
                        'projekt_firmenname_kuerzel' => $firma->name_kuerzel,
                        'projekt_farbe_hex' => $firma->bg,
                    ];

                    try {
                        if (isset($existingOrders[$lookupKey])) {
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
                            
                            DB::table('auftrag_tabelle')->insert($data);
                            $existingOrders[$lookupKey] = true;
                            $totalInserted++;
                        }
                    } catch (Exception $rowEx) {
                        $this->error("Fehler bei Auftrag #{$auftrag_id}: " . $rowEx->getMessage());
                        $totalErrors++;
                    }
                }

                // Löschen von stornierten/nicht mehr vorhandenen Aufträgen
                // Wir löschen Aufträge, die diesem Mandanten (via projekt_id) zugeordnet sind,
                // aber nicht mehr in der Liste der aktiven JTL IDs vorkommen.
                if (!empty($activeJtlIds)) {
                    $deletedCount = DB::table('auftrag_tabelle')
                        ->where('projekt_id', $wawi->auftrag_projekt_id)
                        ->whereNotIn('auftrag_id', $activeJtlIds)
                        ->delete();
                    
                    if ($deletedCount > 0) {
                        $this->warn("{$deletedCount} stornierte/gelöschte Aufträge entfernt für Mandant {$wawi->dataname}.");
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
