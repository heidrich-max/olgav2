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
        Schema::create('produkt_preise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produkt_variante_id')->constrained('produkt_varianten')->onDelete('cascade');
            $table->integer('tier_number');
            $table->integer('quantity');
            $table->decimal('base_price', 12, 4)->nullable();
            $table->decimal('profit_without_print', 12, 4)->nullable();
            $table->decimal('profit_print', 12, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produkt_preise');
    }
};
