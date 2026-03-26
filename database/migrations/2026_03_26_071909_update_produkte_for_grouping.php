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
        Schema::table('produkte', function (Blueprint $table) {
            // Remove unique constraint from artikelnummer
            // Note: In SQLite we might need to recreate the table, but Laravel handles some cases.
            // For MySQL: $table->dropUnique('produkte_artikelnummer_unique');
            
            // To be safe across engines, we first rename and then drop the index if possible.
            // But here we just want to remove the unique constraint.
            $table->dropUnique(['artikelnummer']);
            
            // Rename artikelnummer to base_artikelnummer
            $table->renameColumn('artikelnummer', 'base_artikelnummer');
            
            // Remove columns that are now in variants
            $table->dropColumn(['farbe', 'foto01', 'foto02', 'foto03', 'foto04']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produkte', function (Blueprint $table) {
            $table->string('farbe')->nullable();
            $table->string('foto01')->nullable();
            $table->string('foto02')->nullable();
            $table->string('foto03')->nullable();
            $table->string('foto04')->nullable();
            $table->renameColumn('base_artikelnummer', 'artikelnummer');
            $table->unique('artikelnummer');
        });
    }
};
