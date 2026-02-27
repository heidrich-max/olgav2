<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Support\Facades\Artisan;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    echo "<h1>Deployment Check</h1>";
    $file = __DIR__ . '/../app/Mail/ProjectReminderMail.php';
    if (file_exists($file)) {
        echo "<p style='color:green'>Mailable existiert!</p>";
    } else {
        echo "<p style='color:red'>Mailable fehlt noch.</p>";
    }

    $columns = DB::select('DESCRIBE auftrag_projekt_firma');
    echo "<h2>Spalten in auftrag_projekt_firma:</h2><pre>";
    print_r(array_column($columns, 'Field'));
    echo "</pre>";

    echo "<h1>Migration läuft...</h1>";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<p>Exit Code: " . $exitCode . "</p>";
    if ($exitCode === 0) {
        echo "<h2 style='color:green'>Migration erfolgreich!</h2>";
    }
} catch (Exception $e) {
    echo "<h1>Fehler</h1><pre>" . $e->getMessage() . "</pre>";
}
