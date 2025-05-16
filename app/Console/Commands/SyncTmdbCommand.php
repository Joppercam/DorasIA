<?php

namespace App\Console\Commands;

use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncTmdbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tmdb 
                            {type=all : Tipo de contenido a sincronizar (genres, movies, tvshows, all)}
                            {--pages=1 : Número de páginas a sincronizar}
                            {--force : Forzar sincronización completa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza datos desde la API de TMDB';

    /**
     * Execute the console command.
     */
    public function handle(TmdbService $tmdbService)
    {
        $type = $this->argument('type');
        $pages = (int) $this->option('pages');
        $force = $this->option('force');
        
        $this->info('Iniciando sincronización de TMDB...');
        
        try {
            if ($type === 'all' || $type === 'genres') {
                $this->syncGenres($tmdbService);
            }
            
            if ($type === 'all' || $type === 'movies') {
                $this->syncMovies($tmdbService, $pages);
            }
            
            if ($type === 'all' || $type === 'tvshows') {
                $this->syncTvShows($tmdbService, $pages);
            }
            
            $this->info('Sincronización completada exitosamente.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la sincronización: ' . $e->getMessage());
            Log::error('TMDB Sync Error: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Sincronizar géneros
     */
    protected function syncGenres(TmdbService $tmdbService)
    {
        $this->info('Sincronizando géneros...');
        
        $start = now();
        $result = $tmdbService->syncGenres();
        $time = now()->diffInSeconds($start);
        
        if ($result) {
            $this->info("Géneros sincronizados correctamente en {$time} segundos.");
        } else {
            $this->warn('No se pudieron sincronizar los géneros.');
        }
    }
    
    /**
     * Sincronizar películas
     */
    protected function syncMovies(TmdbService $tmdbService, int $pages)
    {
        $this->info("Sincronizando películas asiáticas ({$pages} páginas)...");
        $totalMovies = 0;
        $bar = $this->output->createProgressBar($pages);
        $start = now();
        
        for ($page = 1; $page <= $pages; $page++) {
            $count = $tmdbService->syncAsianMovies($page);
            $totalMovies += $count;
            $bar->advance();
        }
        
        $bar->finish();
        $time = now()->diffInSeconds($start);
        $this->newLine();
        $this->info("Se han sincronizado {$totalMovies} películas en {$time} segundos.");
    }
    
    /**
     * Sincronizar series
     */
    protected function syncTvShows(TmdbService $tmdbService, int $pages)
    {
        $this->info("Sincronizando series/doramas asiáticos ({$pages} páginas)...");
        $totalShows = 0;
        $bar = $this->output->createProgressBar($pages);
        $start = now();
        
        for ($page = 1; $page <= $pages; $page++) {
            $count = $tmdbService->syncAsianTvShows($page);
            $totalShows += $count;
            $bar->advance();
        }
        
        $bar->finish();
        $time = now()->diffInSeconds($start);
        $this->newLine();
        $this->info("Se han sincronizado {$totalShows} series en {$time} segundos.");
    }
}