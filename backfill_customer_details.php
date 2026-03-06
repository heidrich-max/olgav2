<?php
/**
 * Backfill script for customer group and category.
 * Connects to remote DB, then to each WAWI, then updates remote DB.
 */

$host = 'cms.frankgroup.net';
$user = 'dev.frankgroup.net';
$pass = 'J7xq7~k19';
$db   = 'cms_frankgroup';

try {
    $remote_pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected to remote Olgav2 DB.\n";

    // 1. Get Wawi connections
    $wawis = $remote_pdo->query("SELECT * FROM auftrag_projekt_wawi")->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($wawis) . " WAWI connections.\n";

    foreach ($wawis as $wawi) {
        echo "Processing WAWI: {$wawi['dataname']} ({$wawi['host']})...\n";
        
        try {
            $dsn = "sqlsrv:Server={$wawi['host']};Database={$wawi['dataname']};TrustServerCertificate=yes";
            $wawi_db = new PDO($dsn, $wawi['username'], $wawi['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            echo "  Connected to WAWI.\n";

            // 2. Fetch data from WAWI
            // We fetch kAuftrag and the customer group/category names
            // Note: I'll try to find the actual names if these guess-names fail.
            $query = "
                SELECT kAuftrag, cKundenGruppeName, cKundenKategorieName
                FROM Verkauf.lvAuftragsverwaltung
            ";
            
            // To be safe, I'll first check if columns exist or just try-catch the query
            try {
                $orders = $wawi_db->query($query)->fetchAll();
                echo "  Fetched " . count($orders) . " orders from WAWI.\n";

                // 3. Update remote Olgav2 DB
                $update_stmt = $remote_pdo->prepare("
                    UPDATE auftrag_tabelle 
                    SET kundengruppe = :group, kundenkategorie = :cat
                    WHERE auftrag_id = :aid
                ");

                $updated = 0;
                foreach ($orders as $o) {
                    $update_stmt->execute([
                        'aid'   => $o['kAuftrag'],
                        'group' => $o['cKundenGruppeName'],
                        'cat'   => $o['cKundenKategorieName']
                    ]);
                    if ($update_stmt->rowCount() > 0) {
                        $updated++;
                    }
                }
                echo "  Updated $updated records in Olgav2 DB for this WAWI.\n";

            } catch (Exception $e) {
                echo "  Error querying WAWI: " . $e->getMessage() . "\n";
                echo "  Attempting to discover correct column names...\n";
                $cols = $wawi_db->query("SELECT TOP 1 * FROM Verkauf.lvAuftragsverwaltung")->fetch();
                if ($cols) {
                    echo "  Available columns: " . implode(", ", array_keys($cols)) . "\n";
                }
            }

        } catch (Exception $e) {
            echo "  Error connecting to WAWI: " . $e->getMessage() . "\n";
        }
    }

    echo "\nBackfill finished.\n";

} catch (PDOException $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
