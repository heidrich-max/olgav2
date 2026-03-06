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
echo "<table border='1'><tr><th>Projekt ID</th><th>Firmen ID</th><th>Anzahl</th></tr>";
foreach($stats as $s) {
    echo "<tr><td>{$s->projekt_id}</td><td>{$s->firmen_id}</td><td>{$s->count}</td></tr>";
}
echo "</table>";
