<?php

/**
 * Setup AI Assistant for Olgav2
 * Dieses Skript setzt den OpenAI API-Key in der .env-Datei auf dem Server.
 * SICHERHEITSHINWEIS: Bitte lösche diese Datei nach der Ausführung vom Server!
 */

$apiKey = 'HIER_DEIN_OPENAI_API_KEY_EINTRAGEN';
$envFile = __DIR__ . '/../.env';

echo "<h2>KI-Assistent Setup 🤖</h2>";

if (!file_exists($envFile)) {
    die("<p style='color:red;'>Fehler: .env Datei nicht gefunden unter: $envFile</p>");
}

$content = file_get_contents($envFile);

// Prüfen, ob der Key bereits existiert
if (strpos($content, 'OPENAI_API_KEY=') !== false) {
    // Key aktualisieren
    $content = preg_replace('/OPENAI_API_KEY=.*/', 'OPENAI_API_KEY=' . $apiKey, $content);
    echo "<p>Bestehender API-Key wurde aktualisiert.</p>";
} else {
    // Key am Ende anfügen
    $content .= "\n\n# OpenAI API Configuration\nOPENAI_API_KEY=" . $apiKey . "\n";
    echo "<p>Neuer API-Key wurde zur .env hinzugefügt.</p>";
}

if (file_put_contents($envFile, $content)) {
    echo "<p style='color:green; font-weight:bold;'>Erfolg! Der API-Key wurde erfolgreich in der .env gespeichert.</p>";
    
    // Versuchen, den Cache zu leeren (falls Artisan verfügbar)
    echo "<p>Versuche Konfigurations-Cache zu leeren...</p>";
    @exec('php artisan config:clear 2>&1', $output, $returnVar);
    if ($returnVar === 0) {
        echo "<p style='color:green;'>Artisan: Konfigurations-Cache erfolgreich geleert.</p>";
    } else {
        echo "<p style='color:orange;'>Artisan: Cache konnte nicht automatisch geleert werden (evtl. Cache nicht aktiv oder kein Zugriff).</p>";
    }
} else {
    echo "<p style='color:red;'>Fehler: .env Datei konnte nicht geschrieben werden. Bitte prüfe die Dateirechte.</p>";
}

echo "<hr><p style='color:red; font-weight:bold;'>WICHTIG: Bitte lösche diese Datei (setup_ai.php) jetzt sofort von deinem Server!</p>";
