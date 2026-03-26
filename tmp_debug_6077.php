<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNo = 'FWAU.032026-6077';
$order = DB::table('auftrag_tabelle')->where('auftragsnummer', $orderNo)->first();

echo "--- Order Details for $orderNo ---\n";
if ($order) {
    echo "Internal ID (id): {$order->id}\n";
    echo "WAWI ID (auftrag_id): {$order->auftrag_id}\n";
    echo "Projekt ID: {$order->projekt_id}\n";
    echo "Erstelldatum: {$order->erstelldatum}\n";
    
    // Check assignments
    $assignments = DB::table('auftrag_hersteller')
        ->whereIn('auftrag_id', [$order->id, $order->auftrag_id])
        ->where('projekt_id', $order->projekt_id)
        ->get();
        
    echo "\nAssignments found: " . count($assignments) . "\n";
    foreach($assignments as $a) {
        $h = DB::table('hersteller')->where('id', $a->hersteller_id)->first();
        echo " - Entry ID: {$a->id}, Stored AuftragID: {$a->auftrag_id}, Manufacturer: " . ($h ? $h->firmenname : 'ID ' . $a->hersteller_id) . ", Time: {$a->timestamp}\n";
    }
} else {
    echo "ORDER NOT FOUND\n";
}
