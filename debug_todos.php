<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Order Check FWAU.032026-6068 ---\n";
$order = DB::table('auftrag_tabelle')
    ->where('auftragsnummer', 'FWAU.032026-6068')
    ->first();

if ($order) {
    echo "ID: " . $order->id . "\n";
    echo "Nummer: " . $order->auftragsnummer . "\n";
    echo "Lieferdatum: " . ($order->lieferdatum ?: 'NULL') . "\n";
    echo "Status: " . ($order->abgeschlossen_status ?: 'NULL') . "\n";
    echo "Benutzer: " . ($order->benutzer ?: 'NULL') . "\n";
    echo "Projekt: " . ($order->projektname ?: 'NULL') . "\n";
} else {
    echo "Order not found.\n";
}

echo "\n--- Offer Check FWAB.022026-5202 ---\n";
$offer = DB::table('angebot_tabelle')
    ->where('angebotsnummer', 'FWAB.022026-5202')
    ->first();

if ($offer) {
    echo "ID: " . $offer->id . "\n";
    echo "Nummer: " . $offer->angebotsnummer . "\n";
    echo "Erstelldatum: " . ($offer->erstelldatum ?: 'NULL') . "\n";
    echo "Status: " . ($offer->letzter_status_name ?: 'NULL') . "\n";
    echo "Benutzer: " . ($offer->benutzer ?: 'NULL') . "\n";
    echo "Reminder Date: " . (isset($offer->reminder_date) ? $offer->reminder_date : 'N/A') . "\n";
} else {
    echo "Offer not found.\n";
}

echo "\n--- User Mapping Check ---\n";
$tobias = DB::table('user')->where('name_komplett', 'like', '%Tobias Heidrich%')->first();
if ($tobias) {
    echo "User found: " . $tobias->name_komplett . " (ID: " . $tobias->id . ")\n";
} else {
    echo "User 'Tobias Heidrich' not found in user table.\n";
}
