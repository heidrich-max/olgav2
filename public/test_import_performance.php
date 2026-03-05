<?php
// public/test_import_performance.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<h1>JTL Order Import Performance Test</h1>";
echo "<p>Starte Import...</p>";

$startTime = microtime(true);

try {
    Artisan::call('app:import-jtl-orders');
    $output = Artisan::output();
    
    $duration = microtime(true) - $startTime;
    
    echo "<h2>Ergebnis:</h2>";
    echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ddd;'>" . htmlspecialchars($output) . "</pre>";
    
    echo "<h3>Gesamtdauer (Web-Aufruf): " . round($duration, 2) . " Sekunden</h3>";
    
} catch (\Exception $e) {
    echo "<p style='color:red'>Fehler beim Ausführen: " . $e->getMessage() . "</p>";
}
