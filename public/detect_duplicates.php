<?php
// public/detect_duplicates.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$duplicates = DB::table('auftrag_tabelle')
    ->select('projekt_id', 'auftrag_id', DB::raw('COUNT(*) as count'))
    ->groupBy('projekt_id', 'auftrag_id')
    ->having('count', '>', 1)
    ->get();

// Final Status Summary
echo "<h1>Final Synchronization Status</h1>";

echo "<h2>1. Internal Duplicates (SAME Project, SAME Order-ID)</h2>";
if ($duplicates->isEmpty()) {
    echo "<p style='color:green; font-weight:bold;'>✅ No internal duplicates found.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ Found " . $duplicates->count() . " internal duplicates.</p>";
}

echo "<h2>2. Cross-Project Duplicates (SAME Order-ID in DIFFERENT Projects)</h2>";
$crossProjectDups = DB::table('auftrag_tabelle')
    ->select('auftrag_id', DB::raw('COUNT(DISTINCT projekt_id) as project_count'))
    ->groupBy('auftrag_id')
    ->having('project_count', '>', 1)
    ->get();

if ($crossProjectDups->isEmpty()) {
    echo "<p style='color:green; font-weight:bold;'>✅ No cross-project duplicates found. Every JTL-ID is unique to one Olgav2 project.</p>";
} else {
    echo "<p style='color:orange; font-weight:bold;'>⚠️ Found " . $crossProjectDups->count() . " JTL-IDs that exist in multiple projects.</p>";
}

echo "<h2>3. Redundant Project Definitions</h2>";
$redundantProjects = DB::table('auftrag_projekt')
    ->select('firmenname', DB::raw('COUNT(*) as count'))
    ->groupBy('firmenname')
    ->having('count', '>', 1)
    ->get();

if ($redundantProjects->isEmpty()) {
    echo "<p style='color:green; font-weight:bold;'>✅ No redundant projects (same name) found.</p>";
} else {
    echo "<p style='color:orange; font-weight:bold;'>⚠️ Found " . $redundantProjects->count() . " names that still have multiple project entries.</p>";
    foreach ($redundantProjects as $rp) {
        $pids = DB::table('auftrag_projekt')->where('firmenname', $rp->firmenname)->pluck('id')->toArray();
        echo "  - {$rp->firmenname}: IDs (" . implode(',', $pids) . ")<br>";
    }
}
?>
