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
        // Telegram: ส่งสรุปการลาประจำวัน ทุกวัน 07:00 น.
        $schedule->command('telegram:daily-summary')->dailyAt('07:00');

        // Telegram: แจ้งเตือนตารางเวรประจำวัน ทุกวัน 07:00 น.
        $schedule->command('telegram:duty-roster-notify')->dailyAt('07:00');
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
