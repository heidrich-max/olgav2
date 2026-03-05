<?php
// public/list_angebot_tables.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$tables = DB::select("SHOW TABLES");
$key = key((array) $tables[0]);
$all_tables = array_column(array_map('get_object_vars', $tables), $key);
$angebot_tables = array_filter($all_tables, fn($t) => stripos($t, 'angebot') !== false);

echo "<h1>Angebot-Tabellen in der Datenbank</h1>";
echo "<table border='1'><tr><th>Tabellenname</th><th>Zeilen (ca.)</th></tr>";
foreach ($angebot_tables as $table) {
    $count = DB::table($table)->count();
    echo "<tr><td>{$table}</td><td>{$count}</td></tr>";
}
echo "</table>";
