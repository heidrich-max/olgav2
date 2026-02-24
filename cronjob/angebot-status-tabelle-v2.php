<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $local_db = new PDO('mysql:host=localhost;dbname=cms_frankgroup;charset=utf8', 'cms_frankgroup', 'tpU~1t787', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Verbindung fehlgeschlagen: ' . $e->getMessage());
}

// 1. Status-Lookup-Tabelle einmalig vorab laden (verhindert SQL-Abfragen in der Schleife)
$status_map = [];
foreach ($local_db->query("SELECT id, status_sh, status_lg, bg, color FROM angebot_status") as $s) {
    $status_map[$s['id']] = $s;
}

// 2. Aktuellen Abschluss-Status aller Angebote cachen
$current_states = [];
foreach ($local_db->query("SELECT angebot_id, projekt_id, abgeschlossen_status FROM angebot_tabelle") as $row) {
    $current_states[$row['projekt_id'] . '_' . $row['angebot_id']] = $row['abgeschlossen_status'];
}

// 3. Hoechsten Status pro Angebot ermitteln
$status_query = $local_db->query("
    SELECT angebot_id, projekt_id, MAX(status) as maxstatus 
    FROM angebot_status_a 
    GROUP BY angebot_id, projekt_id
");

// 4. Update-Statement vorbereiten
$update_stmt = $local_db->prepare("
    UPDATE angebot_tabelle 
    SET letzter_status          = :letzter_status, 
        letzter_status_name     = :letzter_status_name, 
        letzter_status_bg_hex   = :letzter_status_bg_hex, 
        letzter_status_farbe_hex = :letzter_status_farbe_hex, 
        abgeschlossen_status    = :abgeschlossen_status 
    WHERE angebot_id = :angebot_id AND projekt_id = :projekt_id
");

$updated = 0;
$skipped = 0;

// 5. Alle Updates in einer Transaktion ausfuehren (schneller & konsistent)
$local_db->beginTransaction();

foreach ($status_query as $row) {
    $angebot_id = $row['angebot_id'];
    $projekt_id = $row['projekt_id'];
    $maxstatus  = $row['maxstatus'];

    // Status aus dem Cache holen - keine weitere SQL-Abfrage noetig
    if (!isset($status_map[$maxstatus])) {
        $skipped++;
        continue;
    }

    $details = $status_map[$maxstatus];

    $letzter_status           = $details['status_sh'];
    $letzter_status_name      = 'Status ' . $details['status_lg'];
    $letzter_status_bg_hex    = $details['bg'];
    $letzter_status_farbe_hex = $details['color'];

    // Abschluss-Status bestimmen
    if ($letzter_status === 'A' || $letzter_status === 'ANG') {
        $abgeschlossen_status = 'Angebot abgeschlossen';
    } else {
        // Bestehenden "Angebot abgeschlossen"-Status beibehalten
        $key = $projekt_id . '_' . $angebot_id;
        $abgeschlossen_status = (isset($current_states[$key]) && $current_states[$key] === 'Angebot abgeschlossen')
            ? 'Angebot abgeschlossen'
            : 'Angebot nicht abgeschlossen';
    }

    $update_stmt->execute([
        'letzter_status'           => $letzter_status,
        'letzter_status_name'      => $letzter_status_name,
        'letzter_status_bg_hex'    => $letzter_status_bg_hex,
        'letzter_status_farbe_hex' => $letzter_status_farbe_hex,
        'abgeschlossen_status'     => $abgeschlossen_status,
        'angebot_id'               => $angebot_id,
        'projekt_id'               => $projekt_id,
    ]);
    $updated++;
}

$local_db->commit();

echo "Synchronisierung abgeschlossen: {$updated} Angebote aktualisiert, {$skipped} uebersprungen.";
