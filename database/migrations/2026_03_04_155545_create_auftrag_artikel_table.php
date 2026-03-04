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
        Schema::create('auftrag_artikel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auftrag_id_lokal');
            $table->unsignedBigInteger('jtl_auftrag_id');
            $table->integer('sort_order')->default(0);
            $table->string('art_nr')->nullable();
            $table->text('bezeichnung')->nullable();
            $table->decimal('menge', 12, 4)->default(0);
            $table->string('einheit')->default('Stk.');
            $table->decimal('einzelpreis_netto', 12, 4)->default(0);
            $table->decimal('mwst_prozent', 5, 2)->default(0);
            $table->decimal('gesamt_netto', 12, 4)->default(0);
            $table->timestamps();

            $table->foreign('auftrag_id_lokal')->references('id')->on('auftrag_tabelle')->onDelete('cascade');
            $table->index('jtl_auftrag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auftrag_artikel');
    }
};
