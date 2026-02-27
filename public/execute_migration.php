<?php
/**
 * OLGA - Migration Executor (Web)
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    echo "<h1>Migration läuft...</h1>";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    
    echo "<pre>" . $output . "</pre>";
    echo "<p>Exit Code: " . $exitCode . "</p>";

    if ($exitCode === 0) {
        echo "<h2 style='color:green'>Erfolg!</h2>";
    } else {
        echo "<h2 style='color:red'>Fehler während der Migration.</h2>";
    }

} catch (\Exception $e) {
    echo "<h1>Fehler</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
