<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$order = DB::table('auftrag_tabelle')->where('id', 9941)->first();

echo "--- Full Data for Order Internal ID 9941 ---\n";
if ($order) {
    foreach ((array)$order as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "ORDER 9941 NOT FOUND\n";
}

echo "\n--- Searching for any order where auftrag_id = 10056 ---\n";
$match = DB::table('auftrag_tabelle')->where('auftrag_id', 10056)->first();
if ($match) {
    echo "Found Order ID: {$match->id}, No: {$match->auftragsnummer}, WAWI: {$match->auftrag_id}\n";
} else {
    echo "NO ORDER WITH WAWI-ID 10056 FOUND\n";
}
