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

    echo "Adding columns kundengruppe and kundenkategorie to auftrag_tabelle...\n";
    
    $pdo->exec("ALTER TABLE auftrag_tabelle ADD kundengruppe VARCHAR(255) NULL AFTER kundennummer, ADD kundenkategorie VARCHAR(255) NULL AFTER kundengruppe");
    
    echo "Columns added successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
