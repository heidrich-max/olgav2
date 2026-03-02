<?php
// rebuild_todos.php
// Dieses Script triggert die automatische Generierung von ToDos für alle Angebote, 
// die überfällig sind (7-Tage-Regel). Da wir GenerateOfferTodos.php aktualisiert haben,
// werden diese nun direkt im neuen Format (mit offer_id und Robot-Icon) erstellt.

use Illuminate\Support\Facades\Artisan;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "<h1>ToDo-Wiederherstellung gestartet</h1>";
    
    echo "Führe 'app:generate-offer-todos' aus...<br>";
    Artisan::call('app:generate-offer-todos');
    echo "<pre>" . Artisan::output() . "</pre>";
    
    echo "<br>Führe 'wiedervorlage:process' aus...<br>";
    Artisan::call('wiedervorlage:process');
    echo "<pre>" . Artisan::output() . "</pre>";
    
    echo "<br><b>Wiederherstellung abgeschlossen.</b> Deine ToDo-Liste im Dashboard sollte nun wieder gefüllt sein – diesmal mit den neuen Robot-Icons und Direkt-Links!";
    
} catch (\Exception $e) {
    echo "<h2 style='color:red'>Fehler bei der Wiederherstellung: " . $e->getMessage() . "</h2>";
}
