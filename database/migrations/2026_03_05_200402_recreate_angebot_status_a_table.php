<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create if it doesn't exist (in case it was restored manually)
        if (!Schema::hasTable('angebot_status_a')) {
            Schema::create('angebot_status_a', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('angebot_id');
                $table->unsignedInteger('projekt_id')->default(0);
                $table->unsignedInteger('status')->default(0);
                $table->unsignedInteger('user_id')->nullable();
                $table->timestamp('timestamp')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('angebot_status_a');
    }
};
