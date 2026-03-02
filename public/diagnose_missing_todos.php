<?php
// diagnose_missing_todos.php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "<h1>Diagnose: Fehlende ToDos für Fabian Frank</h1>";

    $userName = 'Fabian Frank';
    $user = User::where('name_komplett', 'like', "%$userName%")->first();
    
    if (!$user) {
        die("Benutzer '$userName' nicht gefunden.");
    }

    echo "Prüfe Angebote für <b>{$user->name_komplett}</b> (ID: {$user->id})...<br><br>";

    $targetDate = Carbon::now()->subDays(7)->toDateString();
    echo "Target Date (<=): $targetDate<br><br>";

    // Suche alle Angebote in relevanten Status
    $offers = DB::table('angebot_tabelle')
        ->where('benutzer', 'like', "%$userName%")
        ->whereIn('letzter_status_name', ['Status offen', 'Status Erinnerung versendet'])
        ->get();

    echo "Gefundene Angebote im System: " . $offers->count() . "<br>";
    echo "<table border='1'>
            <tr>
                <th>Nummer</th>
                <th>Status</th>
                <th>Erstellt</th>
                <th>Remind-Date</th>
                <th>Grund für Fehlen</th>
            </tr>";

    foreach ($offers as $offer) {
        $reason = "Unklar";
        
        // Check Logic from GenerateOfferTodos
        if ($offer->letzter_status_name === 'Status offen') {
            if ($offer->erstelldatum > $targetDate) {
                $reason = "Noch keine 7 Tage alt (Erstellt: $offer->erstelldatum)";
            } else {
                $reason = "Sollte eigentlich ein ToDo haben (7+ Tage offen)";
            }
        } elseif ($offer->letzter_status_name === 'Status Erinnerung versendet') {
            if (!$offer->reminder_date) {
                $reason = "<b>FEHLER: reminder_date ist NULL</b> (System weiß nicht, wann die 7 Tage um sind)";
            } elseif ($offer->reminder_date > $targetDate) {
                $reason = "Erinnerung ist weniger als 7 Tage her ($offer->reminder_date)";
            } else {
                $reason = "Sollte eigentlich ein ToDo haben (7+ Tage nach Erinnerung)";
            }
        }

        // Check if ToDo already exists
        $exists = Todo::where('offer_id', $offer->id)
                     ->where('is_completed', false)
                     ->exists();
        
        if ($exists) {
            $reason = "ToDo existiert bereits (offen)";
        }

        echo "<tr>
                <td>{$offer->angebotsnummer}</td>
                <td>{$offer->letzter_status_name}</td>
                <td>{$offer->erstelldatum}</td>
                <td>" . ($offer->reminder_date ?? 'NULL') . "</td>
                <td>$reason</td>
              </tr>";
    }
    echo "</table>";

} catch (\Exception $e) {
    echo "Fehler bei der Diagnose: " . $e->getMessage();
}
