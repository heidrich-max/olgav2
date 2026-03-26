<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$hersteller = DB::table('auftrag_hersteller')->orderBy('timestamp', 'desc')->limit(5)->get();

echo "Checking matches for auftrag_hersteller.auftrag_id:\n";
foreach($hersteller as $h) {
    echo "H_ID: {$h->id}, AH_AuftragID: {$h->auftrag_id} -> ";
    $matchId = DB::table('auftrag_tabelle')->where('id', $h->auftrag_id)->first();
    $matchWawi = DB::table('auftrag_tabelle')->where('auftrag_id', $h->auftrag_id)->first();
    
    if($matchId) echo "Matches internal ID (Order: {$matchId->auftragsnummer}) ";
    if($matchWawi) echo "Matches WAWI ID (Order: {$matchWawi->auftragsnummer}) ";
    if(!$matchId && !$matchWawi) echo "NO MATCH FOUND";
    echo "\n";
}
