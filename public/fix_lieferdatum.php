<?php
// public/fix_lieferdatum.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE auftrag_tabelle MODIFY lieferdatum DATETIME NULL DEFAULT NULL");
    echo "Successfully made lieferdatum nullable.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
