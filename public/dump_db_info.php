<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    ob_start();
    echo "Structure of angebot_tabelle:\n";
    $stmt = $pdo->query("DESCRIBE angebot_tabelle");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        print_r($col);
    }

    echo "\nOne Sample Result:\n";
    $stmt = $pdo->query("SELECT * FROM angebot_tabelle LIMIT 1");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($sample);

    $output = ob_get_clean();
    file_put_contents(__DIR__ . '/db_info.txt', $output);
    echo "Output written to db_info.txt";

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
