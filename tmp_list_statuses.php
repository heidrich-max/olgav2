<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$statuses = \Illuminate\Support\Facades\DB::table('auftrag_status')->get();
echo json_encode($statuses, JSON_PRETTY_PRINT);
