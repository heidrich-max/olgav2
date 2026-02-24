<?php
$host = 'localhost:3306';
$db   = 'cms_frankgroup';
$user = 'dev.frankgroup.net';
$pass = 'J7xq7~k19';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (\PDOException $e) {
    die('Fehler: ' . $e->getMessage());
}

echo "<h3>Distinct abgeschlossen_status in auftrag_tabelle:</h3>";
$rows = $pdo->query("SELECT DISTINCT abgeschlossen_status, COUNT(*) as cnt FROM auftrag_tabelle GROUP BY abgeschlossen_status ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($rows, true) . "</pre>";

echo "<h3>Distinct letzter_status_name in auftrag_tabelle:</h3>";
$rows2 = $pdo->query("SELECT DISTINCT letzter_status_name, letzter_status, COUNT(*) as cnt FROM auftrag_tabelle GROUP BY letzter_status_name, letzter_status ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($rows2, true) . "</pre>";
?>
