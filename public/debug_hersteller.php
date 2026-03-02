<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    $columns = Schema::getColumnListing('hersteller');
    echo "COLUMNS:" . implode(',', $columns) . "\n";
    $sample = DB::table('hersteller')->first();
    if($sample) {
        echo "SAMPLE:" . json_encode($sample) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
