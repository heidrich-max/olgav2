<?php
// public/check_status_head_structure.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<h1>Struktur von auftrag_status_head</h1>";
try {
    $cols = DB::select("DESCRIBE auftrag_status_head");
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    foreach ($cols as $c) {
        echo "<tr><td>{$c->Field}</td><td>{$c->Type}</td></tr>";
    }
    echo "</table>";

    echo "<h2>Inhalt von auftrag_status_head</h2>";
    $rows = DB::table('auftrag_status_head')->get();
    echo "<pre>" . print_r($rows->toArray(), true) . "</pre>";
    
    echo "<h2>Inhalt von angebot_status (zum Abgleich)</h2>";
    $ang_status = DB::table('angebot_status')->get();
    echo "<pre>" . print_r($ang_status->toArray(), true) . "</pre>";

} catch (\Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
