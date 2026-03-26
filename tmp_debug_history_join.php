<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderIds = [9941, 10056];

$history = DB::table('auftrag_hersteller')
    ->leftJoin('hersteller', 'auftrag_hersteller.hersteller_id', '=', 'hersteller.id')
    ->leftJoin('user', 'auftrag_hersteller.user_id', '=', 'user.id')
    ->whereIn('auftrag_hersteller.auftrag_id', $orderIds)
    ->select('auftrag_hersteller.id as entry_id', 'auftrag_hersteller.user_id', 'user.name_komplett', 'auftrag_hersteller.timestamp')
    ->get();

echo "DEBUG HISTORY JOIN:\n";
foreach($history as $h) {
    echo "Entry: {$h->entry_id} | UserID: {$h->user_id} | Name: {$h->name_komplett} | Time: {$h->timestamp}\n";
}
