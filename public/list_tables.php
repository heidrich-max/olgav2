<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Support\Facades\DB;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $tables = DB::select('SHOW TABLES');
    echo "<h1>Tabellen:</h1><pre>";
    print_r($tables);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
