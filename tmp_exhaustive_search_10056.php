<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Searching for 10056 in various order columns ---\n";

$byOrderNo = DB::table('auftrag_tabelle')->where('auftragsnummer', 'LIKE', '%10056%')->get();
$byJobNo = DB::table('auftrag_tabelle')->where('projektnummer', 'LIKE', '%10056%')->get();
$byMetadata = DB::table('auftrag_metadaten')->where('meta_value', 'LIKE', '%10056%')->get();

echo "Matches in auftragsnummer: " . count($byOrderNo) . "\n";
foreach($byOrderNo as $o) echo " - ID: {$o->id}, WAWI: {$o->auftrag_id}, No: {$o->auftragsnummer}\n";

echo "Matches in projektnummer: " . count($byJobNo) . "\n";
foreach($byJobNo as $o) echo " - ID: {$o->id}, WAWI: {$o->auftrag_id}, ProjNo: {$o->projektnummer}\n";

echo "Matches in auftrag_metadaten: " . count($byMetadata) . "\n";
foreach($byMetadata as $m) echo " - AuftragID (Internal?): {$m->auftrag_id}, Key: {$m->meta_key}, Val: {$m->meta_value}\n";

echo "\n--- Recent Orders Around the One the User is Viewing ---\n";
$recent = DB::table('auftrag_tabelle')->orderBy('erstelldatum', 'desc')->limit(5)->get();
foreach($recent as $r) echo "ID: {$r->id}, WAWI: {$r->auftrag_id}, No: {$r->auftragsnummer}\n";
