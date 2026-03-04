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
    if (!Schema::hasColumn('todos', 'order_id')) {
        Schema::table('todos', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('offer_id');
            // Adding index/foreign key if possible (ignoring errors if auftrag_tabelle is not perfectly aligned)
            try {
                $table->foreign('order_id')->references('id')->on('auftrag_tabelle')->onDelete('cascade');
                echo "Successfully added 'order_id' column with foreign key.\n";
            } catch (\Exception $e) {
                echo "Added 'order_id' column, but foreign key creation failed (skipping): " . $e->getMessage() . "\n";
            }
        });
    } else {
        echo "Column 'order_id' already exists in 'todos' table.\n";
    }

    echo "\nMigration finished successfully.\n";
} catch (\Exception $e) {
    echo "\nERROR during migration: " . $e->getMessage() . "\n";
}
echo "</pre>";
