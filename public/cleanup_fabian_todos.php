<?php
// cleanup_fabian_todos.php
// Dieses Script löscht alle alten ToDos für Fabian Frank, die mit "Angebots-Nachverfolgung:" beginnen.

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Laravel initialisieren
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $user = User::where('name_komplett', 'like', '%Fabian Frank%')->first();
    
    if (!$user) {
        die("Benutzer 'Fabian Frank' konnte nicht gefunden werden.");
    }

    echo "Benutzer gefunden: {$user->name_komplett} (ID: {$user->id})<br>";

    $todos = Todo::where('user_id', $user->id)
                 ->where('task', 'like', 'Angebots-Nachverfolgung:%')
                 ->get();

    if ($todos->count() > 0) {
        echo "Gefundene alte ToDos (" . $todos->count() . "):<br><ul>";
        foreach ($todos as $todo) {
            echo "<li>ID: {$todo->id}, Task: {$todo->task}</li>";
            $todo->delete();
        }
        echo "</ul>Alle " . $todos->count() . " alten ToDos wurden erfolgreich gelöscht.";
    } else {
        echo "Keine alten ToDos mit 'Angebots-Nachverfolgung:' für Fabian Frank gefunden.";
    }
} catch (\Exception $e) {
    echo "Fehler bei der Bereinigung: " . $e->getMessage();
}
