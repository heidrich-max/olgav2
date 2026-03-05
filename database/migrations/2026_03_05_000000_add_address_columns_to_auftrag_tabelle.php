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
            if (!Schema::hasColumn('auftrag_tabelle', 'ansprechpartner_anrede')) {
                $table->string('ansprechpartner_anrede')->nullable()->after('firmenname');
                $table->string('ansprechpartner_titel')->nullable()->after('ansprechpartner_anrede');
                $table->string('ansprechpartner_vorname')->nullable()->after('ansprechpartner_titel');
                $table->string('ansprechpartner_nachname')->nullable()->after('ansprechpartner_vorname');
                
                $table->string('kunde_strasse')->nullable()->after('projektname');
                $table->string('kunde_plz')->nullable()->after('kunde_strasse');
                $table->string('kunde_ort')->nullable()->after('kunde_plz');
                $table->string('kunde_land')->nullable()->after('kunde_ort');
                $table->string('kunde_mail')->nullable()->after('kunde_land');
                $table->string('kunde_telefon')->nullable()->after('kunde_mail');
                $table->string('ansprechpartner_mobil')->nullable()->after('kunde_telefon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftrag_tabelle', function (Blueprint $table) {
            $table->dropColumn([
                'ansprechpartner_anrede',
                'ansprechpartner_titel',
                'ansprechpartner_vorname',
                'ansprechpartner_nachname',
                'kunde_strasse',
                'kunde_plz',
                'kunde_ort',
                'kunde_land',
                'kunde_mail',
                'kunde_telefon',
                'ansprechpartner_mobil'
            ]);
        });
    }
};
