<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$orderNo = 'FWAU.022026-6052';
$localOrder = DB::table('auftrag_tabelle')->where('auftragsnummer', $orderNo)->first();

if (!$localOrder) {
    die("Local order not found.\n");
}

$wawi = DB::table('auftrag_projekt_wawi')->where('auftrag_projekt_id', $localOrder->projekt_id)->first();

if (!$wawi) {
    die("WAWI connection for project {$localOrder->projekt_id} not found.\n");
}

try {
    $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
    $wawi_db = new PDO($dsn, $wawi->username, $wawi->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $kAuftrag = $localOrder->auftrag_id;
    $stmt = $wawi_db->prepare("SELECT kAuftrag, dVoraussichtlichesLieferdatum FROM Verkauf.lvAuftragsverwaltung WHERE kAuftrag = ?");
    $stmt->execute([$kAuftrag]);
    $wawiOrder = $stmt->fetch();

    if ($wawiOrder) {
        echo "WAWI Order found:\n";
        echo "kAuftrag: " . $wawiOrder['kAuftrag'] . "\n";
        echo "dVoraussichtlichesLieferdatum (WAWI): " . ($wawiOrder['dVoraussichtlichesLieferdatum'] ?? 'NULL') . "\n";
    } else {
        echo "Order $kAuftrag not found in WAWI view Verkauf.lvAuftragsverwaltung.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
