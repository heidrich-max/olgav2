<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessWiedervorlage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'wiedervorlage:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prüft Wiedervorlagen und erstellt ToDos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        $offers = \App\Models\OfferTable::whereNotNull('wiedervorlage_datum')
            ->where('wiedervorlage_datum', '<=', $today)
            ->whereNotIn('letzter_status_name', ['Status angenommen', 'Status abgeschlossen'])
            ->get();

        $count = 0;
        foreach ($offers as $offer) {
            $taskText = "Wiedervorlage Angebot {$offer->angebotsnummer}: {$offer->wiedervorlage_text}";
            
            // ToDo erstellen (nur wenn nicht bereits vorhanden für diesen User)
            $existing = \App\Models\Todo::where('user_id', $offer->benutzer_id ?? 1)
                ->where('task', $taskText)
                ->where('is_completed', false)
                ->exists();

            if (!$existing) {
                \App\Models\Todo::create([
                    'user_id' => $offer->benutzer_id ?? 1, // Fallback auf Admin/User 1
                    'task' => $taskText,
                    'is_completed' => false,
                ]);
            }

            // Wiedervorlage zurücksetzen, damit sie nicht erneut verarbeitet wird
            $offer->update([
                'wiedervorlage_datum' => null,
                'wiedervorlage_text'  => null,
            ]);
            
            $count++;
        }

        $this->info("{$count} Wiedervorlagen erfolgreich verarbeitet und ToDos erstellt.");
    }
}
