<?php
// fix_missing_reminder_dates.php

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "<h1>Reparatur: Fehlende Erinnerungs-Daten nachpflegen</h1>";

    $targetDate = Carbon::now()->subDays(8)->toDateString(); // Ein Datum in der Vergangenheit

    $affectedCount = DB::table('angebot_tabelle')
        ->where('letzter_status_name', 'Status Erinnerung verschickt')
        ->whereNull('reminder_date')
        ->update(['reminder_date' => DB::raw('erstelldatum')]); // Nutze Erstellungsdatum als Fallback

    echo "Erfolg: Bei <b>$affectedCount</b> Angeboten wurde das fehlende Erinnerungs-Datum nachgetragen.<br><br>";
    echo "<b>Nächster Schritt:</b> Klicke jetzt auf den Rebuild-Link, um die ToDos dafür zu erzeugen:<br>";
    echo "<a href='/rebuild_todos.php'>👉 ToDos jetzt neu generieren</a>";

} catch (\Exception $e) {
    echo "Fehler bei der Reparatur: " . $e->getMessage();
}
