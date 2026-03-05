<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixOldOrdersStatus extends Command
{
    protected $signature = 'orders:fix-2025-status {--dry-run}';
    protected $description = 'Set status to A for 2025 orders that are still NEU, except one specific order';

    public function handle()
    {
        $query = DB::table('auftrag_tabelle')
            ->whereYear('erstelldatum', 2025)
            ->where('letzter_status', 'NEU')
            ->where('auftragsnummer', '!=', 'WBAU.112025-8278');

        $count = $query->count();
        $this->info("Found {$count} orders from 2025 with status 'NEU'.");

        if ($this->option('dry-run')) {
            $this->info("Dry run: No changes will be made.");
            foreach ($query->take(10)->get() as $row) {
                $this->line("- ID: {$row->id}, Number: {$row->auftragsnummer}, Date: {$row->erstelldatum}");
            }
            return;
        }

        if ($count > 0) {
            $updated = $query->update([
                'letzter_status' => 'A',
                'letzter_status_name' => 'Status abgeschlossen',
                'letzter_status_bg_hex' => 'f69620',
                'letzter_status_farbe_hex' => 'ffffff',
                'abgeschlossen_status' => 'Auftrag abgeschlossen'
            ]);
            $this->info("Successfully updated {$updated} orders.");
        } else {
            $this->info("Nothing to update.");
        }
    }
}
