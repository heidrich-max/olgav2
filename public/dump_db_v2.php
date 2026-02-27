<?php
/**
 * OLGA - Database Dump Script V2
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    echo "<h1>Struktur von angebot_tabelle:</h1>";
    $columns = DB::select("DESCRIBE angebot_tabelle");
    echo "<pre>";
    foreach ($columns as $col) {
        print_r($col);
    }
    echo "</pre>";

    echo "<h1>Migration Status:</h1>";
    Artisan::call('migrate:status');
    echo "<pre>" . Artisan::output() . "</pre>";

} catch (\Exception $e) {
    echo "<h1>Fehler</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
