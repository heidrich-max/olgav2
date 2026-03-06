<?php
// public/check_firmen_id_dist.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$stats = DB::table('auftrag_tabelle')
    ->select('projekt_id', 'firmen_id', DB::raw('count(*) as count'))
    ->groupBy('projekt_id', 'firmen_id')
    ->get();

echo "<h1>firmen_id Distribution</h1>";
foreach($stats as $s) {
    echo "PROJ: {$s->projekt_id}, FIRM: {$s->firmen_id}, COUNT: {$s->count}<br>";
}
