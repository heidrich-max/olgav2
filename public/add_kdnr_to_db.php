<?php
// DB Migration: Add kunden_nr
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<h1>Datenbank-Erweiterung: Kundennummer</h1>";

try {
    if (Schema::hasTable('angebot_tabelle')) {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            if (!Schema::hasColumn('angebot_tabelle', 'kunden_nr')) {
                $table->string('kunden_nr', 50)->nullable();
                echo " - Spalte 'kunden_nr' hinzugefügt.<br>";
            } else {
                echo " - Spalte 'kunden_nr' existiert bereits.<br>";
            }
        });
    }

    echo "<h2 style='color:green;'>Aktualisierung erfolgreich!</h2>";

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Fehler:</h2><pre>" . $e->getMessage() . "</pre>";
}
