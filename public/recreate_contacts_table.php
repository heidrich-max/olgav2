<?php
// Fix für Pfade, da das Skript im /public Ordner liegt
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

try {
    echo "Überprüfe DB-Konfiguration...\n";
    $dbUser = config('database.connections.mysql.username');
    $dbName = config('database.connections.mysql.database');
    echo "Verwende DB: $dbName mit User: $dbUser\n\n";

    if ($dbUser === 'root' && env('DB_USERNAME') !== 'root') {
        echo "WARNUNG: Konfigurations-Cache scheint aktiv zu sein oder .env wurde nicht korrekt geladen.\n";
        echo "Versuche Cache zu umgehen...\n";
    }

    echo "Lösche Tabelle hersteller_ansprechpartner (falls vorhanden)...\n";
    Schema::dropIfExists('hersteller_ansprechpartner');
    
    echo "Erstelle Tabelle hersteller_ansprechpartner neu...\n";
    Schema::create('hersteller_ansprechpartner', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('hersteller_id');
        $table->string('anrede', 50)->nullable();
        $table->string('vorname', 255)->nullable();
        $table->string('nachname', 255)->nullable();
        $table->string('telefon', 255)->nullable();
        $table->string('email', 255)->nullable();
        $table->timestamps();
        $table->index('hersteller_id');
    });
    
    echo "ERFOLG: Tabelle wurde erfolgreich neu angelegt.";
} catch (\Exception $e) {
    echo "FEHLER: " . $e->getMessage();
    echo "\n\nStack Trace:\n" . $e->getTraceAsString();
}
