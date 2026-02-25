<?php
// DB Migration: Add kunde_telefon
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<h1>Datenbank-Erweiterung: Telefonnummer</h1>";

try {
    if (Schema::hasTable('angebot_tabelle')) {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            if (!Schema::hasColumn('angebot_tabelle', 'kunde_telefon')) {
                $table->string('kunde_telefon', 50)->nullable();
                echo " - Spalte 'kunde_telefon' hinzugefügt.<br>";
            } else {
                echo " - Spalte 'kunde_telefon' existiert bereits.<br>";
            }
        });
    }

    echo "<h2 style='color:green;'>Aktualisierung erfolgreich!</h2>";

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Fehler:</h2><pre>" . $e->getMessage() . "</pre>";
}
