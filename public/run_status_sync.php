<?php
// public/run_status_sync.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Artisan;

echo "<pre>";
echo "Running orders:sync-statuses...\n";
Artisan::call('orders:sync-statuses');
echo Artisan::output();
echo "\nFinished.\n";
echo "</pre>";
