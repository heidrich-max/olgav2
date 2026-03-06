<?php
// public/run_import.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

echo "<h1>Running JTL Import...</h1>";

$output = new BufferedOutput();
try {
    $exitCode = Artisan::call('app:import-jtl-orders', [], $output);
    echo "<h2>Exit Code: $exitCode</h2>";
    echo "<pre>" . $output->fetch() . "</pre>";
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><p>Done.</p>";
