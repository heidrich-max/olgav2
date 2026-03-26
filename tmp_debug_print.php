<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$positions = \App\Models\ProduktDruckPosition::take(20)->get();
foreach($positions as $p) {
    echo "ID: {$p->id} | Pos: {$p->position_name} | Tech: " . json_encode($p->techniques) . PHP_EOL;
}
