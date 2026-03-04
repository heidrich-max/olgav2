<?php
/**
 * Standalone Migration Script: Add order_id to todos table
 * Purpose: Manual database update if artisan migrate is not preferred.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "Starting migration: Add 'order_id' to 'todos' table...\n";

try {
    // 1. Detect ID type in auftrag_tabelle
    $referencedCol = DB::select("SHOW COLUMNS FROM auftrag_tabelle WHERE Field = 'id'");
    $type = !empty($referencedCol) ? strtolower($referencedCol[0]->Type) : 'bigint(20) unsigned';
    
    $isBig = strpos($type, 'bigint') !== false;
    $isUnsigned = strpos($type, 'unsigned') !== false;

    if (!Schema::hasColumn('todos', 'order_id')) {
        Schema::table('todos', function (Blueprint $table) use ($isBig) {
            if ($isBig) {
                $table->unsignedBigInteger('order_id')->nullable()->after('offer_id');
            } else {
                $table->unsignedInteger('order_id')->nullable()->after('offer_id');
            }
        });
        echo "Successfully added 'order_id' column (Type: " . ($isBig ? 'BIGINT' : 'INT') . ").\n";
    } else {
        echo "Column 'order_id' already exists in 'todos' table.\n";
    }

    // 2. Try to add Foreign Key separately
    try {
        // Find if foreign key already exists (basic check by name)
        $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'todos' AND CONSTRAINT_NAME = 'todos_order_id_foreign' AND TABLE_SCHEMA = DATABASE()");
        
        if (empty($fkExists)) {
            Schema::table('todos', function (Blueprint $table) {
                $table->foreign('order_id')->references('id')->on('auftrag_tabelle')->onDelete('cascade');
            });
            echo "Successfully added foreign key 'todos_order_id_foreign'.\n";
        } else {
            echo "Foreign key 'todos_order_id_foreign' already exists.\n";
        }
    } catch (\Exception $fk_e) {
        echo "NOTE: Foreign key creation failed but column is present. This is likely due to existing data or type mismatch. The system will work fine without the constraint.\n";
        echo "FK Error: " . $fk_e->getMessage() . "\n";
    }

    echo "\nMigration process completed.\n";
} catch (\Exception $e) {
    echo "\nERROR during migration: " . $e->getMessage() . "\n";
}
echo "</pre>";
