<?php
// public/run_fix_2025.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Artisan;

echo "<pre>";
echo "Running orders:fix-2025-status...\n";
Artisan::call('orders:fix-2025-status');
echo Artisan::output();
echo "\nFinished.\n";
echo "</pre>";
