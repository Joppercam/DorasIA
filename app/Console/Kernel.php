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
        // Importar doramas coreanos diariamente (2 páginas de resultados)
        $schedule->command('import:korean-dramas --pages=2 --update')
                 ->dailyAt('01:00')
                 ->description('Import Korean dramas daily')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Importar doramas japoneses cada 3 días (2 páginas de resultados)
        $schedule->command('import:japanese-dramas --pages=2 --update')
                 ->cron('0 2 */3 * *')
                 ->description('Import Japanese dramas every 3 days')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Importar doramas chinos cada 3 días (2 páginas de resultados)
        $schedule->command('import:chinese-dramas --pages=2 --update')
                 ->cron('0 3 */3 * *')
                 ->description('Import Chinese dramas every 3 days')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Importar películas coreanas semanalmente (2 páginas de resultados)
        $schedule->command('import:asian-movies --pages=2 --country=KR --update')
                 ->weekly()->mondays()->at('04:00')
                 ->description('Import Korean movies weekly')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Importar películas japonesas semanalmente (2 páginas de resultados)
        $schedule->command('import:asian-movies --pages=2 --country=JP --update')
                 ->weekly()->tuesdays()->at('04:00')
                 ->description('Import Japanese movies weekly')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Importar películas chinas semanalmente (2 páginas de resultados)
        $schedule->command('import:asian-movies --pages=2 --country=CN --update')
                 ->weekly()->wednesdays()->at('04:00')
                 ->description('Import Chinese movies weekly')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Actualizar detalles de personas (actores, directores) diariamente
        $schedule->command('update:person-details --limit=50 --missing-only')
                 ->dailyAt('05:00')
                 ->description('Update person details daily')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
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