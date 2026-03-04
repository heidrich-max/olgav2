<?php
/**
 * Manual Trigger Script: Process Overdue Deliveries
 * Purpose: Run the overdue delivery check without console access.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<pre>";
echo "Starting overdue delivery check (app:process-overdue-deliveries)...\n\n";

try {
    // Run the artisan command
    $exitCode = Artisan::call('app:process-overdue-deliveries');
        echo "Overdue delivery check completed.<br>";
        
        Artisan::call('app:process-bo-status-orders');
        echo "BO status check completed.<br>";
        
        echo "<br><b>Result:</b><br><pre>" . Artisan::output() . "</pre>";
    
    echo "\n\nCommand finished with exit code: " . $exitCode . "\n";
} catch (\Exception $e) {
    echo "\nERROR during command execution: " . $e->getMessage() . "\n";
}
echo "</pre>";
