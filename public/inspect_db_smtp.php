<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database credentials from existing inspect_db.php
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Database Research: SMTP Data</h1>";

    // 1. Check table 'projekt' (common name in old systems)
    echo "<h2>Table: projekt</h2>";
    try {
        $stmt = $pdo->query("SELECT * FROM projekt LIMIT 10");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            echo "<table border='1'><tr>";
            foreach (array_keys($data[0]) as $col) echo "<th>$col</th>";
            echo "</tr>";
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $val) echo "<td>" . htmlspecialchars($val) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Keine Daten in 'projekt' gefunden.";
        }
    } catch (Exception $e) {
        echo "Fehler bei 'projekt': " . $e->getMessage();
    }

    // 2. Check table 'angebot_tabelle' (might have project-specific settings)
    echo "<h2>Table: angebot_tabelle (Sample)</h2>";
    try {
        $stmt = $pdo->query("SELECT * FROM angebot_tabelle LIMIT 5");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            echo "<table border='1'><tr>";
            foreach (array_keys($data[0]) as $col) echo "<th>$col</th>";
            echo "</tr>";
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $val) echo "<td>" . htmlspecialchars($val) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Keine Daten in 'angebot_tabelle' gefunden.";
        }
    } catch (Exception $e) {
        echo "Fehler bei 'angebot_tabelle': " . $e->getMessage();
    }

    // 3. Search for anything that looks like SMTP
    echo "<h2>Global Search for SMTP-like columns</h2>";
    try {
        $stmt = $pdo->query("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME LIKE '%smtp%' OR COLUMN_NAME LIKE '%mail%' OR COLUMN_NAME LIKE '%pass%'");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($results as $res) {
            echo "<li>Table: {$res['TABLE_NAME']} | Column: {$res['COLUMN_NAME']}</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "Fehler bei Suche: " . $e->getMessage();
    }

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
