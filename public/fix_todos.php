<?php
// fix_orphan_todos.php
// Dieses Script sucht nach ToDos für ein bestimmtes Angebot, die keine offer_id haben, und löscht sie.

use App\Models\Todo;
use Illuminate\Support\Facades\DB;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$offerNumber = 'FWAB.022026-5190';

try {
    // Suche nach ToDos, die im Text die Angebotsnummer haben, aber keine offer_id
    $orphans = Todo::where('task', 'like', "%{$offerNumber}%")
                   ->whereNull('offer_id')
                   ->get();

    if ($orphans->count() > 0) {
        echo "Gefundene verwaiste ToDos für {$offerNumber}:\n";
        foreach ($orphans as $todo) {
            echo "- ID: {$todo->id}, Task: {$todo->task}\n";
            $todo->delete();
        }
        echo "Alle " . $orphans->count() . " verwaisten ToDos wurden gelöscht.\n";
    } else {
        // Suche nach allen System-ToDos für dieses Angebot (auch mit offer_id), falls die Löschung im Dashboard nicht gegriffen hat
        $offer = DB::table('angebot_tabelle')->where('angebotsnummer', $offerNumber)->first();
        if ($offer) {
            $deletedCount = Todo::where('offer_id', $offer->id)->where('is_system', true)->delete();
            if ($deletedCount > 0) {
                echo "Es wurden {$deletedCount} System-ToDos für Angebot-ID {$offer->id} gelöscht.\n";
            } else {
                echo "Keine verwaisten oder System-ToDos für {$offerNumber} gefunden.\n";
            }
        } else {
            echo "Angebot {$offerNumber} konnte in der Datenbank nicht gefunden werden.\n";
        }
    }
} catch (\Exception $e) {
    echo "Fehler bei der Bereinigung: " . $e->getMessage() . "\n";
}
