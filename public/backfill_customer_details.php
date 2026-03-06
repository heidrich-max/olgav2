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

                echo "--- Diagnosis for dbo.tKunde ---\n";
                try {
                    $checkKunde = $wawi_db->query("SELECT TOP 1 * FROM dbo.tKunde")->fetch();
                    if ($checkKunde) {
                        $kundeCols = array_keys($checkKunde);
                        echo "  Kunde Columns (filtered): " . implode(", ", preg_grep('/Kategorie|Gruppe|kKunde/i', $kundeCols)) . "\n";
                    }
                } catch (Exception $e) { echo "  dbo.tKunde not accessible: " . $e->getMessage() . "\n"; }

                echo "--- Diagnosis for dbo.tKundenKategorie ---\n";
                try {
                    $checkKK = $wawi_db->query("SELECT TOP 1 * FROM dbo.tKundenKategorie")->fetch();
                    if ($checkKK) {
                        $kkCols = array_keys($checkKK);
                        echo "  KundenKategorie Columns: " . implode(", ", $kkCols) . "\n";
                    }
                } catch (Exception $e) { echo "  dbo.tKundenKategorie not accessible: " . $e->getMessage() . "\n"; }

                echo "--- Checking link for kKunde link ---\n";
                $res = $wawi_db->query("SELECT TOP 1 v.kKunde, k.kKundenKategorie, kk.cName as KatName 
                                        FROM Verkauf.lvAuftragsverwaltung v
                                        JOIN dbo.tKunde k ON k.kKunde = v.kKunde
                                        JOIN dbo.tKundenKategorie kk ON kk.kKundenKategorie = k.kKundenKategorie")->fetch();
                if ($res) {
                    print_r($res);
                } else {
                    echo "  Could not link Order -> Kunde -> Kategorie.\n";
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
