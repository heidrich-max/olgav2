<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('todos');
echo "Columns in 'todos' table:\n";
print_r($columns);

$columnsAuftrag = Schema::getColumnListing('auftrag_tabelle');
echo "\nColumns in 'auftrag_tabelle' table:\n";
print_r($columnsAuftrag);
