<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$wawi = DB::table('auftrag_projekt_wawi')->first();
if (!$wawi) {
    die("Kein Wawi-Mandant gefunden.");
}

echo "Verbinde mit: {$wawi->dataname} ({$wawi->host})...\n";

try {
    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $wawi->username, $wawi->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Erfolgreich verbunden.\n\n";

    // 1. Suche nach Positions-Tabellen
    echo "Suche nach Angebot-Tabellen...\n";
    $tables = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'tAngebot%'")->fetchAll();
    foreach($tables as $t) {
        echo "- {$t['TABLE_NAME']}\n";
    }

    // 2. Inspiziere tAngebotPos (falls vorhanden)
    $posTable = 'tAngebotPos';
    echo "\nStruktur von $posTable:\n";
    try {
        $cols = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$posTable'")->fetchAll();
        foreach($cols as $c) {
            echo "  {$c['COLUMN_NAME']} ({$c['DATA_TYPE']})\n";
        }
        
        echo "\nSample Row from $posTable:\n";
        $row = $pdo->query("SELECT TOP 1 * FROM $posTable")->fetch();
        print_r($row);
    } catch(Exception $e) {
        echo "Fehler beim Lesen von $posTable: " . $e->getMessage() . "\n";
    }

    // 3. Suche nach Adress-Tabellen
    echo "\nSuche nach Adresse-Tabellen...\n";
    $tables = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%Adresse%'")->fetchAll();
    foreach($tables as $t) {
        echo "- {$t['TABLE_NAME']}\n";
    }

} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
