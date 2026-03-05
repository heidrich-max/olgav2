<?php
// public/check_import_results.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>JTL Order Import Results</h1>";

$count = DB::table('auftrag_tabelle')->count();
echo "<h2>Total Orders: $count</h2>";

echo "<h2>Recent 10 Orders</h2>";
$recent = DB::table('auftrag_tabelle')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "<table border='1'><tr>
    <th>ID</th>
    <th>Auftragsnr.</th>
    <th>Firma</th>
    <th>Betrag</th>
    <th>Status</th>
    <th>Kontakt</th>
    <th>Straße</th>
</tr>";

foreach ($recent as $r) {
    echo "<tr>
        <td>{$r->id}</td>
        <td>{$r->auftragsnummer}</td>
        <td>{$r->firmenname}</td>
        <td>{$r->auftragssumme}</td>
        <td>{$r->letzter_status_name}</td>
        <td>{$r->ansprechpartner_vorname} {$r->ansprechpartner_nachname}</td>
        <td>{$r->kunde_strasse}</td>
    </tr>";
}
echo "</table>";

// Check for newly added columns
echo "<h2>Schema Check</h2>";
$cols = DB::select("DESCRIBE auftrag_tabelle");
echo "<ul>";
foreach ($cols as $c) {
    if (strpos($c->Field, 'kunde') === 0 || strpos($c->Field, 'ansprechpartner') === 0) {
        echo "<li><b>{$c->Field}</b> ({$c->Type})</li>";
    }
}
echo "</ul>";
