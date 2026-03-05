<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrderStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize current order statuses from history to main table (optimized for active orders)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting optimized status synchronization...");
        $startTime = microtime(true);

        // 1. Get all active orders (not closed)
        // This is the core optimization requested by the user
        $activeOrders = DB::table('auftrag_tabelle')
            ->where('letzter_status', '!=', 'A')
            ->get(['id', 'auftrag_id', 'projekt_id']);

        $count = $activeOrders->count();
        $this->info("Found {$count} active orders to sync.");

        if ($count === 0) {
            $this->info("No active orders found. Finished.");
            return;
        }

        $processed = 0;
        $updated = 0;

        // Group by project if needed, but the legacy script does a max(status) group by
        // We'll process each active order individually to ensure we get the absolute latest status
        foreach ($activeOrders as $order) {
            $processed++;
            
            // Find the latest status entry for this specific order
            $latestStatus = DB::table('auftrag_status_a')
                ->join('auftrag_status', 'auftrag_status_a.status', '=', 'auftrag_status.id')
                ->where('auftrag_status_a.auftrag_id', $order->auftrag_id)
                ->where('auftrag_status_a.projekt_id', $order->projekt_id)
                ->select(
                    'auftrag_status.id as status_id',
                    'auftrag_status.status_sh',
                    'auftrag_status.status_lg',
                    'auftrag_status.bg',
                    'auftrag_status.color'
                )
                ->orderBy('auftrag_status_a.id', 'desc') // Using ID as chronological sequence as in the legacy logic
                ->first();

            if ($latestStatus) {
                $abgeschlossenStatus = ($latestStatus->status_sh === 'A') 
                    ? 'Auftrag abgeschlossen' 
                    : 'Auftrag nicht abgeschlossen';

                DB::table('auftrag_tabelle')
                    ->where('id', $order->id)
                    ->update([
                        'letzter_status' => $latestStatus->status_sh,
                        'letzter_status_name' => "Status " . $latestStatus->status_lg,
                        'letzter_status_bg_hex' => $latestStatus->bg,
                        'letzter_status_farbe_hex' => $latestStatus->color,
                        'abgeschlossen_status' => $abgeschlossenStatus,
                        'timestamp' => date("Y-m-d H:i:s")
                    ]);
                
                $updated++;
            }

            if ($processed % 100 === 0) {
                $this->line("Processed {$processed}/{$count} orders...");
            }
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Done! Processed: {$processed} | Updated: {$updated} | Duration: {$duration}s");
        Log::info("Order status sync completed: {$updated} orders updated in {$duration}s.");
    }
}
