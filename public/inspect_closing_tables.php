<?php
// public/inspect_closing_tables.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<pre>";

// 1. Table structures
echo "=== angebot_abgeschlossen columns ===\n";
$cols = DB::select("DESCRIBE angebot_abgeschlossen");
foreach ($cols as $c) { echo "  {$c->Field} ({$c->Type})\n"; }

echo "\n=== angebot_status_a columns ===\n";
$cols2 = DB::select("DESCRIBE angebot_status_a");
foreach ($cols2 as $c) { echo "  {$c->Field} ({$c->Type})\n"; }

// 2. How many closed offers have NO entry in angebot_abgeschlossen?
$missing = DB::table('angebot_tabelle')
    ->where('letzter_status', 'A')
    ->whereNotExists(function ($q) {
        $q->select(DB::raw(1))
          ->from('angebot_abgeschlossen')
          ->whereColumn('angebot_abgeschlossen.angebot_id', 'angebot_tabelle.id');
    })
    ->count();

echo "\n=== Closed offers WITHOUT angebot_abgeschlossen entry: {$missing} ===\n";

// 3. Sample of angebot_status_a for a closed offer
$sampleOffer = DB::table('angebot_tabelle')->where('letzter_status', 'A')->whereNotExists(function ($q) {
    $q->select(DB::raw(1))->from('angebot_abgeschlossen')->whereColumn('angebot_abgeschlossen.angebot_id', 'angebot_tabelle.id');
})->first();

if ($sampleOffer) {
    echo "\n=== Sample: angebot_status_a for offer ID {$sampleOffer->id} ===\n";
    $hist = DB::table('angebot_status_a')->where('angebot_id', $sampleOffer->angebot_id)->get();
    foreach ($hist as $h) { print_r((array)$h); }
}

echo "</pre>";
