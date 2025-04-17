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
        // Sincronizar géneros una vez por semana (domingo a medianoche)
        $schedule->command('sync:tmdb genres')
            ->weekly()
            ->sundays()
            ->at('00:00')
            ->withoutOverlapping()
            ->onOneServer();
            
        // Sincronizar películas populares diariamente
        $schedule->command('sync:tmdb movies --pages=2')
            ->dailyAt('01:00')
            ->withoutOverlapping()
            ->onOneServer();
            
        // Sincronizar series/doramas populares diariamente
        $schedule->command('sync:tmdb tvshows --pages=2')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->onOneServer();
            
        // Sincronización completa semanal (sábado en la madrugada)
        $schedule->command('sync:tmdb all --pages=5')
            ->weekly()
            ->saturdays()
            ->at('02:00')
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