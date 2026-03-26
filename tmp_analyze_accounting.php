<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = [
    'auftrag_rechnung_details',
    'auftrag_cash_customer',
    'auftrag_cash_producer',
    'auftrag_cash_rechnung',
    'auftrag_cash_producer_gewinn'
];

foreach ($tables as $table) {
    echo "--- TABLE: $table ---\n";
    if (Schema::hasTable($table)) {
        print_r(Schema::getColumnListing($table));
        echo "\nSample:\n";
        print_r(DB::table($table)->limit(1)->get()->toArray());
    } else {
        echo "TABLE NOT FOUND\n";
    }
    echo "\n";
}
