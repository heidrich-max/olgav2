<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dsn = 'mysql:host=localhost;dbname=frankwerbemittel;charset=utf8';
$user = 'mysqluser';
$password = 'Privat2303';

try {
    $local_db = new PDO($dsn, $user, $password);
    $local_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Alte Projekte (SMTP Einstellungen aus frankwerbemittel)</h1>";
    $stmt = $local_db->query("SELECT id, name, host, port, email_pas, passwort FROM auftrag_projekt");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Host</th>
                <th>Port</th>
                <th>User (email_pas)</th>
                <th>Passwort</th>
            </tr>";
    foreach ($projects as $p) {
        echo "<tr>
                <td>{$p['id']}</td>
                <td>{$p['name']}</td>
                <td>{$p['host']}</td>
                <td>{$p['port']}</td>
                <td>{$p['email_pas']}</td>
                <td>{$p['passwort']}</td>
              </tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
