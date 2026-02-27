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
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            $table->string('ansprechpartner_anrede', 50)->nullable()->after('firmenname');
            $table->string('ansprechpartner_titel', 50)->nullable()->after('ansprechpartner_anrede');
            $table->string('ansprechpartner_vorname', 100)->nullable()->after('ansprechpartner_titel');
            $table->string('ansprechpartner_nachname', 100)->nullable()->after('ansprechpartner_vorname');
            $table->string('ansprechpartner_mobil', 50)->nullable()->after('kunde_telefon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            $table->dropColumn([
                'ansprechpartner_anrede',
                'ansprechpartner_titel',
                'ansprechpartner_vorname',
                'ansprechpartner_nachname',
                'ansprechpartner_mobil'
            ]);
        });
    }
};
