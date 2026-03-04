<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Todo;
use Carbon\Carbon;

class ProcessBoStatusOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-bo-status-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erzeugt To-Dos für Aufträge mit Status BO (Bestellung offen).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Prüfe auf Aufträge mit Status BO...");
        
        // Finde alle nicht abgeschlossenen Aufträge mit letzter_status = 'BO'
        $boOrders = DB::table('auftrag_tabelle')
            ->where('letzter_status', 'BO')
            ->where('abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
            ->get();

        $this->info(count($boOrders) . " Aufträge mit Status BO gefunden.");
        
        // Benutzer-Mapping laden
        $userMap = DB::table('user')->get()->keyBy('name_komplett')->toArray();
        $createdCount = 0;

        foreach ($boOrders as $order) {
            $userName = $order->benutzer;
            $userData = $userMap[$userName] ?? null;

            if (!$userData) {
                $this->warn("Kein Benutzer '{$userName}' gefunden für Auftrag #{$order->auftragsnummer}");
                continue;
            }

            $taskText = "Bestellung offen: {$order->auftragsnummer} - Projekt: " . ($order->projektname ?: '—');
            
            // Prüfen, ob bereits ein OFFENES System-To-Do für diesen Auftrag existiert (egal welcher Text)
            // Oder spezifisch für BO? Der User möchte wahrscheinlich nur eines zur Zeit pro Auftrag.
            // Aber "Lieferdatum überschritten" und "BO" könnten gleichzeitig existieren.
            // Wir prüfen spezifisch auf den Text-Anfang "Bestellung offen".
            $exists = Todo::where('order_id', $order->id)
                ->where('is_completed', false)
                ->where('is_system', true)
                ->where('task', 'LIKE', 'Bestellung offen:%')
                ->exists();

            if (!$exists) {
                Todo::create([
                    'user_id' => $userData->id,
                    'order_id' => $order->id,
                    'task' => $taskText,
                    'is_completed' => false,
                    'is_system' => true
                ]);
                $createdCount++;
            }
        }

        $this->info("Fertig! {$createdCount} neue To-Dos erzeugt.");
    }
}
