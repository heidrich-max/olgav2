<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Inspection of '$tableName'</h1>";

    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
    if ($stmt->rowCount() == 0) {
        echo "Table '$tableName' NOT FOUND. Checking all tables...<br>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<ul><li>" . implode("</li><li>", $tables) . "</li></ul>";
        exit;
    }

    echo "<h2>Columns in $tableName:</h2>";
    $stmt = $pdo->query("DESCRIBE $tableName");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    foreach ($cols as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>Sample Data (Sanitized):</h2>";
    $stmt = $pdo->query("SELECT * FROM $tableName");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "<h3>Project: " . ($row['projekt'] ?? 'unknown') . "</h3>";
        echo "<pre>";
        foreach ($row as $key => $value) {
            // Sanitize password-like fields
            if (stripos($key, 'pass') !== false) {
                echo "$key: ********\n";
            } else {
                echo "$key: $value\n";
            }
        }
        echo "</pre>";
    }

} catch (PDOException $e) {
    echo 'Fehler: ' . $e->getMessage();
}
