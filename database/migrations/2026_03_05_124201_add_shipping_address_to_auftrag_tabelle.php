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
        Schema::table('auftrag_tabelle', function (Blueprint $table) {
            $table->string('lieferadresse_firma')->nullable()->after('ansprechpartner_mobil');
            $table->string('lieferadresse_anrede')->nullable()->after('lieferadresse_firma');
            $table->string('lieferadresse_titel')->nullable()->after('lieferadresse_anrede');
            $table->string('lieferadresse_vorname')->nullable()->after('lieferadresse_titel');
            $table->string('lieferadresse_nachname')->nullable()->after('lieferadresse_vorname');
            $table->string('lieferadresse_strasse')->nullable()->after('lieferadresse_nachname');
            $table->string('lieferadresse_plz')->nullable()->after('lieferadresse_strasse');
            $table->string('lieferadresse_ort')->nullable()->after('lieferadresse_plz');
            $table->string('lieferadresse_land')->nullable()->after('lieferadresse_ort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftrag_tabelle', function (Blueprint $table) {
            $table->dropColumn([
                'lieferadresse_firma',
                'lieferadresse_anrede',
                'lieferadresse_titel',
                'lieferadresse_vorname',
                'lieferadresse_nachname',
                'lieferadresse_strasse',
                'lieferadresse_plz',
                'lieferadresse_ort',
                'lieferadresse_land',
            ]);
        });
    }
};
