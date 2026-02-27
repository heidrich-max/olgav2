<?php
/**
 * OLGA - JTL Column Debugger
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $wawi = DB::table('auftrag_projekt_wawi')->first(); // Nimm den ersten Mandanten zum Testen
    if (!$wawi) throw new Exception("Kein JTL-Mandant gefunden.");

    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Spalten der View LVAngebote abrufen
    $stmt = $wawi_db->query("SELECT TOP 1 * FROM Kunde.lvAngebote");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $columns = array_keys($row);
    $output = "Spalten in Kunde.lvAngebote:\n" . implode("\n", $columns);

    file_put_contents(__DIR__ . '/jtl_columns.txt', $output);
    
    echo "<h1>Erfolg!</h1><p>Spalten wurden in public/jtl_columns.txt gespeichert.</p>";

} catch (Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
