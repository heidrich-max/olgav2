<?php
// public/verify_shipping_data.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$recentOrders = DB::table('auftrag_tabelle')
    ->where('erstelldatum', '>=', now()->subMonths(12))
    ->where(function($query) {
        $query->where('lieferadresse_strasse', '!=', '')
              ->orWhere('lieferadresse_ort', '!=', '');
    })
    ->limit(5)
    ->get();

echo "<h1>Recent Orders with Shipping Data</h1>";
if ($recentOrders->isEmpty()) {
    echo "<p>No recent orders with shipping address found yet. This might be because the JTL data for the last 12 months mostly uses billing address as shipping address.</p>";
    
    // Show any recent orders to see if they were updated at all
    $anyRecent = DB::table('auftrag_tabelle')
        ->where('erstelldatum', '>=', now()->subMonths(12))
        ->limit(3)
        ->get();
    echo "<h2>Any Recent Orders:</h2><pre>";
    print_r($anyRecent);
    echo "</pre>";
} else {
    echo "<pre>";
    print_r($recentOrders);
    echo "</pre>";
}
