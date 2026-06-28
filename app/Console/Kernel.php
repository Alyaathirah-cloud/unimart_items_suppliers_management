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
        $schedule->command('expiry:check')->daily();
        $schedule->command('low-stock:check')->daily();

        $schedule->command('inventory:send-alerts')->dailyAt('08:00');
        
        // Runs the stock/expiry check once every day at 8:00 AM (WhatsApp Notification System)
        $schedule->command('whatsapp:check-stock-expiry')
                 ->dailyAt('08:00');
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
