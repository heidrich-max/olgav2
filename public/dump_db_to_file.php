<?php
/**
 * OLGA - Database Dump Script
 * Schreibt die Tabellenstruktur in eine Datei, damit der Agent sie lesen kann.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    echo "<h1>1. Migration läuft...</h1>";
    $exitMig = Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<p>Migration Exit Code: " . $exitMig . "</p>";

    echo "<h1>2. JTL-Import läuft...</h1>";
    $exitImp = Artisan::call('app:import-jtl-offers');
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<p>Import Exit Code: " . $exitImp . "</p>";

    if ($exitMig === 0 && $exitImp === 0) {
        echo "<h2 style='color:green'>Alles erfolgreich ausgeführt!</h2>";
    }

} catch (\Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
