<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<h1>OLGA - Database Status Check</h1>";

$tables = [
    'auftrag_artikel' => ['auftrag_id_lokal', 'jtl_auftrag_id', 'art_nr'],
    'todos' => ['order_id'],
    'angebot_tabelle' => ['wiedervorlage_datum', 'wiedervorlage_text']
];

foreach ($tables as $table => $columns) {
    if (Schema::hasTable($table)) {
        echo "<h3 style='color: green;'>✔ Tabelle '$table' existiert.</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                echo "<li style='color: green;'>✔ Spalte '$column' existiert.</li>";
            } else {
                echo "<li style='color: red;'>✘ Spalte '$column' FEHLT!</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<h3 style='color: red;'>✘ Tabelle '$table' FEHLT!</h3>";
    }
}

echo "<hr>";
echo "<h3>Migrations-Status:</h3>";
try {
    $migrations = DB::table('migrations')->orderBy('id', 'desc')->limit(5)->get();
    echo "<table border='1'><tr><th>Migration</th><th>Batch</th></tr>";
    foreach ($migrations as $m) {
        echo "<tr><td>{$m->migration}</td><td>{$m->batch}</td></tr>";
    }
    echo "</table>";
} catch (\Exception $e) {
    echo "Fehler beim Lesen der Migrations-Tabelle: " . $e->getMessage();
}
