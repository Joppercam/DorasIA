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
<<<<<<< HEAD
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
                 
        // Importar doramas románticos asiáticos cada 2 días
        $schedule->command('dorasia:import-romantic-dramas --pages=2 --country=all')
                 ->cron('0 1 */2 * *')
                 ->description('Import romantic Asian dramas every 2 days')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
                 
        // Importar doramas románticos por subgéneros cada semana
        $subgenres = ['historical_romance', 'romantic_comedy', 'melodrama', 'office_romance', 'youth_romance'];
        foreach ($subgenres as $index => $subgenre) {
            $schedule->command("dorasia:import-romantic-dramas --pages=1 --subgenre={$subgenre}")
                     ->weekly()->days([$index + 1]) // Monday through Friday for different subgenres
                     ->at('03:00')
                     ->description("Import {$subgenre} dramas weekly")
                     ->onOneServer()
                     ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        }
        
        // Obtener noticias sobre actores populares diariamente
        $schedule->command('dorasia:fetch-news --source=newsapi --limit=20 --add-images')
                 ->dailyAt('06:00')
                 ->description('Fetch daily news about popular actors')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Generar noticias de actores con IA semanalmente
        $schedule->command('dorasia:fetch-news --source=ai --limit=5')
                 ->weekly()->sundays()->at('06:30')
                 ->description('Generate AI news about actors weekly')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Obtener noticias sobre películas y series semanalmente
        $schedule->command('dorasia:fetch-movie-news --source=newsapi --limit=15 --add-images')
                 ->weekly()->saturdays()->at('07:00')
                 ->description('Fetch weekly news about movies and shows')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Obtener noticias sobre lanzamientos de TMDB semanalmente
        $schedule->command('dorasia:fetch-movie-news --source=tmdb --limit=10')
                 ->weekly()->fridays()->at('07:30')
                 ->description('Fetch weekly updates from TMDB about movies and shows')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
                 
        // Generar noticias asiáticas adicionales diariamente
        $schedule->command('news:generate-more-asian')
                 ->dailyAt('05:00')
                 ->description('Generate additional Asian entertainment news daily')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
                 
        // Validar imágenes de actores en noticias (después de todas las importaciones)
        $schedule->command('news:validate-actor-images --fix --update-generic')
                 ->dailyAt('08:00')
                 ->description('Validate and fix actor images in news articles')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
                 
        // Actualizar imágenes de actores semanalmente
        $schedule->command('actors:update-images --limit=50')
                 ->weekly()->mondays()->at('09:00')
                 ->description('Update actor images from TMDB')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
                 
        // Importar críticas profesionales diariamente
        $schedule->command('reviews:import --limit=20')
                 ->dailyAt('10:00')
                 ->description('Import professional reviews for titles')
                 ->onOneServer()
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
=======
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
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
<<<<<<< HEAD
    
    /**
     * Get the commands that should be removed from the list of available commands.
     *
     * @return array
     */
    protected function getRouteMiddleware()
    {
        return [
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ];
    }
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
}