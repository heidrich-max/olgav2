<?php
// public/run_import_orders.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<h1>Triggering JTL Order Import...</h1>";
try {
    Artisan::call('app:import-jtl-orders');
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<h2>Import Finished!</h2>";
} catch (\Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
