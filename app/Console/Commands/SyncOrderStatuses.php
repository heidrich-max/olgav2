<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrderStatuses extends Command
{
    protected $signature = 'orders:sync-statuses';
    protected $description = 'Synchronize order statuses from auftrag_status_a history to auftrag_tabelle (active orders only)';

    public function handle()
    {
        $this->info("Starting order status synchronization...");
        $startTime = microtime(true);

        // Get all active orders (not yet closed) — performance optimization
        $activeOrders = DB::table('auftrag_tabelle')
            ->where('letzter_status', '!=', 'A')
            ->get(['id', 'auftrag_id', 'projekt_id']);

        $count = $activeOrders->count();
        $this->info("Found {$count} active orders to sync.");

        if ($count === 0) {
            $this->info("Nothing to sync. Finished.");
            return;
        }

        $processed = 0;
        $updated   = 0;
        $skipped   = 0;

        foreach ($activeOrders as $order) {
            $processed++;

            // Find the latest status history entry for this order
            $latestStatus = DB::table('auftrag_status_a')
                ->join('auftrag_status', 'auftrag_status_a.status', '=', 'auftrag_status.id')
                ->where('auftrag_status_a.auftrag_id', $order->auftrag_id)
                ->where('auftrag_status_a.projekt_id', $order->projekt_id)
                ->select(
                    'auftrag_status.status_sh',
                    'auftrag_status.status_lg',
                    'auftrag_status.bg',
                    'auftrag_status.color'
                )
                ->orderBy('auftrag_status_a.id', 'desc')
                ->first();

            // No history entry → status in auftrag_tabelle is already authoritative
            if (!$latestStatus) {
                $skipped++;
                continue;
            }

            $abgeschlossenStatus = ($latestStatus->status_sh === 'A')
                ? 'Auftrag abgeschlossen'
                : 'Auftrag nicht abgeschlossen';

            DB::table('auftrag_tabelle')
                ->where('id', $order->id)
                ->update([
                    'letzter_status'           => $latestStatus->status_sh,
                    'letzter_status_name'      => 'Status ' . $latestStatus->status_lg,
                    'letzter_status_bg_hex'    => $latestStatus->bg,
                    'letzter_status_farbe_hex' => $latestStatus->color,
                    'abgeschlossen_status'     => $abgeschlossenStatus,
                ]);

            $updated++;
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Done! Processed: {$processed} | Updated: {$updated} | Skipped: {$skipped} | Duration: {$duration}s");
        Log::info("Order status sync: {$updated} updated, {$skipped} skipped in {$duration}s.");
    }
}
