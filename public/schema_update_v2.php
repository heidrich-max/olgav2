<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Schema Update v2</h1>";

    $tableName = 'auftrag_projekt_firma';
    $columns = [
        'smtp_host' => "VARCHAR(255) NULL",
        'smtp_port' => "INT NULL",
        'smtp_user' => "VARCHAR(255) NULL",
        'smtp_password' => "VARCHAR(255) NULL",
        'smtp_encryption' => "VARCHAR(10) NULL",
        'mail_from_address' => "VARCHAR(255) NULL",
        'mail_from_name' => "VARCHAR(255) NULL"
    ];

    $stmt = $pdo->query("DESCRIBE $tableName");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $column => $definition) {
        if (!in_array($column, $existingColumns)) {
            echo "Adding '$column'... ";
            $pdo->exec("ALTER TABLE $tableName ADD COLUMN $column $definition");
            echo "Done.<br>";
        } else {
            echo "'$column' exists.<br>";
        }
    }

    echo "<h2>Finished!</h2>";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
