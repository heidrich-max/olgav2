<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Database Manager</h1>";

    $table = 'auftrag_projekt_firma';
    echo "<h2>Table: $table</h2>";
    $stmt = $pdo->query("SELECT * FROM $table LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "<h3>Project: " . ($row['name'] ?? 'unbekannt') . "</h3>";
        echo "<pre>";
        foreach ($row as $k => $v) {
            if (stripos($k, 'pass') !== false) $v = '********';
            echo "$k: $v\n";
        }
        echo "</pre>";
    }

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
