<?php
// Script to run Artisan Command via Web
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<h1>Starte JTL-Import (inkl. Auftragskonvertierung)...</h1>";

try {
    Artisan::call('app:import-jtl-offers');
    $output = Artisan::output();
    
    echo "<h2>Ergebnis:</h2>";
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ccc;'>" . $output . "</pre>";
    echo "<p style='color:green; font-weight:bold;'>Import erfolgreich!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Fehler:</h2><pre>" . $e->getMessage() . "</pre>";
}
