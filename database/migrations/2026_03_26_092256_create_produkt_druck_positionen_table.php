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
        Schema::create('produkt_druck_positionen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produkt_variante_id')->constrained('produkt_varianten')->onDelete('cascade');
            $table->string('position_name');
            $table->string('print_size')->nullable();
            $table->json('techniques')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produkt_druck_positionen');
    }
};
