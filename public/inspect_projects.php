<?php
// public/inspect_projects.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$projects = DB::table('auftrag_projekt')->get();

echo "<h1>auftrag_projekt Inspection</h1>";
echo "<table border='1'><tr><th>ID</th><th>Firmenname</th><th>Firma ID</th><th>Kuerzel</th></tr>";
foreach ($projects as $p) {
    echo "<tr><td>{$p->id}</td><td>{$p->firmenname}</td><td>{$p->firma_id}</td><td>{$p->name_kuerzel}</td></tr>";
}
echo "</table>";

$aliases = DB::table('auftrag_projekt_firma_namen')->get();
echo "<h2>Aliases</h2><table border='1'><tr><th>Name ID (v. auftrag_projekt_firma)</th><th>Begriff</th></tr>";
foreach ($aliases as $a) {
    echo "<tr><td>{$a->name_id}</td><td>{$a->begriff}</td></tr>";
}
echo "</table>";
