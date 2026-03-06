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

echo "<h2>1. auftrag_projekt</h2>";
$projects = DB::table('auftrag_projekt')->get();
foreach($projects as $p) {
    echo "PROJ: ID={$p->id}, Name={$p->firmenname}, Kuerzel=" . ($p->name_kuerzel ?? '') . "<br>";
}

echo "<h2>2. Aliases</h2>";
$aliases = DB::table('auftrag_projekt_firma_namen')
    ->join('auftrag_projekt_firma', 'auftrag_projekt_firma.id', '=', 'auftrag_projekt_firma_namen.name_id')
    ->select('auftrag_projekt_firma_namen.begriff', 'auftrag_projekt_firma.name')
    ->get();
foreach($aliases as $a) {
    echo "ALIAS: Begriff={$a->begriff} -> Ziel={$a->name}<br>";
}

echo "<h2>3. WAWI Mandanten (auftrag_projekt_wawi)</h2>";
$wawis = DB::table('auftrag_projekt_wawi')->get();
foreach($wawis as $w) {
    echo "WAWI: Name={$w->dataname}, ProjectID={$w->auftrag_projekt_id}<br>";
}

echo "<h2>4. Search in JTL</h2>";
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
            echo "FOUND in {$wawi->dataname}: " . print_r($result, true) . "<br>";
            
            // Simulation logic
            $fname = trim($result['cFirmenname'] ?? '');
            $fname_lower = strtolower($fname);
            echo "DEBUG: cFirmenname='$fname' (lower: '$fname_lower')<br>";
            
            // Check alias
            $foundAlias = false;
            foreach($aliases as $a) {
                if (strtolower($a->begriff) == $fname_lower) {
                    echo "MATCHED ALIAS: {$a->begriff} -> {$a->name}<br>";
                    $fname = $a->name;
                    $fname_lower = strtolower($fname);
                    $foundAlias = true;
                    break;
                }
            }
            
            // Check project
            $foundProj = false;
            foreach($projects as $p) {
                if (strtolower($p->firmenname) == $fname_lower) {
                    echo "MATCHED PROJECT: ID={$p->id}, Name={$p->firmenname}<br>";
                    $foundProj = true;
                    break;
                }
            }
            
            if (!$foundProj) {
                echo "<b style='color:red'>FAILURE: No matching project found for '$fname'!</b><br>";
            } else {
                echo "<b style='color:green'>SUCCESS: Should be imported!</b><br>";
            }

        } else {
            echo "Not found in {$wawi->dataname}.<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}
echo "<hr><p>Done.</p>";
