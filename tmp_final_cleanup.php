<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Bereinige ALLE Einträge von heute (2026-03-09), die fälschlicherweise die interne ID statt der WAWI-ID haben
$entries = DB::table('auftrag_hersteller')
    ->where('timestamp', '>=', '2026-03-09 00:00:00')
    ->get();

echo "Analyzing entries from today:\n";
foreach($entries as $e) {
    // Suche in auftrag_tabelle nach der INTERNEN ID (id)
    $order = DB::table('auftrag_tabelle')->where('id', $e->auftrag_id)->first();
    
    // Falls ein Treffer über die interne ID gefunden wurde und diese NICHT der WAWI-ID entspricht
    if ($order && $order->id != $order->auftrag_id) {
        echo "Entry {$e->id}: Found internal ID match ({$e->auftrag_id}). Changing to WAWI-ID ({$order->auftrag_id})\n";
        DB::table('auftrag_hersteller')->where('id', $e->id)->update(['auftrag_id' => $order->auftrag_id]);
    } else {
        echo "Entry {$e->id}: Already correct or No internal match (Stored: {$e->auftrag_id})\n";
    }
}

echo "\n--- Final Check for Order 9941/10056 ---\n";
$final = DB::table('auftrag_hersteller')
    ->where('timestamp', '>=', '2026-03-09 11:00:00')
    ->orderBy('id', 'desc')
    ->get();
foreach($final as $f) {
    echo "Entry ID: {$f->id}, Stored AuftragID: {$f->auftrag_id}, Timestamp: {$f->timestamp}\n";
}
