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
    echo "<h1>Migration läuft...</h1>";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<p>Exit Code: " . $exitCode . "</p>";
} catch (\Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
