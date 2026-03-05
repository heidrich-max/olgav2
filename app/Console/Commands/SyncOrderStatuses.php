<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrderStatuses extends Command
{
    protected $signature = 'orders:sync-statuses';
    protected $description = 'Synchronize offer statuses from history to angebot_tabelle (active offers only)';

    public function handle()
    {
        $this->info("Starting optimized status synchronization...");
        $startTime = microtime(true);

        // Fetch active offers (not yet closed) from the OFFER table
        $activeOffers = DB::table('angebot_tabelle')
            ->where('letzter_status', '!=', 'A')
            ->get(['id', 'angebot_id', 'projekt_id']);

        $count = $activeOffers->count();
        $this->info("Found {$count} active offers to sync.");

        if ($count === 0) {
            $this->info("Nothing to sync. Finished.");
            return;
        }

        $processed = 0;
        $updated   = 0;
        $skipped   = 0;

        foreach ($activeOffers as $offer) {
            $processed++;

            // Find the latest status history entry for this offer
            $latestStatus = DB::table('angebot_status_a')
                ->join('angebot_status', 'angebot_status_a.status', '=', 'angebot_status.id')
                ->where('angebot_status_a.angebot_id', $offer->angebot_id)
                ->where('angebot_status_a.projekt_id', $offer->projekt_id)
                ->select(
                    'angebot_status.status_sh',
                    'angebot_status.status_lg',
                    'angebot_status.bg',
                    'angebot_status.color'
                )
                ->orderBy('angebot_status_a.id', 'desc')
                ->first();

            // If no history exists, the status in angebot_tabelle is already authoritative
            if (!$latestStatus) {
                $skipped++;
                continue;
            }

            $abgeschlossenStatus = ($latestStatus->status_sh === 'A')
                ? 'Angebot abgeschlossen'
                : 'Angebot nicht abgeschlossen';

            DB::table('angebot_tabelle')
                ->where('id', $offer->id)
                ->update([
                    'letzter_status'          => $latestStatus->status_sh,
                    'letzter_status_name'     => 'Status ' . $latestStatus->status_lg,
                    'letzter_status_bg_hex'   => $latestStatus->bg,
                    'letzter_status_farbe_hex' => $latestStatus->color,
                    'abgeschlossen_status'    => $abgeschlossenStatus,
                ]);

            $updated++;
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Done! Processed: {$processed} | Updated: {$updated} | Skipped (no history): {$skipped} | Duration: {$duration}s");
        Log::info("Offer status sync: {$updated} updated, {$skipped} skipped in {$duration}s.");
    }
}
