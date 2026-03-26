<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
foreach($tables as $t) {
    if(strpos($t, 'auftrag_') === 0) {
        echo $t . "\n";
    }
}
