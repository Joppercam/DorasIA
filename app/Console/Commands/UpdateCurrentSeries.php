<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\UpcomingSeries;
use App\Services\TmdbService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateCurrentSeries extends Command
{
    protected $signature = 'kdrama:update-current 
                            {--limit=50 : Number of series to update per run}
                            {--status=all : Filter by status: all, ongoing, ended, returning}
                            {--force : Force update even if recently updated}';
                            
    protected $description = 'Update existing series information and check for new seasons or status changes';

    private $tmdbService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->tmdbService = app(TmdbService::class);
        
        $limit = $this->option('limit');
        $status = $this->option('status');
        $force = $this->option('force');
        
        $this->info("🔄 Actualizando series existentes...");
        $this->info("📊 Límite por ejecución: {$limit}");
        $this->info("🎯 Estado: {$status}");
        
        // Obtener series a actualizar
        $seriesToUpdate = $this->getSeriesToUpdate($limit, $status, $force);
        
        if ($seriesToUpdate->isEmpty()) {
            $this->info("✅ No hay series que actualizar en este momento");
            return 0;
        }
        
        $this->info("📺 Series a actualizar: {$seriesToUpdate->count()}");
        
        $bar = $this->output->createProgressBar($seriesToUpdate->count());
        $bar->start();
        
        $updated = 0;
        $newSeasons = 0;
        $statusChanges = 0;
        $errors = 0;
        
        foreach ($seriesToUpdate as $series) {
            try {
                $result = $this->updateSeries($series);
                
                if ($result['updated']) $updated++;
                if ($result['new_seasons']) $newSeasons += $result['new_seasons'];
                if ($result['status_changed']) $statusChanges++;
                
                $bar->advance();
                
                // Delay para evitar rate limiting
                usleep(300000); // 300ms delay
                
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error actualizando {$series->title}: " . $e->getMessage());
                Log::error("Error updating series", [
                    'series_id' => $series->id,
                    'tmdb_id' => $series->tmdb_id,
                    'error' => $e->getMessage()
                ]);
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->newLine();
        
        // Verificar series en emisión que podrían haber terminado
        $this->checkOngoingSeries();
        
        // Limpiar cache
        $this->clearCache();
        
        // Mostrar resultados
        $this->displayResults($updated, $newSeasons, $statusChanges, $errors);
        
        return 0;
    }
    
    private function getSeriesToUpdate($limit, $status, $force)
    {
        $query = Series::whereNotNull('tmdb_id');
        
        // Filtrar por status si se especifica
        if ($status !== 'all') {
            switch ($status) {
                case 'ongoing':
                    $query->whereIn('status', ['Returning Series', 'In Production']);
                    break;
                case 'ended':
                    $query->where('status', 'Ended');
                    break;
                case 'returning':
                    $query->where('status', 'Returning Series');
                    break;
            }
        }
        
        // Si no es forzado, actualizar solo series que no se han actualizado recientemente
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('updated_at')
                  ->orWhere('updated_at', '<', Carbon::now()->subDays(7));
            });
        }
        
        // Priorizar series en emisión y populares
        return $query->orderByRaw("CASE 
                                    WHEN status = 'Returning Series' THEN 1
                                    WHEN status = 'In Production' THEN 2  
                                    WHEN status = 'Ended' THEN 3
                                    ELSE 4 END")
                    ->orderBy('popularity', 'desc')
                    ->limit($limit)
                    ->get();
    }
    
    private function updateSeries($series)
    {
        $result = [
            'updated' => false,
            'new_seasons' => 0,
            'status_changed' => false
        ];
        
        try {
            // Obtener información actualizada de TMDB
            $tmdbData = $this->tmdbService->getTvShowDetails($series->tmdb_id);
            
            if (!$tmdbData) {
                return $result;
            }
            
            $originalStatus = $series->status;
            
            // Actualizar información básica
            $updates = [
                'status' => $tmdbData['status'] ?? $series->status,
                'vote_average' => $tmdbData['vote_average'] ?? $series->vote_average,
                'vote_count' => $tmdbData['vote_count'] ?? $series->vote_count,
                'popularity' => $tmdbData['popularity'] ?? $series->popularity,
                'number_of_episodes' => $tmdbData['number_of_episodes'] ?? $series->number_of_episodes,
                'number_of_seasons' => $tmdbData['number_of_seasons'] ?? $series->number_of_seasons,
                'last_air_date' => isset($tmdbData['last_air_date']) ? 
                    Carbon::parse($tmdbData['last_air_date']) : $series->last_air_date,
            ];
            
            // Verificar si hay cambios significativos
            $hasChanges = false;
            foreach ($updates as $key => $value) {
                if ($series->$key != $value) {
                    $hasChanges = true;
                    break;
                }
            }
            
            if ($hasChanges) {
                $series->update($updates);
                $result['updated'] = true;
                
                // Verificar cambio de estado
                if ($originalStatus !== $updates['status']) {
                    $result['status_changed'] = true;
                    $this->newLine();
                    $this->info("📊 Estado cambiado para '{$series->title}': {$originalStatus} → {$updates['status']}");
                }
            }
            
            // Verificar nuevas temporadas
            $newSeasons = $this->checkForNewSeasons($series, $tmdbData);
            $result['new_seasons'] = $newSeasons;
            
            if ($newSeasons > 0) {
                $this->newLine();
                $this->info("🔄 Nuevas temporadas encontradas para '{$series->title}': {$newSeasons}");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Error in updateSeries", [
                'series_id' => $series->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    private function checkForNewSeasons($series, $tmdbData)
    {
        if (!isset($tmdbData['seasons'])) {
            return 0;
        }
        
        $currentSeasons = $series->seasons()->max('season_number') ?: 0;
        $newSeasonsCount = 0;
        
        foreach ($tmdbData['seasons'] as $seasonData) {
            // Ignorar temporada 0 (especiales)
            if ($seasonData['season_number'] == 0) {
                continue;
            }
            
            // Si es una temporada nueva
            if ($seasonData['season_number'] > $currentSeasons) {
                $airDate = $seasonData['air_date'] ?? null;
                
                // Si tiene fecha de emisión futura, agregar a próximos estrenos
                if ($airDate && Carbon::parse($airDate)->isFuture()) {
                    UpcomingSeries::updateOrCreate([
                        'tmdb_id' => $series->tmdb_id,
                        'type' => 'new_season',
                        'season_number' => $seasonData['season_number']
                    ], [
                        'title' => $series->title,
                        'spanish_title' => $series->display_title,
                        'overview' => $seasonData['overview'] ?? $series->overview,
                        'spanish_overview' => $series->display_overview,
                        'poster_path' => $seasonData['poster_path'] ?? $series->poster_path,
                        'backdrop_path' => $series->backdrop_path,
                        'release_date' => $airDate,
                        'episode_count' => $seasonData['episode_count'] ?? null,
                        'vote_average' => $series->vote_average,
                        'popularity' => $series->popularity,
                        'status' => 'upcoming',
                        'existing_series_id' => $series->id
                    ]);
                    
                    $newSeasonsCount++;
                }
                // Si ya se emitió, actualizar la serie para que importe la temporada
                elseif ($airDate && Carbon::parse($airDate)->isPast()) {
                    // Aquí se podría triggear la importación de la nueva temporada
                    $this->info("⚠️  Temporada {$seasonData['season_number']} de '{$series->title}' ya se emitió. Considerar importar episodios.");
                }
            }
        }
        
        return $newSeasonsCount;
    }
    
    private function checkOngoingSeries()
    {
        $this->newLine();
        $this->info("🔍 Verificando series en emisión que podrían haber terminado...");
        
        // Buscar series que dicen estar "en emisión" pero no han tenido actividad reciente
        $possiblyEnded = Series::where('status', 'Returning Series')
            ->where('last_air_date', '<', Carbon::now()->subMonths(6))
            ->whereNotNull('last_air_date')
            ->limit(10)
            ->get();
        
        $checkedCount = 0;
        foreach ($possiblyEnded as $series) {
            try {
                $tmdbData = $this->tmdbService->getTvShowDetails($series->tmdb_id);
                
                if ($tmdbData && isset($tmdbData['status']) && $tmdbData['status'] === 'Ended') {
                    $series->update(['status' => 'Ended']);
                    $this->info("📊 Serie marcada como terminada: {$series->title}");
                    $checkedCount++;
                }
                
                usleep(200000); // 200ms delay
                
            } catch (\Exception $e) {
                Log::error("Error checking ongoing series", [
                    'series_id' => $series->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        if ($checkedCount === 0) {
            $this->info("✅ No se encontraron series que hayan terminado recientemente");
        }
    }
    
    private function clearCache()
    {
        $cacheKeys = [
            'series.popular',
            'series.top_rated',
            'series.recent',
            'hero.featured_candidates',
            'upcoming.widget',
            'upcoming.stats'
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
    
    private function displayResults($updated, $newSeasons, $statusChanges, $errors)
    {
        $this->newLine();
        $this->info("✅ Actualización completada!");
        $this->info("   📺 Series actualizadas: {$updated}");
        $this->info("   🔄 Nuevas temporadas encontradas: {$newSeasons}");
        $this->info("   📊 Cambios de estado: {$statusChanges}");
        
        if ($errors > 0) {
            $this->warn("   ⚠️  Errores encontrados: {$errors}");
        }
        
        // Mostrar estadísticas actuales
        $this->showCurrentStats();
    }
    
    private function showCurrentStats()
    {
        $this->newLine();
        $this->info("📊 Estadísticas actuales:");
        
        $totalSeries = Series::count();
        $ongoingSeries = Series::whereIn('status', ['Returning Series', 'In Production'])->count();
        $endedSeries = Series::where('status', 'Ended')->count();
        $upcomingSeasons = UpcomingSeries::upcoming()->newSeasons()->count();
        
        $this->info("   📺 Total series: {$totalSeries}");
        $this->info("   🔄 En emisión: {$ongoingSeries}");
        $this->info("   🏁 Terminadas: {$endedSeries}");
        $this->info("   ⏭️  Próximas temporadas: {$upcomingSeasons}");
    }
}