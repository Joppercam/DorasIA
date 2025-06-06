<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Genre;
use App\Models\Image;
use App\Models\Soundtrack;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportKoreanDramas extends Command
{
    protected $signature = 'import:korean-dramas {--pages=5 : Number of pages to import} {--with-details : Import full details including episodes}';
    protected $description = 'Import Korean dramas from TMDB with complete information';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
    }

    public function handle()
    {
        $this->info('🎭 Starting Korean Drama import from TMDB...');
        
        $pages = (int) $this->option('pages');
        $withDetails = $this->option('with-details');
        
        // First, import genres
        $this->importGenres();
        
        $totalImported = 0;
        
        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Processing page {$page} of {$pages}...");
            
            // Get popular Korean dramas
            $dramas = $this->tmdb->getPopularKoreanDramas($page);
            
            if (empty($dramas['results'])) {
                $this->warn("No results found for page {$page}");
                continue;
            }
            
            foreach ($dramas['results'] as $dramaData) {
                try {
                    $series = $this->importSeries($dramaData);
                    
                    if ($series && $withDetails) {
                        $this->importSeriesDetails($series);
                    }
                    
                    $totalImported++;
                    $this->line("✅ Imported: {$dramaData['name']}");
                    
                } catch (\Exception $e) {
                    $this->error("❌ Failed to import {$dramaData['name']}: " . $e->getMessage());
                    Log::error('Drama import failed', [
                        'drama' => $dramaData['name'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Small delay to be respectful to API
            usleep(200000); // 0.2 seconds
        }
        
        $this->info("🎉 Import completed! Total imported: {$totalImported} dramas");
        
        return Command::SUCCESS;
    }

    private function importGenres(): void
    {
        $this->info('📚 Importing genres...');
        
        $genresData = $this->tmdb->getTvGenres();
        
        if (empty($genresData['genres'])) {
            $this->warn('No genres found');
            return;
        }
        
        foreach ($genresData['genres'] as $genreData) {
            Genre::updateOrCreate(
                ['tmdb_id' => $genreData['id']],
                ['name' => $genreData['name']]
            );
        }
        
        $this->info('✅ Genres imported successfully');
    }

    private function importSeries(array $dramaData): ?Series
    {
        // Check if already exists
        $existingSeries = Series::where('tmdb_id', $dramaData['id'])->first();
        if ($existingSeries) {
            return $existingSeries;
        }
        
        $series = Series::create([
            'title' => $dramaData['name'],
            'original_title' => $dramaData['original_name'] ?? $dramaData['name'],
            'overview' => $dramaData['overview'] ?? null,
            'first_air_date' => $dramaData['first_air_date'] ?? null,
            'vote_average' => $dramaData['vote_average'] ?? 0,
            'vote_count' => $dramaData['vote_count'] ?? 0,
            'popularity' => $dramaData['popularity'] ?? 0,
            'poster_path' => $dramaData['poster_path'] ?? null,
            'backdrop_path' => $dramaData['backdrop_path'] ?? null,
            'original_language' => $dramaData['original_language'] ?? 'ko',
            'origin_country' => is_array($dramaData['origin_country'] ?? []) ? 
                implode(',', $dramaData['origin_country']) : 'KR',
            'tmdb_id' => $dramaData['id'],
            'is_korean_drama' => true
        ]);
        
        // Attach genres
        if (!empty($dramaData['genre_ids'])) {
            $genreIds = Genre::whereIn('tmdb_id', $dramaData['genre_ids'])->pluck('id');
            $series->genres()->attach($genreIds);
        }
        
        return $series;
    }

    private function importSeriesDetails(Series $series): void
    {
        $this->line("🔍 Getting detailed info for: {$series->title}");
        
        $details = $this->tmdb->getTvShowDetails($series->tmdb_id);
        
        if (empty($details)) {
            return;
        }
        
        // Update series with detailed information
        $series->update([
            'synopsis' => $details['overview'] ?? $series->overview,
            'status' => $details['status'] ?? null,
            'last_air_date' => $details['last_air_date'] ?? null,
            'number_of_seasons' => $details['number_of_seasons'] ?? 0,
            'number_of_episodes' => $details['number_of_episodes'] ?? 0,
            'episode_run_time' => !empty($details['episode_run_time']) ? 
                $details['episode_run_time'][0] : null,
            'homepage' => $details['homepage'] ?? null,
            'in_production' => $details['in_production'] ?? false,
            'production_companies' => $details['production_companies'] ?? null,
            'production_countries' => $details['production_countries'] ?? null,
            'spoken_languages' => $details['spoken_languages'] ?? null,
            'networks' => $details['networks'] ?? null,
            'tagline' => $details['tagline'] ?? null,
            'type' => $details['type'] ?? null,
            'imdb_id' => $details['external_ids']['imdb_id'] ?? null
        ]);
        
        // Import cast and crew
        $this->importCastAndCrew($series, $details);
        
        // Import seasons and episodes
        $this->importSeasons($series, $details);
        
        // Import images
        $this->importImages($series, $details);
        
        // Import videos/soundtracks
        $this->importVideos($series, $details);
    }

    private function importCastAndCrew(Series $series, array $details): void
    {
        if (empty($details['credits'])) {
            return;
        }
        
        // Import cast
        if (!empty($details['credits']['cast'])) {
            foreach (array_slice($details['credits']['cast'], 0, 20) as $castMember) {
                $person = $this->importPerson($castMember);
                
                if ($person) {
                    $series->people()->syncWithoutDetaching([
                        $person->id => [
                            'role' => 'actor',
                            'character' => $castMember['character'] ?? null,
                            'order' => $castMember['order'] ?? null,
                            'department' => 'Acting',
                            'job' => 'Actor'
                        ]
                    ]);
                }
            }
        }
        
        // Import crew
        if (!empty($details['credits']['crew'])) {
            foreach (array_slice($details['credits']['crew'], 0, 10) as $crewMember) {
                $person = $this->importPerson($crewMember);
                
                if ($person) {
                    $role = strtolower($crewMember['job'] ?? 'crew');
                    
                    $series->people()->syncWithoutDetaching([
                        $person->id => [
                            'role' => $role,
                            'character' => null,
                            'order' => null,
                            'department' => $crewMember['department'] ?? null,
                            'job' => $crewMember['job'] ?? null
                        ]
                    ]);
                }
            }
        }
    }

    private function importPerson(array $personData): ?Person
    {
        $person = Person::updateOrCreate(
            ['tmdb_id' => $personData['id']],
            [
                'name' => $personData['name'],
                'known_for_department' => $personData['known_for_department'] ?? null,
                'profile_path' => $personData['profile_path'] ?? null,
                'popularity' => $personData['popularity'] ?? 0,
                'adult' => $personData['adult'] ?? false,
                'gender' => $personData['gender'] ?? null
            ]
        );
        
        // Get detailed person info if needed
        if (!$person->biography) {
            $personDetails = $this->tmdb->getPersonDetails($person->tmdb_id);
            
            if (!empty($personDetails)) {
                $person->update([
                    'biography' => $personDetails['biography'] ?? null,
                    'birthday' => $personDetails['birthday'] ?? null,
                    'deathday' => $personDetails['deathday'] ?? null,
                    'place_of_birth' => $personDetails['place_of_birth'] ?? null,
                    'homepage' => $personDetails['homepage'] ?? null,
                    'also_known_as' => $personDetails['also_known_as'] ?? null,
                    'imdb_id' => $personDetails['external_ids']['imdb_id'] ?? null
                ]);
            }
        }
        
        return $person;
    }

    private function importSeasons(Series $series, array $details): void
    {
        if (empty($details['seasons'])) {
            return;
        }
        
        foreach ($details['seasons'] as $seasonData) {
            $season = Season::updateOrCreate(
                [
                    'series_id' => $series->id,
                    'season_number' => $seasonData['season_number']
                ],
                [
                    'name' => $seasonData['name'] ?? "Season {$seasonData['season_number']}",
                    'overview' => $seasonData['overview'] ?? null,
                    'air_date' => $seasonData['air_date'] ?? null,
                    'episode_count' => $seasonData['episode_count'] ?? 0,
                    'poster_path' => $seasonData['poster_path'] ?? null,
                    'vote_average' => $seasonData['vote_average'] ?? 0,
                    'tmdb_id' => $seasonData['id'] ?? null
                ]
            );
            
            // Import episodes for this season
            $this->importEpisodes($series, $season);
        }
    }

    private function importEpisodes(Series $series, Season $season): void
    {
        $seasonDetails = $this->tmdb->getSeasonDetails(
            $series->tmdb_id, 
            $season->season_number
        );
        
        if (empty($seasonDetails['episodes'])) {
            return;
        }
        
        foreach ($seasonDetails['episodes'] as $episodeData) {
            Episode::updateOrCreate(
                [
                    'series_id' => $series->id,
                    'season_id' => $season->id,
                    'episode_number' => $episodeData['episode_number'],
                    'season_number' => $season->season_number
                ],
                [
                    'name' => $episodeData['name'],
                    'overview' => $episodeData['overview'] ?? null,
                    'air_date' => $episodeData['air_date'] ?? null,
                    'runtime' => $episodeData['runtime'] ?? null,
                    'still_path' => $episodeData['still_path'] ?? null,
                    'vote_average' => $episodeData['vote_average'] ?? 0,
                    'vote_count' => $episodeData['vote_count'] ?? 0,
                    'tmdb_id' => $episodeData['id'] ?? null,
                    'guest_stars' => $episodeData['guest_stars'] ?? null,
                    'crew' => $episodeData['crew'] ?? null
                ]
            );
        }
    }

    private function importImages(Series $series, array $details): void
    {
        if (empty($details['images'])) {
            return;
        }
        
        // Import posters
        if (!empty($details['images']['posters'])) {
            foreach (array_slice($details['images']['posters'], 0, 5) as $posterData) {
                $series->images()->updateOrCreate(
                    [
                        'file_path' => $posterData['file_path'],
                        'type' => 'poster'
                    ],
                    [
                        'width' => $posterData['width'] ?? null,
                        'height' => $posterData['height'] ?? null,
                        'aspect_ratio' => $posterData['aspect_ratio'] ?? null,
                        'vote_average' => $posterData['vote_average'] ?? 0,
                        'vote_count' => $posterData['vote_count'] ?? 0,
                        'iso_639_1' => $posterData['iso_639_1'] ?? null
                    ]
                );
            }
        }
        
        // Import backdrops
        if (!empty($details['images']['backdrops'])) {
            foreach (array_slice($details['images']['backdrops'], 0, 5) as $backdropData) {
                $series->images()->updateOrCreate(
                    [
                        'file_path' => $backdropData['file_path'],
                        'type' => 'backdrop'
                    ],
                    [
                        'width' => $backdropData['width'] ?? null,
                        'height' => $backdropData['height'] ?? null,
                        'aspect_ratio' => $backdropData['aspect_ratio'] ?? null,
                        'vote_average' => $backdropData['vote_average'] ?? 0,
                        'vote_count' => $backdropData['vote_count'] ?? 0,
                        'iso_639_1' => $backdropData['iso_639_1'] ?? null
                    ]
                );
            }
        }
    }

    private function importVideos(Series $series, array $details): void
    {
        if (empty($details['videos']['results'])) {
            return;
        }
        
        foreach ($details['videos']['results'] as $videoData) {
            if ($videoData['type'] === 'Trailer' || $videoData['type'] === 'Teaser') {
                // This could be stored as soundtracks or a separate videos table
                // For now, we'll skip video storage and focus on actual soundtrack data
                continue;
            }
        }
    }
}