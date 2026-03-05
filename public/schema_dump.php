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

$sample = DB::table($table)
    ->where('erstelldatum', '>=', now()->subMonths(12))
    ->where(function($query) {
        $query->where('lieferadresse_strasse', '!=', '')
              ->orWhere('lieferadresse_ort', '!=', '');
    })
    ->orderBy('id', 'desc')
    ->first();

if ($sample) {
    echo "<h2>Newest Row with Shipping Data (last 12 months)</h2><pre>";
    print_r($sample);
    echo "</pre>";
} else {
    echo "<h2>No recent row with shipping data found.</h2>";
    $latest = DB::table($table)->orderBy('id', 'desc')->first();
    echo "<h3>Latest row (any data):</h3><pre>";
    print_r($latest);
    echo "</pre>";
}
