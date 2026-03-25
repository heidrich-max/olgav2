<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessOverdueDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-overdue-deliveries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erzeugt To-Dos für Aufträge mit überschrittenem Lieferdatum.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Prüfe auf überschrittene Liefertermine...");
        
        $today = \Carbon\Carbon::now()->toDateString();
        
        // Finde alle nicht abgeschlossenen Aufträge mit Lieferdatum in der Vergangenheit (unter Berücksichtigung von Korrekturen)
        $overdueOrders = \Illuminate\Support\Facades\DB::table('auftrag_tabelle')
            ->leftJoin('bestelldatum_korrektur', function($join) {
                $join->on('auftrag_tabelle.auftrag_id', '=', 'bestelldatum_korrektur.auftrag_id')
                     ->on('auftrag_tabelle.projekt_id', '=', 'bestelldatum_korrektur.projekt_id');
            })
            ->select('auftrag_tabelle.*')
            ->where(function($query) use ($today) {
                $query->where(function($q) use ($today) {
                    $q->whereNotNull('bestelldatum_korrektur.bestelldatum')
                      ->where('bestelldatum_korrektur.bestelldatum', '<', $today);
                })
                ->orWhere(function($q) use ($today) {
                    $q->whereNull('bestelldatum_korrektur.bestelldatum')
                      ->whereNotNull('auftrag_tabelle.lieferdatum')
                      ->where('auftrag_tabelle.lieferdatum', '<', $today);
                });
            })
            ->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
            ->get();

        $this->info(count($overdueOrders) . " überfällige Aufträge gefunden.");
        
        // Benutzer-Mapping laden
        $userMap = \Illuminate\Support\Facades\DB::table('user')->get()->keyBy('name_komplett')->toArray();
        $createdCount = 0;

        foreach ($overdueOrders as $order) {
            $userName = $order->benutzer;
            $userData = $userMap[$userName] ?? null;

            if (!$userData) {
                $this->warn("Kein Benutzer '{$userName}' gefunden für Auftrag #{$order->auftragsnummer}");
                continue;
            }

            $taskText = "Lieferdatum überschritten: {$order->auftragsnummer} - Projekt: " . ($order->projektname ?: '—');
            
            // Prüfen, ob bereits ein OFFENES To-Do für diesen Auftrag existiert
            $exists = \App\Models\Todo::where('order_id', $order->id)
                ->where('is_completed', false)
                ->where('is_system', true)
                ->exists();

            if (!$exists) {
                \App\Models\Todo::create([
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

        // BEREINIGUNG: Lösche System-To-Dos für Aufträge, die NICHT mehr überfällig sind
        $this->info("Bereinige veraltete To-Dos...");
        $overdueOrderIds = $overdueOrders->pluck('id')->toArray();
        
        $deletedCount = \App\Models\Todo::where('is_system', true)
            ->where('is_completed', false)
            ->where('task', 'like', 'Lieferdatum überschritten:%')
            ->whereNotIn('order_id', $overdueOrderIds)
            ->delete();

        $this->info("{$deletedCount} veraltete To-Dos entfernt.");
    }
}
