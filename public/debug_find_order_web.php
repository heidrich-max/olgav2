<?php
// public/debug_find_order_web.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNo = $_GET['order'] ?? 'PAU.032026-10049';
echo "<h1>Debug Info for Order: $orderNo</h1>";

// 1. Projects and Aliases
echo "<h2>Local Project Configuration</h2>";
$projects = DB::table('auftrag_projekt')->get();
echo "<h3>auftrag_projekt</h3><table border='1'><tr><th>ID</th><th>Firmenname</th><th>Prefix</th></tr>";
foreach($projects as $p) {
    echo "<tr><td>{$p->id}</td><td>{$p->firmenname}</td><td>" . ($p->name_kuerzel ?? 'N/A') . "</td></tr>";
}
echo "</table>";

$aliases = DB::table('auftrag_projekt_firma_namen')
    ->join('auftrag_projekt_firma', 'auftrag_projekt_firma.id', '=', 'auftrag_projekt_firma_namen.name_id')
    ->select('auftrag_projekt_firma_namen.begriff', 'auftrag_projekt_firma.name')
    ->get();
echo "<h3>Aliases (auftrag_projekt_firma_namen)</h3><table border='1'><tr><th>Begriff</th><th>Zielname</th></tr>";
foreach($aliases as $a) {
    echo "<tr><td>{$a->begriff}</td><td>{$a->name}</td></tr>";
}
echo "</table>";

// 2. WAWI Mandanten
echo "<h2>WAWI Mandanten (auftrag_projekt_wawi)</h2>";
$wawis = DB::table('auftrag_projekt_wawi')->get();
echo "<table border='1'><tr><th>Name</th><th>DB</th><th>Projekt ID</th></tr>";
foreach($wawis as $w) {
    echo "<tr><td>{$w->dataname}</td><td>{$w->host}</td><td>{$w->auftrag_projekt_id}</td></tr>";
}
echo "</table>";

// 3. Search in JTL
foreach ($wawis as $wawi) {
    echo "<h3>Checking JTL: {$wawi->dataname}</h3>";
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
            echo "<p style='color:green'>FOUND!</p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
        } else {
            echo "<p>Not found.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}
echo "<hr><p>Done.</p>";
