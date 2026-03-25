<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produkte', function (Blueprint $table) {
            $table->id();
            $table->string('artikelnummer')->unique();
            $table->integer('hersteller_id')->nullable();
            $table->string('produktname_hersteller')->nullable();
            $table->string('produktname')->nullable();
            $table->string('material')->nullable();
            $table->string('produktmasse')->nullable();
            $table->string('gewicht_g')->nullable();
            $table->string('grammatur')->nullable();
            $table->integer('menge_pro_karton')->nullable();
            $table->string('herkunftsland')->nullable();
            $table->string('zolltarifnummer')->nullable();
            $table->integer('mindestmenge')->nullable();
            $table->string('kategorie1')->nullable();
            $table->string('kategorie2')->nullable();
            $table->string('kategorie3')->nullable();
            $table->string('kategorie4')->nullable();
            $table->text('beschreibung')->nullable();
            $table->integer('lieferzeit_ohne_druck_min')->nullable();
            $table->integer('lieferzeit_ohne_druck_max')->nullable();
            $table->integer('lieferzeit_mit_druck_min')->nullable();
            $table->integer('lieferzeit_mit_druck_max')->nullable();
            $table->string('masse_produktkarton')->nullable();
            $table->string('bruttogewicht_produktkarton')->nullable();
            $table->decimal('preis', 10, 2)->nullable();
            $table->string('sale')->nullable();
            $table->string('minenfarbe')->nullable();
            $table->string('bearbeitungscode')->nullable();
            $table->text('hinweis')->nullable();
            $table->string('kapazitaet')->nullable();
            $table->string('farbe')->nullable();
            $table->string('foto01')->nullable();
            $table->string('foto02')->nullable();
            $table->string('foto03')->nullable();
            $table->string('foto04')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produkte');
    }
};
