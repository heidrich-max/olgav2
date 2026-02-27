<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

try {
    $statuses = DB::table('angebot_status')->get();
    echo "<h1>Angebot Status Tabelle</h1>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Status SH</th><th>Status LG</th><th>BG</th><th>Color</th></tr>";
    foreach ($statuses as $status) {
        echo "<tr>";
        echo "<td>{$status->id}</td>";
        echo "<td>{$status->status_sh}</td>";
        echo "<td>{$status->status_lg}</td>";
        echo "<td>{$status->bg}</td>";
        echo "<td>{$status->color}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (\Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
