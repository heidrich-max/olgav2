<?php
// DB Progress Check
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Import-Check: Kundennummer</h1>";

$withKdNr = DB::table('angebot_tabelle')->whereNotNull('kunden_nr')->where('kunden_nr', '!=', '')->count();
$total = DB::table('angebot_tabelle')->count();

echo "<p>Angebote mit echter Kundennummer: <strong>$withKdNr</strong> / $total</p>";

if ($withKdNr > 0) {
    echo "<h2>Stichprobe (letzte 5):</h2><ul>";
    $samples = DB::table('angebot_tabelle')->whereNotNull('kunden_nr')->where('kunden_nr', '!=', '')->latest()->limit(5)->get();
    foreach($samples as $s) {
        echo "<li>{$s->angebotsnummer}: {$s->kunden_nr} ({$s->firmenname})</li>";
    }
    echo "</ul>";
}
