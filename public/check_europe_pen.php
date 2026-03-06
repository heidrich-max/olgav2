<?php
// public/check_europe_pen.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$search = 'Europe Pen GmbH';
$search_lower = strtolower($search);

echo "<h1>Checking for '$search' in local DB</h1>";

$project = DB::table('auftrag_projekt')
    ->where('firmenname', $search)
    ->orWhere(DB::raw('LOWER(firmenname)'), $search_lower)
    ->get();

echo "<h2>Projects found: " . $project->count() . "</h2>";
echo "<pre>" . print_r($project->toArray(), true) . "</pre>";

$alias = DB::table('auftrag_projekt_firma_namen')
    ->join('auftrag_projekt_firma', 'auftrag_projekt_firma.id', '=', 'auftrag_projekt_firma_namen.name_id')
    ->where('auftrag_projekt_firma_namen.begriff', $search)
    ->orWhere(DB::raw('LOWER(auftrag_projekt_firma_namen.begriff)'), $search_lower)
    ->select('auftrag_projekt_firma_namen.begriff', 'auftrag_projekt_firma.name')
    ->get();

echo "<h2>Aliases found: " . $alias->count() . "</h2>";
echo "<pre>" . print_r($alias->toArray(), true) . "</pre>";

$wawi = DB::table('auftrag_projekt_wawi')
    ->where('dataname', 'Mandant_4')
    ->first();
echo "<h2>Wawi Config for Mandant_4:</h2>";
echo "<pre>" . print_r($wawi, true) . "</pre>";
