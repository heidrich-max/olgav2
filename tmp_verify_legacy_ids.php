<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Suche einen Auftrag aus 2024 oder 2025
$oldOrder = DB::table('auftrag_tabelle')
    ->where('erstelldatum', '<', '2025-01-01')
    ->orderBy('erstelldatum', 'desc')
    ->first();

if (!$oldOrder) {
    echo "No old order found.\n";
    exit;
}

echo "Old Order: {$oldOrder->auftragsnummer}\n";
echo "Internal ID: {$oldOrder->id}\n";
echo "WAWI ID (auftrag_id): {$oldOrder->auftrag_id}\n";

$matches = DB::table('auftrag_hersteller')
    ->where('auftrag_id', $oldOrder->id)
    ->orWhere('auftrag_id', $oldOrder->auftrag_id)
    ->get();

echo "\nFound " . count($matches) . " entries in auftrag_hersteller:\n";
foreach($matches as $m) {
    if ($m->auftrag_id == $oldOrder->id) {
        echo "- Entry ID {$m->id}: Matches INTERNAL ID ({$m->auftrag_id})\n";
    }
    if ($m->auftrag_id == $oldOrder->auftrag_id) {
        echo "- Entry ID {$m->id}: Matches WAWI ID ({$m->auftrag_id})\n";
    }
}
