<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use Exception;

class ImportJtlOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-jtl-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronisiert Angebote aus allen JTL-Wawi-Mandanten in die lokale Datenbank';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starte JTL-Angebots-Import...");
        $startTime = microtime(true);

        // 1. Lookup-Daten laden (Pre-Caching)
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

        $existingAngebote = DB::table('angebot_tabelle')
            ->select(DB::raw("CONCAT(projekt_id, '_', angebot_id) as key_id"))
            ->pluck('key_id')
            ->flip()
            ->toArray();

        $existingStatus = DB::table('angebot_status_a')
            ->select(DB::raw("CONCAT(projekt_id, '_', angebot_id) as key_id"))
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
                // Dynamische Verbindung zu SQL Server (JTL)
                $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
                $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                // Angebote abrufen (inkl. Adressdaten)
                $offers = $wawi_db->query("
                    SELECT dErstellt, kBestellung, cBenutzername, cAngebotsnummer,
                           cRechnungsadresseFirma, cStatustext, fAngebotswert, cFirmenname,
                           cRechnungsadresseStrasse, cRechnungsadressePlz, cRechnungsadresseOrt, 
                           cRechnungsadresseLand, cRechnungsadresseMail
                    FROM Kunde.lvAngebote
                    ORDER BY dErstellt DESC
                ")->fetchAll();

                foreach ($offers as $obj) {
                    $angebot_id = $obj['kBestellung'];
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
                    $lookupKey = "{$projekt_id}_{$angebot_id}";

                    $benutzer = !empty($obj['cBenutzername']) ? $obj['cBenutzername'] : 'Fabian Frank';
                    $userData = $userMap[$benutzer] ?? $userMap['Fabian Frank'] ?? null;
                    
                    $data = [
                        'angebot_id' => $angebot_id,
                        'projekt_id' => $projekt_id,
                        'firmen_id' => $firma->firma_id,
                        'benutzer' => $benutzer,
                        'benutzer_kuerzel' => $userData->kuerzel ?? '',
                        'angebotsnummer' => $obj['cAngebotsnummer'] ?? '',
                        'firmenname' => $obj['cRechnungsadresseFirma'] ?? '',
                        'projektname' => $obj['cStatustext'] ?? '',
                        'erstelldatum' => !empty($obj['dErstellt']) ? date("Y-m-d", strtotime($obj['dErstellt'])) : date('Y-m-d'),
                        'angebotssumme' => $obj['fAngebotswert'] ?? 0,
                        'projekt_firmenname' => $projekt_firmenname,
                        'kunde_strasse' => $obj['cRechnungsadresseStrasse'] ?? null,
                        'kunde_plz' => $obj['cRechnungsadressePlz'] ?? null,
                        'kunde_ort' => $obj['cRechnungsadresseOrt'] ?? null,
                        'kunde_land' => $obj['cRechnungsadresseLand'] ?? null,
                        'kunde_mail' => $obj['cRechnungsadresseMail'] ?? null,
                        'gueltig_bis' => null,
                    ];

                    try {
                        // Status sicherstellen
                        if (!isset($existingStatus[$lookupKey])) {
                            DB::table('angebot_status_a')->insert([
                                'projekt_id' => $projekt_id,
                                'angebot_id' => $angebot_id,
                                'user_id' => $userData->id ?? null,
                                'status' => 1
                            ]);
                            $existingStatus[$lookupKey] = true;
                        }

                        // Update oder Insert
                        if (isset($existingAngebote[$lookupKey])) {
                            DB::table('angebot_tabelle')
                                ->where('angebot_id', $angebot_id)
                                ->where('projekt_id', $projekt_id)
                                ->update($data);
                            $totalUpdated++;
                        } else {
                            $data['projekt_firmenname_kuerzel'] = $firma->name_kuerzel;
                            $data['projekt_farbe_hex'] = $firma->bg;
                            $data['letzter_status'] = 'O';
                            $data['letzter_status_name'] = 'Status offen';
                            $data['letzter_status_bg_hex'] = '653191';
                            $data['letzter_status_farbe_hex'] = 'fff';
                            $data['abgeschlossen_status'] = 'Angebot nicht abgeschlossen';
                            
                            DB::table('angebot_tabelle')->insert($data);
                            $existingAngebote[$lookupKey] = true;
                            $totalInserted++;
                        }

                        // 3. Artikel-Positionen synchronisieren (immer bei Änderungen oder neuen Einträgen)
                        // Nur wenn das Angebot innerhalb der letzten 30 Tage erstellt wurde oder wir keine Artikel haben
                        $hasArticles = DB::table('angebot_artikel')->where('jtl_angebot_id', $angebot_id)->exists();
                        $isRecent = strtotime($data['erstelldatum']) > strtotime('-30 days');

                        if (!$hasArticles || $isRecent) {
                            $positions = $wawi_db->prepare("
                                SELECT cString, cArtNr, nAnzahl, fVkNetto, fMwSt, cEinheit
                                FROM Kunde.lvAngebotsPositionen
                                WHERE kBestellung = :kBestellung
                                ORDER BY nSort
                            ");
                            $positions->execute(['kBestellung' => $angebot_id]);
                            $rows = $positions->fetchAll();

                            if (!empty($rows)) {
                                $lokal_id = DB::table('angebot_tabelle')
                                    ->where('angebot_id', $angebot_id)
                                    ->where('projekt_id', $projekt_id)
                                    ->value('id');

                                if ($lokal_id) {
                                    // Bestehende Positionen löschen und neu schreiben
                                    DB::table('angebot_artikel')->where('angebot_id_lokal', $lokal_id)->delete();
                                    
                                    foreach ($rows as $index => $pos) {
                                        DB::table('angebot_artikel')->insert([
                                            'angebot_id_lokal' => $lokal_id,
                                            'jtl_angebot_id' => $angebot_id,
                                            'sort_order' => $index,
                                            'art_nr' => $pos['cArtNr'] ?? '',
                                            'bezeichnung' => $pos['cString'] ?? '',
                                            'menge' => $pos['nAnzahl'] ?? 0,
                                            'einheit' => $pos['cEinheit'] ?? 'Stk.',
                                            'einzelpreis_netto' => $pos['fVkNetto'] ?? 0,
                                            'mwst_prozent' => $pos['fMwSt'] ?? 0,
                                            'gesamt_netto' => ($pos['nAnzahl'] ?? 0) * ($pos['fVkNetto'] ?? 0),
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    }
                                }
                            }
                        }
                    } catch (Exception $rowEx) {
                        $this->error("Fehler bei Angebot #{$angebot_id}: " . $rowEx->getMessage());
                        $totalErrors++;
                    }
                }

            } catch (Exception $e) {
                $this->error("Fehler bei Mandant {$wawi->dataname}: " . $e->getMessage());
                Log::error("JTL Import Error ({$wawi->dataname}): " . $e->getMessage());
                $totalErrors++;
            }
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Import abgeschlossen in {$duration}s.");
        $this->info("Neu: {$totalInserted} | Aktualisiert: {$totalUpdated} | Übersprungen: {$totalSkipped} | Fehler: {$totalErrors}");
        
        Log::info("JTL Angebots-Import abgeschlossen: {$totalInserted} neu, {$totalUpdated} aktualisiert, {$totalErrors} Fehler.");
    }
}
