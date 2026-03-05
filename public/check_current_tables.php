<?php
// public/check_current_tables.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$tables = DB::select("SHOW TABLES");
$key = key((array) $tables[0]);
$all_tables = array_column(array_map('get_object_vars', $tables), $key);
$angebot_tables = array_filter($all_tables, fn($t) => stripos($t, 'angebot') !== false);

echo "<h1>Aktuelle Angebot-Tabellen</h1>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #eee;'><th>Tabellenname</th><th>Zeilenanzahl</th></tr>";

foreach ($angebot_tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "<tr><td><strong>{$table}</strong></td><td>{$count}</td></tr>";
    } catch (\Exception $e) {
        echo "<tr><td><strong>{$table}</strong></td><td>Fehler: {$e->getMessage()}</td></tr>";
    }
}
echo "</table>";
