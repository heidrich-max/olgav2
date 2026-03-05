<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupOrderDuplicates extends Command
{
    protected $signature = 'orders:cleanup-duplicates';
    protected $description = 'Consolidate redundant projects and merge duplicate orders';

    public function handle()
    {
        $this->info("Starting Final Cleanup...");

        // 1. Find Projects with duplicate names
        $projectGroups = DB::table('auftrag_projekt')
            ->select('firmenname', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('firmenname')
            ->having('count', '>', 1)
            ->get();

        foreach ($projectGroups as $group) {
            $ids = explode(',', $group->ids);
            sort($ids);
            $primaryId = $ids[0];
            $secondaryIds = array_slice($ids, 1);
            
            $this->info("Consolidating Projects for '{$group->firmenname}': Primary: {$primaryId}, Secondaries: " . implode(',', $secondaryIds));
            
            foreach ($secondaryIds as $secId) {
                // Move orders
                $updatedOrders = DB::table('auftrag_tabelle')
                    ->where('projekt_id', $secId)
                    ->update(['projekt_id' => $primaryId]);
                $this->line("  - Moved {$updatedOrders} orders from P-ID {$secId} to {$primaryId}");
                
                // Update Wawi mappings
                $updatedWawi = DB::table('auftrag_projekt_wawi')
                    ->where('auftrag_projekt_id', $secId)
                    ->update(['auftrag_projekt_id' => $primaryId]);
                $this->line("  - Updated {$updatedWawi} Wawi connections from P-ID {$secId} to {$primaryId}");
            }
        }

        // 2. Merge Duplicate Orders (same projekt_id and auftrag_id)
        $duplicateOrders = DB::table('auftrag_tabelle')
            ->select('projekt_id', 'auftrag_id', DB::raw('COUNT(*) as count'))
            ->groupBy('projekt_id', 'auftrag_id')
            ->having('count', '>', 1)
            ->get();

        $this->info("\nMerging Duplicate Orders...");
        foreach ($duplicateOrders as $dup) {
            $rows = DB::table('auftrag_tabelle')
                ->where('projekt_id', $dup->projekt_id)
                ->where('auftrag_id', $dup->auftrag_id)
                ->orderBy('timestamp', 'desc')
                ->get();
            
            $bestRecord = null;
            foreach ($rows as $row) {
                if ($row->letzter_status === 'A') {
                    $bestRecord = $row;
                    break;
                }
            }
            if (!$bestRecord) $bestRecord = $rows[0];

            $this->line("  - Merging {$dup->count} records for Key {$dup->projekt_id}_{$dup->auftrag_id} (Preserving ID: {$bestRecord->id})");

            foreach ($rows as $row) {
                if ($row->id === $bestRecord->id) continue;
                
                $updates = [];
                $fieldsToSync = [
                    'lieferadresse_firma', 'lieferadresse_anrede', 'lieferadresse_titel', 
                    'lieferadresse_vorname', 'lieferadresse_nachname', 'lieferadresse_strasse',
                    'lieferadresse_plz', 'lieferadresse_ort', 'lieferadresse_land',
                    'kunde_strasse', 'kunde_plz', 'kunde_ort', 'kunde_land', 'kunde_mail', 'kunde_telefon'
                ];
                
                foreach ($fieldsToSync as $field) {
                    if (empty($bestRecord->$field) && !empty($row->$field)) {
                        $updates[$field] = $row->$field;
                    }
                }
                
                if (!empty($updates)) {
                    DB::table('auftrag_tabelle')->where('id', $bestRecord->id)->update($updates);
                    $this->line("    - Updated ID {$bestRecord->id} with missing data from ID {$row->id}");
                }
                
                DB::table('auftrag_tabelle')->where('id', $row->id)->delete();
                $this->line("    - Deleted redundant record ID {$row->id}");
            }
        }

        // 3. Cleanup unused projects
        $deletedProjects = DB::table('auftrag_projekt')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('auftrag_tabelle')
                      ->whereColumn('auftrag_tabelle.projekt_id', 'auftrag_projekt.id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('auftrag_projekt_wawi')
                      ->whereColumn('auftrag_projekt_wawi.auftrag_projekt_id', 'auftrag_projekt.id');
            })
            ->delete();

        $this->info("\nDeleted {$deletedProjects} unused project definitions.");
        $this->info("Cleanup Finished.");
    }
}
