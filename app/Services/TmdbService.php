<?php

namespace App\Services;

<<<<<<< HEAD
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
=======
use App\Models\Country;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use App\Models\Season;
use App\Models\Episode;
use App\Models\MovieCast;
use App\Models\MovieCrew;
use App\Models\TvShowCast;
use App\Models\TvShowCrew;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
use Illuminate\Support\Facades\Storage;

class TmdbService
{
<<<<<<< HEAD
    public $client;
    public $apiKey;
    protected $accessToken;
    protected $baseUrl;
    protected $imageBaseUrl;
    protected $posterSize;
    protected $backdropSize;
    public $language;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Usar la configuración del archivo config/tmdb.php
        $this->apiKey = config('tmdb.api_key') ?: '69e4c68f7f83d0388b2f66bfaca96337';
        $this->accessToken = env('TMDB_ACCESS_TOKEN') ?: 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2OWU0YzY4ZjdmODNkMDM4OGIyZjY2YmZhY2E5NjMzNyIsIm5iZiI6MTc0MzU0OTE2MS42NDQsInN1YiI6IjY3ZWM3MmU5ZTE1N2FlZjNmMjAxMWNlYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.ashHsPk2NyxnEWQN7CuGoVBzOPUzFCU8RzT3SEjOlQA';
        
        // Usar valores de la configuración
        $this->baseUrl = config('tmdb.base_url', 'https://api.themoviedb.org/3');
        $this->imageBaseUrl = config('tmdb.image_base_url', 'https://image.tmdb.org/t/p/');
        $this->posterSize = config('tmdb.poster_sizes.large', 'w500');
        $this->backdropSize = config('tmdb.backdrop_sizes.large', 'original');
        $this->language = 'es-ES';

        // Log for debugging the base URL
        Log::info('TMDB API base URL', ['base_url' => $this->baseUrl]);

