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
        // Handle expired subscriptions daily at 2 AM
        $schedule->command('subscriptions:handle-expired')->dailyAt('02:00');

        // Send email verification reminders daily at 10 AM
        $schedule->command('email:send-verification-reminders')->dailyAt('10:00');

        // Post due prepaid expense amortization installments daily at 6 AM
        $schedule->command('prepaid:post-installments')->dailyAt('06:00');

        // Re-verify pending subscription payments every 15 minutes
        $schedule->command('subscriptions:reconfirm-pending')->everyFifteenMinutes();
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
