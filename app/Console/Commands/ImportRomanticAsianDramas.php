<?php

namespace App\Console\Commands;

use App\Models\Title;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Category;
use App\Services\TmdbService;
use App\Traits\PersonImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportRomanticAsianDramas extends Command
{
    use PersonImporter;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:import-romantic-dramas {--country=all : Country to import from (all, kr, jp, cn, th)}
                            {--pages=1 : Number of pages to import}
                            {--page=1 : Specific page to start from when importing multiple pages}
                            {--subgenre= : Specific romantic subgenre to import}
                            {--skip-existing : Skip titles that already exist in the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import romantic Asian dramas from TMDB';

    /**
     * The TMDB service instance.
     *
     * @var \App\Services\TmdbService
     */
    protected $tmdbService;

    /**
     * Genre mappings.
     *
     * @var array
     */
    protected $genreMappings = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $country = $this->option('country');
        $pages = (int) $this->option('pages');
        $startPage = (int) $this->option('page');
        $subgenre = $this->option('subgenre');

        $this->info("Importing romantic Asian dramas from TMDB...");
        $this->info("Country: " . $country);
        $this->info("Pages: " . $pages);
        
        if ($startPage > 1) {
            $this->info("Starting from page: " . $startPage);
        }
        
        if (!empty($subgenre)) {
            $this->info("Filtering by subgenre: " . $subgenre);
        }

        // Load genre mappings
        $this->loadGenreMappings();

        // Create a progress bar
        $progressBar = $this->output->createProgressBar($pages);
        $progressBar->start();

        // Import dramas based on country selection
        $importCount = 0;
        
        for ($page = $startPage; $page < $startPage + $pages; $page++) {
            $dramas = $this->fetchDramasByCountry($country, $page);
            
            // Filter by subgenre if specified
            if (!empty($subgenre)) {
                $dramas = $this->filterByRomanticSubgenre($dramas, $subgenre);
            }
            
            foreach ($dramas as $drama) {
                if ($this->importDrama($drama)) {
                    $importCount++;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("Imported $importCount romantic Asian dramas successfully!");

        return Command::SUCCESS;
    }

    /**
     * Fetch dramas based on country selection.
     *
     * @param string $country
     * @param int $page
     * @return \Illuminate\Support\Collection
     */
    protected function fetchDramasByCountry(string $country, int $page)
    {
        switch ($country) {
            case 'kr':
                $this->line("Fetching Korean romantic dramas (page $page)...");
                return $this->tmdbService->getKoreanRomanticDramas($page);
            
            case 'jp':
                $this->line("Fetching Japanese romantic dramas (page $page)...");
                return $this->tmdbService->getJapaneseRomanticDramas($page);
            
            case 'cn':
                $this->line("Fetching Chinese romantic dramas (page $page)...");
                return $this->tmdbService->getChineseRomanticDramas($page);
            
            case 'all':
            default:
                $this->line("Fetching all Asian romantic dramas (page $page)...");
                return $this->tmdbService->getRomanticAsianDramas($page);
        }
    }

    /**
     * Filter dramas by romantic subgenre.
     *
     * @param \Illuminate\Support\Collection $dramas
     * @param string $subgenre
     * @return \Illuminate\Support\Collection
     */
    protected function filterByRomanticSubgenre($dramas, string $subgenre)
    {
        // For enhanced filtering, we need to fetch the full details of each drama
        return $dramas->filter(function ($drama) use ($subgenre) {
            // Get full details for better categorization
            $details = $this->tmdbService->getShowDetails($drama['id']);
            $detectedSubgenre = $this->tmdbService->categorizeRomanticSubgenre($details);
            
            return $detectedSubgenre === $subgenre;
        });
    }

    /**
     * Import a drama into the database.
     *
     * @param array $drama
     * @return bool
     */
    protected function importDrama($drama)
    {
        // Skip if the drama already exists
        if (Title::where('tmdb_id', $drama['id'])->exists()) {
            if ($this->option('skip-existing')) {
                $this->line("Skipping existing title: {$drama['name']} (ID: {$drama['id']})");
            }
            return false;
        }

        try {
            DB::beginTransaction();

            // Get detailed information about the drama
            try {
                $dramaDetails = $this->tmdbService->getShowDetails($drama['id']);
                if (empty($dramaDetails)) {
                    $this->error("Failed to get details for drama ID: {$drama['id']}");
                    DB::rollBack();
                    return false;
                }
                // Log para depuración
                $this->info("Successfully retrieved details for: {$drama['name']}");
            } catch (\Exception $e) {
                $this->error("Exception getting drama details: " . $e->getMessage());
                Log::error("TMDB Details Error: " . $e->getMessage(), ['drama_id' => $drama['id']]);
                DB::rollBack();
                return false;
            }

            // Determine the category based on origin country
            $categoryId = $this->getCategoryIdByOriginCountry($dramaDetails['origin_country'] ?? []);
            
            // Determine the romantic subgenre
            $subgenre = $this->tmdbService->categorizeRomanticSubgenre($dramaDetails);

            // Create the drama entry
            $title = new Title();
            $title->title = $drama['name'];
            $title->original_title = $drama['original_name'];
            
            // Generate a slug - handle non-Latin characters
            $slug = Str::slug($drama['name']);
            // If the slug is empty (because it contains only non-Latin characters)
            if (empty($slug)) {
                // Try using the ID as a fallback
                $slug = 'drama-' . $drama['id'];
                $this->info("Generated slug from ID for {$drama['name']}: {$slug}");
            }
            $title->slug = $slug;
            
            $title->description = $drama['overview'];
            $title->release_date = $drama['first_air_date'] ?? null;
            $title->type = 'series';
            $title->tmdb_id = $drama['id'];
            $title->poster_path = $drama['poster_path'] ? $this->tmdbService->downloadImage($drama['poster_path'], 'poster') : null;
            $title->backdrop_path = $drama['backdrop_path'] ? $this->tmdbService->downloadImage($drama['backdrop_path'], 'backdrop') : null;
            $title->vote_average = $drama['vote_average'] ?? 0;
            $title->vote_count = $drama['vote_count'] ?? 0;
            $title->popularity = $drama['popularity'] ?? 0;
            $title->category_id = $categoryId;
            $title->runtime = $dramaDetails['episode_run_time'][0] ?? 60;
            $title->status = $dramaDetails['status'] ?? 'Released';
            $title->is_featured = false;
            $title->is_trending = $drama['popularity'] > 50; // Set trending if popular
            $title->metadata = [
                'romantic_subgenre' => $subgenre,
                'languages' => $dramaDetails['languages'] ?? [],
                'origin_countries' => $dramaDetails['origin_country'] ?? [],
                'trailer_url' => $this->tmdbService->getTrailerUrl($dramaDetails['videos'] ?? []),
                'content_rating' => $this->tmdbService->getContentRating($dramaDetails['content_ratings'] ?? []),
                'genres' => collect($dramaDetails['genres'] ?? [])->pluck('name')->toArray(),
                'networks' => collect($dramaDetails['networks'] ?? [])->pluck('name')->toArray(),
                'keywords' => collect($dramaDetails['keywords']['results'] ?? [])->pluck('name')->toArray(),
            ];
            
            // Set streaming platforms based on metadata
            $streamingPlatforms = [];
            if (!empty($dramaDetails['networks'])) {
                foreach ($dramaDetails['networks'] as $network) {
                    // Map common networks to streaming platforms
                    if (strpos(strtolower($network['name']), 'netflix') !== false) {
                        $streamingPlatforms[] = 'netflix';
                    } elseif (strpos(strtolower($network['name']), 'viki') !== false) {
                        $streamingPlatforms[] = 'viki';
                    } elseif (strpos(strtolower($network['name']), 'crunchyroll') !== false) {
                        $streamingPlatforms[] = 'crunchyroll';
                    } elseif (strpos(strtolower($network['name']), 'disney') !== false) {
                        $streamingPlatforms[] = 'disney';
                    } elseif (strpos(strtolower($network['name']), 'apple') !== false) {
                        $streamingPlatforms[] = 'apple';
                    } elseif (strpos(strtolower($network['name']), 'hbo') !== false) {
                        $streamingPlatforms[] = 'hbo';
                    }
                }
            }
            $title->streaming_platforms = $streamingPlatforms;
            
            $title->save();

            // Associate genres
            $this->associateGenres($title, $dramaDetails['genres'] ?? []);

            // Import seasons and episodes if they exist
            if (!empty($dramaDetails['seasons'])) {
                $this->importSeasonsAndEpisodes($title, $dramaDetails);
            } else {
                $this->info("No seasons found for: {$title->title}");
            }

            // Associate cast and crew
            $this->associatePeople($title, $dramaDetails['credits'] ?? []);

            DB::commit();
            $this->line("Imported: {$title->title}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to import drama: {$drama['name']} - {$e->getMessage()}");
            Log::error("Import error: " . $e->getMessage(), [
                'drama' => $drama['name'],
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get the category ID based on origin country.
     *
     * @param array $originCountries
     * @return int
     */
    protected function getCategoryIdByOriginCountry(array $originCountries)
    {
        if (empty($originCountries)) {
            // Default to Korean category if no origin countries
            return Category::where('name', 'K-Drama')->first()->id ?? null;
        }

        $country = $originCountries[0];
        
        switch ($country) {
            case 'KR':
                return Category::where('name', 'K-Drama')->first()->id ?? null;
            case 'JP':
                return Category::where('name', 'J-Drama')->first()->id ?? null;
            case 'CN':
            case 'TW':
            case 'HK':
                return Category::where('name', 'C-Drama')->first()->id ?? null;
            case 'TH':
                return Category::where('name', 'Thai Drama')->first()->id ?? null;
            default:
                return Category::where('name', 'Asian Drama')->first()->id ?? null;
        }
    }

    /**
     * Associate genres with the title.
     *
     * @param \App\Models\Title $title
     * @param array $genres
     * @return void
     */
    protected function associateGenres(Title $title, array $genres)
    {
        $genreIds = [];
        
        foreach ($genres as $genre) {
            // Map TMDB genre ID to our genre ID
            $localGenre = Genre::where('tmdb_id', $genre['id'])->first();
            
            if ($localGenre) {
                $genreIds[] = $localGenre->id;
            } else {
                // Create the genre if it doesn't exist
                $newGenre = Genre::create([
                    'name' => $genre['name'],
                    'tmdb_id' => $genre['id']
                ]);
                
                $genreIds[] = $newGenre->id;
            }
        }

        // Add the Romance genre if it's not already associated
        $romanceGenre = Genre::where('name', 'Romance')->first();
        if ($romanceGenre && !in_array($romanceGenre->id, $genreIds)) {
            $genreIds[] = $romanceGenre->id;
        }
        
        $title->genres()->sync($genreIds);
    }

    /**
     * Import seasons and episodes for a drama.
     *
     * @param \App\Models\Title $title
     * @param array $dramaDetails
     * @return void
     */
    protected function importSeasonsAndEpisodes(Title $title, array $dramaDetails)
    {
        if (empty($dramaDetails['seasons'])) {
            return;
        }

        foreach ($dramaDetails['seasons'] as $seasonData) {
            // Skip special seasons/specials
            if ($seasonData['season_number'] == 0) {
                continue;
            }

            // Create the season
            $season = new Season();
            $season->title_id = $title->id;
            $season->name = $seasonData['name'];
            $season->number = $seasonData['season_number']; // Use 'number' instead of 'season_number'
            $season->air_date = $seasonData['air_date'] ?? null; // Use 'air_date' instead of 'release_date'
            $season->overview = $seasonData['overview'] ?? null;
            $season->poster = $seasonData['poster_path'] ? $this->tmdbService->downloadImage($seasonData['poster_path'], 'poster') : null; // Use 'poster' instead of 'poster_path'
            $season->tmdb_id = $seasonData['id'];
            $season->save();

            // Get detailed season info
            try {
                $seasonDetails = $this->tmdbService->getSeasonDetails($title->tmdb_id, $seasonData['season_number']);
                
                if (!empty($seasonDetails['episodes'])) {
                    foreach ($seasonDetails['episodes'] as $episodeData) {
                        try {
                            // Verificar que los campos requeridos no sean nulos
                            if (empty($episodeData['name'])) {
                                $episodeName = "Episodio {$episodeData['episode_number']}";
                            } else {
                                $episodeName = $episodeData['name'];
                            }
                            
                            // Verificar que el número de episodio no sea nulo
                            if (empty($episodeData['episode_number'])) {
                                $this->warn("Missing episode number for episode in season {$seasonData['season_number']}. Skipping...");
                                continue;
                            }
                            
                            $episode = new Episode();
                            $episode->title_id = $title->id;
                            $episode->season_id = $season->id;
                            $episode->name = $episodeName;
                            $episode->number = $episodeData['episode_number']; // Use 'number' instead of 'episode_number'
                            $episode->overview = $episodeData['overview'] ?? null;
                            $episode->air_date = $episodeData['air_date'] ?? null; // Use 'air_date' instead of 'release_date'
                            // vote_average is not in the table schema, so don't save it
                            $episode->runtime = $episodeData['runtime'] ?? ($title->runtime ?: 60);
                            $episode->still = $episodeData['still_path'] ? $this->tmdbService->downloadImage($episodeData['still_path'], 'backdrop') : null; // Use 'still' instead of 'still_path'
                            $episode->tmdb_id = $episodeData['id'];
                            $episode->save();
                        } catch (\Exception $e) {
                            $this->warn("Error adding episode {$episodeData['episode_number']} for season {$seasonData['season_number']}: " . $e->getMessage());
                        }
                    }
                } else {
                    $this->info("No episodes found for season {$seasonData['season_number']} of {$title->title}");
                }
            } catch (\Exception $e) {
                $this->warn("Error getting season {$seasonData['season_number']} details for {$title->title}: " . $e->getMessage());
                // La temporada se ha creado pero no pudimos obtener sus episodios
                // Continuamos con la siguiente temporada
            }
        }
    }

    /**
     * Associate people (cast and crew) with the title.
     *
     * @param \App\Models\Title $title
     * @param array $credits
     * @return void
     */
    protected function associatePeople(Title $title, array $credits)
    {
        if (empty($credits)) {
            return;
        }

        // Process cast
        if (!empty($credits['cast'])) {
            foreach (array_slice($credits['cast'], 0, 10) as $castMember) {
                $person = $this->findOrCreatePersonSafely($castMember);
                if ($person) {
                    $title->people()->attach($person->id, [
                        'role' => 'actor',
                        'character' => $castMember['character'] ?? null,
                        'order' => $castMember['order'] ?? 0
                    ]);
                }
            }
        }

        // Process crew
        if (!empty($credits['crew'])) {
            $importantRoles = ['Director', 'Producer', 'Executive Producer', 'Writer', 'Creator'];
            
            foreach ($credits['crew'] as $crewMember) {
                if (in_array($crewMember['job'], $importantRoles)) {
                    $person = $this->findOrCreatePersonSafely($crewMember);
                    if ($person) {
                        // Map job to standard role types to avoid constraint errors
                        $roleMap = [
                            'Director' => 'director',
                            'Producer' => 'producer',
                            'Executive Producer' => 'producer',
                            'Writer' => 'writer',
                            'Creator' => 'creator',
                            'Screenplay' => 'writer',
                            'Story' => 'writer',
                        ];
                        
                        // Get standardized role or fallback to generic 'crew'
                        $role = $roleMap[$crewMember['job']] ?? 'crew';
                        
                        // Check if this person is already associated with this title in this role
                        $exists = DB::table('title_person')
                            ->where('title_id', $title->id)
                            ->where('person_id', $person->id)
                            ->where('role', $role)
                            ->exists();
                            
                        if (!$exists) {
                            $title->people()->attach($person->id, [
                                'role' => $role,
                                'character' => null,
                                'order' => 0
                            ]);
                        }
                    }
                }
            }
        }
    }


    /**
     * Load genre mappings from database.
     *
     * @return void
     */
    protected function loadGenreMappings()
    {
        $genres = Genre::all();
        
        foreach ($genres as $genre) {
            if ($genre->tmdb_id) {
                $this->genreMappings[$genre->tmdb_id] = $genre->id;
            }
        }
    }
}