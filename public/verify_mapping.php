<?php
// public/verify_mapping.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>Safe Deletion Verification</h1>";

echo "<h2>WAWI Tenants (auftrag_projekt_wawi)</h2>";
$wawis = DB::table('auftrag_projekt_wawi')->get();
echo "<table border='1'><tr><th>ID</th><th>auftrag_projekt_id</th><th>dataname</th></tr>";
foreach ($wawis as $w) {
    echo "<tr><td>{$w->id}</td><td>{$w->auftrag_projekt_id}</td><td>{$w->dataname}</td></tr>";
}
echo "</table>";

echo "<h2>Projects (auftrag_projekt_firma)</h2>";
$firma = DB::table('auftrag_projekt_firma')->get();
echo "<table border='1'><tr><th>ID</th><th>firma_id</th><th>name</th></tr>";
foreach ($firma as $f) {
    echo "<tr><td>{$f->id}</td><td>{$f->firma_id}</td><td>{$f->name}</td></tr>";
}
echo "</table>";

echo "<h2>Sample Orders (auftrag_tabelle)</h2>";
$orders = DB::table('auftrag_tabelle')->limit(5)->get();
echo "<table border='1'><tr><th>auftrag_id</th><th>projekt_id</th><th>firmen_id</th><th>projekt_firmenname</th></tr>";
foreach ($orders as $o) {
    echo "<tr><td>{$o->auftrag_id}</td><td>{$o->projekt_id}</td><td>{$o->firmen_id}</td><td>{$o->projekt_firmenname}</td></tr>";
}
echo "</table>";
