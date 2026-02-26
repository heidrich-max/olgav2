<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'user'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h1>Table Info for 'user'</h1>";
    echo "Engine: " . $status['Engine'] . "<br>";
    echo "Collation: " . $status['Collation'] . "<br>";

    $stmt2 = $pdo->query("DESCRIBE user");
    $cols = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Structure</h2><table border='1'><tr><th>Field</th><th>Type</th></tr>";
    foreach ($cols as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo $e->getMessage();
}
