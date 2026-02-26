<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $stmt = $pdo->query("SELECT name, strasse, plz, ort FROM auftrag_projekt_firma WHERE strasse != '' LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "Keine Adressdaten gefunden.";
    } else {
        foreach ($rows as $row) {
            echo "Projekt: {$row['name']} | Strasse: {$row['strasse']} | PLZ: {$row['plz']} | Ort: {$row['ort']}<br>";
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
