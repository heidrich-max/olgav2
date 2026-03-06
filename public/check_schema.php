<?php
// public/check_schema.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<h1>Schema Check</h1>";

foreach(['auftrag_projekt', 'auftrag_tabelle'] as $table) {
    echo "<h2>Table: $table</h2>";
    $columns = Schema::getColumnListing($table);
    echo "<ul>";
    foreach($columns as $col) {
        echo "<li>$col</li>";
    }
    echo "</ul>";
}
