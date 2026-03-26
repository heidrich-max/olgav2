<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = DB::select('SHOW TABLES');
$dbname = DB::getDatabaseName();
$key = "Tables_in_" . $dbname;

foreach($results as $r) {
    $t = $r->$key;
    if(strpos($t, 'auftrag_') === 0) {
        echo $t . "\n";
    }
}
