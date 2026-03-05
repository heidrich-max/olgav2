<?php
// public/check_wawi_mapping.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$wawis = DB::table('auftrag_projekt_wawi')->get();
echo "<h1>WAWI Mapping check</h1>";
foreach ($wawis as $w) {
    echo "ID: {$w->id}, auftrag_projekt_id: {$w->auftrag_projekt_id}, dataname: {$w->dataname}<br>";
}

$firma = DB::table('auftrag_projekt_firma')->get();
echo "<h1>Firma Mapping check</h1>";
foreach ($firma as $f) {
    echo "ID: {$f->id}, firma_id: {$f->firma_id}, name: {$f->name}<br>";
}
