<?php
// migrate_todos.php
// Dieses Script sucht nach alten ToDos (Text-basiert) und verknüpft sie mit den entsprechenden Angeboten.
// Außerdem werden sie als "System-ToDos" markiert.

use App\Models\Todo;
use Illuminate\Support\Facades\DB;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "<h1>ToDo-Migration gestartet</h1>";
    
    // 1. Suche nach ToDos ohne offer_id, die wie System-ToDos aussehen
    $todos = Todo::whereNull('offer_id')
                 ->where(function($query) {
                     $query->where('task', 'like', 'Angebots-Nachverfolgung:%')
                           ->orWhere('task', 'like', 'Wiedervorlage Angebot%');
                 })
                 ->get();

    echo "Gefundene Kandidaten für Migration: " . $todos->count() . "<br><ul>";
    
    $migratedCount = 0;
    foreach ($todos as $todo) {
        $offerNum = null;
        
        // Extrahiere Angebotsnummer
        // Variante A: "Angebots-Nachverfolgung: WBAN.012026-9129 (..."
        if (preg_match('/Angebots-Nachverfolgung:\s*([A-Z0-9\.-]+)/i', $todo->task, $matches)) {
            $offerNum = trim($matches[1]);
        }
        // Variante B: "Wiedervorlage Angebot WBAN.012026-9130: ..."
        elseif (preg_match('/Wiedervorlage Angebot\s*([A-Z0-9\.-]+)/i', $todo->task, $matches)) {
            $offerNum = trim($matches[1]);
        }
        
        if ($offerNum) {
            $offer = DB::table('angebot_tabelle')->where('angebotsnummer', $offerNum)->first();
            
            if ($offer) {
                $todo->update([
                    'offer_id' => $offer->id,
                    'is_system' => true
                ]);
                echo "<li>Migriert: ID {$todo->id} -> Angebot {$offerNum} (ID: {$offer->id})</li>";
                $migratedCount++;
            } else {
                echo "<li style='color:orange'>Warnung: Angebot {$offerNum} für ToDo #{$todo->id} nicht in Datenbank gefunden.</li>";
            }
        } else {
            echo "<li style='color:red'>Fehler: Konnte Angebotsnummer aus Task-Text nicht extrahieren: \"{$todo->task}\"</li>";
        }
    }
    
    echo "</ul><br><b>Migration abgeschlossen. {$migratedCount} ToDos wurden aktualisiert.</b>";
    
} catch (\Exception $e) {
    echo "<h2 style='color:red'>Fehler bei der Migration: " . $e->getMessage() . "</h2>";
}
