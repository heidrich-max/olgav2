<?php
// public/schema_dump.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$table = 'auftrag_tabelle';
$columns = Schema::getColumnListing($table);

echo "<h1>Schema Dump: $table</h1>";
echo "<ul>";
foreach ($columns as $column) {
    $type = Schema::getColumnType($table, $column);
    echo "<li>$column ($type)</li>";
}
echo "</ul>";

$sample = DB::table($table)->first();
if ($sample) {
    echo "<h2>Sample Row</h2><pre>";
    print_r($sample);
    echo "</pre>";
}
