<?php
// public/check_2025_status.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$affected = DB::table('auftrag_tabelle')
    ->whereYear('erstelldatum', 2025)
    ->where('letzter_status', 'NEU')
    ->where('auftragsnummer', '!=', 'WBAU.112025-8278')
    ->get();

echo "<h1>Affected Orders for 2025</h1>";
echo "<p>Total found: " . $affected->count() . "</p>";

if ($affected->isNotEmpty()) {
    echo "<table border='1'><tr><th>ID</th><th>A.-Nummer</th><th>Status</th><th>Erstellt</th></tr>";
    foreach ($affected->take(20) as $row) {
        echo "<tr><td>{$row->id}</td><td>{$row->auftragsnummer}</td><td>{$row->letzter_status}</td><td>{$row->erstelldatum}</td></tr>";
    }
    echo "</table>";
}

$exception = DB::table('auftrag_tabelle')
    ->where('auftragsnummer', 'WBAU.112025-8278')
    ->first();

echo "<h2>Exception Check (WBAU.112025-8278)</h2>";
if ($exception) {
    echo "<pre>";
    print_r($exception);
    echo "</pre>";
} else {
    echo "<p>Exception order not found.</p>";
}
