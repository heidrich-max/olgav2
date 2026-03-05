<?php
// public/diagnose_orders_v1.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Order Meta Diagnose</h1>";

echo "<h2>1. auftrag_status Tabelle</h2>";
$statuses = DB::table('auftrag_status')->get();
echo "<table border='1'><tr><th>SH</th><th>LG</th><th>BG</th><th>Color</th></tr>";
foreach ($statuses as $s) {
    echo "<tr><td>{$s->status_sh}</td><td>{$s->status_lg}</td><td>{$s->bg}</td><td>{$s->color}</td></tr>";
}
echo "</table>";

echo "<h2>2. Letzte 5 Aufträge (lokal)</h2>";
$orders = DB::table('auftrag_tabelle')->orderBy('id', 'desc')->limit(5)->get();
echo "<pre>" . print_r($orders, true) . "</pre>";
