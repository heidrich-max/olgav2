<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Known working credentials from inspect_db.php
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Database Research: SMTP Data (cms_frankgroup)</h1>";

    $tables = ['auftrag_projekt', 'auftrag_projekt_firma', 'projekt'];

    foreach ($tables as $table) {
        echo "<h2>Table: $table</h2>";
        try {
            $stmt = $pdo->query("SELECT * FROM $table");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                echo "<table border='1'><tr>";
                foreach (array_keys($data[0]) as $col) echo "<th>$col</th>";
                echo "</tr>";
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $col => $val) {
                        // show full password for identification
                        echo "<td>" . htmlspecialchars($val) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Keine Daten in '$table' gefunden.";
            }
        } catch (Exception $e) {
            echo "Fehler bei '$table': " . $e->getMessage();
        }
    }

    echo "<h2>Alle verfügbaren Tabellen</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul><li>" . implode("</li><li>", $allTables) . "</li></ul>";

} catch (Exception $e) {
    echo 'Verbindungsfehler: ' . $e->getMessage();
}
