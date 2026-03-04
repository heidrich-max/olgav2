<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tableName = 'auftrag_tabelle';
$columns = DB::select("SHOW COLUMNS FROM $tableName WHERE Field = 'id'");
echo "Type of 'id' in $tableName:\n";
print_r($columns);

$todosColumns = DB::select("SHOW COLUMNS FROM todos WHERE Field = 'id'");
echo "\nType of 'id' in todos:\n";
print_r($todosColumns);
