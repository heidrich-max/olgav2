<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$o = DB::table('auftrag_tabelle')->orderBy('erstelldatum', 'desc')->first();
echo "Internal ID (id): " . $o->id . "\n";
echo "WAWI ID (auftrag_id): " . $o->auftrag_id . "\n";
echo "Order Number: " . $o->auftragsnummer . "\n";

$ah = DB::table('auftrag_hersteller')->where('auftrag_id', $o->id)->first();
$ah_wawi = DB::table('auftrag_hersteller')->where('auftrag_id', $o->auftrag_id)->first();

echo "\nSearch in auftrag_hersteller by internal ID ($o->id): " . ($ah ? "FOUND (ID $ah->id)" : "NOT FOUND") . "\n";
echo "Search in auftrag_hersteller by WAWI ID ($o->auftrag_id): " . ($ah_wawi ? "FOUND (ID $ah_wawi->id)" : "NOT FOUND") . "\n";
