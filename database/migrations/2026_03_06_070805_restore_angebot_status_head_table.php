<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('angebot_status_head')) {
            Schema::create('angebot_status_head', function (Blueprint $table) {
                // Table must use int(11) to be consistent with legacy and foreign keys
                $table->integer('id')->primary();
                $table->string('status', 50);
                $table->string('bg', 6);
                $table->string('color', 6);
            });

            // Re-inserting the missing data based on the status_head_id links in angebot_status
            DB::table('angebot_status_head')->insert([
                ['id' => 1, 'status' => 'offen', 'bg' => '653191', 'color' => 'fff'],
                ['id' => 2, 'status' => 'Erinnerung', 'bg' => '80c143', 'color' => 'fff'],
                ['id' => 3, 'status' => 'In Klärung', 'bg' => 'd91f37', 'color' => 'fff'],
                ['id' => 4, 'status' => 'abgeschlossen', 'bg' => 'f69620', 'color' => 'fff'],
                ['id' => 5, 'status' => 'angenommen', 'bg' => 'ffff00', 'color' => '000'],
                // Optional ID 0 if used as placeholder
                ['id' => 0, 'status' => 'Alle', 'bg' => '000000', 'color' => 'fff'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angebot_status_head');
    }
};
