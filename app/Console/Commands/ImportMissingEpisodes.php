<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Services\TmdbService;

class ImportMissingEpisodes extends Command
{
    protected $signature = 'tmdb:import-missing-episodes {--limit=50 : Limit number of series to process}';
    protected $description = 'Import episodes for series that do not have episodes';

    private $tmdbService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->tmdbService = app(TmdbService::class);
        
        $limit = $this->option('limit');
        
        // Get series without episodes
        $seriesWithoutEpisodes = Series::whereDoesntHave('episodes')
            ->whereNotNull('tmdb_id')
            ->limit($limit)
            ->get();
        
        if ($seriesWithoutEpisodes->isEmpty()) {
            $this->info("All series already have episodes!");
            return 0;
        }

        $this->info("Found {$seriesWithoutEpisodes->count()} series without episodes. Processing...");
        
        $bar = $this->output->createProgressBar($seriesWithoutEpisodes->count());
        $bar->start();

        $totalSeasons = 0;
        $totalEpisodes = 0;
        $successCount = 0;
        $errorCount = 0;

        foreach ($seriesWithoutEpisodes as $series) {
            try {
                $result = $this->importSeasonsForSeries($series);
                $totalSeasons += $result['seasons'];
                $totalEpisodes += $result['episodes'];
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error with {$series->title}: " . $e->getMessage());
            }
            
            $bar->advance();
            
            // Small delay to avoid rate limiting
            usleep(200000); // 200ms delay
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Import completed!");
        $this->info("   Series processed: {$successCount}");
        $this->info("   Errors: {$errorCount}");
        $this->info("   Total seasons: {$totalSeasons}");
        $this->info("   Total episodes: {$totalEpisodes}");
        
        // Show final statistics
        $totalSeriesWithEpisodes = Series::whereHas('episodes')->count();
        $totalSeries = Series::count();
        $coverage = round(($totalSeriesWithEpisodes / $totalSeries) * 100, 1);
        
        $this->info("📊 Episodes Coverage:");
        $this->info("   Series with episodes: {$totalSeriesWithEpisodes}/{$totalSeries} ({$coverage}%)");

        return 0;
    }

    private function importSeasonsForSeries($series)
    {
        if (!$series->tmdb_id) {
            throw new \Exception("No TMDB ID");
        }

        $seasonsCount = 0;
        $episodesCount = 0;

        // Get series details to get seasons info
        $response = $this->tmdbService->getTvShowDetails($series->tmdb_id);
        
        if (!isset($response['seasons'])) {
            return ['seasons' => 0, 'episodes' => 0];
        }

        foreach ($response['seasons'] as $seasonData) {
            // Skip season 0 (specials) 
            if ($seasonData['season_number'] == 0) {
                continue;
            }

            // Create season
            $season = Season::updateOrCreate([
                'series_id' => $series->id,
                'season_number' => $seasonData['season_number']
            ], [
                'name' => $seasonData['name'] ?? "Temporada {$seasonData['season_number']}",
                'overview' => $seasonData['overview'] ?? null,
                'air_date' => $seasonData['air_date'] ?? null,
                'episode_count' => $seasonData['episode_count'] ?? 0,
                'poster_path' => $seasonData['poster_path'] ?? null,
                'vote_average' => $seasonData['vote_average'] ?? 0,
                'tmdb_id' => $seasonData['id'] ?? null
            ]);

            $seasonsCount++;

            // Import episodes for this season (limit to avoid overload)
            $episodesCount += $this->importEpisodesForSeason($series, $season);
        }

        return ['seasons' => $seasonsCount, 'episodes' => $episodesCount];
    }

    private function importEpisodesForSeason($series, $season)
    {
        $episodesCount = 0;

        try {
            // Get season details with episodes
            $response = $this->tmdbService->getSeasonDetails($series->tmdb_id, $season->season_number);
            
            if (!isset($response['episodes'])) {
                return 0;
            }

            foreach ($response['episodes'] as $episodeData) {
                // Ensure we have basic episode info
                if (!isset($episodeData['episode_number'])) {
                    continue;
                }

                Episode::updateOrCreate([
                    'series_id' => $series->id,
                    'season_id' => $season->id,
                    'episode_number' => $episodeData['episode_number']
                ], [
                    'season_number' => $season->season_number,
                    'name' => $episodeData['name'] ?? "Episodio {$episodeData['episode_number']}",
                    'overview' => $episodeData['overview'] ?? null,
                    'detailed_summary' => $episodeData['overview'] ?? null, // Use overview as detailed summary too
                    'air_date' => $episodeData['air_date'] ?? null,
                    'runtime' => $episodeData['runtime'] ?? null,
                    'still_path' => $episodeData['still_path'] ?? null,
                    'vote_average' => $episodeData['vote_average'] ?? 0,
                    'vote_count' => $episodeData['vote_count'] ?? 0,
                    'tmdb_id' => $episodeData['id'] ?? null,
                    'guest_stars' => $episodeData['guest_stars'] ?? [],
                    'crew' => $episodeData['crew'] ?? []
                ]);

                $episodesCount++;
            }
        } catch (\Exception $e) {
            throw new \Exception("Error importing episodes for season {$season->season_number}: " . $e->getMessage());
        }

        return $episodesCount;
    }
}