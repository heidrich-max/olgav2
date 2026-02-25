<?php
// DB Inspection Script for Closing Logic
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tablesToCheck = ['angebot_status', 'angebot_status_a', 'angebot_abgeschlossen', 'angebot_ablehnen', 'angebot_ablehngrund'];

echo "<h1>Datenbank-Inspektion: Closing Logic</h1>";

foreach ($tablesToCheck as $tableName) {
    echo "<h2>Tabelle: $tableName</h2>";
    try {
        if (Schema::hasTable($tableName)) {
            $columns = DB::select("SHOW COLUMNS FROM $tableName");
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'><thead><tr><th>Field</th><th>Type</th></tr></thead><tbody>";
            foreach ($columns as $col) {
                echo "<tr><td>{$col->Field}</td><td>{$col->Type}</td></tr>";
            }
            echo "</tbody></table>";

            echo "<h3>Stichprobe:</h3>";
            $samples = DB::table($tableName)->limit(3)->get();
            echo "<pre>" . print_r($samples, true) . "</pre>";
        } else {
            echo "<p style='color:orange;'>Tabelle '$tableName' existiert nicht.</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color:red;'>Fehler bei $tableName: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}
