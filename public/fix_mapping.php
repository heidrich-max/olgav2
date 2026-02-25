<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $begriff = 'FRANK Werbeartikel';
    $name_id = 2; // ID für 'FRANK WERBEARTIKEL' aus der list_firmen.php

    // Prüfen ob bereits vorhanden
    $stmt = $pdo->prepare("SELECT id FROM auftrag_projekt_firma_namen WHERE begriff = ?");
    $stmt->execute([$begriff]);
    if ($stmt->fetch()) {
        echo "Der Alias '$begriff' existiert bereits.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO auftrag_projekt_firma_namen (name_id, begriff) VALUES (?, ?)");
        $stmt->execute([$name_id, $begriff]);
        echo "Erfolg: Alias '$begriff' wurde für Firma ID $name_id angelegt!";
    }

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
