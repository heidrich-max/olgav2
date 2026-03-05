<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:import-jtl-offers')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('app:import-jtl-order-articles')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('app:import-jtl-orders')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('orders:sync-statuses')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('app:generate-offer-todos')->dailyAt('06:00');
        $schedule->command('wiedervorlage:process')->dailyAt('06:00');
        $schedule->command('app:process-overdue-deliveries')->dailyAt('06:00');
        $schedule->command('app:process-bo-status-orders')->dailyAt('06:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
