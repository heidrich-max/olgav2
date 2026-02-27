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
            $table->date('reminder_date')->nullable()->after('erstelldatum');
            $table->integer('reminder_count')->default(0)->after('reminder_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angebot_tabelle', function (Blueprint $table) {
            $table->dropColumn(['reminder_date', 'reminder_count']);
        });
    }
};
