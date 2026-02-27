<?php
/**
 * OLGA - Database Dump Script
 * Schreibt die Tabellenstruktur in eine Datei, damit der Agent sie lesen kann.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    ob_start();
    echo "Struktur von angebot_tabelle:\n";
    $columns = DB::select("DESCRIBE angebot_tabelle");
    foreach ($columns as $col) {
        print_r($col);
    }

    echo "\nBeispiel-Datensatz:\n";
    $sample = DB::table('angebot_tabelle')->first();
    print_r($sample);

    $output = ob_get_clean();
    file_put_contents(__DIR__ . '/db_structure_dump.txt', $output);
    
    echo "<h1>Erfolg!</h1>";
    echo "<p>Die Tabellenstruktur wurde in <b>public/db_structure_dump.txt</b> gespeichert.</p>";
    echo "<p>Der Agent kann diese Informationen nun verarbeiten.</p>";

} catch (\Exception $e) {
    echo "<h1>Fehler</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
