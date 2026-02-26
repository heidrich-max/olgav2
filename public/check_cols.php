<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    echo "--- auftrag_projekt ---\n";
    $stmt = $pdo->query("DESCRIBE auftrag_projekt");
    echo implode(", ", $stmt->fetchAll(PDO::FETCH_COLUMN)) . "\n\n";
    echo "--- auftrag_projekt_firma ---\n";
    $stmt = $pdo->query("DESCRIBE auftrag_projekt_firma");
    echo implode(", ", $stmt->fetchAll(PDO::FETCH_COLUMN)) . "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
