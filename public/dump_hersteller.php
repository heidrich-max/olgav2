<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $columns = DB::select("SHOW COLUMNS FROM hersteller");
    file_put_contents(__DIR__ . '/hersteller_cols.txt', print_r($columns, true));
    $sample = DB::table('hersteller')->first();
    file_put_contents(__DIR__ . '/hersteller_sample.txt', print_r($sample, true));
    echo "Files created.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
