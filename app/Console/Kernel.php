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
        // $schedule->command('inspire')->hourly();
    $schedule->command('correos:procesar')->everyMinute(); // O el intervalo que desees
    $schedule->command('correos:analizar')->everyMinute(); // O el intervalo que desees
    $schedule->command('correos:responder')->everyMinute(); // O el intervalo que desees

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
