<?php
// public/verify_restoration.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<h1>Verifizierung von angebot_status_head</h1>";
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('angebot_status_head')) {
        echo "Tabelle existiert! ✅<br>";
        $rows = DB::table('angebot_status_head')->get();
        echo "<h2>Inhalt von angebot_status_head</h2>";
        echo "<pre>" . print_r($rows->toArray(), true) . "</pre>";
    } else {
        echo "Tabelle existiert NICHT! ❌";
    }
} catch (\Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
