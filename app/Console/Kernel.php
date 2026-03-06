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
        // ส่งแจ้งเตือนเวรยามประจำวันเข้ากลุ่ม LINE ทุกวัน เวลา 07:00 น.
        $schedule->command('line:daily-duty-roster')
                 ->dailyAt('07:00')
                 ->timezone('Asia/Bangkok')
                 ->withoutOverlapping()
                 ->onOneServer();

        // ส่งสรุปการลาประจำวันเข้ากลุ่ม LINE ทุกวัน เวลา 08:00 น.
        $schedule->command('line:daily-leave-summary')
                 ->dailyAt('08:00')
                 ->timezone('Asia/Bangkok')
                 ->withoutOverlapping()
                 ->onOneServer();
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
