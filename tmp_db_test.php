<?php
$configs = [
    ['host' => '127.0.0.1', 'port' => '3306'],
    ['host' => 'localhost', 'port' => '3306'],
];

foreach ($configs as $config) {
    echo "Testing {$config['host']}:{$config['port']}...\n";
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=cms_frankgroup";
        $pdo = new PDO($dsn, 'cms_frankgroup', 'tpU~1t787', [PDO::ATTR_TIMEOUT => 2]);
        echo "Successfully connected to {$config['host']}!\n";
        
        $res = $pdo->query("SELECT COUNT(*) as count FROM auftrag_tabelle")->fetch(PDO::FETCH_ASSOC);
        echo "Order count: " . $res['count'] . "\n";
    } catch (Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}
