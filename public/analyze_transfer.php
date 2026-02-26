<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Data Transfer Analysis</h1>";

    // Get old data
    echo "<h2>Old Data (auftrag_projekt)</h2>";
    $stmt = $pdo->query("SELECT id, projekt, firmenname, host, port, email_pas, passwort FROM auftrag_projekt");
    $oldProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'><tr><th>ID</th><th>Projekt</th><th>Firma</th><th>Host</th><th>Email</th></tr>";
    foreach ($oldProjects as $op) {
        echo "<tr><td>{$op['id']}</td><td>{$op['projekt']}</td><td>{$op['firmenname']}</td><td>{$op['host']}</td><td>{$op['email_pas']}</td></tr>";
    }
    echo "</table>";

    // Get new data structure
    echo "<h2>New Target (auftrag_projekt_firma)</h2>";
    $stmt = $pdo->query("SELECT id, name, projekt_id FROM auftrag_projekt_firma");
    $newProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Old Projekt ID</th></tr>";
    foreach ($newProjects as $np) {
        echo "<tr><td>{$np['id']}</td><td>{$np['name']}</td><td>{$np['projekt_id']}</td></tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
