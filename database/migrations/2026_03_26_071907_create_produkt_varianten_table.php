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
        Schema::create('produkt_varianten', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produkt_id')->index();
            $table->string('artikelnummer_full')->unique();
            $table->string('farbcode')->nullable();
            $table->string('farbe')->nullable();
            $table->string('foto01')->nullable();
            $table->string('foto02')->nullable();
            $table->string('foto03')->nullable();
            $table->string('foto04')->nullable();
            $table->timestamps();

            $table->foreign('produkt_id')->references('id')->on('produkte')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produkt_varianten');
    }
};
