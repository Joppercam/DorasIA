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
    
    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
        $this->baseUrl = 'https://api.themoviedb.org/3';
        $this->imageBaseUrl = 'https://image.tmdb.org/t/p/';
    }
    
    /**
     * Realizar petición a la API de TMDB
     */
    protected function request($endpoint, $params = [])
    {
        $url = $this->baseUrl . $endpoint;
        $params = array_merge($params, ['api_key' => $this->apiKey, 'language' => 'es-ES']);
        
        try {
            $response = Http::get($url, $params);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('TMDB API error: ' . $response->status() . ' - ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('TMDB API exception: ' . $e->getMessage());
            return null;
        }
    }
    
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
            $contents = file_get_contents($url);
            
            $filename = $type . '/' . basename($path);
            Storage::disk('public')->put($filename, $contents);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('Image download error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener y guardar géneros
     */
    public function syncGenres()
    {
        $movieGenres = $this->request('/genre/movie/list');
        $tvGenres = $this->request('/genre/tv/list');
        
        if (!$movieGenres || !$tvGenres) {
            return false;
        }
        
        $allGenres = array_merge($movieGenres['genres'], $tvGenres['genres']);
        $uniqueGenres = collect($allGenres)->unique('id')->toArray();
        
        foreach ($uniqueGenres as $genreData) {
            Genre::updateOrCreate(
                ['api_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'slug' => Str::slug($genreData['name']),
                ]
            );
        }
        
        return true;
    }
    
    /**
     * Obtener y guardar películas asiáticas populares
     */
    public function syncAsianMovies($page = 1)
    {
        // Códigos de país para países asiáticos
        $asianRegions = ['CN', 'JP', 'KR', 'TW', 'HK', 'TH', 'VN', 'ID', 'MY', 'PH'];
        $processedCount = 0;
        
        foreach ($asianRegions as $region) {
            $discoverData = $this->request('/discover/movie', [
                'region' => $region,
                'sort_by' => 'popularity.desc',
                'page' => $page,
                'with_original_language' => strtolower($region),
            ]);
            
            if (!$discoverData || empty($discoverData['results'])) {
                continue;
            }
            
            foreach ($discoverData['results'] as $movieData) {
                // Obtener detalles completos de la película
                $movieDetail = $this->request('/movie/' . $movieData['id'], [
                    'append_to_response' => 'credits,images,keywords,watch/providers'
                ]);
                
                if (!$movieDetail) {
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
                
                if (!empty($movieDetail['poster_path'])) {
                    $posterPath = $this->storeImage($movieDetail['poster_path'], 'w500', 'posters');
                }
                
                if (!empty($movieDetail['backdrop_path'])) {
                    $backdropPath = $this->storeImage($movieDetail['backdrop_path'], 'w1280', 'backdrops');
                }
                
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
                
                // Sincronizar géneros
                if (!empty($movieDetail['genres'])) {
                    $genreIds = collect($movieDetail['genres'])->pluck('id')->toArray();
                    $genres = Genre::whereIn('api_id', $genreIds)->get();
                    $movie->genres()->sync($genres->pluck('id')->toArray());
                }
                
                // Guardar reparto
                if (!empty($movieDetail['credits']['cast'])) {
                    foreach (array_slice($movieDetail['credits']['cast'], 0, 20) as $index => $castData) {
                        $person = $this->syncPerson($castData['id']);
                        
                        if ($person) {
                            MovieCast::updateOrCreate(
                                ['movie_id' => $movie->id, 'person_id' => $person->id],
                                [
                                    'character' => $castData['character'],
                                    'order' => $index,
                                ]
                            );
                        }
                    }
                }
                
                // Guardar equipo técnico
                if (!empty($movieDetail['credits']['crew'])) {
                    // Filtrar roles importantes
                    $keyRoles = ['Director', 'Producer', 'Screenplay', 'Writer', 'Story'];
                    $keyCrewData = array_filter($movieDetail['credits']['crew'], function($crew) use ($keyRoles) {
                        return in_array($crew['job'], $keyRoles);
                    });
                    
                    foreach ($keyCrewData as $crewData) {
                        $person = $this->syncPerson($crewData['id']);
                        
                        if ($person) {
                            MovieCrew::updateOrCreate(
                                [
                                    'movie_id' => $movie->id, 
                                    'person_id' => $person->id,
                                    'job' => $crewData['job'],
                                ],
                                [
                                    'department' => $crewData['department'],
                                ]
                            );
                        }
                    }
                }
                
                $processedCount++;
            }
        }
        
        return $processedCount;
    }
    
    /**
     * Obtener y guardar series asiáticas populares
     */
    public function syncAsianTvShows($page = 1)
    {
        // Códigos de país para países asiáticos
        $asianRegions = ['CN', 'JP', 'KR', 'TW', 'HK', 'TH', 'VN', 'ID', 'MY', 'PH'];
        $processedCount = 0;
        
        foreach ($asianRegions as $region) {
            $discoverData = $this->request('/discover/tv', [
                'with_origin_country' => $region,
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ]);
            
            if (!$discoverData || empty($discoverData['results'])) {
                continue;
            }
            
            foreach ($discoverData['results'] as $tvData) {
                // Obtener detalles completos de la serie
                $tvDetail = $this->request('/tv/' . $tvData['id'], [
                    'append_to_response' => 'credits,images,keywords,seasons,watch/providers'
                ]);
                
                if (!$tvDetail) {
                    continue;
                }
                
                // Determinar tipo de serie
                $showType = 'drama';
                if (in_array(16, collect($tvDetail['genres'])->pluck('id')->toArray())) {
                    $showType = 'anime';  // ID 16 es Animación en TMDB
                } elseif (strpos(strtolower($tvDetail['name']), 'variety') !== false || 
                          strpos(strtolower($tvDetail['name']), 'show') !== false) {
                    $showType = 'variety';
                }
                
                // Guardar país
                $country = null;
                if (!empty($tvDetail['origin_country'][0])) {
                    $countryCode = $tvDetail['origin_country'][0];
                    
                    $country = Country::firstOrCreate(
                        ['code' => $countryCode],
                        ['name' => $this->getCountryName($countryCode)]
                    );
                }
                
                // Descargar imágenes
                $posterPath = null;
                $backdropPath = null;
                
                if (!empty($tvDetail['poster_path'])) {
                    $posterPath = $this->storeImage($tvDetail['poster_path'], 'w500', 'posters');
                }
                
                if (!empty($tvDetail['backdrop_path'])) {
                    $backdropPath = $this->storeImage($tvDetail['backdrop_path'], 'w1280', 'backdrops');
                }
                
                // Crear o actualizar la serie
                $tvShow = TvShow::updateOrCreate(
                    ['api_id' => $tvDetail['id'], 'api_source' => 'tmdb'],
                    [
                        'title' => $tvDetail['name'],
                        'original_title' => $tvDetail['original_name'],
                        'slug' => Str::slug($tvDetail['name'] . '-' . $tvDetail['id']),
                        'overview' => $tvDetail['overview'],
                        'poster_path' => $posterPath,
                        'backdrop_path' => $backdropPath,
                        'number_of_seasons' => $tvDetail['number_of_seasons'],
                        'number_of_episodes' => $tvDetail['number_of_episodes'],
                        'first_air_date' => $tvDetail['first_air_date'] ?? null,
                        'last_air_date' => $tvDetail['last_air_date'] ?? null,
                        'original_language' => $tvDetail['original_language'],
                        'country_of_origin' => $country ? $country->code : null,
                        'in_production' => $tvDetail['in_production'],
                        'popularity' => $tvDetail['popularity'],
                        'vote_average' => $tvDetail['vote_average'],
                        'vote_count' => $tvDetail['vote_count'],
                        'status' => $tvDetail['status'],
                        'show_type' => $showType,
                    ]
                );
                
                // Sincronizar géneros
                if (!empty($tvDetail['genres'])) {
                    $genreIds = collect($tvDetail['genres'])->pluck('id')->toArray();
                    $genres = Genre::whereIn('api_id', $genreIds)->get();
                    $tvShow->genres()->sync($genres->pluck('id')->toArray());
                }
                
                // Guardar temporadas y episodios
                if (!empty($tvDetail['seasons'])) {
                    foreach ($tvDetail['seasons'] as $seasonData) {
                        // Descargar póster de temporada
                        $seasonPosterPath = null;
                        if (!empty($seasonData['poster_path'])) {
                            $seasonPosterPath = $this->storeImage($seasonData['poster_path'], 'w500', 'seasons');
                        }
                        
                        // Crear o actualizar temporada
                        $season = Season::updateOrCreate(
                            ['tv_show_id' => $tvShow->id, 'season_number' => $seasonData['season_number']],
                            [
                                'name' => $seasonData['name'],
                                'overview' => $seasonData['overview'],
                                'poster_path' => $seasonPosterPath,
                                'air_date' => $seasonData['air_date'] ?? null,
                                'episode_count' => $seasonData['episode_count'],
                                'api_id' => $seasonData['id'],
                            ]
                        );
                        
                        // Obtener detalles de la temporada para los episodios
                        $seasonDetail = $this->request('/tv/' . $tvDetail['id'] . '/season/' . $seasonData['season_number']);
                        
                        if ($seasonDetail && !empty($seasonDetail['episodes'])) {
                            foreach ($seasonDetail['episodes'] as $episodeData) {
                                // Descargar imagen del episodio
                                $stillPath = null;
                                if (!empty($episodeData['still_path'])) {
                                    $stillPath = $this->storeImage($episodeData['still_path'], 'w300', 'episodes');
                                }
                                
                                // Crear o actualizar episodio
                                Episode::updateOrCreate(
                                    [
                                        'tv_show_id' => $tvShow->id,
                                        'season_id' => $season->id,
                                        'episode_number' => $episodeData['episode_number'],
                                    ],
                                    [
                                        'name' => $episodeData['name'],
                                        'overview' => $episodeData['overview'],
                                        'still_path' => $stillPath,
                                        'runtime' => $episodeData['runtime'] ?? null,
                                        'air_date' => $episodeData['air_date'] ?? null,
                                        'api_id' => $episodeData['id'],
                                    ]
                                );
                            }
                        }
                    }
                }
                
                // Guardar reparto
                if (!empty($tvDetail['credits']['cast'])) {
                    foreach (array_slice($tvDetail['credits']['cast'], 0, 20) as $index => $castData) {
                        $person = $this->syncPerson($castData['id']);
                        
                        if ($person) {
                            TvShowCast::updateOrCreate(
                                ['tv_show_id' => $tvShow->id, 'person_id' => $person->id],
                                [
                                    'character' => $castData['character'],
                                    'order' => $index,
                                ]
                            );
                        }
                    }
                }
                
                // Guardar equipo técnico
                if (!empty($tvDetail['credits']['crew'])) {
                    // Filtrar roles importantes
                    $keyRoles = ['Creator', 'Executive Producer', 'Director', 'Writer'];
                    $keyCrewData = array_filter($tvDetail['credits']['crew'], function($crew) use ($keyRoles) {
                        return in_array($crew['job'], $keyRoles);
                    });
                    
                    foreach ($keyCrewData as $crewData) {
                        $person = $this->syncPerson($crewData['id']);
                        
                        if ($person) {
                            TvShowCrew::updateOrCreate(
                                [
                                    'tv_show_id' => $tvShow->id, 
                                    'person_id' => $person->id,
                                    'job' => $crewData['job'],
                                ],
                                [
                                    'department' => $crewData['department'],
                                ]
                            );
                        }
                    }
                }
                
                $processedCount++;
            }
        }
        
        return $processedCount;
    }
    
    /**
     * Sincronizar persona/actor
     */
    protected function syncPerson($personId)
    {
        $personDetail = $this->request('/person/' . $personId);
        
        if (!$personDetail) {
            return null;
        }
        
        // Descargar imagen de perfil
        $profilePath = null;
        if (!empty($personDetail['profile_path'])) {
            $profilePath = $this->storeImage($personDetail['profile_path'], 'w300', 'profiles');
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