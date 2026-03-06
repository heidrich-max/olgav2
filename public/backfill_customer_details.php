<?php
/**
 * Backfill script for customer group and category.
 * This script is intended to be run ON THE SERVER because it needs the sqlsrv driver.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "Starting backfill...\n";

try {
    // 1. Get Wawi connections
    $wawis = DB::table('auftrag_projekt_wawi')->get();
    echo "Found " . $wawis->count() . " WAWI connections.\n";

    foreach ($wawis as $wawi) {
        echo "Processing WAWI: {$wawi->dataname} ({$wawi->host})...\n";
        
        try {
            $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
            $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            echo "  Connected to WAWI.\n";

            // 2. Fetch data from WAWI (NO DATE FILTER as requested)
            $query = "
                SELECT kAuftrag, cKundenGruppeName, cKundenKategorieName
                FROM Verkauf.lvAuftragsverwaltung
            ";
            
            $orders = $wawi_db->query($query)->fetchAll();
            echo "  Fetched " . count($orders) . " orders from WAWI.\n";

            // 3. Update Olgav2 DB
            $updated = 0;
            foreach ($orders as $o) {
                $affected = DB::table('auftrag_tabelle')
                    ->where('auftrag_id', $o['kAuftrag'])
                    // Wir filtern NICHT nach Projekt_id, da auftrag_id im JTL Mandanten eindeutig ist
                    ->update([
                        'kundengruppe' => $o['cKundenGruppeName'] ?? '',
                        'kundenkategorie' => $o['cKundenKategorieName'] ?? ''
                    ]);
                
                if ($affected > 0) {
                    $updated++;
                }
            }
            echo "  Updated $updated records in Olgav2 DB for this WAWI.\n";

        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }

    echo "\nBackfill finished.\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
