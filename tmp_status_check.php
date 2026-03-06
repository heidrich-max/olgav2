<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$status = Illuminate\Support\Facades\DB::table('auftrag_status')->get();
foreach($status as $s) {
    echo "{$s->status_sh}: {$s->status_lg} (BG: {$s->bg}, Color: {$s->color})\n";
}
