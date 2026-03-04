<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use Exception;

class ImportJtlOrderArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-jtl-order-articles {--order_id= : Specific local order ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronisiert Artikelpositionen für Aufträge aus JTL-Wawi Mandanten';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starte Auftragsartikel-Import...");
        $startTime = microtime(true);

        $specificOrderId = $this->option('order_id');

        // 1. WAWI Mandanten abrufen
        $wawiConnections = DB::table('auftrag_projekt_wawi')->get();
        
        $totalSynced = 0;
        $totalErrors = 0;
        $totalOrdersProcessed = 0;

        foreach ($wawiConnections as $wawi) {
            $this->info("Verarbeite Mandant: {$wawi->dataname} ({$wawi->host})");
            
            try {
                // Dynamische Verbindung zu SQL Server (JTL)
                $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
                $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE => true,
                ]);

                // Aufträge laden, die zu diesem Mandanten gehören könnten
                // Wir nutzen projekt_id und verknüpfen über auftrag_projekt_firma
                $query = DB::table('auftrag_tabelle')
                    ->join('auftrag_projekt_firma', 'auftrag_tabelle.projekt_id', '=', 'auftrag_projekt_firma.id')
                    // Wir nehmen an, dass der Name im Wawi-Eintrag dem Projektnamen entspricht oder im Mandantennamen steckt
                    // Da wir nicht sicher sind, laden wir alle Aufträge der letzten Zeit, wenn kein Mandant Match möglich ist
                    // Aber im PHP Script ImportJtlOffers wird $wawi->name nicht direkt für Join genutzt.
                    // Wir nehmen alle Aufträge, die zeitlich relevant sind (z.B. letzte 30 Tage) oder spezifisch angefordert wurden
                    ->select('auftrag_tabelle.*');

                if ($specificOrderId) {
                    $query->where('auftrag_tabelle.id', $specificOrderId);
                } else {
                    $query->where('auftrag_tabelle.erstelldatum', '>=', date('Y-m-d', strtotime('-30 days')));
                }

                $orders = $query->get();

                foreach ($orders as $order) {
                    $totalOrdersProcessed++;
                    try {
                        // Prüfen ob Auftrag in diesem Mandanten existiert (kAuftrag = auftrag_id)
                        $check = $wawi_db->prepare("SELECT kAuftrag FROM Verkauf.lvAuftragsverwaltung WHERE kAuftrag = :kAuftrag");
                        $check->execute(['kAuftrag' => $order->auftrag_id]);
                        if (!$check->fetch()) {
                            continue; // Nicht in diesem Mandanten
                        }

                        $this->info("Synchronisiere Artikel für Auftrag #{$order->auftragsnummer} (ID: {$order->auftrag_id})");

                        // Artikel abrufen
                        $positions = $wawi_db->prepare("
                            SELECT cString, cArtNr, nAnzahl, fVkNetto, fMwSt, cEinheit
                            FROM tBestellPos
                            WHERE tBestellung_kBestellung = :kBestellung
                            ORDER BY nSort
                        ");
                        $positions->execute(['kBestellung' => $order->auftrag_id]);
                        $rows = $positions->fetchAll();

                        if (!empty($rows)) {
                            DB::transaction(function() use ($order, $rows) {
                                DB::table('auftrag_artikel')->where('auftrag_id_lokal', $order->id)->delete();
                                
                                foreach ($rows as $index => $pos) {
                                    DB::table('auftrag_artikel')->insert([
                                        'auftrag_id_lokal' => $order->id,
                                        'jtl_auftrag_id' => $order->auftrag_id,
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
                            });
                            $totalSynced++;
                        }
                    } catch (Exception $rowEx) {
                        $this->error("Fehler bei Auftrag #{$order->auftragsnummer}: " . $rowEx->getMessage());
                        $totalErrors++;
                    }
                }

            } catch (Exception $e) {
                $this->error("Fehler bei Mandant {$wawi->dataname}: " . $e->getMessage());
                Log::error("JTL Auftrag Artikel Import Error ({$wawi->dataname}): " . $e->getMessage());
                $totalErrors++;
            }
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Import abgeschlossen in {$duration}s.");
        $this->info("Verarbeitet: {$totalOrdersProcessed} | Synchronisiert: {$totalSynced} | Fehler: {$totalErrors}");
        
        Log::info("JTL Auftragsartikel-Import abgeschlossen: {$totalSynced} Aufträge synchronisiert, {$totalErrors} Fehler.");
    }
}
