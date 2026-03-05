<?php
// public/check_defaults.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$columns = DB::select("SHOW COLUMNS FROM auftrag_tabelle");
echo "<h1>Columns without default and NOT NULL</h1>";
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
foreach ($columns as $c) {
    if ($c->Null === 'NO' && $c->Default === null && $c->Extra !== 'auto_increment') {
        echo "<tr><td>{$c->Field}</td><td>{$c->Type}</td><td>{$c->Null}</td><td>" . var_export($c->Default, true) . "</td></tr>";
    }
}
echo "</table>";
