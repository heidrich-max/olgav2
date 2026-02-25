<?php
// DB Migration Script
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<h1>Datenbank-Aktualisierung</h1>";

try {
    // 1. angebot_tabelle erweitern
    echo "Prufe angebot_tabelle...<br>";
    if (Schema::hasTable('angebot_tabelle')) {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            if (!Schema::hasColumn('angebot_tabelle', 'kunde_strasse')) {
                $table->string('kunde_strasse', 255)->nullable();
                $table->string('kunde_plz', 20)->nullable();
                $table->string('kunde_ort', 100)->nullable();
                $table->string('kunde_land', 100)->nullable();
                $table->string('kunde_mail', 255)->nullable();
                echo " - Adressfelder hinzugefügt.<br>";
            } else {
                echo " - Adressfelder existieren bereits.<br>";
            }
            
            if (!Schema::hasColumn('angebot_tabelle', 'gueltig_bis')) {
                $table->date('gueltig_bis')->nullable();
                echo " - Feld 'gueltig_bis' hinzugefügt.<br>";
            }
        });
    }

    // 2. neue Tabelle angebot_artikel erstellen
    echo "Prufe angebot_artikel...<br>";
    if (!Schema::hasTable('angebot_artikel')) {
        Schema::create('angebot_artikel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('angebot_id_lokal'); // FK auf angebot_tabelle.id
            $table->integer('jtl_angebot_id');
            $table->integer('sort_order')->default(0);
            $table->string('art_nr', 50)->nullable();
            $table->text('bezeichnung')->nullable();
            $table->decimal('menge', 15, 4)->default(0);
            $table->string('einheit', 20)->nullable();
            $table->decimal('einzelpreis_netto', 15, 4)->default(0);
            $table->decimal('mwst_prozent', 5, 2)->default(0);
            $table->decimal('gesamt_netto', 15, 4)->default(0);
            $table->timestamps();

            $table->index('angebot_id_lokal');
            $table->index('jtl_angebot_id');
        });
        echo " - Tabelle 'angebot_artikel' erstellt.<br>";
    } else {
        echo " - Tabelle 'angebot_artikel' existiert bereits.<br>";
    }

    echo "<h2 style='color:green;'>Aktualisierung erfolgreich abgeschlossen!</h2>";

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Fehler:</h2><pre>" . $e->getMessage() . "</pre>";
}
