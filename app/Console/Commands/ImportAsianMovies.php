<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Title;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportAsianMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:asian-movies {--pages=5} {--country=KR} {--update} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asian movies from TMDB API (KR=Korea, JP=Japan, CN=China, TW=Taiwan)';

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
        $country = $this->option('country');
        $update = $this->option('update');
        $force = $this->option('force');

        // Validate country code
        $validCountries = ['KR', 'JP', 'CN', 'TW', 'HK', 'TH'];
        if (!in_array($country, $validCountries)) {
            $this->error("Invalid country code. Must be one of: " . implode(', ', $validCountries));
            return 1;
        }

        $this->info("Importing {$this->getCountryName($country)} movies (pages: {$pages}, update: " . ($update ? 'yes' : 'no') . ")");

        // Ensure Movies category exists
        $category = Category::firstOrCreate(
            ['slug' => 'peliculas'],
            [
                'name' => 'Películas',
                'description' => 'Películas asiáticas',
                'country' => 'Asia',
            ]
        );

        $this->info("Category: {$category->name}");

        $totalImported = 0;
        $totalActors = 0;

        for ($page = 1; $page <= $pages; $page++) {
            $this->info("Processing page {$page} of {$pages}...");
            
            // Use direct curl approach as we did with Korean dramas
            try {
                $apiKey = $this->tmdbService->apiKey;
                $baseUrl = 'https://api.themoviedb.org/3';
                $url = "{$baseUrl}/discover/movie?" . http_build_query([
                    'api_key' => $apiKey,
                    'language' => $this->tmdbService->language,
                    'sort_by' => 'popularity.desc',
                    'page' => $page,
                    'with_origin_country' => $country,
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
                    continue;
                }
                
                $data = json_decode($response, true);
                $movies = collect($data['results'] ?? []);
            } catch (\Exception $e) {
                $this->error("API Error: " . $e->getMessage());
                continue;
            }
            
            if ($movies->isEmpty()) {
                $this->warn("No movies found on page {$page}. Stopping.");
                break;
            }
            
            $this->info("Found " . $movies->count() . " movies on page {$page}");
            
            $progressBar = $this->output->createProgressBar($movies->count());
            $progressBar->start();
            
            foreach ($movies as $movie) {
                $tmdbId = $movie['id'];
                
                // Check if movie already exists
                $existingTitle = Title::where('tmdb_id', $tmdbId)->first();
                
                if ($existingTitle && !$update && !$force) {
                    $progressBar->advance();
                    continue;
                }
                
                try {
                    // Get detailed information using direct curl
                    $apiKey = $this->tmdbService->apiKey;
                    $baseUrl = 'https://api.themoviedb.org/3';
                    $url = "{$baseUrl}/movie/{$tmdbId}?" . http_build_query([
                        'api_key' => $apiKey,
                        'language' => $this->tmdbService->language,
                        'append_to_response' => 'credits,external_ids,images,videos,release_dates,recommendations,similar,keywords,watch/providers',
                    ]);
                    
                    // Realizar la petición HTTP
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode != 200) {
                        $this->error("Error al obtener detalles para la película {$tmdbId}: HTTP {$httpCode}");
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
                    
                    // Determine country name
                    $countryName = $this->getCountryName($country);
                    
                    // Prepare data for title
                    $titleData = [
                        'tmdb_id' => $tmdbId,
                        'title' => $details['title'],
                        'original_title' => $details['original_title'],
                        'synopsis' => $details['overview'],
                        'type' => 'movie',
                        'country' => $countryName,
                        'release_year' => substr($details['release_date'] ?? '', 0, 4),
                        'duration' => $details['runtime'],
                        'category_id' => $category->id,
                        'slug' => Str::slug($details['title']) . '-' . Str::random(5),
                        'featured' => $details['popularity'] > 50,
                        'content_rating' => isset($details['release_dates']) ? $this->getContentRatingFromMovie($details['release_dates']) : '',
                        'status' => $details['status'],
                        'original_language' => $details['original_language'],
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
                        $this->info("Updated movie: {$title->title}");
                    } else {
                        $title = Title::create($titleData);
                        $this->info("Created new movie: {$title->title}");
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
                    
                    // Import cast (only for new or forced update)
                    if (!$existingTitle || $force) {
                        $this->importCast($title, $details);
                        $totalActors += $title->actors()->count();
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error importing movie {$tmdbId}: " . $e->getMessage());
                    $this->error("Error importing movie {$tmdbId}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->line('');
        }
        
        $this->info("Import completed. Imported {$totalImported} new movies and {$totalActors} actors/crew.");
    }
    
    /**
     * Get content rating from movie release dates
     */
    protected function getContentRatingFromMovie(array $releaseDates): string
    {
        if (empty($releaseDates['results'])) {
            return '';
        }

        // Look for the rating from specific countries (KR, JP, US)
        $priorityCountries = ['KR', 'JP', 'CN', 'US'];
        
        foreach ($priorityCountries as $country) {
            $countryData = collect($releaseDates['results'])->firstWhere('iso_3166_1', $country);
            if ($countryData && !empty($countryData['release_dates'])) {
                foreach ($countryData['release_dates'] as $releaseDate) {
                    if (!empty($releaseDate['certification'])) {
                        return $releaseDate['certification'];
                    }
                }
            }
        }

        // If no priority country rating found, return the first available
        foreach ($releaseDates['results'] as $countryData) {
            if (!empty($countryData['release_dates'])) {
                foreach ($countryData['release_dates'] as $releaseDate) {
                    if (!empty($releaseDate['certification'])) {
                        return $releaseDate['certification'];
                    }
                }
            }
        }

        return '';
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
    
    /**
     * Get country name from ISO code
     */
    protected function getCountryName(string $countryCode): string
    {
        $countries = [
            'KR' => 'Corea del Sur',
            'JP' => 'Japón',
            'CN' => 'China',
            'TW' => 'Taiwán',
            'HK' => 'Hong Kong',
            'TH' => 'Tailandia',
        ];
        
        return $countries[$countryCode] ?? 'Asia';
    }
}