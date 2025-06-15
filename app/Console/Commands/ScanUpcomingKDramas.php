<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\UpcomingSeries;
use App\Services\TmdbService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ScanUpcomingKDramas extends Command
{
    protected $signature = 'kdrama:scan-upcoming {--pages=5 : Number of pages to scan} {--days=90 : Days ahead to look for releases}';
    protected $description = 'Scan TMDB for upcoming Korean dramas and new seasons';

    private $tmdbService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->tmdbService = app(TmdbService::class);
        
        $pages = (int) $this->option('pages');
        $daysAhead = (int) $this->option('days');
        
        $this->info("🔍 Escaneando próximos K-Dramas...");
        $this->info("📄 Páginas a revisar: {$pages}");
        $this->info("📅 Días hacia adelante: {$daysAhead}");
        
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays($daysAhead)->format('Y-m-d');
        
        $newSeries = 0;
        $newSeasons = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($pages);
        $bar->start();
        
        for ($page = 1; $page <= $pages; $page++) {
            try {
                // Buscar series que se estrenan en el período
                $response = $this->tmdbService->getUpcomingTvShows($page, $startDate, $endDate);
                
                if (isset($response['results'])) {
                    foreach ($response['results'] as $seriesData) {
                        // Solo procesar series coreanas
                        if ($this->isKoreanSeries($seriesData)) {
                            $result = $this->processUpcomingSeries($seriesData);
                            
                            if ($result['type'] === 'new_series') {
                                $newSeries++;
                            } elseif ($result['type'] === 'new_season') {
                                $newSeasons++;
                            }
                        }
                    }
                }
                
                $bar->advance();
                
                // Delay para evitar rate limiting
                usleep(250000); // 250ms
                
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error en página {$page}: " . $e->getMessage());
                Log::error("Error scanning upcoming K-dramas", [
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $bar->finish();
        $this->newLine();
        
        // Buscar nuevas temporadas de series existentes
        $this->info("🔄 Buscando nuevas temporadas de series existentes...");
        $newSeasonResults = $this->scanForNewSeasons();
        $newSeasons += $newSeasonResults;
        
        // Actualizar series en emisión recientes
        $this->info("🔄 Actualizando series en emisión...");
        $updatedCurrent = $this->updateRecentOngoingSeries();
        
        // Limpiar cache relacionado
        $this->clearUpcomingCache();
        
        // Mostrar resultados
        $this->displayResults($newSeries, $newSeasons, $errors, $updatedCurrent);
        
        return 0;
    }
    
    private function isKoreanSeries($seriesData)
    {
        // Verificar si es una serie coreana
        $originCountry = $seriesData['origin_country'] ?? [];
        $originalLanguage = $seriesData['original_language'] ?? '';
        
        return in_array('KR', $originCountry) || $originalLanguage === 'ko';
    }
    
    private function processUpcomingSeries($seriesData)
    {
        $tmdbId = $seriesData['id'];
        $firstAirDate = $seriesData['first_air_date'] ?? null;
        
        if (!$firstAirDate || Carbon::parse($firstAirDate)->isPast()) {
            return ['type' => 'skipped', 'reason' => 'past_date'];
        }
        
        // Verificar si ya existe en nuestra base de datos
        $existingSeries = Series::where('tmdb_id', $tmdbId)->first();
        
        if ($existingSeries) {
            // Es una serie existente, verificar si hay nueva temporada
            return $this->checkForNewSeason($existingSeries, $seriesData);
        } else {
            // Es una serie completamente nueva
            return $this->createUpcomingSeries($seriesData);
        }
    }
    
    private function checkForNewSeason($existingSeries, $seriesData)
    {
        try {
            // Obtener detalles completos de la serie desde TMDB
            $fullDetails = $this->tmdbService->getTvShowDetails($seriesData['id']);
            
            if (!isset($fullDetails['seasons'])) {
                return ['type' => 'skipped', 'reason' => 'no_seasons_data'];
            }
            
            $latestSeasonNumber = $existingSeries->seasons()->max('season_number') ?: 0;
            $hasNewSeason = false;
            
            foreach ($fullDetails['seasons'] as $seasonData) {
                if ($seasonData['season_number'] > $latestSeasonNumber && 
                    $seasonData['season_number'] > 0) { // Ignorar especiales
                    
                    $airDate = $seasonData['air_date'] ?? null;
                    if ($airDate && Carbon::parse($airDate)->isFuture()) {
                        // Crear entrada de próxima temporada
                        UpcomingSeries::updateOrCreate([
                            'tmdb_id' => $seriesData['id'],
                            'type' => 'new_season'
                        ], [
                            'title' => $seriesData['name'],
                            'spanish_title' => $this->translateTitle($seriesData['name']),
                            'overview' => $seriesData['overview'] ?? '',
                            'spanish_overview' => $this->translateOverview($seriesData['overview'] ?? ''),
                            'poster_path' => $seriesData['poster_path'],
                            'backdrop_path' => $seriesData['backdrop_path'],
                            'release_date' => $airDate,
                            'season_number' => $seasonData['season_number'],
                            'episode_count' => $seasonData['episode_count'] ?? null,
                            'vote_average' => $seriesData['vote_average'] ?? 0,
                            'popularity' => $seriesData['popularity'] ?? 0,
                            'status' => 'upcoming',
                            'existing_series_id' => $existingSeries->id
                        ]);
                        
                        $hasNewSeason = true;
                        break;
                    }
                }
            }
            
            return $hasNewSeason ? 
                ['type' => 'new_season'] : 
                ['type' => 'skipped', 'reason' => 'no_new_seasons'];
                
        } catch (\Exception $e) {
            Log::error("Error checking for new season", [
                'series_id' => $existingSeries->id,
                'tmdb_id' => $seriesData['id'],
                'error' => $e->getMessage()
            ]);
            
            return ['type' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function createUpcomingSeries($seriesData)
    {
        try {
            UpcomingSeries::updateOrCreate([
                'tmdb_id' => $seriesData['id'],
                'type' => 'new_series'
            ], [
                'title' => $seriesData['name'],
                'spanish_title' => $this->translateTitle($seriesData['name']),
                'overview' => $seriesData['overview'] ?? '',
                'spanish_overview' => $this->translateOverview($seriesData['overview'] ?? ''),
                'poster_path' => $seriesData['poster_path'],
                'backdrop_path' => $seriesData['backdrop_path'],
                'release_date' => $seriesData['first_air_date'],
                'season_number' => 1,
                'vote_average' => $seriesData['vote_average'] ?? 0,
                'popularity' => $seriesData['popularity'] ?? 0,
                'status' => 'upcoming',
                'existing_series_id' => null
            ]);
            
            return ['type' => 'new_series'];
            
        } catch (\Exception $e) {
            Log::error("Error creating upcoming series", [
                'tmdb_id' => $seriesData['id'],
                'error' => $e->getMessage()
            ]);
            
            return ['type' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function scanForNewSeasons()
    {
        // Obtener series populares existentes para verificar nuevas temporadas
        $existingSeries = Series::where('status', '!=', 'Ended')
            ->whereNotNull('tmdb_id')
            ->orderBy('popularity', 'desc')
            ->limit(50) // Limitar para evitar sobrecarga
            ->get();
        
        $newSeasons = 0;
        $bar = $this->output->createProgressBar($existingSeries->count());
        $bar->start();
        
        foreach ($existingSeries as $series) {
            try {
                $result = $this->checkExistingSeriesForNewSeasons($series);
                if ($result) {
                    $newSeasons++;
                }
                
                $bar->advance();
                usleep(200000); // 200ms delay
                
            } catch (\Exception $e) {
                Log::error("Error checking existing series for new seasons", [
                    'series_id' => $series->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $bar->finish();
        $this->newLine();
        
        return $newSeasons;
    }
    
    private function checkExistingSeriesForNewSeasons($series)
    {
        try {
            $fullDetails = $this->tmdbService->getTvShowDetails($series->tmdb_id);
            
            if (!isset($fullDetails['seasons'])) {
                return false;
            }
            
            $latestSeasonNumber = $series->seasons()->max('season_number') ?: 0;
            
            foreach ($fullDetails['seasons'] as $seasonData) {
                if ($seasonData['season_number'] > $latestSeasonNumber && 
                    $seasonData['season_number'] > 0) {
                    
                    $airDate = $seasonData['air_date'] ?? null;
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
                        
                        return true;
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Error in checkExistingSeriesForNewSeasons", [
                'series_id' => $series->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function translateTitle($title)
    {
        // Mapeo básico de títulos conocidos
        $titleMappings = [
            'Kingdom' => 'Kingdom',
            'Squid Game' => 'El Juego del Calamar',
            'Crash Landing on You' => 'Aterrizaje de Emergencia en tu Corazón',
            'Goblin' => 'Goblin',
            'Descendants of the Sun' => 'Descendientes del Sol',
            // Agregar más mapeos según sea necesario
        ];
        
        return $titleMappings[$title] ?? $title;
    }
    
    private function translateOverview($overview)
    {
        // Por ahora mantener el overview original
        // En el futuro se puede integrar un servicio de traducción
        return $overview;
    }
    
    private function clearUpcomingCache()
    {
        $cacheKeys = [
            'upcoming.new_series',
            'upcoming.new_seasons',
            'upcoming.all',
            'upcoming.this_month',
            'upcoming.next_month'
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
    
    private function updateRecentOngoingSeries()
    {
        // Actualizar series en emisión que han sido populares recientemente
        $ongoingSeries = Series::whereIn('status', ['Returning Series', 'In Production'])
            ->orderBy('popularity', 'desc')
            ->limit(20) // Limitar para evitar sobrecarga
            ->get();
        
        $updated = 0;
        $bar = $this->output->createProgressBar($ongoingSeries->count());
        $bar->start();
        
        foreach ($ongoingSeries as $series) {
            try {
                $tmdbData = $this->tmdbService->getTvShowDetails($series->tmdb_id);
                
                if ($tmdbData) {
                    $hasUpdates = false;
                    $updates = [];
                    
                    // Verificar cambios importantes
                    if (isset($tmdbData['status']) && $tmdbData['status'] !== $series->status) {
                        $updates['status'] = $tmdbData['status'];
                        $hasUpdates = true;
                    }
                    
                    if (isset($tmdbData['number_of_episodes']) && $tmdbData['number_of_episodes'] > $series->number_of_episodes) {
                        $updates['number_of_episodes'] = $tmdbData['number_of_episodes'];
                        $hasUpdates = true;
                    }
                    
                    if (isset($tmdbData['last_air_date'])) {
                        $newLastAirDate = Carbon::parse($tmdbData['last_air_date']);
                        if (!$series->last_air_date || $newLastAirDate->gt($series->last_air_date)) {
                            $updates['last_air_date'] = $newLastAirDate;
                            $hasUpdates = true;
                        }
                    }
                    
                    if ($hasUpdates) {
                        $series->update($updates);
                        $updated++;
                    }
                }
                
                $bar->advance();
                usleep(200000); // 200ms delay
                
            } catch (\Exception $e) {
                Log::error("Error updating ongoing series", [
                    'series_id' => $series->id,
                    'error' => $e->getMessage()
                ]);
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->newLine();
        
        return $updated;
    }

    private function displayResults($newSeries, $newSeasons, $errors, $updatedCurrent = 0)
    {
        $this->newLine();
        $this->info("✅ Escaneo completado!");
        $this->info("   📺 Nuevas series encontradas: {$newSeries}");
        $this->info("   🔄 Nuevas temporadas encontradas: {$newSeasons}");
        $this->info("   📊 Series actualizadas: {$updatedCurrent}");
        
        if ($errors > 0) {
            $this->warn("   ⚠️  Errores encontrados: {$errors}");
        }
        
        // Mostrar próximos estrenos por fecha
        $this->showUpcomingByDate();
        
        // Mostrar estadísticas de series en emisión
        $this->showOngoingStats();
    }
    
    private function showOngoingStats()
    {
        $this->newLine();
        $this->info("📊 Series en emisión:");
        
        $ongoing = Series::whereIn('status', ['Returning Series', 'In Production'])->count();
        $recentlyAired = Series::where('last_air_date', '>=', Carbon::now()->subDays(30))->count();
        
        $this->info("   🔄 Total en emisión: {$ongoing}");
        $this->info("   📅 Con episodios recientes: {$recentlyAired}");
    }
    
    private function showUpcomingByDate()
    {
        $upcoming = UpcomingSeries::where('status', 'upcoming')
            ->where('release_date', '>=', Carbon::now())
            ->orderBy('release_date')
            ->limit(10)
            ->get();
        
        if ($upcoming->count() > 0) {
            $this->newLine();
            $this->info("📅 Próximos estrenos:");
            
            foreach ($upcoming as $item) {
                $date = Carbon::parse($item->release_date)->format('d/m/Y');
                $type = $item->type === 'new_series' ? '🆕 Nueva serie' : '🔄 Nueva temporada';
                $season = $item->season_number ? " (Temporada {$item->season_number})" : '';
                
                $this->line("   {$date} - {$type}: {$item->spanish_title}{$season}");
            }
        }
    }
}