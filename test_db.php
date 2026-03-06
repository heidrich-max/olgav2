<?php
$host = '127.0.0.1';
$user = 'cms_frankgroup';
$pass = 'tpU~1t787';
$db   = 'cms_frankgroup';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " (Code: " . $conn->connect_errno . ")\n");
}
echo "Connected successfully to {$db}!\n";

$res = $conn->query("SELECT id, auftragsnummer, lieferdatum, abgeschlossen_status, benutzer FROM auftrag_tabelle WHERE auftragsnummer = 'FWAU.032026-6068'");
if ($res && $row = $res->fetch_assoc()) {
    print_r($row);
} else {
    echo "Order not found or query error.\n";
}
$conn->close();
