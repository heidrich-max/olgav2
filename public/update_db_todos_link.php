<?php
// update_todos_offer_id.php
// Dieses Script fügt die Spalte 'offer_id' zur Tabelle 'todos' hinzu.

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    if (!Schema::hasColumn('todos', 'offer_id')) {
        Schema::table('todos', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->nullable()->after('user_id');
        });
        echo "Spalte 'offer_id' erfolgreich zur Tabelle 'todos' hinzugefügt.\n";
    } else {
        echo "Spalte 'offer_id' existiert bereits in der Tabelle 'todos'.\n";
    }
} catch (\Exception $e) {
    echo "Fehler beim Aktualisieren der Datenbank: " . $e->getMessage() . "\n";
}
