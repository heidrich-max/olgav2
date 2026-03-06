<?php
// public/check_existing_order.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$order = DB::table('auftrag_tabelle')->orderBy('id', 'desc')->first();

echo "<h1>Latest successfully imported order:</h1>";
echo "<pre>" . print_r($order, true) . "</pre>";

$project = DB::table('auftrag_projekt')->where('id', $order->projekt_id)->first();
echo "<h2>Associated Project:</h2>";
echo "<pre>" . print_r($project, true) . "</pre>";
