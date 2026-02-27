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
        $this->info("Starte Generierung von automatischen To-Dos für Angebote...");
        
        // Der 7. Tag nach Erstellung entspricht einer Differenz von 6 Tagen
        $targetDate = Carbon::now()->subDays(6)->toDateString();
        
        $openOffers = DB::table('angebot_tabelle')
            ->where('letzter_status_name', 'Status offen')
            ->where('erstelldatum', $targetDate)
            ->get();

        $this->info(count($openOffers) . " offene Angebote vom {$targetDate} gefunden.");

        // Benutzer-Mapping laden
        $userMap = DB::table('user')->get()->keyBy('name_komplett')->toArray();
        $createdCount = 0;

        foreach ($openOffers as $offer) {
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

        $this->info("Fertig! {$createdCount} neue To-Dos erzeugt.");
    }
}
