<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "<h2>Haupt-Firmen (auftrag_projekt_firma)</h2>";
    $firmen = $pdo->query("SELECT * FROM auftrag_projekt_firma")->fetchAll();
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Kuerzel</th></tr>";
    foreach ($firmen as $f) {
        echo "<tr><td>{$f['id']}</td><td>{$f['name']}</td><td>{$f['name_kuerzel']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>Aliase (auftrag_projekt_firma_namen)</h2>";
    $aliase = $pdo->query("SELECT apfn.*, apf.name as firmenname FROM auftrag_projekt_firma_namen apfn JOIN auftrag_projekt_firma apf ON apf.id = apfn.name_id")->fetchAll();
    echo "<table border='1'><tr><th>ID</th><th>Begriff</th><th>Zugeordnete Firma</th></tr>";
    foreach ($aliase as $a) {
        echo "<tr><td>{$a['id']}</td><td>{$a['begriff']}</td><td>{$a['firmenname']}</td></tr>";
    }
    echo "</table>";

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
