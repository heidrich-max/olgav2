<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Database Manager</h1>";

    foreach (['auftrag_projekt', 'auftrag_projekt_firma'] as $table) {
        echo "<h2>Table: $table</h2>";
        $stmt = $pdo->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Columns: " . implode(", ", $cols) . "</p>";
    }

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
