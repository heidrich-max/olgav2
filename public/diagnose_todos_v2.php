<?php
// public/diagnose_todos_v2.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Schema;

echo "<h1>ToDo Diagnose v2</h1>";
echo "Server Zeit: " . Carbon::now()->toDateTimeString() . "<br>";
echo "Heute (toDateString): " . Carbon::now()->toDateString() . "<br>";

$hasIsSystem = Schema::hasColumn('todos', 'is_system');
echo "Spalte 'is_system' in Tabelle 'todos'? " . ($hasIsSystem ? "JA" : "<b>NEIN (Das könnte der Fehler sein!)</b>") . "<br><br>";

// 1. Order Check
$orderNum = 'FWAU.032026-6068';
$order = DB::table('auftrag_tabelle')->where('auftragsnummer', $orderNum)->first();

echo "<h2>1. Auftrag Check ($orderNum)</h2>";
if ($order) {
    echo "ID: {$order->id}<br>";
    echo "Lieferdatum: " . ($order->lieferdatum ?? 'NULL') . "<br>";
    echo "Status: " . ($order->abgeschlossen_status ?? 'NULL') . "<br>";
    echo "Benutzer: " . ($order->benutzer ?? 'NULL') . "<br>";
    
    $today = Carbon::now()->toDateString();
    $isOverdue = $order->lieferdatum && $order->lieferdatum < $today;
    echo "Ist überfällig (< $today)? " . ($isOverdue ? "JA" : "NEIN") . "<br>";
    echo "Status != 'Auftrag abgeschlossen'? " . ($order->abgeschlossen_status !== 'Auftrag abgeschlossen' ? "JA" : "NEIN") . "<br>";
} else {
    echo "Auftrag nicht gefunden!<br>";
}

// 2. Offer Check
$offerNum = 'FWAB.022026-5202';
$offer = DB::table('angebot_tabelle')->where('angebotsnummer', $offerNum)->first();

echo "<h2>2. Angebot Check ($offerNum)</h2>";
if ($offer) {
    echo "ID: {$offer->id}<br>";
    echo "Erstelldatum: " . ($offer->erstelldatum ?? 'NULL') . "<br>";
    echo "Letzter Status: " . ($offer->letzter_status_name ?? 'NULL') . "<br>";
    echo "Benutzer: " . ($offer->benutzer ?? 'NULL') . "<br>";
    
    $targetDate7 = Carbon::now()->subDays(7)->startOfDay();
    $targetDate6 = Carbon::now()->subDays(6)->startOfDay();
    $offerDate = Carbon::parse($offer->erstelldatum)->startOfDay();
    
    echo "Alter >= 7 Tage (<= " . $targetDate7->toDateString() . ")? " . ($offerDate->lte($targetDate7) ? "JA" : "NEIN") . "<br>";
    echo "Alter >= 6 Tage (<= " . $targetDate6->toDateString() . ")? " . ($offerDate->lte($targetDate6) ? "JA" : "NEIN") . "<br>";
    echo "Status ist 'Status offen'? " . ($offer->letzter_status_name === 'Status offen' ? "JA" : "NEIN") . "<br>";
} else {
    echo "Angebot nicht gefunden!<br>";
}

// 3. User Check
$userName = 'Tobias Heidrich';
$user = DB::table('user')->where('name_komplett', $userName)->first();

echo "<h2>3. Benutzer Check ($userName)</h2>";
if ($user) {
    echo "ID: {$user->id}<br>";
    echo "Gefunden in user_tabelle.<br>";
} else {
    echo "Benutzer '$userName' nicht in der Tabelle 'user' gefunden!<br>";
    echo "Verfügbare User:<br>";
    $users = DB::table('user')->pluck('name_komplett');
    foreach($users as $u) echo "- $u<br>";
}

// 4. Todo Check
echo "<h2>4. Bestehende ToDos</h2>";
$todos = DB::table('todos')
    ->where(function($q) use ($order, $offer) {
        if ($order) $q->where('order_id', $order->id);
        if ($offer) $q->orWhere('offer_id', $offer->id);
    })
    ->get();

if ($todos->count() > 0) {
    foreach($todos as $t) {
        echo "Todo: {$t->task} | Erstellt: {$t->created_at} | Abgeschlossen: " . ($t->is_completed ? "JA" : "NEIN") . "<br>";
    }
} else {
    echo "Keine ToDos für diese IDs gefunden.<br>";
}
