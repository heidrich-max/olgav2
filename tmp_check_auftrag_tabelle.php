<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- AUFTRAG_TABELLE COLUMNS ---\n";
print_r(Schema::getColumnListing('auftrag_tabelle'));

echo "\n--- SAMPLE DATA ---\n";
$samples = DB::table('auftrag_tabelle')->limit(1)->get();
print_r($samples->toArray());
