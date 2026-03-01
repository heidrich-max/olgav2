<?php

/**
 * Manuelles Datenbank-Update für die Wiedervorlage-Funktion.
 * Erstellt die Spalten 'wiedervorlage_datum' und 'wiedervorlage_text' in der 'angebot_tabelle'.
 */

// Lade Laravel Umgebung (falls möglich) oder definiere DB-Zugriff manuell
// Da wir uns im Root-Verzeichnis befinden sollten:
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<h1>Datenbank-Update: Wiedervorlage</h1>";

try {
    if (!Schema::hasColumn('angebot_tabelle', 'wiedervorlage_datum')) {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            $table->date('wiedervorlage_datum')->nullable()->after('letzter_status_farbe_hex');
            $table->text('wiedervorlage_text')->nullable()->after('wiedervorlage_datum');
        });
        echo "<p style='color: green;'>✅ Spalten 'wiedervorlage_datum' und 'wiedervorlage_text' wurden erfolgreich zur Tabelle 'angebot_tabelle' hinzugefügt.</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Die Spalten existieren bereits in der Tabelle 'angebot_tabelle'. Keine Aktion erforderlich.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color: red;'>❌ Fehler beim Update: " . $e->getMessage() . "</p>";
    
    echo "<h3>Manueller SQL-Befehl (falls das Skript oben fehlschlägt):</h3>";
    echo "<pre>ALTER TABLE angebot_tabelle ADD COLUMN wiedervorlage_datum DATE NULL AFTER letzter_status_farbe_hex, ADD COLUMN wiedervorlage_text TEXT NULL AFTER wiedervorlage_datum;</pre>";
}

echo "<hr><p>Bitte lösche dieses Skript nach der Ausführung aus Sicherheitsgründen vom Server.</p>";
