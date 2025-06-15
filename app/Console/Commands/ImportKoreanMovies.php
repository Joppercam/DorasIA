<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Genre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportKoreanMovies extends Command
{
    protected $signature = 'movies:import-korean 
                            {--limit=50 : Límite de películas a importar}
                            {--page=1 : Página inicial de TMDB}
                            {--force : Forzar reimport de películas existentes}';

    protected $description = 'Importar películas coreanas desde TMDB con traducción al español';

    private $tmdbApiKey;
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    private $importedCount = 0;
    private $errorCount = 0;

    public function __construct()
    {
        parent::__construct();
        $this->tmdbApiKey = env('TMDB_API_KEY');
    }

    public function handle()
    {
        if (!$this->tmdbApiKey) {
            $this->error('❌ TMDB_API_KEY no configurado en .env');
            return 1;
        }

        $limit = (int) $this->option('limit');
        $startPage = (int) $this->option('page');
        $force = $this->option('force');

        $this->info("🎬 Iniciando importación de películas coreanas");
        $this->info("📊 Límite: {$limit} películas | Página inicial: {$startPage}");
        
        if ($force) {
            $this->warn("⚠️  Modo FORCE activado - Se reimportarán películas existentes");
        }

        $progressBar = $this->output->createProgressBar($limit);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setMessage('Iniciando...');

        try {
            $this->importMovies($limit, $startPage, $force, $progressBar);
            
            $progressBar->finish();
            $this->newLine(2);
            
            $this->info("✅ Importación completada:");
            $this->info("   🎬 Películas importadas: {$this->importedCount}");
            $this->info("   ❌ Errores: {$this->errorCount}");
            
            return 0;
            
        } catch (Exception $e) {
            $progressBar->finish();
            $this->newLine();
            $this->error("❌ Error durante la importación: " . $e->getMessage());
            Log::error('Error importando películas coreanas', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    private function importMovies($limit, $startPage, $force, $progressBar)
    {
        $imported = 0;
        $page = $startPage;
        
        while ($imported < $limit) {
            $progressBar->setMessage("Obteniendo página {$page}...");
            
            // Obtener películas coreanas de TMDB
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/discover/movie", [
                'api_key' => $this->tmdbApiKey,
                'language' => 'ko-KR', // Idioma original coreano
                'with_origin_country' => 'KR', // Solo películas de Corea del Sur
                'sort_by' => 'popularity.desc',
                'page' => $page,
                'vote_count.gte' => 10 // Mínimo 10 votos para filtrar calidad
            ]);

            if (!$response->successful()) {
                $this->error("Error obteniendo página {$page}: " . $response->status());
                break;
            }

            $data = $response->json();
            $movies = $data['results'] ?? [];

            if (empty($movies)) {
                $this->warn("No se encontraron más películas en la página {$page}");
                break;
            }

            foreach ($movies as $movieData) {
                if ($imported >= $limit) break;

                $progressBar->setMessage("Procesando: " . ($movieData['title'] ?? 'Sin título'));
                
                try {
                    $this->importSingleMovie($movieData, $force);
                    $this->importedCount++;
                    $imported++;
                    
                } catch (Exception $e) {
                    $this->errorCount++;
                    Log::warning('Error importando película', [
                        'tmdb_id' => $movieData['id'] ?? 'unknown',
                        'title' => $movieData['title'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
                
                $progressBar->advance();
                
                // Pausa pequeña para no saturar la API
                usleep(100000); // 0.1 segundos
            }

            $page++;
        }
    }

    private function importSingleMovie($movieData, $force)
    {
        $tmdbId = $movieData['id'];
        
        // Verificar si ya existe
        $existingMovie = Movie::where('tmdb_id', $tmdbId)->first();
        if ($existingMovie && !$force) {
            return; // Ya existe y no es modo force
        }

        // Obtener detalles completos de la película
        $detailsResponse = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}", [
            'api_key' => $this->tmdbApiKey,
            'language' => 'ko-KR'
        ]);

        if (!$detailsResponse->successful()) {
            throw new Exception("Error obteniendo detalles de película ID: {$tmdbId}");
        }

        $details = $detailsResponse->json();

        // Obtener traducción al español
        $spanishData = $this->getSpanishTranslation($tmdbId);

        // Crear o actualizar película
        $movieAttributes = [
            'title' => $details['title'],
            'display_title' => $spanishData['title'] ?? $details['title'],
            'overview' => $details['overview'],
            'display_overview' => $spanishData['overview'] ?? $details['overview'],
            'poster_path' => $details['poster_path'],
            'backdrop_path' => $details['backdrop_path'],
            'release_date' => $details['release_date'] ? date('Y-m-d', strtotime($details['release_date'])) : null,
            'runtime' => $details['runtime'],
            'vote_average' => $details['vote_average'] ?? 0,
            'vote_count' => $details['vote_count'] ?? 0,
            'status' => strtolower($details['status'] ?? 'released'),
            'original_language' => $details['original_language'] ?? 'ko',
            'tmdb_id' => $tmdbId,
            'imdb_id' => $details['imdb_id'],
            'budget' => $details['budget'] ?? 0,
            'revenue' => $details['revenue'] ?? 0,
            'production_companies' => $details['production_companies'] ?? [],
            'production_countries' => $details['production_countries'] ?? [],
            'spoken_languages' => $details['spoken_languages'] ?? [],
            'tagline' => $spanishData['tagline'] ?? $details['tagline'],
            'adult' => $details['adult'] ?? false,
            'popularity' => $details['popularity'] ?? 0
        ];

        if ($existingMovie) {
            $existingMovie->update($movieAttributes);
            $movie = $existingMovie;
        } else {
            $movie = Movie::create($movieAttributes);
        }

        // Importar géneros
        $this->importGenres($movie, $details['genres'] ?? []);

        // Importar reparto y crew
        $this->importCredits($movie, $tmdbId);
    }

    private function getSpanishTranslation($tmdbId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}", [
                'api_key' => $this->tmdbApiKey,
                'language' => 'es-ES' // Español
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception $e) {
            // Si falla la traducción, continuar sin ella
        }

        return [];
    }

    private function importGenres($movie, $genresData)
    {
        $genreIds = [];
        
        foreach ($genresData as $genreData) {
            $genre = Genre::firstOrCreate(
                ['tmdb_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'name_es' => $this->translateGenre($genreData['name'])
                ]
            );
            
            $genreIds[] = $genre->id;
        }

        $movie->genres()->sync($genreIds);
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
            'Western' => 'Western'
        ];

        return $translations[$genreName] ?? $genreName;
    }

    private function importCredits($movie, $tmdbId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/movie/{$tmdbId}/credits", [
                'api_key' => $this->tmdbApiKey
            ]);

            if (!$response->successful()) {
                return;
            }

            $credits = $response->json();

            // Importar reparto (actores)
            foreach (($credits['cast'] ?? []) as $index => $castMember) {
                if ($index >= 20) break; // Limitamos a 20 actores principales

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

            // Importar crew (director, guionista, etc.)
            foreach (($credits['crew'] ?? []) as $crewMember) {
                if (!in_array($crewMember['department'] ?? '', ['Directing', 'Writing', 'Production'])) {
                    continue; // Solo importamos roles principales
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
            // Si falla la importación de créditos, continuar
            Log::warning('Error importando créditos', [
                'movie_id' => $movie->id,
                'tmdb_id' => $tmdbId,
                'error' => $e->getMessage()
            ]);
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
                'known_for_department' => $personData['known_for_department'] ?? 'Acting'
            ]
        );
    }
}
