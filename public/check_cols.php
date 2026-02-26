<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $stmt = $pdo->query("DESCRIBE auftrag_projekt_firma");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(", ", $cols);
} catch (Exception $e) {
    echo $e->getMessage();
}
