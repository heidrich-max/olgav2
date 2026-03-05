<?php
// public/detect_duplicates.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$duplicates = DB::table('auftrag_tabelle')
    ->select('projekt_id', 'auftrag_id', DB::raw('COUNT(*) as count'))
    ->groupBy('projekt_id', 'auftrag_id')
    ->having('count', '>', 1)
    ->get();

echo "<h1>Duplicate Orders Detected</h1>";
if ($duplicates->isEmpty()) {
    echo "<p>No duplicates found based on (projekt_id, auftrag_id).</p>";
} else {
    echo "<table border='1'><tr><th>Projekt ID</th><th>Auftrag ID (JTL)</th><th>Count</th></tr>";
    foreach ($duplicates as $d) {
        echo "<tr><td>{$d->projekt_id}</td><td>{$d->auftrag_id}</td><td>{$d->count}</td></tr>";
    }
    echo "</table>";
    
    echo "<h2>Example Duplicate Data</h2>";
    foreach ($duplicates->take(3) as $d) {
        $rows = DB::table('auftrag_tabelle')
            ->where('projekt_id', $d->projekt_id)
            ->where('auftrag_id', $d->auftrag_id)
            ->get();
        echo "<h3>Key: {$d->projekt_id}_{$d->auftrag_id}</h3><pre>";
        print_r($rows);
        echo "</pre>";
    }
}
