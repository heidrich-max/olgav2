<?php
$host = 'localhost:3306';
$db   = 'cms_frankgroup';
$user = 'dev.frankgroup.net';
$pass = 'J7xq7~k19';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die('Connection failed: ' . $e->getMessage());
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'sum_year') {
        echo "<h3>Sum of auftrag_tabelle for 2026:</h3>";
        $res = $pdo->query("SELECT SUM(auftragssumme) as total FROM auftrag_tabelle WHERE firmen_id = 1 AND YEAR(erstelldatum) = 2026")->fetch();
        echo "Total Branding Europe: " . $res['total'] . "<br>";
        
        echo "<h3>Monthly Breakdowns from auftrag_tabelle (2026):</h3>";
        $mo = $pdo->query("SELECT MONTH(erstelldatum) as m, SUM(auftragssumme) as total FROM auftrag_tabelle WHERE firmen_id = 1 AND YEAR(erstelldatum) = 2026 GROUP BY m")->fetchAll();
        echo "<pre>" . print_r($mo, true) . "</pre>";
        
        echo "<h3>Values in auftrag_umsatz for BE Projects:</h3>";
        $ums = $pdo->query("SELECT projekt_id, SUM(netto_umsatz) as total, SUM(netto_umsatz_vorjahr) as vtotal FROM auftrag_umsatz WHERE projekt_id IN (1,2,3) GROUP BY projekt_id")->fetchAll();
        echo "<pre>" . print_r($ums, true) . "</pre>";
    }
} else {
    echo "<a href='?action=sum_year'>Run Discrepancy Analysis</a>";
}
?>
