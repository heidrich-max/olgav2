<?php
/**
 * OLGA - Migration Status (Web)
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    echo "<h1>Migrations-Status:</h1>";
    Artisan::call('migrate:status');
    echo "<pre>" . Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
