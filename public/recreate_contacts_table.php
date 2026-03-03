<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
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
}
