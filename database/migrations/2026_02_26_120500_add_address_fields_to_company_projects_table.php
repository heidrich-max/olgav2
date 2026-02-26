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
        Schema::table('auftrag_projekt_firma', function (Blueprint $table) {
            $table->string('strasse')->nullable();
            $table->string('plz', 10)->nullable();
            $table->string('ort')->nullable();
            $table->string('telefon')->nullable();
            $table->string('email')->nullable();
            $table->string('inhaber')->nullable();
            $table->string('ust_id')->nullable();
            $table->string('handelsregister')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftrag_projekt_firma', function (Blueprint $table) {
            $table->dropColumn([
                'strasse', 'plz', 'ort', 'telefon', 'email', 'inhaber', 'ust_id', 'handelsregister'
            ]);
        });
    }
};
