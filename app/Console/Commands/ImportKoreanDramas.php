<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Title;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportKoreanDramas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:korean-dramas {--pages=5} {--update} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Korean dramas from TMDB API';

    /**
     * @var TmdbService
     */
    protected $tmdbService;

    /**
     * Create a new command instance.
     */
    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pages = $this->option('pages');
        $update = $this->option('update');
        $force = $this->option('force');

        $this->info("Importing Korean dramas (pages: {$pages}, update: " . ($update ? 'yes' : 'no') . ")");

        // Ensure K-Drama category exists
        $category = Category::firstOrCreate(
            ['slug' => 'k-drama'],
            [
                'name' => 'K-Drama',
                'description' => 'Series de televisión coreanas',
                'language' => 'ko',
                'country' => 'Corea del Sur',
            ]
        );

        $this->info("Category: {$category->name}");

        $totalImported = 0;
        $totalSeasons = 0;
        $totalEpisodes = 0;
        $totalActors = 0;

        for ($page = 1; $page <= $pages; $page++) {
            $this->info("Processing page {$page} of {$pages}...");
            
            // Usamos el enfoque directo que ya comprobamos que funciona
            $apiKey = '69e4c68f7f83d0388b2f66bfaca96337';
            $baseUrl = 'https://api.themoviedb.org/3';
            $url = "{$baseUrl}/discover/tv?" . http_build_query([
                'api_key' => $apiKey,
                'language' => 'es-ES',
                'sort_by' => 'popularity.desc',
                'page' => $page,
                'with_origin_country' => 'KR', 
                'include_adult' => false,
            ]);
            
            // Realizamos la petición HTTP directamente
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode != 200) {
                $this->error("API Error: HTTP {$httpCode}");
                $this->error("Response: " . substr($response, 0, 100) . "...");
                $this->warn("No dramas found on page {$page}. Stopping.");
                break;
            }
            
            $data = json_decode($response, true);
            if (empty($data['results'])) {
                $this->warn("No dramas found on page {$page}. Stopping.");
                break;
            }
            
            // Convertir a Collection para mantener compatibilidad con el resto del código
            $dramas = collect($data['results']);
            
            $this->info("Found " . $dramas->count() . " dramas on page {$page}");
            
            $progressBar = $this->output->createProgressBar($dramas->count());
            $progressBar->start();
            
            foreach ($dramas as $drama) {
                $tmdbId = $drama['id'];
                
                // Check if drama already exists
                $existingTitle = Title::where('tmdb_id', $tmdbId)->first();
                
                if ($existingTitle && !$update && !$force) {
                    $progressBar->advance();
                    continue;
                }
                
                try {
                    // Obtener detalles directamente para evitar problemas con el servicio
                    $apiKey = '69e4c68f7f83d0388b2f66bfaca96337';
                    $baseUrl = 'https://api.themoviedb.org/3';
                    $url = "{$baseUrl}/tv/{$tmdbId}?" . http_build_query([
                        'api_key' => $apiKey,
                        'language' => 'es-ES',
                        'append_to_response' => 'credits,external_ids,images,videos,content_ratings,recommendations,similar,keywords,watch/providers',
                    ]);
                    
                    // Realizar la petición HTTP
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode != 200) {
                        $this->error("Error al obtener detalles para el drama {$tmdbId}: HTTP {$httpCode}");
                        $progressBar->advance();
                        continue;
                    }
                    
                    $details = json_decode($response, true);
                    
                    if (empty($details)) {
                        $progressBar->advance();
                        continue;
                    }
                    
                    // Begin transaction
                    DB::beginTransaction();
                    
                    // Obtener información de plataformas de streaming si está disponible
                    $streamingPlatforms = [];
                    if (!empty($details['watch/providers']) && !empty($details['watch/providers']['results']['ES'])) {
                        $providers = $details['watch/providers']['results']['ES'];
                        
                        // Unir todas las plataformas disponibles
                        foreach (['flatrate', 'rent', 'buy'] as $type) {
                            if (!empty($providers[$type])) {
                                foreach ($providers[$type] as $provider) {
                                    $streamingPlatforms[] = $provider['provider_name'];
                                }
                            }
                        }
                    }
                    
                    // Prepare data for title
                    $titleData = [
                        'tmdb_id' => $tmdbId,
                        'title' => $details['name'],
                        'original_title' => $details['original_name'],
                        'synopsis' => $details['overview'],
                        'type' => 'series',
                        'country' => 'Corea del Sur',
                        'release_year' => substr($details['first_air_date'] ?? '', 0, 4),
                        'category_id' => $category->id,
                        'slug' => Str::slug($details['name']) . '-' . Str::random(5),
                        'featured' => $details['popularity'] > 50,
                        'vote_average' => $details['vote_average'] ?? null,
                        'vote_count' => $details['vote_count'] ?? null,
                        'popularity' => $details['popularity'] ?? null,
                        'streaming_platforms' => !empty($streamingPlatforms) ? implode(', ', array_unique($streamingPlatforms)) : null,
                        'content_rating' => $this->tmdbService->getContentRating($details['content_ratings'] ?? []),
                        'status' => $details['status'],
                        'original_language' => $details['original_language'],
                        'number_of_seasons' => $details['number_of_seasons'],
                        'number_of_episodes' => $details['number_of_episodes'],
                    ];
                    
                    // Download images if available
                    if (!empty($details['poster_path'])) {
                        $titleData['poster'] = $this->tmdbService->downloadImage($details['poster_path'], 'poster');
                    }
                    
                    if (!empty($details['backdrop_path'])) {
                        $titleData['backdrop'] = $this->tmdbService->downloadImage($details['backdrop_path'], 'backdrop');
                    }
                    
                    // Get trailer URL
                    if (!empty($details['videos'])) {
                        $titleData['trailer_url'] = $this->tmdbService->getTrailerUrl($details['videos']);
                    }
                    
                    // Create or update title
                    if ($existingTitle) {
                        $existingTitle->update($titleData);
                        $title = $existingTitle;
                        $this->info("Updated drama: {$title->title}");
                    } else {
                        $title = Title::create($titleData);
                        $this->info("Created new drama: {$title->title}");
                        $totalImported++;
                    }
                    
                    // Sync genres
                    if (!empty($details['genres'])) {
                        $genreIds = [];
                        
                        foreach ($details['genres'] as $genreData) {
                            $genre = Genre::firstOrCreate(
                                ['tmdb_id' => $genreData['id']],
                                [
                                    'name' => $genreData['name'],
                                    'slug' => Str::slug($genreData['name']),
                                ]
                            );
                            
                            $genreIds[] = $genre->id;
                        }
                        
                        $title->genres()->sync($genreIds);
                    }
                    
                    // Import seasons and episodes (only for new or forced update)
                    if (!$existingTitle || $force) {
                        $this->importSeasonsAndEpisodes($title, $details);
                        $totalSeasons += $title->seasons()->count();
                        $totalEpisodes += $title->seasons->sum(function ($season) {
                            return $season->episodes()->count();
                        });
                    }
                    
                    // Import cast (only for new or forced update)
                    if (!$existingTitle || $force) {
                        $this->importCast($title, $details);
                        $totalActors += $title->actors()->count();
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error importing drama {$tmdbId}: " . $e->getMessage());
                    $this->error("Error importing drama {$tmdbId}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->line('');
        }
        
        $this->info("Import completed. Imported {$totalImported} new titles, {$totalSeasons} seasons, {$totalEpisodes} episodes, and {$totalActors} actors/crew.");
    }
    
    /**
     * Import seasons and episodes for a title
     */
    protected function importSeasonsAndEpisodes(Title $title, array $details): void
    {
        $seasons = $details['seasons'] ?? [];
        
        foreach ($seasons as $seasonData) {
            // Skip season 0 (usually specials)
            if ($seasonData['season_number'] === 0) {
                continue;
            }
            
            $season = Season::updateOrCreate(
                [
                    'title_id' => $title->id,
                    'number' => $seasonData['season_number'],
                ],
                [
                    'title' => $seasonData['name'],
                    'overview' => $seasonData['overview'],
                    'air_date' => $seasonData['air_date'],
                    'episode_count' => $seasonData['episode_count'],
                    'tmdb_id' => $seasonData['id'],
                    'poster' => !empty($seasonData['poster_path']) 
                        ? $this->tmdbService->downloadImage($seasonData['poster_path'], 'poster')
                        : null,
                ]
            );
            
            // Get season details
            $seasonDetails = $this->tmdbService->getSeasonDetails($title->tmdb_id, $season->number);
            
            if (!empty($seasonDetails['episodes'])) {
                foreach ($seasonDetails['episodes'] as $episodeData) {
                    Episode::updateOrCreate(
                        [
                            'season_id' => $season->id,
                            'number' => $episodeData['episode_number'],
                        ],
                        [
                            'title' => $episodeData['name'],
                            'synopsis' => $episodeData['overview'],
                            'air_date' => $episodeData['air_date'],
                            'tmdb_id' => $episodeData['id'],
                            'duration' => $episodeData['runtime'] ?? 60,
                            'thumbnail' => !empty($episodeData['still_path']) 
                                ? $this->tmdbService->downloadImage($episodeData['still_path'], 'backdrop')
                                : null,
                        ]
                    );
                }
            }
        }
    }
    
    /**
     * Import cast and crew for a title
     */
    protected function importCast(Title $title, array $details): void
    {
        if (empty($details['credits'])) {
            return;
        }
        
        $cast = $details['credits']['cast'] ?? [];
        $crew = $details['credits']['crew'] ?? [];
        
        // Handle cast (actors)
        $actorData = [];
        
        foreach ($cast as $index => $actorInfo) {
            // Only import top 20 actors
            if ($index >= 20) {
                break;
            }
            
            $person = $this->getOrCreatePerson($actorInfo);
            
            $actorData[$person->id] = [
                'role' => 'actor',
                'character' => $actorInfo['character'],
                'order' => $actorInfo['order'],
            ];
        }
        
        // Handle crew (directors, writers, etc.)
        $crewByDepartment = collect($crew)->groupBy('department');
        
        // Directors
        $directors = $crewByDepartment->get('Directing', collect())->filter(function ($item) {
            return $item['job'] === 'Director';
        });
        
        foreach ($directors->take(5) as $directorInfo) {
            $person = $this->getOrCreatePerson($directorInfo);
            
            $actorData[$person->id] = [
                'role' => 'director',
                'character' => null,
                'order' => 0,
            ];
        }
        
        // Writers
        $writers = $crewByDepartment->get('Writing', collect())->filter(function ($item) {
            return in_array($item['job'], ['Writer', 'Screenplay']);
        });
        
        foreach ($writers->take(5) as $writerInfo) {
            $person = $this->getOrCreatePerson($writerInfo);
            
            $actorData[$person->id] = [
                'role' => 'writer',
                'character' => null,
                'order' => 0,
            ];
        }
        
        // Sync relationships
        $title->people()->sync($actorData);
    }
    
    /**
     * Get or create a person record
     */
    protected function getOrCreatePerson(array $personData): Person
    {
        // Generate a proper slug - ensure it's not empty
        $slug = Str::slug($personData['name']);
        
        // If slug is empty (e.g., for Korean names), use a random string
        if (empty($slug)) {
            $slug = 'person-' . Str::random(10);
        }
        
        $person = Person::firstOrCreate(
            ['tmdb_id' => $personData['id']],
            [
                'name' => $personData['name'],
                'slug' => $slug,
            ]
        );
        
        // Handle a case where the slug might still be empty in database
        if (empty($person->slug)) {
            $person->slug = 'person-' . Str::random(10);
            $person->save();
        }
        
        // If we have a profile path and the person doesn't have a photo yet, download it
        if (!empty($personData['profile_path']) && empty($person->photo)) {
            $photo = $this->tmdbService->downloadImage($personData['profile_path'], 'poster');
            $person->update(['photo' => $photo]);
        }
        
        return $person;
    }
}