<?php
// public/cleanup_duplicates.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<h1>Auftrags-Duplikate Bereinigung</h1>";

// Finde Auftragsnummern, die mehrfach vorhanden sind
$duplicates = DB::select("
    SELECT auftragsnummer, COUNT(*) as anzahl
    FROM auftrag_tabelle
    WHERE auftragsnummer != '' AND auftragsnummer IS NOT NULL
    GROUP BY auftragsnummer
    HAVING COUNT(*) > 1
");

echo "<p>Gefundene doppelte Auftragsnummern: " . count($duplicates) . "</p>";

$deletedCount = 0;

foreach ($duplicates as $dup) {
    // Hole alle Datensätze für diese Auftragsnummer, sortiert nach ID aufsteigend
    // Der älteste Datensatz (kleinste ID) ist das Original, das wir behalten wollen.
    // Die neueren (höhere ID) löschen wir.
    $records = DB::table('auftrag_tabelle')
        ->where('auftragsnummer', $dup->auftragsnummer)
        ->orderBy('id', 'asc')
        ->get(['id', 'auftragsnummer', 'hersteller', 'projekt_id', 'auftrag_id']);
        
    $original = $records->first();
    
    echo "<hr/><h3>Auftrag: {$dup->auftragsnummer}</h3>";
    echo "<ul>";
    
    // Überspringe das erste Element (Original)
    $toDelete = $records->slice(1);
    
    echo "<li><strong>Original (Wird behalten):</strong> ID {$original->id} | JTL-ID: {$original->auftrag_id} | Projekt: {$original->projekt_id} | Hersteller: '{$original->hersteller}'</li>";
    
    foreach ($toDelete as $record) {
        echo "<li style='color:red;'><strong>Duplikat (Wird gelöscht):</strong> ID {$record->id} | JTL-ID: {$record->auftrag_id} | Projekt: {$record->projekt_id} | Hersteller: '{$record->hersteller}'</li>";
        
        // Lösche das Duplikat
        DB::table('auftrag_tabelle')->where('id', $record->id)->delete();
        $deletedCount++;
    }
    
    echo "</ul>";
}

echo "<h2>Fertig! {$deletedCount} Duplikate wurden sicher entfernt.</h2>";
