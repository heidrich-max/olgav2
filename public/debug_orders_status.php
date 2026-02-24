<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=cms_frankgroup;charset=utf8', 'cms_frankgroup', 'tpU~1t787');
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

echo "<h1>Unique Status values in auftrag_tabelle</h1>";
$sql = "SELECT abgeschlossen_status, COUNT(*) as count FROM auftrag_tabelle GROUP BY abgeschlossen_status";
$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'><tr><th>Status</th><th>Count</th></tr>";
foreach($res as $row) {
    echo "<tr><td>" . ($row['abgeschlossen_status'] ?: 'NULL/Empty') . "</td><td>{$row['count']}</td></tr>";
}
echo "</table>";

echo "<h1>Recently added orders for comparison</h1>";
$sql_recent = "SELECT auftragsnummer, benutzer, abgeschlossen_status, erstelldatum FROM auftrag_tabelle ORDER BY id DESC LIMIT 10";
$recent = $db->query($sql_recent)->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'><tr><th>Nummer</th><th>Benutzer</th><th>Status</th><th>Datum</th></tr>";
foreach($recent as $r) {
    echo "<tr><td>{$r['auftragsnummer']}</td><td>{$r['benutzer']}</td><td>{$r['abgeschlossen_status']}</td><td>{$r['erstelldatum']}</td></tr>";
}
echo "</table>";
?>
