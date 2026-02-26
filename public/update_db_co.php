<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Füge 'co' Spalte hinzu...</h1>";

    $tableName = 'auftrag_projekt_firma';
    $stmt = $pdo->query("DESCRIBE $tableName");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('co', $existingColumns)) {
        $pdo->exec("ALTER TABLE $tableName ADD COLUMN co VARCHAR(255) NULL AFTER bg");
        echo "<span style='color:green;'>Erfolgreich hinzugefügt.</span>";
    } else {
        echo "Spalte 'co' existiert bereits.";
    }

} catch (PDOException $e) {
    echo 'Fehler: ' . $e->getMessage();
}
