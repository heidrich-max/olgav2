<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNumber = 'FWAU.032026-6068';
$order = DB::table('auftrag_tabelle')->where('auftragsnummer', $orderNumber)->first();

if (!$order) {
    echo "Auftrag {$orderNumber} nicht gefunden.\n";
} else {
    echo "Auftragsdaten für {$orderNumber}:\n";
    echo "ID: " . $order->id . "\n";
    echo "Lieferdatum: " . $order->lieferdatum . "\n";
    echo "Abgeschlossen Status: " . $order->abgeschlossen_status . "\n";
    echo "Benutzer: " . $order->benutzer . "\n";
    
    $todo = DB::table('todos')
        ->where('order_id', $order->id)
        ->where('is_system', true)
        ->get();
    
    echo "Anzahl System-To-Dos: " . $todo->count() . "\n";
    foreach ($todo as $t) {
        echo "- Task: " . $t->task . " (Completed: " . ($t->is_completed ? 'ja' : 'nein') . ")\n";
    }
}
