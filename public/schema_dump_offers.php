<?php
// public/schema_dump_offers.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$table = 'angebot_tabelle';
$columns = Schema::getColumnListing($table);

echo "<h1>Schema Dump: $table</h1>";
echo "<ul>";
foreach ($columns as $column) {
    echo "<li>$column</li>";
}
echo "</ul>";
