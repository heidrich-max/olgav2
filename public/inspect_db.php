<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    
    echo "<h1>Old Project Data Analysis</h1>";
    $stmt = $pdo->query("SELECT id, projekt, firmenname, strasse, plz, ort, telefon, inhaber, ust_id, handelsregister FROM auftrag_projekt");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>ID</th><th>Projekt</th><th>Firma</th><th>Strasse</th><th>PLZ</th><th>Ort</th></tr>";
    foreach ($data as $row) {
        echo "<tr><td>{$row['id']}</td><td>{$row['projekt']}</td><td>{$row['firmenname']}</td><td>{$row['strasse']}</td><td>{$row['plz']}</td><td>{$row['ort']}</td></tr>";
    }
    echo "</table>";
    echo "<hr>";

    $interesting = ['angebot_tabelle', 'angebot_artikel', 'angebot_details', 'angebot_status', 'angebot_status_a', 'rechnung', 'liefer', 'adresse', 'projekt'];
    
    foreach ($tables as $table) {
        $isInteresting = false;
        foreach ($interesting as $keyword) {
            if (strpos($table, $keyword) !== false) {
                $isInteresting = true;
                break;
            }
        }
        
        if ($isInteresting) {
            echo "<h1>Transferred Data Verification</h1>";
    $stmt = $pdo->query("SELECT name, strasse, plz, ort, telefon, ust_id FROM auftrag_projekt_firma");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Name</th><th>Strasse</th><th>PLZ</th><th>Ort</th><th>Tel</th><th>UStID</th></tr>";
    foreach ($data as $row) {
        echo "<tr><td>{$row['name']}</td><td>{$row['strasse']}</td><td>{$row['plz']}</td><td>{$row['ort']}</td><td>{$row['telefon']}</td><td>{$row['ust_id']}</td></tr>";
    }
    echo "</table>";
    echo "<hr>";
            echo "<h2>Columns in $table</h2>";
            $stmt = $pdo->query("DESCRIBE $table");
            $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
            foreach ($cols as $col) {
                echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
            }
            echo "</table>";
            
            // Show sample data for offer tables
            if (strpos($table, 'angebot') !== false) {
                echo "<h3>Sample data (1 row) from $table</h3>";
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data) {
                    echo "<pre>" . print_r($data, true) . "</pre>";
                }
            }
        }
    }

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
