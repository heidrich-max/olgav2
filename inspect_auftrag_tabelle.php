<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'auftrag_tabelle';
if (Schema::hasTable($table)) {
    $columns = Schema::getColumnListing($table);
    echo "Columns for $table:\n";
    print_r($columns);
    
    $first = DB::table($table)->first();
    echo "\nSample row:\n";
    print_r($first);
} else {
    echo "Table $table not found.\n";
}
