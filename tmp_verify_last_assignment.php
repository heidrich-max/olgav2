<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$lastEntry = DB::table('auftrag_hersteller')->orderBy('id', 'desc')->first();
if (!$lastEntry) {
    echo "No entries found.\n";
    exit;
}

echo "Last Entry ID: {$lastEntry->id}\n";
echo "Stored auftrag_id in table: {$lastEntry->auftrag_id}\n";

$orderMatchesInternal = DB::table('auftrag_tabelle')->where('id', $lastEntry->auftrag_id)->first();
$orderMatchesWawi = DB::table('auftrag_tabelle')->where('auftrag_id', $lastEntry->auftrag_id)->first();

if ($orderMatchesInternal) echo "Internal ID Match: {$orderMatchesInternal->auftragsnummer} (Internal ID: {$orderMatchesInternal->id})\n";
if ($orderMatchesWawi) echo "WAWI ID Match: {$orderMatchesWawi->auftragsnummer} (WAWI ID: {$orderMatchesWawi->auftrag_id})\n";
