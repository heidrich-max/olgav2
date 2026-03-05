<?php
// public/verify_2025_fix.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$exception = DB::table('auftrag_tabelle')->where('auftragsnummer', 'WBAU.112025-8278')->first();
$remainingNeu = DB::table('auftrag_tabelle')->whereYear('erstelldatum', 2025)->where('letzter_status', 'NEU')->count();

echo "<pre>";
echo "Verification:\n";
echo "Exceptions (WBAU.112025-8278): " . ($exception ? $exception->letzter_status : 'Not found') . "\n";
echo "Remaining 'NEU' orders in 2025: " . $remainingNeu . "\n";
echo "</pre>";