        // Creación del cliente Guzzle como dirección base completa
        // ¡IMPORTANTE! No terminar la URL base con "/" para que GuzzleHTTP la concatene correctamente
        // También es importante tener '/3' en la URL para la API v3
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'timeout' => config('tmdb.request.timeout', 10),
        ]);
        
        // Log para depuración
        \Illuminate\Support\Facades\Log::info('TMDB Service initialized', [
            'api_key' => substr($this->apiKey, 0, 3) . '...' . substr($this->apiKey, -3),
            'base_url' => $this->baseUrl
        ]);
    }

    /**
     * Search TV shows by query (doramas, animes, etc.)
     */
    public function searchShows(string $query, int $page = 1): Collection
    {
        try {
            $cacheKey = "tmdb_search_shows_{$query}_{$page}";
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($query, $page) {
                $endpoint = config('tmdb.endpoints.search_tv', '/search/tv');
                $response = $this->client->request('GET', $endpoint, [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'query' => $query,
                        'page' => $page,
                        'include_adult' => false,
                    ],
                ]);
                
                $data = json_decode($response->getBody()->getContents(), true);
                return collect($data['results']);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get Asian dramas by filtering for origin countries
     */
    public function getAsianDramas(int $page = 1, array $originCountries = ['KR', 'JP', 'CN', 'TW', 'TH']): Collection
    {
        try {
            $countriesString = implode('|', $originCountries);
            $cacheKey = "tmdb_asian_dramas_{$countriesString}_{$page}";
            
            // Registrar la información que enviaremos a la API para diagnóstico
            Log::info('Enviando solicitud a TMDB para dramas asiáticos', [
                'api_key' => substr($this->apiKey, 0, 3) . '...' . substr($this->apiKey, -3),
                'countries' => $countriesString,
                'page' => $page
            ]);
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($page, $originCountries) {
                // Obtener el endpoint correcto desde la configuración
                $url = config('tmdb.endpoints.discover_tv', '/discover/tv');
                
                Log::info('URL de consulta TMDB', [
                    'url' => $url, 
                    'api_key_present' => !empty($this->apiKey)
                ]);
                
                $response = $this->client->request('GET', $url, [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'sort_by' => 'popularity.desc',
                        'page' => $page,
                        'with_origin_country' => implode('|', $originCountries),
                        'include_adult' => false,
                    ]
                ]);
                
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                
                // Registrar resumen de respuesta
                Log::info('Respuesta de TMDB para dramas asiáticos', [
                    'status' => $response->getStatusCode(),
                    'total_results' => $data['total_results'] ?? 0,
                    'page' => $data['page'] ?? 0
                ]);
                
                return collect($data['results'] ?? []);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            
            // Intentar obtener más detalles del error
            $context = [];
            if ($e->hasResponse()) {
                $context['response_status'] = $e->getResponse()->getStatusCode();
                $context['response_body'] = substr($e->getResponse()->getBody()->getContents(), 0, 500);
            }
            Log::error('TMDB API Error Details', $context);
            
            return collect();
        }
    }

    /**
     * Get Korean dramas specifically
     */
    public function getKoreanDramas(int $page = 1): Collection
    {
        return $this->getAsianDramas($page, ['KR']);
    }

    /**
     * Get Japanese dramas specifically
     */
    public function getJapaneseDramas(int $page = 1): Collection
    {
        return $this->getAsianDramas($page, ['JP']);
    }

    /**
     * Get Chinese dramas specifically
     */
    public function getChineseDramas(int $page = 1): Collection
    {
        return $this->getAsianDramas($page, ['CN', 'TW']);
    }

    /**
     * Get details for a specific TV show by ID
     */
    public function getShowDetails(int $tmdbId): array
    {
        try {
            $cacheKey = "tmdb_show_details_{$tmdbId}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId) {
                // Create a new client without base URL to ensure full URL is used
                $client = new Client([
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 10,
                ]);
                
                // Build the full URL manually
                $fullUrl = "https://api.themoviedb.org/3/tv/{$tmdbId}";
                
                $queryParams = [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                    'append_to_response' => 'credits,external_ids,images,videos,content_ratings,recommendations,similar,keywords,watch/providers',
                ];
                
                // Log the full URL for debugging
                Log::info('Full TMDB URL for show details', [
                    'url' => $fullUrl . '?' . http_build_query($queryParams)
                ]);
                
                $response = $client->request('GET', $fullUrl, [
                    'query' => $queryParams
                ]);
                
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get details for a season of a TV show
     */
    public function getSeasonDetails(int $tmdbId, int $seasonNumber): array
    {
        try {
            $cacheKey = "tmdb_season_details_{$tmdbId}_{$seasonNumber}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId, $seasonNumber) {
                // Create a new client without base URL to ensure full URL is used
                $client = new Client([
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 10,
                ]);
                
                // Build the full URL manually
                $fullUrl = "https://api.themoviedb.org/3/tv/{$tmdbId}/season/{$seasonNumber}";
                
                $queryParams = [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                ];
                
                // Log the full URL for debugging
                Log::info('Full TMDB URL for season details', [
                    'url' => $fullUrl . '?' . http_build_query($queryParams)
                ]);
                
                $response = $client->request('GET', $fullUrl, [
                    'query' => $queryParams
                ]);
                
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get details for a specific episode
     */
    public function getEpisodeDetails(int $tmdbId, int $seasonNumber, int $episodeNumber): array
    {
        try {
            $cacheKey = "tmdb_episode_details_{$tmdbId}_{$seasonNumber}_{$episodeNumber}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId, $seasonNumber, $episodeNumber) {
                $endpoint = str_replace(['{tv_id}', '{season_number}', '{episode_number}'], [$tmdbId, $seasonNumber, $episodeNumber], config('tmdb.endpoints.episode_details', '/tv/{tv_id}/season/{season_number}/episode/{episode_number}'));
                $response = $this->client->request('GET', $endpoint, [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'append_to_response' => 'credits,images,videos',
                    ],
                ]);
                
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get person details (actors, directors, etc.)
     */
    public function getPersonDetails(int $personId): array
    {
        try {
            $cacheKey = "tmdb_person_details_{$personId}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($personId) {
                // Create a new client without base URL to ensure full URL is used
                $client = new Client([
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 10,
                ]);
                
                // Build the full URL manually
                $fullUrl = "https://api.themoviedb.org/3/person/{$personId}";
                
                $queryParams = [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                    'append_to_response' => 'combined_credits,external_ids,images',
                ];
                
                // Log the full URL for debugging
                Log::info('Full TMDB URL for person details', [
                    'url' => $fullUrl . '?' . http_build_query($queryParams)
                ]);
                
                $response = $client->request('GET', $fullUrl, [
                    'query' => $queryParams
                ]);
                
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Download an image from TMDB and store it locally
     */
    public function downloadImage(string $path, string $type = 'poster'): string
    {
        if (empty($path)) {
            return '';
        }

        try {
            $size = $type === 'poster' ? $this->posterSize : $this->backdropSize;
            $directory = $type === 'poster' ? 'posters' : 'backdrops';
            $filename = basename($path);
            $storagePath = "{$directory}/{$filename}";
            
            // Log para depuración
            Log::info('Descargando imagen', [
                'path' => $path,
                'tipo' => $type,
                'storagePath' => $storagePath
            ]);

            // Check if we already have this image in public directory
            $publicPath = public_path($storagePath);
            if (file_exists($publicPath)) {
                Log::info('Imagen ya existe en public', ['path' => $publicPath]);
                return $storagePath;
            }
            
            // Check if we already have this image in storage
            if (Storage::disk('public')->exists($storagePath)) {
                Log::info('Imagen ya existe en storage/public', ['path' => $storagePath]);
                
                // Asegurarse de que también esté en public (copiando si es necesario)
                $storageContent = Storage::disk('public')->get($storagePath);
                if (!file_exists(dirname($publicPath))) {
                    mkdir(dirname($publicPath), 0755, true);
                }
                file_put_contents($publicPath, $storageContent);
                
                return $storagePath;
            }

            // Download the image
            $imageUrl = $this->imageBaseUrl . $size . $path;
            Log::info('URL de descarga', ['url' => $imageUrl]);
            
            $response = $this->client->request('GET', $imageUrl);
            $imageContent = $response->getBody()->getContents();
            
            // Store it in both locations
            // 1. Store in storage/app/public
            Storage::disk('public')->put($storagePath, $imageContent);
            
            // 2. Store directly in public
            if (!file_exists(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0755, true);
            }
            file_put_contents($publicPath, $imageContent);
            
            Log::info('Imagen guardada exitosamente', [
                'storage' => storage_path('app/public/' . $storagePath),
                'public' => $publicPath
            ]);
            
            return $storagePath;
        } catch (GuzzleException $e) {
            Log::error('TMDB Image Download Error: ' . $e->getMessage(), ['exception' => $e]);
            return '';
        }
    }

    /**
     * Get trailer URL for a show
     */
    public function getTrailerUrl(array $videos): string
    {
        if (empty($videos['results'])) {
            return '';
        }

        // First, look for official trailers
        $trailers = collect($videos['results'])->filter(function ($video) {
            return $video['type'] === 'Trailer' && $video['site'] === 'YouTube';
        });

        if ($trailers->isNotEmpty()) {
            return 'https://www.youtube.com/watch?v=' . $trailers->first()['key'];
        }

        // If no trailers, look for teasers
        $teasers = collect($videos['results'])->filter(function ($video) {
            return $video['type'] === 'Teaser' && $video['site'] === 'YouTube';
        });

        if ($teasers->isNotEmpty()) {
            return 'https://www.youtube.com/watch?v=' . $teasers->first()['key'];
        }

        // If no official videos, just return the first one
        $firstVideo = collect($videos['results'])->first(function ($video) {
            return $video['site'] === 'YouTube';
        });

        return $firstVideo ? 'https://www.youtube.com/watch?v=' . $firstVideo['key'] : '';
    }

    /**
     * Get formatted content rating
     */
    public function getContentRating(array $contentRatings): string
    {
        if (empty($contentRatings['results'])) {
            return '';
        }

        // Look for the rating from specific countries (KR, JP, US)
        $priorityCountries = ['KR', 'JP', 'CN', 'US'];
        
        foreach ($priorityCountries as $country) {
            $rating = collect($contentRatings['results'])->firstWhere('iso_3166_1', $country);
            if ($rating) {
                return $rating['rating'];
            }
        }

        // If no priority country rating found, return the first available
        return $contentRatings['results'][0]['rating'] ?? '';
    }

    /**
     * Get romantic Asian dramas by filtering for origin countries and genres
     * 
     * @param int $page Page number
     * @param array $originCountries Array of country codes (KR, JP, CN, etc.)
     * @param bool $includeNonRomantic Whether to include non-romantic dramas
     * @return \Illuminate\Support\Collection
     */
    public function getRomanticAsianDramas(int $page = 1, array $originCountries = ['KR', 'JP', 'CN', 'TW', 'TH'], bool $includeNonRomantic = false): Collection
    {
        try {
            $genreString = $includeNonRomantic ? '' : '18|10749'; // Drama (18) OR Romance (10749)
            $countriesString = implode('|', $originCountries);
            $cacheKey = "tmdb_romantic_asian_dramas_{$countriesString}_g{$genreString}_{$page}";
            
            // Debug de configuración para asegurarnos que todo está correcto
            Log::info('Configuración TMDB', [
                'apiKey' => !empty($this->apiKey),
                'baseUrl' => $this->baseUrl,
                'language' => $this->language
            ]);
            
            Log::info('Enviando solicitud a TMDB para doramas románticos asiáticos', [
                'api_key' => substr($this->apiKey, 0, 3) . '...' . substr($this->apiKey, -3),
                'countries' => $countriesString,
                'genres' => $genreString,
                'page' => $page
            ]);
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($page, $originCountries, $includeNonRomantic, $genreString) {
                // Create a new client without base URL to ensure full URL is used
                $client = new Client([
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 10,
                ]);
                
                // Build the full URL manually
                $fullUrl = "https://api.themoviedb.org/3/discover/tv";
                
                $queryParams = [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                    'sort_by' => 'popularity.desc',
                    'page' => $page,
                    'with_origin_country' => implode('|', $originCountries),
                    'include_adult' => false,
                ];
                
                // Add genre filter if we're filtering for romantic content
                if (!$includeNonRomantic && !empty($genreString)) {
                    $queryParams['with_genres'] = $genreString;
                }
                
                // Log the full URL for debugging
                Log::info('Full TMDB URL', [
                    'url' => $fullUrl . '?' . http_build_query($queryParams)
                ]);
                
                $response = $client->request('GET', $fullUrl, [
                    'query' => $queryParams
                ]);
                
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                
                // Debug de la respuesta para verificar que lleguen resultados
                Log::info('Respuesta TMDB detallada', [
                    'total_results' => $data['total_results'] ?? 0,
                    'total_pages' => $data['total_pages'] ?? 0,
                    'result_count' => count($data['results'] ?? []),
                    'first_result' => !empty($data['results']) ? $data['results'][0]['name'] : 'No results'
                ]);
                
                // Filter results to prioritize romantic content
                $results = collect($data['results'] ?? []);
                
                // Process results to identify potential romantic content by title keywords
                $processedResults = $results->map(function ($item) {
                    // Check title and overview for romance keywords
                    $romanticKeywords = ['love', 'romance', 'romantic', 'relationship', 'heart', 'passion', 'amor', 'romántico', 'corazón'];
                    
                    $title = strtolower($item['name'] ?? '');
                    $overview = strtolower($item['overview'] ?? '');
                    
                    $romanticScore = 0;
                    foreach ($romanticKeywords as $keyword) {
                        if (strpos($title, $keyword) !== false) {
                            $romanticScore += 2;
                        }
                        if (strpos($overview, $keyword) !== false) {
                            $romanticScore += 1;
                        }
                    }
                    
                    $item['romantic_score'] = $romanticScore;
                    return $item;
                });
                
                // Sort by romantic score if we're focusing on romantic content
                if (!$includeNonRomantic) {
                    $processedResults = $processedResults->sortByDesc('romantic_score');
                }
                
                Log::info('Respuesta de TMDB para doramas románticos asiáticos', [
                    'total_results' => $data['total_results'] ?? 0,
                    'page' => $data['page'] ?? 0,
                    'filtered_count' => $processedResults->count()
                ]);
                
                return $processedResults;
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            
            // Intentar obtener más detalles del error
            $context = [];
            if ($e->hasResponse()) {
                $context['response_status'] = $e->getResponse()->getStatusCode();
                $context['response_body'] = substr($e->getResponse()->getBody()->getContents(), 0, 500);
            }
            Log::error('TMDB API Error Details', $context);
            
            return collect();
        }
    }

    /**
     * Get Korean romantic dramas specifically
     */
    public function getKoreanRomanticDramas(int $page = 1): Collection
    {
        return $this->getRomanticAsianDramas($page, ['KR']);
    }

    /**
     * Get Japanese romantic dramas specifically
     */
    public function getJapaneseRomanticDramas(int $page = 1): Collection
    {
        return $this->getRomanticAsianDramas($page, ['JP']);
    }

    /**
     * Get Chinese romantic dramas specifically
     */
    public function getChineseRomanticDramas(int $page = 1): Collection
    {
        return $this->getRomanticAsianDramas($page, ['CN', 'TW']);
    }

    /**
     * Get romantic movies from Asian countries
     */
    public function getRomanticAsianMovies(int $page = 1, array $originCountries = ['KR', 'JP', 'CN', 'TW', 'TH']): Collection
    {
        try {
            $genreString = '18|10749'; // Drama (18) OR Romance (10749)
            $countriesString = implode('|', $originCountries);
            $cacheKey = "tmdb_romantic_asian_movies_{$countriesString}_{$page}";
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($page, $originCountries, $genreString) {
                $url = config('tmdb.endpoints.discover_movie', '/discover/movie');
                
                $response = $this->client->request('GET', $url, [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'sort_by' => 'popularity.desc',
                        'page' => $page,
                        'with_genres' => $genreString,
                        'with_original_language' => implode('|', array_map(function($country) {
                            return strtolower(config('tmdb.languages.' . array_search($country, config('tmdb.regions', []), true), 'en'));
                        }, $originCountries)),
                        'include_adult' => false,
                    ]
                ]);
                
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                
                return collect($data['results'] ?? []);
            });
        } catch (GuzzleException $e) {
            Log::error('TMDB API Error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Categorize a drama into a romantic subgenre based on keywords and overview
     */
    public function categorizeRomanticSubgenre(array $showDetails): string
    {
        // Get the romance subgenres from config
        $subgenres = config('tmdb.romance_subgenres', []);
        if (empty($subgenres)) {
            return 'romance';
        }
        
        // Get all keywords from the show
        $keywords = collect($showDetails['keywords']['results'] ?? [])
            ->pluck('name')
            ->map(function ($keyword) {
                return strtolower($keyword);
            })
            ->toArray();
            
        // Add words from title and overview
        $title = strtolower($showDetails['name'] ?? '');
        $overview = strtolower($showDetails['overview'] ?? '');
        $allText = $title . ' ' . $overview;
        
        // Calculate score for each subgenre
        $scores = [];
        foreach ($subgenres as $key => $subgenre) {
            $score = 0;
            foreach ($subgenre['keywords'] as $keyword) {
                if (in_array($keyword, $keywords)) {
                    $score += 3; // Direct keyword match has highest weight
                }
                if (strpos($allText, $keyword) !== false) {
                    $score += 1; // Text contains keyword
                }
            }
            $scores[$key] = $score;
        }
        
        // Get the highest scoring subgenre
        arsort($scores);
        $topSubgenre = key($scores);
        
        // If no clear match, return generic romance
        if ($scores[$topSubgenre] == 0) {
            return 'romance';
        }
        
        return $topSubgenre;
    }

    /**
     * Get recommendations based on a show's romantic subgenre
     */
    public function getSubgenreRecommendations(string $subgenre, int $page = 1, int $limit = 10): Collection
    {
        $allRomanticDramas = $this->getRomanticAsianDramas($page, ['KR', 'JP', 'CN', 'TW', 'TH']);
        
        // For now, return all romantic dramas
        // This would be enhanced with proper subgenre categorization in the future
        return $allRomanticDramas->take($limit);
=======
    protected $apiKey;
    protected $baseUrl;
    protected $imageBaseUrl;
    protected $certPath;
    
    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
        $this->baseUrl = 'https://api.themoviedb.org/3';
        $this->imageBaseUrl = 'https://image.tmdb.org/t/p/';
        $this->certPath = storage_path('app/private/cert/cacert-2025-02-25.pem');
        
        // Log de inicio para verificar ruta del certificado
        Log::info("TmdbService inicializado con certificado en: " . $this->certPath);
        Log::info("Certificado existe: " . (file_exists($this->certPath) ? 'SÍ' : 'NO'));
    }
    
    /**
     * Realizar petición a la API de TMDB
     */
    /**
     * Realizar petición a la API de TMDB
     */
    protected function request($endpoint, $params = [], $retries = 3)
    {
        $url = $this->baseUrl . $endpoint;
        $params = array_merge($params, ['api_key' => $this->apiKey, 'language' => 'es-ES']);
        
        // Log de depuración
        Log::debug("Iniciando petición a TMDB: " . $url . " - Parámetros: " . json_encode($params));
        echo "Solicitando: " . $endpoint . PHP_EOL;
        
        $attempt = 0;
        while ($attempt < $retries) {
            $startTime = microtime(true);
            try {
                Log::debug("Intento #" . ($attempt + 1) . " para: " . $endpoint);
                echo "  Intento #" . ($attempt + 1) . "...\r";
                
                $response = Http::timeout(30) // Aumentado a 30 segundos
                    ->withOptions([
                        'verify' => $this->certPath
                    ])
                    ->get($url, $params);
                
                $endTime = microtime(true);
                $duration = round($endTime - $startTime, 2);
                
                if ($response->successful()) {
                    Log::debug("Petición exitosa a: " . $endpoint . " (Tiempo: " . $duration . "s)");
                    echo "  Completado en " . $duration . "s" . PHP_EOL;
                    return $response->json();
                } else {
                    Log::error("TMDB API error: " . $response->status() . " - " . $response->body() . " (Tiempo: " . $duration . "s)");
                    echo "  Error " . $response->status() . " en " . $duration . "s" . PHP_EOL;
                    
                    // Si es un error 429 (too many requests), esperamos antes de reintentar
                    if ($response->status() === 429) {
                        $waitTime = 2;
                        Log::warning("Límite de peticiones alcanzado, esperando " . $waitTime . " segundos");
                        echo "  Límite de peticiones alcanzado, esperando " . $waitTime . " segundos..." . PHP_EOL;
                        sleep($waitTime);
                        $attempt++;
                        continue;
                    }
                    
                    return null;
                }
            } catch (\Exception $e) {
                $endTime = microtime(true);
                $duration = round($endTime - $startTime, 2);
                
                Log::error("TMDB API excepción: " . $e->getMessage() . " (Tiempo: " . $duration . "s)");
                echo "  Excepción: " . $e->getMessage() . " en " . $duration . "s" . PHP_EOL;
                
                $attempt++;
                
                if ($attempt >= $retries) {
                    Log::error("Máximo de intentos alcanzado para: " . $endpoint);
                    echo "  Máximo de intentos alcanzado" . PHP_EOL;
                    return null;
                }
                
                // Esperar antes de reintentar (backoff exponencial)
                $waitTime = pow(2, $attempt);
                Log::info("Esperando " . $waitTime . " segundos antes de reintentar");
                echo "  Esperando " . $waitTime . " segundos antes de reintentar..." . PHP_EOL;
                sleep($waitTime);
            }
        }
        
        return null;
    }
    
    /**
     * Descargar y almacenar imagen
     */
    /**
     * Descargar y almacenar imagen
     */
    protected function storeImage($path, $size = 'original', $type = 'posters')
    {
        if (empty($path)) {
            return null;
        }
        
        try {
            $url = $this->imageBaseUrl . $size . $path;
            Log::info("Descargando imagen desde: {$url}");
            
            $downloadStartTime = now();
            $contents = file_get_contents($url);
            $downloadTime = now()->diffInMilliseconds($downloadStartTime);
            
            $filename = $type . '/' . basename($path);
            
            $saveStartTime = now();
            Storage::disk('public')->put($filename, $contents);
            $saveTime = now()->diffInMilliseconds($saveStartTime);
            
            Log::info("Imagen guardada como: {$filename} (Descarga: {$downloadTime}ms, Guardado: {$saveTime}ms)");
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('Error al descargar imagen: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener y guardar géneros
     */
    public function syncGenres()
    {
        Log::info("Iniciando sincronización de géneros");
        echo "Sincronizando géneros..." . PHP_EOL;
        
        $movieGenres = $this->request('/genre/movie/list');
        $tvGenres = $this->request('/genre/tv/list');
        
        if (!$movieGenres || !$tvGenres) {
            Log::error("Error al obtener géneros de TMDB");
            echo "Error al obtener géneros" . PHP_EOL;
            return false;
        }
        
        $allGenres = array_merge($movieGenres['genres'], $tvGenres['genres']);
        $uniqueGenres = collect($allGenres)->unique('id')->toArray();
        
        Log::info("Géneros obtenidos: " . count($uniqueGenres));
        echo "Géneros encontrados: " . count($uniqueGenres) . PHP_EOL;
        
        foreach ($uniqueGenres as $genreData) {
            Genre::updateOrCreate(
                ['api_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'slug' => Str::slug($genreData['name']),
                ]
            );
        }
        
        Log::info("Sincronización de géneros completada");
        echo "Sincronización de géneros completada" . PHP_EOL;
        
        return true;
    }
    
    /**
     * Obtener y guardar películas asiáticas populares
     */
    public function syncAsianMovies($page = 1)
    {
        // Enfocamos solo en los tres países principales
        $asianRegions = ['CN', 'JP', 'KR'];
        $processedCount = 0;
        
        foreach ($asianRegions as $region) {
            Log::info("Sincronizando películas de la región: {$region} (página {$page})");
            $startTime = now();
            
            // Ajustamos los parámetros para filtrar mejor:
            // 1. Especificamos la región
            // 2. Filtramos por idioma original del país
            // 3. Excluimos películas occidentales que pueden estar disponibles en esa región
            $discoverData = $this->request('/discover/movie', [
                'region' => $region,
                'with_original_language' => strtolower($region), // Esto es clave - asegura idioma original
                'sort_by' => 'popularity.desc',
                'page' => $page,
                // Podríamos agregar también filtros por palabras clave si es necesario
            ]);
            
            $apiTime = now()->diffInMilliseconds($startTime);
            Log::info("Tiempo de respuesta de API: {$apiTime}ms");
            
            if (!$discoverData || empty($discoverData['results'])) {
                Log::warning("No se encontraron películas para la región {$region}");
                continue;
            }
            
            Log::info("Encontradas " . count($discoverData['results']) . " películas para la región {$region}");
            
            foreach ($discoverData['results'] as $movieData) {
                $detailStartTime = now();
                
                // Obtener detalles completos de la película
                $movieDetail = $this->request('/movie/' . $movieData['id'], [
                    'append_to_response' => 'credits,images,keywords,watch/providers'
                ]);
                
                $detailTime = now()->diffInMilliseconds($detailStartTime);
                Log::info("Tiempo de obtención de detalles para película ID {$movieData['id']}: {$detailTime}ms");
                
                if (!$movieDetail) {
                    Log::warning("No se pudieron obtener detalles para la película ID {$movieData['id']}");
                    continue;
                }
                
                // Verificación adicional: asegurarnos que el país de producción coincide
                $isAsianProduction = false;
                if (!empty($movieDetail['production_countries'])) {
                    foreach ($movieDetail['production_countries'] as $prodCountry) {
                        if (in_array($prodCountry['iso_3166_1'], $asianRegions)) {
                            $isAsianProduction = true;
                            break;
                        }
                    }
                }
                
                // Si no es una producción asiática, saltamos esta película
                if (!$isAsianProduction) {
                    Log::info("Película ID {$movieData['id']} no es una producción asiática, omitiendo");
                    continue;
                }
                
                // Guardar país
                $country = null;
                if (!empty($movieDetail['production_countries'][0]['iso_3166_1'])) {
                    $countryCode = $movieDetail['production_countries'][0]['iso_3166_1'];
                    $countryName = $movieDetail['production_countries'][0]['name'];
                    
                    $country = Country::firstOrCreate(
                        ['code' => $countryCode],
                        ['name' => $countryName]
                    );
                }
                
                // Descargar imágenes
                $posterPath = null;
                $backdropPath = null;
                
                $imageStartTime = now();
                
                if (!empty($movieDetail['poster_path'])) {
                    $posterPath = $this->storeImage($movieDetail['poster_path'], 'w500', 'posters');
                    Log::info("Poster descargado: {$posterPath}");
                }
                
                if (!empty($movieDetail['backdrop_path'])) {
                    $backdropPath = $this->storeImage($movieDetail['backdrop_path'], 'w1280', 'backdrops');
                    Log::info("Backdrop descargado: {$backdropPath}");
                }
                
                $imageTime = now()->diffInMilliseconds($imageStartTime);
                Log::info("Tiempo de descarga de imágenes: {$imageTime}ms");
                
                // Crear o actualizar la película
                $movie = Movie::updateOrCreate(
                    ['api_id' => $movieDetail['id'], 'api_source' => 'tmdb'],
                    [
                        'title' => $movieDetail['title'],
                        'original_title' => $movieDetail['original_title'],
                        'slug' => Str::slug($movieDetail['title'] . '-' . $movieDetail['id']),
                        'overview' => $movieDetail['overview'],
                        'poster_path' => $posterPath,
                        'backdrop_path' => $backdropPath,
                        'runtime' => $movieDetail['runtime'] ?? null,
                        'release_date' => $movieDetail['release_date'] ?? null,
                        'original_language' => $movieDetail['original_language'],
                        'country_of_origin' => $country ? $country->code : null,
                        'popularity' => $movieDetail['popularity'],
                        'vote_average' => $movieDetail['vote_average'],
                        'vote_count' => $movieDetail['vote_count'],
                        'status' => $movieDetail['status'],
                    ]
                );
                
                // Resto del código igual...
                
                $processedCount++;
                Log::info("Película asiática procesada: {$movie->title} (ID: {$movie->id}, País: {$country->code})");
            }
        }
        
        Log::info("Sincronización completa. Total de películas asiáticas procesadas: {$processedCount}");
        return $processedCount;
    }
    
    /**
     * Obtener y guardar series asiáticas populares
     */
    public function syncAsianTvShows($page = 1)
    {
        // Enfocamos solo en los tres países principales
        $asianRegions = ['CN', 'JP', 'KR'];
        $processedCount = 0;
        
        foreach ($asianRegions as $region) {
            Log::info("Sincronizando series de la región: {$region} (página {$page})");
            $startTime = now();
            
            // Para series, usamos with_origin_country para asegurar que son de origen asiático
            $discoverData = $this->request('/discover/tv', [
                'with_origin_country' => $region, // Mejor que 'region' para TV shows
                'with_original_language' => strtolower($region), // Filtro de idioma para mayor precisión
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ]);
            
            $apiTime = now()->diffInMilliseconds($startTime);
            Log::info("Tiempo de respuesta de API: {$apiTime}ms");
            
            if (!$discoverData || empty($discoverData['results'])) {
                Log::warning("No se encontraron series para la región {$region}");
                continue;
            }
            
            Log::info("Encontradas " . count($discoverData['results']) . " series para la región {$region}");
            
            // Resto del código similar a syncAsianMovies...
            
            foreach ($discoverData['results'] as $tvData) {
                // Verificar que realmente sea una serie asiática
                if (!in_array($region, $tvData['origin_country'] ?? [])) {
                    Log::info("Serie ID {$tvData['id']} no es de origen {$region}, omitiendo");
                    continue;
                }
                
                // Procesamiento normal...
            }
        }
    }
    
    /**
     * Sincronizar persona/actor
     */
    /**
     * Sincronizar persona/actor
     */
    protected function syncPerson($personId)
    {
        $startTime = now();
        $personDetail = $this->request('/person/' . $personId);
        $apiTime = now()->diffInMilliseconds($startTime);
        Log::info("Tiempo de obtención de detalles para persona ID {$personId}: {$apiTime}ms");
        
        if (!$personDetail) {
            Log::warning("No se pudieron obtener detalles para la persona ID {$personId}");
            return null;
        }
        
        // Descargar imagen de perfil
        $profilePath = null;
        if (!empty($personDetail['profile_path'])) {
            $imageStartTime = now();
            $profilePath = $this->storeImage($personDetail['profile_path'], 'w300', 'profiles');
            $imageTime = now()->diffInMilliseconds($imageStartTime);
            Log::info("Imagen de perfil descargada: {$profilePath} en {$imageTime}ms");
        }
        
        // Crear o actualizar persona
        return Person::updateOrCreate(
            ['api_id' => $personDetail['id'], 'api_source' => 'tmdb'],
            [
                'name' => $personDetail['name'],
                'original_name' => $personDetail['name'],
                'slug' => Str::slug($personDetail['name'] . '-' . $personDetail['id']),
                'biography' => $personDetail['biography'],
                'profile_path' => $profilePath,
                'birthday' => $personDetail['birthday'] ?? null,
                'deathday' => $personDetail['deathday'] ?? null,
                'place_of_birth' => $personDetail['place_of_birth'],
                'gender' => $personDetail['gender'],
                'popularity' => $personDetail['popularity'],
            ]
        );
    }
    
    /**
     * Obtener nombre de país a partir del código
     */
    protected function getCountryName($code)
    {
        $countries = [
            'CN' => 'China',
            'JP' => 'Japón',
            'KR' => 'Corea del Sur',
            'TW' => 'Taiwán',
            'HK' => 'Hong Kong',
            'TH' => 'Tailandia',
            'VN' => 'Vietnam',
            'ID' => 'Indonesia',
            'MY' => 'Malasia',
            'PH' => 'Filipinas',
        ];
        
        return $countries[$code] ?? $code;
    }
    
    /**
     * Buscar películas y series por término
     */
    public function search($query, $page = 1)
    {
        $searchData = $this->request('/search/multi', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ]);
        
        if (!$searchData || empty($searchData['results'])) {
            return null;
        }
        
        // Filtrar resultados para quedarnos solo con películas y series
        $results = array_filter($searchData['results'], function($item) {
            return $item['media_type'] === 'movie' || $item['media_type'] === 'tv';
        });
        
        return [
            'results' => array_values($results),
            'total_results' => $searchData['total_results'],
            'total_pages' => $searchData['total_pages'],
        ];
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}