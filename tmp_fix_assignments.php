<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Finde Einträge, die fälschlicherweise die interne ID (größer 8000) statt der WAWI-ID (kleiner 1000 bei neuen Aufträgen) enthalten
$problematic = DB::table('auftrag_hersteller')
    ->where('auftrag_id', '>', 9000)
    ->where('timestamp', '>', '2026-03-09 11:00:00')
    ->get();

echo "Found " . count($problematic) . " problematic entries.\n";

foreach($problematic as $e) {
    $order = DB::table('auftrag_tabelle')->where('id', $e->auftrag_id)->first();
    if ($order && $order->auftrag_id != $e->auftrag_id) {
        echo "Fixing Entry ID {$e->id}: Changing auftrag_id from {$e->auftrag_id} (Internal) to {$order->auftrag_id} (WAWI)\n";
        DB::table('auftrag_hersteller')->where('id', $e->id)->update(['auftrag_id' => $order->auftrag_id]);
    }
}
