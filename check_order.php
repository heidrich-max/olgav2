<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNum = 'FWAU.022026-6041';
$order = DB::table('auftrag_tabelle')->where('auftragsnummer', $orderNum)->first();

if ($order) {
    echo "Order Found:\n";
    echo "ID: " . $order->id . "\n";
    echo "Status: " . $order->abgeschlossen_status . "\n";
    echo "Lieferdatum: " . $order->lieferdatum . "\n";
    
    $todos = DB::table('todos')->where('order_id', $order->id)->get();
    echo "\nTodos for this order:\n";
    foreach ($todos as $t) {
        echo "- ID: {$t->id}, Text: {$t->task}, Completed: {$t->is_completed}, System: {$t->is_system}\n";
    }
} else {
    echo "Order $orderNum not found.\n";
    // Try partial match
    $order = DB::table('auftrag_tabelle')->where('auftragsnummer', 'LIKE', '%' . $orderNum . '%')->first();
    if ($order) {
        echo "Found similar order: " . $order->auftragsnummer . "\n";
    }
}
