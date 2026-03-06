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

    $backupTable = "auftrag_tabelle_backup_" . date('Ymd_His');
    echo "Creating backup table $backupTable...\n";
    
    $pdo->exec("CREATE TABLE `$backupTable` AS SELECT * FROM auftrag_tabelle");
    
    echo "Backup created successfully: $backupTable\n";
    
    $count = $pdo->query("SELECT COUNT(*) FROM `$backupTable`")->fetchColumn();
    echo "Backup contains $count rows.\n";

} catch (PDOException $e) {
    echo "Connection or Query failed: " . $e->getMessage() . "\n";
}
