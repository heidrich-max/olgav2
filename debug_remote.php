<?php
$host = 'cms.frankgroup.net';
$user = 'cms_frankgroup';
$pass = 'tpU~1t787';
$db   = 'cms_frankgroup';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to remote DB at $host!\n\n";

    // Check Order
    $orderNum = 'FWAU.032026-6068';
    $stmt = $pdo->prepare("SELECT id, auftragsnummer, lieferdatum, abgeschlossen_status, benutzer FROM auftrag_tabelle WHERE auftragsnummer = ?");
    $stmt->execute([$orderNum]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo "Auftragsdaten für $orderNum:\n";
        print_r($order);
    } else {
        echo "Auftrag $orderNum wurde nicht gefunden.\n";
    }

    echo "\n-------------------\n";

    // Check Offer
    $offerNum = 'FWAB.022026-5202';
    $stmt = $pdo->prepare("SELECT id, angebotsnummer, erstelldatum, letzter_status_name, benutzer, reminder_date FROM angebot_tabelle WHERE angebotsnummer = ?");
    $stmt->execute([$offerNum]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($offer) {
        echo "Angebotsdaten für $offerNum:\n";
        print_r($offer);
    } else {
        echo "Angebot $offerNum wurde nicht gefunden.\n";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
