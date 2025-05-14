<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TmdbService
{
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
        // Definimos la API key manualmente para estar seguros (ya la hemos verificado en test-api-key.php)
        $this->apiKey = '69e4c68f7f83d0388b2f66bfaca96337';
        $this->accessToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2OWU0YzY4ZjdmODNkMDM4OGIyZjY2YmZhY2E5NjMzNyIsIm5iZiI6MTc0MzU0OTE2MS42NDQsInN1YiI6IjY3ZWM3MmU5ZTE1N2FlZjNmMjAxMWNlYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.ashHsPk2NyxnEWQN7CuGoVBzOPUzFCU8RzT3SEjOlQA';
        
        // Valores fijos para el resto de la configuración
        $this->baseUrl = 'https://api.themoviedb.org/3';
        $this->imageBaseUrl = 'https://image.tmdb.org/t/p/';
        $this->posterSize = 'w500';
        $this->backdropSize = 'original';
        $this->language = 'es-ES';

        // Creación del cliente Guzzle como dirección base completa
        // ¡IMPORTANTE! No terminar la URL base con "/" para que GuzzleHTTP la concatene correctamente
        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3',
            'headers' => [
                'Accept' => 'application/json',
            ],
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
                $response = $this->client->request('GET', '/search/tv', [
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
                // Construye la URL correcta - usando el formato que ya verificamos que funciona
                $url = "discover/tv";
                
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
                $response = $this->client->request('GET', "/tv/{$tmdbId}", [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'append_to_response' => 'credits,external_ids,images,videos,content_ratings,recommendations,similar,keywords,watch/providers',
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
     * Get details for a season of a TV show
     */
    public function getSeasonDetails(int $tmdbId, int $seasonNumber): array
    {
        try {
            $cacheKey = "tmdb_season_details_{$tmdbId}_{$seasonNumber}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId, $seasonNumber) {
                $response = $this->client->request('GET', "/tv/{$tmdbId}/season/{$seasonNumber}", [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
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
     * Get details for a specific episode
     */
    public function getEpisodeDetails(int $tmdbId, int $seasonNumber, int $episodeNumber): array
    {
        try {
            $cacheKey = "tmdb_episode_details_{$tmdbId}_{$seasonNumber}_{$episodeNumber}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId, $seasonNumber, $episodeNumber) {
                $response = $this->client->request('GET', "/tv/{$tmdbId}/season/{$seasonNumber}/episode/{$episodeNumber}", [
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
                $response = $this->client->request('GET', "/person/{$personId}", [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => $this->language,
                        'append_to_response' => 'combined_credits,external_ids,images',
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
}