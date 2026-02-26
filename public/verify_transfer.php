<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $stmt = $pdo->query("SELECT name, smtp_host, smtp_user FROM auftrag_projekt_firma WHERE smtp_host IS NOT NULL");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "Keine Daten übertragen.";
    } else {
        foreach ($rows as $row) {
            echo "Projekt: {$row['name']} | Host: {$row['smtp_host']} | User: {$row['smtp_user']}<br>";
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
