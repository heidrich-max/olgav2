<?php
// update_todos_table.php
// Dieses Script fügt die Spalte 'is_system' zur Tabelle 'todos' hinzu.

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    if (!Capsule::schema()->hasColumn('todos', 'is_system')) {
        Capsule::schema()->table('todos', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('is_completed');
        });
        echo "Spalte 'is_system' erfolgreich zur Tabelle 'todos' hinzugefügt.\n";
    } else {
        echo "Spalte 'is_system' existiert bereits in der Tabelle 'todos'.\n";
    }
} catch (\Exception $e) {
    echo "Fehler beim Aktualisieren der Datenbank: " . $e->getMessage() . "\n";
}
