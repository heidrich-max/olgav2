<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Todo;
use Carbon\Carbon;

class GenerateOfferTodos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-offer-todos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erzeugt To-Dos für Angebote, die seit 7 Tagen offen sind.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starte Generierung und Bereinigung von automatischen To-Dos für Angebote...");
        
        // 1. CLEANUP: Bestehende To-Dos löschen, wenn das Angebot nicht mehr offen ist
        $existingTodos = Todo::where('task', 'like', 'Angebots-Nachverfolgung: %')->get();
        $deletedCount = 0;

        foreach ($existingTodos as $todo) {
            // Angebotsnummer aus dem Task-Text extrahieren (zwischen : und ()
            if (preg_match('/Angebots-Nachverfolgung: (.*?) \(Kunde/', $todo->task, $matches)) {
                $offerNum = trim($matches[1]);
                
                $offer = DB::table('angebot_tabelle')
                    ->where('angebotsnummer', $offerNum)
                    ->first();

                // Wenn das Angebot nicht mehr existiert oder nicht mehr offen ist, To-Do löschen
                if (!$offer || $offer->letzter_status_name !== 'Status offen') {
                    $todo->delete();
                    $deletedCount++;
                }
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("{$deletedCount} veraltete To-Dos wurden entfernt.");
        }

        // 2. GENERIERUNG: Neue To-Dos für Angebote vom 7. Tag
        // Der 7. Tag nach Erstellung entspricht einer Differenz von 6 Tagen
        $targetDate = Carbon::now()->subDays(6)->toDateString();
        
        // A) Offene Angebote (Ersterstellung)
        $openOffers = DB::table('angebot_tabelle')
            ->where('letzter_status_name', 'Status offen')
            ->where('erstelldatum', $targetDate)
            ->get();

        // B) Angebote mit versendeter Erinnerung (Wiedervorlage nach 7 Tagen)
        $reminderOffers = DB::table('angebot_tabelle')
            ->where('letzter_status_name', 'Status Erinnerung versendet')
            ->where('reminder_date', $targetDate)
            ->get();

        $allTargetOffers = $openOffers->merge($reminderOffers);

        $this->info(count($allTargetOffers) . " relevante Angebote für To-Dos gefunden.");

        // Benutzer-Mapping laden
        $userMap = DB::table('user')->get()->keyBy('name_komplett')->toArray();
        $createdCount = 0;

        foreach ($allTargetOffers as $offer) {
            $userName = $offer->benutzer;
            $userData = $userMap[$userName] ?? null;

            if (!$userData) {
                $this->warn("Kein Benutzer '{$userName}' in der Datenbank gefunden für Angebot #{$offer->angebotsnummer}");
                continue;
            }

            $taskText = "Angebots-Nachverfolgung: {$offer->angebotsnummer} (Kunde anrufen oder Erinnerung senden)";
            
            // Prüfen, ob bereits existiert
            $exists = Todo::where('user_id', $userData->id)
                ->where('task', $taskText)
                ->exists();

            if (!$exists) {
                Todo::create([
                    'user_id' => $userData->id,
                    'task' => $taskText,
                    'is_completed' => false
                ]);
                $createdCount++;
            }
        }

        $this->info("Fertig! {$createdCount} neue To-Dos erzeugt, {$deletedCount} bereinigt.");
    }
}
