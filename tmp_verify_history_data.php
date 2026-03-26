<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$wawiId = 10056;
$internalId = 9941;
$orderIds = [$wawiId, $internalId];

$history = DB::table('auftrag_hersteller')
    ->leftJoin('hersteller', 'auftrag_hersteller.hersteller_id', '=', 'hersteller.id')
    ->leftJoin('user', 'auftrag_hersteller.user_id', '=', 'user.id')
    ->whereIn('auftrag_hersteller.auftrag_id', $orderIds)
    ->select('auftrag_hersteller.*', 'hersteller.firmenname', 'user.name_komplett')
    ->get();

echo "History for WAWI $wawiId / Internal $internalId:\n";
foreach($history as $h) {
    echo "ID: {$h->id}, Hersteller: {$h->firmenname}, UserID: {$h->user_id}, UserName: {$h->name_komplett}, Time: {$h->timestamp}\n";
}
