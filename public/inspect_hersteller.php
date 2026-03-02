<?php
// inspect_hersteller.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "<h1>Inspektion Tabelle: hersteller</h1>";

    if (Schema::hasTable('hersteller')) {
        $columns = Schema::getColumnListing('hersteller');
        echo "<h2>Spalten:</h2><ul>";
        foreach ($columns as $column) {
            $type = Schema::getColumnType('hersteller', $column);
            echo "<li>$column ($type)</li>";
        }
        echo "</ul>";

        echo "<h2>Beispiel-Datensatz:</h2>";
        $row = DB::table('hersteller')->first();
        echo "<pre>" . print_r($row, true) . "</pre>";
    } else {
        echo "Tabelle 'hersteller' existiert nicht.";
    }
} catch (\Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
