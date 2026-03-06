<?php
// public/inspect_legacy_code.php
$file = '/var/www/vhosts/frankgroup.net/httpdocs/frankgroup/cms/parameter.php';
if (file_exists($file)) {
    echo "<h1>Inhalt von parameter.php (Ausschnitt um Zeile 74)</h1>";
    $lines = file($file);
    echo "<pre>";
    for ($i = max(0, 74 - 20); $i < min(count($lines), 74 + 20); $i++) {
        $num = $i + 1;
        $mark = ($num == 74) ? " >>> " : "     ";
        echo $num . $mark . htmlspecialchars($lines[$i]);
    }
    echo "</pre>";
} else {
    echo "Datei $file nicht gefunden. Prüfe alternative Pfade...";
    // Falls der Pfad lokal anders ist (Windows/Linux Mapping)
    $local_path = "C:/inetpub/vhosts/frankgroup.net/httpdocs/frankgroup/cms/parameter.php"; 
    if (file_exists($local_path)) {
        echo "Lokal gefunden...";
    }
}
