<?php

namespace App\Services;

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
use Illuminate\Support\Facades\Storage;

class TmdbService
{
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
    }
}