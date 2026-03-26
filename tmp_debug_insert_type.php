<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 9941;
$order = DB::table('auftrag_tabelle')->where('id', $id)->first();

echo "Order internal ID: " . $order->id . " (Type: " . gettype($order->id) . ")\n";
echo "Order wawi ID (auftrag_id): " . $order->auftrag_id . " (Type: " . gettype($order->auftrag_id) . ")\n";

// Simuliere den Insert
$insertData = [
    'auftrag_id'    => (int)$order->auftrag_id,
    'projekt_id'    => $order->projekt_id,
    'hersteller_id' => 97, // Beispiel
    'user_id'       => 3,
    'timestamp'     => now(),
];

echo "\nData prepared for Insert:\n";
print_r($insertData);
?>
