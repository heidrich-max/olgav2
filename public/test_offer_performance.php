<?php
// public/test_offer_performance.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Artisan;

$time_start = microtime(true);

echo "<h1>Performance Test: JTL Angebots-Import</h1>";
echo "<pre>";

try {
    Artisan::call('app:import-jtl-offers');
    echo Artisan::output();
} catch (\Exception $e) {
    echo "Fehler aufgetreten: " . $e->getMessage();
}

echo "</pre>";

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<hr/>";
echo "<h3>Gesamtdauer (Web-Aufruf): " . number_format($time, 2) . " Sekunden</h3>";
