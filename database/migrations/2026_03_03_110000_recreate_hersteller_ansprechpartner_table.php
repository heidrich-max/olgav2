<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('hersteller_ansprechpartner');
        
        Schema::create('hersteller_ansprechpartner', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('hersteller_id');
            $blueprint->string('anrede', 50)->nullable();
            $blueprint->string('vorname', 255)->nullable();
            $blueprint->string('nachname', 255)->nullable();
            $blueprint->string('telefon', 255)->nullable();
            $blueprint->string('email', 255)->nullable();
            $blueprint->timestamps();

            // Optional: Index hinzufügen für schnellere Abfragen
            $blueprint->index('hersteller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hersteller_ansprechpartner');
    }
};
