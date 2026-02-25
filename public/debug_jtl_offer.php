<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mini-Bootstrap für DB
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Local MySQL Connection failed: ' . $e->getMessage());
}

$searchNum = 'FWAB.022026-5199';
echo "<h1>Debug JTL Import for $searchNum</h1>";

// 1. Get WAWI Connections
$wawiConnections = $pdo->query("SELECT * FROM auftrag_projekt_wawi")->fetchAll();

// 2. Get Lookup Maps
$firmenMap = $pdo->query("SELECT id, firma_id, name, name_kuerzel, bg FROM auftrag_projekt_firma")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
$aliasMap = $pdo->query("SELECT apfn.begriff, apf.name 
                         FROM auftrag_projekt_firma_namen apfn
                         JOIN auftrag_projekt_firma apf ON apf.id = apfn.name_id")
                ->fetchAll(PDO::FETCH_KEY_PAIR);

foreach ($wawiConnections as $wawi) {
    echo "<h2>Checking Mandant: {$wawi['dataname']} ({$wawi['host']})</h2>";
    
    try {
        $dsn = "sqlsrv:Server={$wawi['host']};Database={$wawi['dataname']};TrustServerCertificate=yes";
        $wawi_db = new PDO($dsn, $wawi['username'], $wawi['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        echo "<p style='color:green'>Connected to JTL Wawi.</p>";

        // Search for the offer
        $stmt = $wawi_db->prepare("SELECT dErstellt, kBestellung, cBenutzername, cAngebotsnummer,
                                          cRechnungsadresseFirma, cStatustext, fAngebotswert, cFirmenname
                                   FROM Kunde.lvAngebote
                                   WHERE cAngebotsnummer = ?");
        $stmt->execute([$searchNum]);
        $offer = $stmt->fetch();

        if ($offer) {
            echo "<h3>Offer FOUND in JTL!</h3>";
            echo "<pre>" . print_r($offer, true) . "</pre>";
            
            // Trace Logic
            $projekt_firmenname = trim($offer['cFirmenname'] ?? '');
            echo "Initial Firmenname from JTL: '$projekt_firmenname'<br>";
            
            if (!isset($firmenMap[$projekt_firmenname]) && isset($aliasMap[$projekt_firmenname])) {
                echo "Alias matched: '$projekt_firmenname' -> '{$aliasMap[$projekt_firmenname]}'<br>";
                $projekt_firmenname = $aliasMap[$projekt_firmenname];
            }

            if (empty($projekt_firmenname)) {
                echo "<b style='color:red'>FAILURE: Firmenname is EMPTY!</b><br>";
            } elseif (!isset($firmenMap[$projekt_firmenname])) {
                echo "<b style='color:red'>FAILURE: Firmenname '$projekt_firmenname' NOT FOUND in local 'auftrag_projekt_firma'!</b><br>";
                echo "Available names in map: " . implode(", ", array_keys($firmenMap)) . "<br>";
            } else {
                echo "<b style='color:green'>SUCCESS: Firmenname matched to ID {$firmenMap[$projekt_firmenname]['id']}</b><br>";
                echo "Firma: " . print_r($firmenMap[$projekt_firmenname], true) . "<br>";
            }

        } else {
            echo "<p style='color:orange'>Offer $searchNum not found in this Mandant.</p>";
            
            // List last 10 to see what's there
            echo "<h4>Last 10 offers in JTL:</h4>";
            $lastOnes = $wawi_db->query("SELECT TOP 10 cAngebotsnummer, dErstellt FROM Kunde.lvAngebote ORDER BY dErstellt DESC")->fetchAll();
            foreach($lastOnes as $l) {
                echo "{$l['cAngebotsnummer']} ({$l['dErstellt']})<br>";
            }
        }

    } catch (Exception $e) {
        echo "<p style='color:red'>Connection/Query Error: " . $e->getMessage() . "</p>";
    }
}
