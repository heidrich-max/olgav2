<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$total = DB::table('auftrag_hersteller')->count();
$zeroUser = DB::table('auftrag_hersteller')->where('user_id', 0)->count();
$nullUser = DB::table('auftrag_hersteller')->whereNull('user_id')->count();

echo "Total Entries in auftrag_hersteller: $total\n";
echo "Entries with user_id = 0: $zeroUser\n";
echo "Entries with user_id IS NULL: $nullUser\n";

$samples = DB::table('auftrag_hersteller')->where('user_id', 0)->limit(5)->get();
echo "\nSamples with user_id=0:\n";
foreach($samples as $s) echo "ID: {$s->id}, AuftragID: {$s->auftrag_id}, Time: {$s->timestamp}\n";
