<?php
// DB Inspection Script
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>Datenbank-Inspektion: angebot_information</h1>";

try {
    if (Schema::hasTable('angebot_information')) {
        $columns = DB::select("SHOW COLUMNS FROM angebot_information");
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead><tbody>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col->Field}</td>";
            echo "<td>{$col->Type}</td>";
            echo "<td>{$col->Null}</td>";
            echo "<td>{$col->Key}</td>";
            echo "<td>{$col->Default}</td>";
            echo "<td>{$col->Extra}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        echo "<h2>Stichprobe (letzte 5):</h2>";
        $samples = DB::table('angebot_information')->latest()->limit(5)->get();
        echo "<pre>" . print_r($samples, true) . "</pre>";
    } else {
        echo "<p style='color:red;'>Tabelle 'angebot_information' wurde nicht gefunden!</p>";
    }
} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Fehler:</h2><pre>" . $e->getMessage() . "</pre>";
}
