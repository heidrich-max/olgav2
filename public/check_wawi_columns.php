<?php
// public/check_wawi_columns.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$wawi = DB::table('auftrag_projekt_wawi')->first();
if (!$wawi) {
    die("No Wawi connection found.");
}

try {
    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $wawi->username, $wawi->password);
    
    // Get columns for Verkauf.lvAuftragsverwaltung
    $stmt = $pdo->query("SELECT TOP 1 * FROM Verkauf.lvAuftragsverwaltung");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h1>Columns in Verkauf.lvAuftragsverwaltung</h1>";
    echo "<ul>";
    foreach (array_keys($row) as $column) {
        if (strpos($column, 'Liefer') !== false || strpos($column, 'Adresse') !== false) {
            echo "<li><strong>$column</strong></li>";
        } else {
            echo "<li>$column</li>";
        }
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
