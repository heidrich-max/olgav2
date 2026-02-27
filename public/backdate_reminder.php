<?php

/**
 * OLGA - Backdate Reminder Script
 * Dieses Script pflegt den Reminder-Status für ein spezifisches Angebot nach.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$offerNumber = 'FWAB.022026-5190';
$backdate = '2026-02-24';

echo "<h1>OLGA - Status-Nachpflege</h1>";

try {
    $offer = DB::table('angebot_tabelle')->where('angebotsnummer', $offerNumber)->first();

    if (!$offer) {
        throw new Exception("Angebot {$offerNumber} nicht gefunden.");
    }

    // 1. Status Details für ID 2 (Erinnerung verschickt) holen
    $status = DB::table('angebot_status')->where('id', 2)->first();
    $statusName = $status ? 'Status ' . $status->status_lg : 'Status Erinnerung versendet';
    $bg = $status ? $status->bg : 'warning';
    $color = $status ? $status->color : 'white';
    $statusSh = $status ? $status->status_sh : 'EV';

    // 2. Update angebot_tabelle
    DB::table('angebot_tabelle')
        ->where('id', $offer->id)
        ->update([
            'letzter_status'           => $statusSh,
            'letzter_status_name'      => $statusName,
            'letzter_status_bg_hex'    => $bg,
            'letzter_status_farbe_hex' => $color,
            'reminder_date'            => $backdate,
            'reminder_count'           => 1
        ]);

    // 3. Historien-Eintrag (AngebotInformation)
    DB::table('angebot_information')->insert([
        'angebot_id' => $offer->id,
        'projekt_id' => $offer->projekt_id,
        'user_id'    => 1, // System oder Admin
        'information' => "Nachträglicher Eintrag: Erinnerung wurde am {$backdate} versendet. Wiedervorlage-Logik aktiviert.",
        'timestamp'   => $backdate . ' 09:00:00',
        'created_at'  => $backdate . ' 09:00:00',
        'updated_at'  => Carbon::now()
    ]);

    echo "<h3 style='color: green;'>Erfolg!</h3>";
    echo "Das Angebot <strong>{$offerNumber}</strong> wurde auf den <strong>{$backdate}</strong> datiert.";
    echo "<p>Die Wiedervorlage-Logik wird dieses Angebot nun beim nächsten automatischen Check berücksichtigen.</p>";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>Fehler:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
