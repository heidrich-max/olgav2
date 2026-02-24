<?php
// Script to clear Laravel cache when terminal access is limited
// Location: public/clear-cache.php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Fix for "A facade root has not been set."
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

echo "<h1>Laravel Cache Cleaner</h1>";

try {
    echo "Clearing View Cache... ";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "DONE<br>";

    echo "Clearing Application Cache... ";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "DONE<br>";

    echo "Clearing Config Cache... ";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "DONE<br>";

    echo "<h2>Alles erledigt! Bitte Dashboard jetzt neu laden.</h2>";
} catch (\Exception $e) {
    echo "<h2>Fehler: " . $e->getMessage() . "</h2>";
}
