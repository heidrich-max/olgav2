<?php
// Standalone Wawi Inspector
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $wawi = DB::table('auftrag_projekt_wawi')->first();
    if (!$wawi) {
        die("<h1>Fehler</h1><p>Kein Wawi-Mandant in der lokalen DB gefunden.</p>");
    }

    echo "<h1>JTL Wawi Inspektion: {$wawi->dataname}</h1>";
    echo "<p>Versuche Verbindung zu <b>{$wawi->host}</b>...</p>";

    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $wawi->username, $wawi->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE => true,
    ]);

    echo "<p style='color:green'>Verbindung erfolgreich!</p>";

    $checkTables = [
        'Kunde.lvAngebotsPositionen',
        'tBestellPos',
    ];

    foreach ($checkTables as $table) {
        echo "<h2>Tabelle: $table</h2>";
        try {
            // Spalten abrufen
            $stmt = $pdo->prepare("SELECT TOP 0 * FROM $table");
            $stmt->execute();
            $colCount = $stmt->columnCount();
            
            echo "<table border='1' style='border-collapse:collapse; font-size:12px;'>";
            echo "<tr style='background:#eee;'><th>Index</th><th>Name</th></tr>";
            for ($i = 0; $i < $colCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                echo "<tr><td>$i</td><td><b>{$meta['name']}</b></td></tr>";
            }
            echo "</table>";

            // Beispiel-Zeile
            echo "<h3>Beispiel-Daten (Top 1):</h3>";
            $row = $pdo->query("SELECT TOP 1 * FROM $table")->fetch();
            if ($row) {
                echo "<pre style='background:#f4f4f4; padding:10px; border:1;'>" . print_r($row, true) . "</pre>";
            } else {
                echo "<p>Keine Daten gefunden.</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color:red;'>Fehler beim Lesen von $table: " . $e->getMessage() . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<h1>Kritischer Fehler</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
