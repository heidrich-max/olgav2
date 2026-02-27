<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Support\Facades\Artisan;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    echo "<h1>Migration läuft...</h1>";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<p>Exit Code: " . $exitCode . "</p>";
    if ($exitCode === 0) {
        echo "<h2 style='color:green'>Migration erfolgreich!</h2>";
    }
} catch (Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
