<?php
$host = 'cms.frankgroup.net';
$user = 'dev.frankgroup.net';
$pass = 'J7xq7~k19';
$db   = 'cms_frankgroup';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "Connected successfully to remote DB!\n";

    echo "Describing auftrag_tabelle...\n";
    $res = $pdo->query("DESCRIBE auftrag_tabelle")->fetchAll(PDO::FETCH_ASSOC);
    foreach($res as $r) {
        if (in_array($r['Field'], ['kundennummer', 'kundengruppe', 'kundenkategorie'])) {
            echo "Field: {$r['Field']}, Type: {$r['Type']}, Null: {$r['Null']}\n";
        }
    }
    
    echo "\nSample Row (with kundengruppe/kategorie):\n";
    $sample = $pdo->query("SELECT id, auftragsnummer, kundennummer, kundengruppe, kundenkategorie FROM auftrag_tabelle WHERE kundengruppe IS NOT NULL OR kundenkategorie IS NOT NULL LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($sample) {
        print_r($sample);
    } else {
        echo "No rows found with populated customer group/category.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
