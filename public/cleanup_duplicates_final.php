<?php
// public/cleanup_duplicates_final.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "Starting Final Cleanup...\n";

// 1. Find Projects with duplicate names
$projectGroups = DB::table('auftrag_projekt')
    ->select('firmenname', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
    ->groupBy('firmenname')
    ->having('count', '>', 1)
    ->get();

foreach ($projectGroups as $group) {
    $ids = explode(',', $group->ids);
    sort($ids);
    $primaryId = $ids[0]; // Take the lowest ID as primary
    $secondaryIds = array_slice($ids, 1);
    
    echo "Consolidating Projects for '{$group->firmenname}': Primary: {$primaryId}, Secondaries: " . implode(',', $secondaryIds) . "\n";
    
    foreach ($secondaryIds as $secId) {
        // Move orders
        $updatedOrders = DB::table('auftrag_tabelle')
            ->where('projekt_id', $secId)
            ->update(['projekt_id' => $primaryId]);
        echo "  - Moved {$updatedOrders} orders from P-ID {$secId} to {$primaryId}\n";
        
        // Update Wawi mappings
        $updatedWawi = DB::table('auftrag_projekt_wawi')
            ->where('auftrag_projekt_id', $secId)
            ->update(['auftrag_projekt_id' => $primaryId]);
        echo "  - Updated {$updatedWawi} Wawi connections from P-ID {$secId} to {$primaryId}\n";
    }
}

// 2. Merge Duplicate Orders (same projekt_id and auftrag_id)
$duplicateOrders = DB::table('auftrag_tabelle')
    ->select('projekt_id', 'auftrag_id', DB::raw('COUNT(*) as count'))
    ->groupBy('projekt_id', 'auftrag_id')
    ->having('count', '>', 1)
    ->get();

echo "\nMerging Duplicate Orders...\n";
foreach ($duplicateOrders as $dup) {
    $rows = DB::table('auftrag_tabelle')
        ->where('projekt_id', $dup->projekt_id)
        ->where('auftrag_id', $dup->auftrag_id)
        ->orderBy('timestamp', 'desc')
        ->get();
    
    // Pick the "best" record
    // Rules:
    // - If one is already 'A' (Abgeschlossen), prioritize keeping that status/record
    // - Always take the newest contact/shipping info
    
    $bestRecord = null;
    foreach ($rows as $row) {
        if ($row->letzter_status === 'A') {
            $bestRecord = $row;
            break;
        }
    }
    if (!$bestRecord) $bestRecord = $rows[0];

    echo "  - Merging {$dup->count} records for Key {$dup->projekt_id}_{$dup->auftrag_id} (Preserving ID: {$bestRecord->id})\n";

    foreach ($rows as $row) {
        if ($row->id === $bestRecord->id) continue;
        
        // If the discarded record has shipping info that the best one lacks, copy it
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
            echo "    - Updated ID {$bestRecord->id} with missing data from ID {$row->id}\n";
        }
        
        // Delete redundant record
        DB::table('auftrag_tabelle')->where('id', $row->id)->delete();
        echo "    - Deleted redundant record ID {$row->id}\n";
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

echo "\nDeleted {$deletedProjects} unused project definitions.\n";

echo "\nCleanup Finished.\n";
echo "</pre>";
