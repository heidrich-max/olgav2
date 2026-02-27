<?php

/**
 * OLGA Database Migration Script
 * 
 * Dieses Script führt die Laravel-Migrationen über den Webserver aus.
 * Es ist hilfreich, wenn der CLI-Zugriff eingeschränkt ist.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Http\Kernel;

$kernel = $app->make(Kernel::class);
$app->boot();

echo "<h1>OLGA - Datenbank Migration</h1>";
echo "<pre>Starte 'php artisan migrate --force'...</pre>";

try {
    // Führt den Migrations-Befehl aus
    Artisan::call('migrate', ['--force' => true]);
    
    echo "<h3 style='color: green;'>Erfolg!</h3>";
    echo "<pre>" . Artisan::output() . "</pre>";
    
    echo "<hr>";
    echo "<p>Die Datenbank ist nun auf dem neuesten Stand. Du kannst dieses Fenster schließen.</p>";
    echo "<p><strong style='color: red;'>WICHTIG:</strong> Bitte lösche diese Datei (public/run_migration.php) aus Sicherheitsgründen, sobald die Migration erfolgreich war.</p>";

} catch (\Exception $e) {
    echo "<h3 style='color: red;'>Fehler während der Migration:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
