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

    // --- NEU: Übersicht aller Status ---
    echo "<h2>Verfügbare Status in angebot_status:</h2><ul>";
    $allStatuses = DB::table('angebot_status')->select('id', 'status_lg', 'status_sh')->get();
    foreach($allStatuses as $s) {
        echo "<li>ID: {$s->id} | LG: '{$s->status_lg}' | SH: '{$s->status_sh}'</li>";
    }
    echo "</ul>";

    // --- NEU: Alle verwendeten letzter_status_name in angebot_tabelle ---
    echo "<h2>Verwendete 'letzter_status_name' in angebot_tabelle:</h2><ul>";
    $usedStatusNames = DB::table('angebot_tabelle')->distinct()->pluck('letzter_status_name');
    foreach($usedStatusNames as $name) {
        echo "<li>'{$name}'</li>";
    }
    echo "</ul>";

    // --- NEU: Alle Benutzernamen ---
    echo "<h2>Verwendete 'benutzer' in angebot_tabelle:</h2><ul>";
    $usedUserNames = DB::table('angebot_tabelle')->distinct()->pluck('benutzer');
    foreach($usedUserNames as $name) {
        echo "<li>'{$name}'</li>";
    }
    echo "</ul>";

    $userName = 'Fabian Frank';
    $user = User::where('name_komplett', 'like', "%$userName%")->first();
    
    if (!$user) {
        die("Benutzer '$userName' nicht gefunden.");
    }

    echo "<h2>Analyse für <b>{$user->name_komplett}</b> (ID: {$user->id})</h2>";

    $targetDate = Carbon::now()->subDays(7)->toDateString();
    echo "Target Date (<=): $targetDate<br><br>";

    // Suche alle Angebote für diesen Benutzer (großzügigerer Filter)
    $offers = DB::table('angebot_tabelle')
        ->where('benutzer', 'like', "%Fabian%") // Noch großzügiger suchen
        ->where(function($q) {
            $q->where('letzter_status_name', 'like', '%offen%')
              ->orWhere('letzter_status_name', 'like', '%Erinnerung%');
        })
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
