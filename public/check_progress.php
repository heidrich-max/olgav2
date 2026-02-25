<?php
// DB Progress Check
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Import-Fortschrittsprüfung</h1>";

$offerCount = DB::table('angebot_tabelle')->whereNotNull('kunde_strasse')->count();
$itemCount = DB::table('angebot_artikel')->count();

echo "<p>Angebote mit Adressdaten: <strong>$offerCount</strong></p>";
echo "<p>Gesamtzahl Artikel-Positionen: <strong>$itemCount</strong></p>";

if ($itemCount > 0) {
    echo "<h2>Letzte 5 Artikel:</h2><ul>";
    $recentItems = DB::table('angebot_artikel')->latest()->limit(5)->get();
    foreach($recentItems as $item) {
        echo "<li>{$item->bezeichnung} ({$item->menge} {$item->einheit})</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange;'>Noch keine Artikel importiert.</p>";
}
echo "<p><a href='/run_import.php'>Import erneut anstoßen (falls abgebrochen)</a></p>";
