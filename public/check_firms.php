<?php
// public/check_firms.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<h1>Firms Table Check</h1>";

$tables = [
    'auftrag_projekt_firma',
    'auftrag_projekt_firma_namen',
    'firma',
    'firmen'
];

foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        echo "<h2>Table: $t</h2>";
        $data = DB::table($t)->get();
        echo "<pre>" . print_r($data->toArray(), true) . "</pre>";
    } else {
        echo "<p>Table $t does not exist.</p>";
    }
}
