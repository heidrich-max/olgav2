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
    echo "<h1>Struktur von angebot_tabelle:</h1>";
    $columns = DB::select("DESCRIBE angebot_tabelle");
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    echo "<h1>Migration Status:</h1>";
    Artisan::call('migrate:status');
    echo "<pre>" . Artisan::output() . "</pre>";

} catch (\Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
