<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cms_frankgroup', 'cms_frankgroup', 'tpU~1t787');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Address Data Verification (auftrag_projekt_firma)</h1>";
    $stmt = $pdo->query("SELECT name, strasse, plz, ort, telefon, inhaber, ust_id, handelsregister FROM auftrag_projekt_firma");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>
            <tr>
                <th>Projekt</th>
                <th>Strasse</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Tel</th>
                <th>Inhaber</th>
                <th>UStID</th>
                <th>Handelsregister</th>
            </tr>";
    foreach ($data as $row) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['strasse']}</td>
                <td>{$row['plz']}</td>
                <td>{$row['ort']}</td>
                <td>{$row['telefon']}</td>
                <td>{$row['inhaber']}</td>
                <td>{$row['ust_id']}</td>
                <td>{$row['handelsregister']}</td>
              </tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage();
}
