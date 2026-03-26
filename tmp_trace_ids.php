<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$entries = DB::table('auftrag_hersteller')
    ->orderBy('timestamp', 'asc')
    ->limit(20)
    ->get();

echo "Analyzing first 20 entries of auftrag_hersteller:\n";
foreach($entries as $e) {
    echo "Entry ID: {$e->id}, stored auftrag_id: {$e->auftrag_id} | ";
    
    $matchInternal = DB::table('auftrag_tabelle')->where('id', $e->auftrag_id)->first();
    $matchWawi = DB::table('auftrag_tabelle')->where('auftrag_id', $e->auftrag_id)->first();
    
    if ($matchInternal) echo "Matches Internal ID ({$matchInternal->auftragsnummer}) ";
    if ($matchWawi) echo "Matches WAWI ID ({$matchWawi->auftragsnummer}) ";
    if (!$matchInternal && !$matchWawi) echo "NO MATCH IN auftrag_tabelle";
    echo "\n";
}
