<?php
// public/check_hersteller.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$orders = DB::table('auftrag_tabelle')
    ->orderBy('id', 'desc')
    ->limit(50)
    ->get(['id', 'auftrag_id', 'projekt_id', 'auftragsnummer', 'hersteller', 'erstelldatum', 'letzter_status']);

echo "<h1>Recent Orders (Checking Hersteller)</h1>";
echo "<table border='1'><tr><th>ID</th><th>Auftragsnr</th><th>Hersteller</th><th>Erstelldatum</th><th>Status</th></tr>";
foreach ($orders as $o) {
    echo "<tr><td>{$o->id}</td><td>{$o->auftragsnummer}</td><td>" . var_export($o->hersteller, true) . "</td><td>{$o->erstelldatum}</td><td>{$o->letzter_status}</td></tr>";
}
echo "</table>";
