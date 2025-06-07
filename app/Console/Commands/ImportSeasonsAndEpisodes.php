<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Services\TmdbService;

class ImportSeasonsAndEpisodes extends Command
{
    protected $signature = 'tmdb:import-seasons {--series=all : Series ID or "all" for all series}';
    protected $description = 'Import seasons and episodes from TMDB for series';

    private $tmdbService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->tmdbService = app(TmdbService::class);
        
        $seriesOption = $this->option('series');
        
        if ($seriesOption === 'all') {
            $series = Series::whereNotNull('tmdb_id')->get();
            $this->info("Importing seasons and episodes for all series ({$series->count()} series)...");
        } else {
            $series = Series::where('id', $seriesOption)->orWhere('tmdb_id', $seriesOption)->get();
            if ($series->isEmpty()) {
                $this->error("Series not found!");
                return 1;
            }
        }

        $bar = $this->output->createProgressBar($series->count());
        $bar->start();

        $totalSeasons = 0;
        $totalEpisodes = 0;

        foreach ($series as $serie) {
            $result = $this->importSeasonsForSeries($serie);
            $totalSeasons += $result['seasons'];
            $totalEpisodes += $result['episodes'];
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Import completed! Seasons: {$totalSeasons}, Episodes: {$totalEpisodes}");

        return 0;
    }

    private function importSeasonsForSeries($series)
    {
        if (!$series->tmdb_id) {
            return ['seasons' => 0, 'episodes' => 0];
        }

        $seasonsCount = 0;
        $episodesCount = 0;

        try {
            // Get series details to get seasons info
            $response = $this->tmdbService->getTvShowDetails($series->tmdb_id);
            
            if (!isset($response['seasons'])) {
                return ['seasons' => 0, 'episodes' => 0];
            }

            foreach ($response['seasons'] as $seasonData) {
                // Skip season 0 (specials) for simplicity
                if ($seasonData['season_number'] == 0) {
                    continue;
                }

                // Check if season already exists
                $existingSeason = Season::where('series_id', $series->id)
                    ->where('season_number', $seasonData['season_number'])
                    ->first();

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

                if (!$existingSeason) {
                    $seasonsCount++;
                }

                // Import episodes for this season
                $episodesCount += $this->importEpisodesForSeason($series, $season);
            }

        } catch (\Exception $e) {
            $this->error("Error importing seasons for {$series->title}: " . $e->getMessage());
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
                // Check if episode already exists
                $existingEpisode = Episode::where('season_id', $season->id)
                    ->where('episode_number', $episodeData['episode_number'])
                    ->first();

                Episode::updateOrCreate([
                    'series_id' => $series->id,
                    'season_id' => $season->id,
                    'episode_number' => $episodeData['episode_number']
                ], [
                    'season_number' => $season->season_number,
                    'name' => $episodeData['name'] ?? "Episodio {$episodeData['episode_number']}",
                    'overview' => $episodeData['overview'] ?? null,
                    'air_date' => $episodeData['air_date'] ?? null,
                    'runtime' => $episodeData['runtime'] ?? null,
                    'still_path' => $episodeData['still_path'] ?? null,
                    'vote_average' => $episodeData['vote_average'] ?? 0,
                    'vote_count' => $episodeData['vote_count'] ?? 0,
                    'tmdb_id' => $episodeData['id'] ?? null
                ]);

                if (!$existingEpisode) {
                    $episodesCount++;
                }
            }

        } catch (\Exception $e) {
            $this->error("Error importing episodes for season {$season->season_number}: " . $e->getMessage());
        }

        return $episodesCount;
    }
}
