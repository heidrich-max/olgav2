<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Support\Facades\Artisan;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    echo "Migrating...<br>";
    Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "Importing...<br>";
    Artisan::call('app:import-jtl-offers');
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "Done.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
