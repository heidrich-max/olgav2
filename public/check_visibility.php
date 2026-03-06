<?php
// public/check_visibility.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$missingOrderNo = 'PAU.032026-10049';

echo "<h1>Visibility Check</h1>";

// 1. Missing order
$missing = DB::table('auftrag_tabelle')->where('auftragsnummer', $missingOrderNo)->first();
echo "<h2>Missing Order: $missingOrderNo</h2>";
echo "<pre>" . print_r($missing, true) . "</pre>";

// 2. Visible order for firmen_id=2 (Europe Pen GmbH)
$visible = DB::table('auftrag_tabelle')
    ->where('firmen_id', 2)
    ->where('auftragsnummer', '!=', $missingOrderNo)
    ->orderBy('id', 'desc')
    ->first();

if ($visible) {
    echo "<h2>A Visible Order (firmen_id=2): {$visible->auftragsnummer}</h2>";
    echo "<pre>" . print_r($visible, true) . "</pre>";
} else {
    echo "<p>No other order found for firmen_id=2.</p>";
}

// 3. Check for any seiten_id mapping or similar in other tables
echo "<h2>Related Tables Check</h2>";
$tables = ['auftrag_projekt', 'auftrag_projekt_firma', 'seiten']; // Testing common names
foreach($tables as $t) {
    if (Illuminate\Support\Facades\Schema::hasTable($t)) {
        echo "<h3>Table: $t</h3>";
        $data = DB::table($t)->where('id', 2)->orWhere('id', 6)->get();
        echo "<pre>" . print_r($data->toArray(), true) . "</pre>";
    }
}
