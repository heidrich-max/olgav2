<?php
// public/debug_find_order_web.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// We need Laravel's DB config or just query our own DB for connection info
// This is a quick & dirty approach using the database table directly
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNo = $_GET['order'] ?? 'PAU.032026-10049';
echo "<h1>Searching for Order: $orderNo</h1>";

$wawis = DB::table('auftrag_projekt_wawi')->get();

foreach ($wawis as $wawi) {
    echo "<h2>Checking Mandant: {$wawi->dataname}</h2>";
    try {
        $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
        $pdo = new PDO($dsn, $wawi->username, $wawi->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $query = "SELECT kAuftrag, cAuftragsnummer, cFirmenname, dErstellt, nStorniert 
                  FROM Verkauf.lvAuftragsverwaltung 
                  WHERE cAuftragsnummer = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$orderNo]);
        $result = $stmt->fetch();

        if ($result) {
            echo "<p style='color:green'>FOUND in {$wawi->dataname}!</p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
        } else {
            echo "<p>Not found in {$wawi->dataname}.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error connecting to {$wawi->dataname}: " . $e->getMessage() . "</p>";
    }
}
echo "<hr><p>Done.</p>";
