<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Structure of angebot_tabelle</h1>";
    $stmt = $pdo->query("DESCRIBE angebot_tabelle");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>";
    foreach ($columns as $col) {
        echo "<tr>
                <td>{$col['Field']}</td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>{$col['Default']}</td>
                <td>{$col['Extra']}</td>
              </tr>";
    }
    echo "</table>";

    echo "<h1>One Sample Result (Full Data)</h1>";
    $stmt = $pdo->query("SELECT * FROM angebot_tabelle LIMIT 1");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($sample);
    echo "</pre>";

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
