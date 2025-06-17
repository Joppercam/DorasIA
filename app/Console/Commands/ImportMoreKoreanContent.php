<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Person;
use App\Models\Genre;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use OpenAI\Laravel\Facades\OpenAI;

class ImportMoreKoreanContent extends Command
{
    protected $signature = 'import:more-korean-content 
                            {--movies=50 : Número de películas a importar}
                            {--series=30 : Número de series a importar}';

    protected $description = 'Importar más contenido coreano con información en español y actores';

    private TmdbService $tmdb;
    private $tmdbApiKey;
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
        $this->tmdbApiKey = config('tmdb.api_key');
    }

    public function handle()
    {
        if (!$this->tmdbApiKey) {
            $this->error('❌ TMDB_API_KEY no configurado');
            return 1;
        }

        $moviesLimit = (int) $this->option('movies');
        $seriesLimit = (int) $this->option('series');

        $this->info("🎬 Importando contenido coreano con información en español");
        $this->info("📊 Películas: {$moviesLimit} | Series: {$seriesLimit}");

        try {
            // Importar más películas
            $this->info("\n🎬 === IMPORTANDO PELÍCULAS ===");
            $moviesImported = $this->importMoreMovies($moviesLimit);
            
            // Importar más series
            $this->info("\n📺 === IMPORTANDO SERIES ===");
            $seriesImported = $this->importMoreSeries($seriesLimit);

            $this->info("\n✅ === RESUMEN ===");
            $this->info("🎬 Películas importadas: {$moviesImported}");
            $this->info("📺 Series importadas: {$seriesImported}");

            return 0;

        } catch (Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function importMoreMovies($limit)
    {
        $imported = 0;
        $page = 1;

        $progressBar = $this->output->createProgressBar($limit);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% - %message%');

        while ($imported < $limit) {
            $progressBar->setMessage("Obteniendo página {$page}...");

            // Obtener películas coreanas populares
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/discover/movie", [
                'api_key' => $this->tmdbApiKey,
                'with_origin_country' => 'KR',
                'sort_by' => 'popularity.desc',
                'page' => $page,
                'vote_count.gte' => 5
            ]);

            if (!$response->successful()) {
                $this->warn("Error en página {$page}");
                break;
            }

            $movies = $response->json()['results'] ?? [];
            if (empty($movies)) break;

            foreach ($movies as $movieData) {
                if ($imported >= $limit) break;

                $progressBar->setMessage("Procesando: " . ($movieData['title'] ?? 'Sin título'));

                if ($this->importSingleMovie($movieData)) {
                    $imported++;
                }

                $progressBar->advance();
                usleep(100000); // 0.1 segundos
            }

            $page++;
        }

        $progressBar->finish();
        $this->newLine();

        return $imported;
    }

    private function importMoreSeries($limit)
    {
        $imported = 0;
        $page = 1;

        $progressBar = $this->output->createProgressBar($limit);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% - %message%');

        while ($imported < $limit) {
            $progressBar->setMessage("Obteniendo página {$page}...");

            // Obtener series coreanas populares
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/discover/tv", [
                'api_key' => $this->tmdbApiKey,
                'with_origin_country' => 'KR',
                'sort_by' => 'popularity.desc',
                'page' => $page,
                'vote_count.gte' => 5
            ]);

            if (!$response->successful()) {
                $this->warn("Error en página {$page}");
                break;
            }

            $series = $response->json()['results'] ?? [];
            if (empty($series)) break;

            foreach ($series as $seriesData) {
                if ($imported >= $limit) break;

                $progressBar->setMessage("Procesando: " . ($seriesData['name'] ?? 'Sin título'));

                if ($this->importSingleSeries($seriesData)) {
                    $imported++;
                }

                $progressBar->advance();
                usleep(100000); // 0.1 segundos
            }

            $page++;
        }

        $progressBar->finish();
        $this->newLine();

        return $imported;
    }

    private function importSingleMovie($movieData)
    {
        try {
            $tmdbId = $movieData['id'];

            // Verificar si ya existe
            if (Movie::where('tmdb_id', $tmdbId)->exists()) {
                return false;
            }

            // Obtener detalles completos
            $details = $this->getMovieDetails($tmdbId);
            if (!$details) return false;

            // Obtener traducción al español
            $spanishData = $this->getMovieSpanishTranslation($tmdbId);

            // Crear película
            $movie = Movie::create([
                'title' => $details['title'],
                'title_es' => $spanishData['title'] ?? null,
                'spanish_title' => $this->translateTitleToSpanish($details['title']),
                'original_title' => $details['original_title'] ?? $details['title'],
                'overview' => $details['overview'],
                'overview_es' => $spanishData['overview'] ?? null,
                'spanish_overview' => $this->translateOverviewToSpanish($details['overview']),
                'poster_path' => $details['poster_path'],
                'backdrop_path' => $details['backdrop_path'],
                'release_date' => $details['release_date'] ? date('Y-m-d', strtotime($details['release_date'])) : null,
                'runtime' => $details['runtime'],
                'vote_average' => $details['vote_average'] ?? 0,
                'vote_count' => $details['vote_count'] ?? 0,
                'status' => strtolower($details['status'] ?? 'released'),
                'original_language' => 'ko',
                'tmdb_id' => $tmdbId,
                'imdb_id' => $details['imdb_id'],
                'budget' => $details['budget'] ?? 0,
                'revenue' => $details['revenue'] ?? 0,
                'tagline' => $spanishData['tagline'] ?? $details['tagline'],
                'adult' => $details['adult'] ?? false,
                'popularity' => $details['popularity'] ?? 0
            ]);

            // Importar géneros
            $this->importMovieGenres($movie, $details['genres'] ?? []);

            // Importar reparto
            $this->importMovieCredits($movie, $tmdbId);

            return true;

        } catch (Exception $e) {
            Log::warning('Error importando película', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function importSingleSeries($seriesData)
    {
        try {
            $tmdbId = $seriesData['id'];

            // Verificar si ya existe
            if (Series::where('tmdb_id', $tmdbId)->exists()) {
                return false;
            }

            // Obtener detalles completos
            $details = $this->getSeriesDetails($tmdbId);
            if (!$details) return false;

            // Obtener traducción al español
            $spanishData = $this->getSeriesSpanishTranslation($tmdbId);

            // Crear serie
            $series = Series::create([
                'title' => $details['name'],
                'title_es' => $spanishData['name'] ?? null,
                'spanish_title' => $this->translateTitleToSpanish($details['name']),
                'original_title' => $details['original_name'] ?? $details['name'],
                'overview' => $details['overview'],
                'overview_es' => $spanishData['overview'] ?? null,
                'spanish_overview' => $this->translateOverviewToSpanish($details['overview']),
                'poster_path' => $details['poster_path'],
                'backdrop_path' => $details['backdrop_path'],
                'first_air_date' => $details['first_air_date'],
                'last_air_date' => $details['last_air_date'],
                'vote_average' => $details['vote_average'] ?? 0,
                'vote_count' => $details['vote_count'] ?? 0,
                'status' => $details['status'],
                'original_language' => 'ko',
                'tmdb_id' => $tmdbId,
                'imdb_id' => $details['external_ids']['imdb_id'] ?? null,
                'number_of_seasons' => $details['number_of_seasons'] ?? 0,
                'number_of_episodes' => $details['number_of_episodes'] ?? 0,
                'episode_run_time' => !empty($details['episode_run_time']) ? $details['episode_run_time'][0] : null,
                'popularity' => $details['popularity'] ?? 0,
                'is_korean_drama' => true
            ]);

            // Importar géneros
            $this->importSeriesGenres($series, $details['genres'] ?? []);

            // Importar reparto
            $this->importSeriesCredits($series, $tmdbId);

            return true;

        } catch (Exception $e) {
            Log::warning('Error importando serie', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function getMovieDetails($tmdbId)
    {
        $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}", [
            'api_key' => $this->tmdbApiKey,
            'language' => 'ko-KR'
        ]);

        return $response->successful() ? $response->json() : null;
    }

    private function getSeriesDetails($tmdbId)
    {
        $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/tv/{$tmdbId}", [
            'api_key' => $this->tmdbApiKey,
            'language' => 'ko-KR',
            'append_to_response' => 'external_ids'
        ]);

        return $response->successful() ? $response->json() : null;
    }

    private function getMovieSpanishTranslation($tmdbId)
    {
        $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}", [
            'api_key' => $this->tmdbApiKey,
            'language' => 'es-ES'
        ]);

        return $response->successful() ? $response->json() : [];
    }

    private function getSeriesSpanishTranslation($tmdbId)
    {
        $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/tv/{$tmdbId}", [
            'api_key' => $this->tmdbApiKey,
            'language' => 'es-ES'
        ]);

        return $response->successful() ? $response->json() : [];
    }

    private function translateTitleToSpanish($title)
    {
        if (!$title || strlen($title) < 3) return $title;

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional especializado en títulos de películas y series coreanas. Traduce únicamente el título al español de manera natural y comercial. Responde SOLO con el título traducido, sin explicaciones.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Título coreano: {$title}"
                    ]
                ],
                'max_tokens' => 100,
                'temperature' => 0.3
            ]);

            return trim($response['choices'][0]['message']['content']);

        } catch (Exception $e) {
            return $title;
        }
    }

    private function translateOverviewToSpanish($overview)
    {
        if (!$overview || strlen($overview) < 10) return $overview;

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional de sinopsis de películas y series coreanas. Traduce la sinopsis al español de manera fluida y natural, manteniendo el tono y estilo original. Responde SOLO con la traducción.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Sinopsis: {$overview}"
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.4
            ]);

            return trim($response['choices'][0]['message']['content']);

        } catch (Exception $e) {
            return $overview;
        }
    }

    private function importMovieGenres($movie, $genresData)
    {
        $genreIds = [];
        
        foreach ($genresData as $genreData) {
            $genre = Genre::firstOrCreate(
                ['tmdb_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'display_name' => $this->translateGenre($genreData['name'])
                ]
            );
            
            $genreIds[] = $genre->id;
        }

        $movie->genres()->sync($genreIds);
    }

    private function importSeriesGenres($series, $genresData)
    {
        $genreIds = [];
        
        foreach ($genresData as $genreData) {
            $genre = Genre::firstOrCreate(
                ['tmdb_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'display_name' => $this->translateGenre($genreData['name'])
                ]
            );
            
            $genreIds[] = $genre->id;
        }

        $series->genres()->sync($genreIds);
    }

    private function translateGenre($genreName)
    {
        $translations = [
            'Action' => 'Acción',
            'Adventure' => 'Aventura',
            'Animation' => 'Animación',
            'Comedy' => 'Comedia',
            'Crime' => 'Crimen',
            'Documentary' => 'Documental',
            'Drama' => 'Drama',
            'Family' => 'Familia',
            'Fantasy' => 'Fantasía',
            'History' => 'Historia',
            'Horror' => 'Terror',
            'Music' => 'Música',
            'Mystery' => 'Misterio',
            'Romance' => 'Romance',
            'Science Fiction' => 'Ciencia Ficción',
            'TV Movie' => 'Película de TV',
            'Thriller' => 'Thriller',
            'War' => 'Guerra',
            'Western' => 'Western',
            'News' => 'Noticias',
            'Reality' => 'Reality',
            'Talk' => 'Talk Show'
        ];

        return $translations[$genreName] ?? $genreName;
    }

    private function importMovieCredits($movie, $tmdbId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}/credits", [
                'api_key' => $this->tmdbApiKey
            ]);

            if (!$response->successful()) return;

            $credits = $response->json();

            // Importar reparto principal (máximo 15 actores)
            foreach (array_slice($credits['cast'] ?? [], 0, 15) as $index => $castMember) {
                $person = $this->getOrCreatePerson($castMember);
                
                $movie->people()->syncWithoutDetaching([
                    $person->id => [
                        'character' => $castMember['character'] ?? null,
                        'department' => 'Acting',
                        'job' => null,
                        'order' => $castMember['order'] ?? $index
                    ]
                ]);
            }

            // Importar crew principal
            foreach (($credits['crew'] ?? []) as $crewMember) {
                if (!in_array($crewMember['department'] ?? '', ['Directing', 'Writing'])) {
                    continue;
                }

                $person = $this->getOrCreatePerson($crewMember);
                
                $movie->people()->syncWithoutDetaching([
                    $person->id => [
                        'character' => null,
                        'department' => $crewMember['department'],
                        'job' => $crewMember['job'] ?? null,
                        'order' => 0
                    ]
                ]);
            }

        } catch (Exception $e) {
            Log::warning('Error importando créditos de película', ['error' => $e->getMessage()]);
        }
    }

    private function importSeriesCredits($series, $tmdbId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/tv/{$tmdbId}/credits", [
                'api_key' => $this->tmdbApiKey
            ]);

            if (!$response->successful()) return;

            $credits = $response->json();

            // Importar reparto principal (máximo 15 actores)
            foreach (array_slice($credits['cast'] ?? [], 0, 15) as $index => $castMember) {
                $person = $this->getOrCreatePerson($castMember);
                
                $series->people()->syncWithoutDetaching([
                    $person->id => [
                        'role' => 'actor',
                        'character' => $castMember['character'] ?? null,
                        'department' => 'Acting',
                        'job' => 'Actor',
                        'order' => $castMember['order'] ?? $index
                    ]
                ]);
            }

            // Importar crew principal
            foreach (($credits['crew'] ?? []) as $crewMember) {
                if (!in_array($crewMember['department'] ?? '', ['Directing', 'Writing'])) {
                    continue;
                }

                $person = $this->getOrCreatePerson($crewMember);
                
                $series->people()->syncWithoutDetaching([
                    $person->id => [
                        'role' => strtolower($crewMember['job'] ?? 'crew'),
                        'character' => null,
                        'department' => $crewMember['department'],
                        'job' => $crewMember['job'] ?? null,
                        'order' => 0
                    ]
                ]);
            }

        } catch (Exception $e) {
            Log::warning('Error importando créditos de serie', ['error' => $e->getMessage()]);
        }
    }

    private function getOrCreatePerson($personData)
    {
        return Person::firstOrCreate(
            ['tmdb_id' => $personData['id']],
            [
                'name' => $personData['name'],
                'profile_path' => $personData['profile_path'],
                'gender' => $personData['gender'] ?? 0,
                'known_for_department' => $personData['known_for_department'] ?? 'Acting',
                'popularity' => $personData['popularity'] ?? 0
            ]
        );
    }
}