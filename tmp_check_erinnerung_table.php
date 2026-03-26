<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- AUFTRAG_HERSTELLER_ERINNERUNG COLUMNS ---\n";
print_r(Schema::getColumnListing('auftrag_hersteller_erinnerung'));

echo "\n--- SAMPLE DATA ---\n";
$samples = DB::table('auftrag_hersteller_erinnerung')->orderBy('timestamp', 'desc')->limit(3)->get();
print_r($samples->toArray());
