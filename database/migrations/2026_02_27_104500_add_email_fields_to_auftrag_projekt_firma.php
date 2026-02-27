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
            $table->string('reminder_subject', 255)->nullable()->after('mail_from_name');
            $table->text('reminder_text')->nullable()->after('reminder_subject');
            $table->string('bcc_address', 255)->nullable()->after('reminder_text');
            $table->boolean('bcc_enabled')->default(false)->after('bcc_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftrag_projekt_firma', function (Blueprint $table) {
            $table->dropColumn(['reminder_subject', 'reminder_text', 'bcc_address', 'bcc_enabled']);
        });
    }
};
