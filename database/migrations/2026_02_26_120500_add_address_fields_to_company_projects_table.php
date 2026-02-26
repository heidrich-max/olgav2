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
            if (!Schema::hasColumn('auftrag_projekt_firma', 'strasse')) {
                $table->string('strasse')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'plz')) {
                $table->string('plz', 10)->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'ort')) {
                $table->string('ort')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'telefon')) {
                $table->string('telefon')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'inhaber')) {
                $table->string('inhaber')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'ust_id')) {
                $table->string('ust_id')->nullable();
            }
            if (!Schema::hasColumn('auftrag_projekt_firma', 'handelsregister')) {
                $table->string('handelsregister')->nullable();
            }
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
