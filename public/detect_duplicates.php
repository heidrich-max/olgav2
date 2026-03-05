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

$duplicatesNr = DB::table('auftrag_tabelle')
    ->select('auftragsnummer', DB::raw('COUNT(*) as count'))
    ->where('auftragsnummer', '!=', '')
    ->groupBy('auftragsnummer')
    ->having('count', '>', 1)
    ->get();

$duplicatesGlobalId = DB::table('auftrag_tabelle')
    ->select('auftrag_id', DB::raw('COUNT(*) as count'))
    ->groupBy('auftrag_id')
    ->having('count', '>', 1)
    ->get();

echo "<h1>Duplicate Orders Detected</h1>";

echo "<h2>1. Duplicates by (projekt_id, auftrag_id)</h2>";
if ($duplicates->isEmpty()) {
    echo "<p>No duplicates found based on (projekt_id, auftrag_id).</p>";
} else {
    echo "<table border='1'><tr><th>Projekt ID</th><th>Auftrag ID (JTL)</th><th>Count</th></tr>";
    foreach ($duplicates as $d) {
        echo "<tr><td>{$d->projekt_id}</td><td>{$d->auftrag_id}</td><td>{$d->count}</td></tr>";
    }
    echo "</table>";
}

echo "<h2>2. Duplicates by Auftragsnummer</h2>";
if ($duplicatesNr->isEmpty()) {
    echo "<p>No duplicates found based on auftragsnummer.</p>";
} else {
    echo "<table border='1'><tr><th>Auftragsnummer</th><th>Count</th></tr>";
    foreach ($duplicatesNr as $d) {
        echo "<tr><td>{$d->auftragsnummer}</td><td>{$d->count}</td></tr>";
    }
    echo "</table>";
    
    foreach ($duplicatesNr->take(10) as $d) {
        echo "<h3>Key: {$d->auftragsnummer}</h3>";
        $rows = DB::table('auftrag_tabelle')->where('auftragsnummer', $d->auftragsnummer)->get();
        echo "<table border='1'><tr><th>ID</th><th>A.-ID</th><th>P.-ID</th><th>Firma-Scope</th><th>Kunde</th><th>Erstellt</th><th>Timestamp</th></tr>";
        foreach ($rows as $r) {
            echo "<tr>
                <td>{$r->id}</td>
                <td>{$r->auftrag_id}</td>
                <td>{$r->projekt_id}</td>
                <td>{$r->projekt_firmenname}</td>
                <td>{$r->firmenname}</td>
                <td>{$r->erstelldatum}</td>
                <td>{$r->timestamp}</td>
            </tr>";
        }
        echo "</table>";
    }
}

echo "<h2>3. Duplicates by Global Auftrag ID (kAuftrag)</h2>";
if ($duplicatesGlobalId->isEmpty()) {
    echo "<p>No duplicates found based on global auftrag_id.</p>";
} else {
    echo "<p>Total Global IDs with duplicates: " . $duplicatesGlobalId->count() . "</p>";
    foreach ($duplicatesGlobalId->take(5) as $d) {
        echo "<h3>kAuftrag: {$d->auftrag_id}</h3>";
        $rows = DB::table('auftrag_tabelle')->where('auftrag_id', $d->auftrag_id)->get();
        echo "<pre>";
        print_r($rows);
        echo "</pre>";
    }
}
