<?php
// public/run_migration_direct.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Artisan;

try {
    Artisan::call('migrate', ['--force' => true]);
    echo "<h1>Migration Results:</h1>";
    echo "<pre>" . Artisan::output() . "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
