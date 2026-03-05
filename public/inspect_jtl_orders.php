<?php
// public/inspect_jtl_orders.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $wawi = DB::table('auftrag_projekt_wawi')->first();
    if (!$wawi) {
        die("Kein Wawi-Mandant gefunden.");
    }

    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $wawi->username, $wawi->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h1>JTL Wawi Order Inspection: {$wawi->dataname}</h1>";

    $view = 'Verkauf.lvAuftragsverwaltung';
    echo "<h2>Columns in $view</h2>";
    
    $cols = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'Verkauf' AND TABLE_NAME = 'lvAuftragsverwaltung'")->fetchAll();
    
    if (empty($cols)) {
        // Try without schema prefix in lookup
        $cols = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'lvAuftragsverwaltung'")->fetchAll();
    }

    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    foreach($cols as $c) {
        echo "<tr><td>{$c['COLUMN_NAME']}</td><td>{$c['DATA_TYPE']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>Sample Row from $view</h2>";
    $row = $pdo->query("SELECT TOP 5 * FROM $view ORDER BY dErstellt DESC")->fetch();
    echo "<pre>" . print_r($row, true) . "</pre>";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
