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
            try {
                echo "--- Diagnosis for Verkauf.lvAuftragsverwaltung ---\n";
                $checkCols = $wawi_db->query("SELECT TOP 1 * FROM Verkauf.lvAuftragsverwaltung")->fetch();
                if ($checkCols) {
                    $allCols = array_keys($checkCols);
                    $foundGroup = preg_grep('/Gruppe/i', $allCols);
                    $foundCat = preg_grep('/Kategorie/i', $allCols);
                    echo "  Potential Group: " . implode(", ", $foundGroup) . "\n";
                    echo "  Potential Cat: " . implode(", ", $foundCat) . "\n";
                }

                echo "--- Diagnosis for Kunde.tKunde --- (via kKunde link)\n";
                try {
                    $checkKunde = $wawi_db->query("SELECT TOP 1 * FROM Kunde.tKunde")->fetch();
                    if ($checkKunde) {
                        $kundeCols = array_keys($checkKunde);
                        echo "  Kunde Columns: " . implode(", ", preg_grep('/Kategorie|Gruppe/i', $kundeCols)) . "\n";
                    }
                } catch (Exception $e) { echo "  Kunde.tKunde not accessible: " . $e->getMessage() . "\n"; }

                echo "--- List of all Tables/Views with 'Kategorie' in name ---\n";
                $tables = $wawi_db->query("SELECT TABLE_SCHEMA, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%Kategorie%'")->fetchAll();
                foreach($tables as $t) {
                    echo "  " . $t['TABLE_SCHEMA'] . "." . $t['TABLE_NAME'] . "\n";
                }
                
                exit("Diagnostic finished.");
            } catch (Exception $e) {
                echo "  Error querying WAWI: " . $e->getMessage() . "\n";
            }

        } catch (Exception $e) {
            echo "  Error connecting to WAWI: " . $e->getMessage() . "\n";
        }
    }

    echo "\nBackfill finished.\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
