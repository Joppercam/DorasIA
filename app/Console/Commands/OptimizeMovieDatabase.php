<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Genre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeMovieDatabase extends Command
{
    protected $signature = 'movies:optimize
                            {--cleanup : Limpiar registros duplicados}
                            {--reindex : Reindexar base de datos}
                            {--stats : Mostrar estadísticas}';

    protected $description = 'Optimizar y mantener la base de datos de películas';

    public function handle()
    {
        $this->info('🔧 Iniciando optimización de la base de datos de películas...');

        if ($this->option('cleanup')) {
            $this->cleanupDuplicates();
        }

        if ($this->option('reindex')) {
            $this->reindexDatabase();
        }

        if ($this->option('stats') || (!$this->option('cleanup') && !$this->option('reindex'))) {
            $this->showStats();
        }

        $this->info('✅ Optimización completada');
        return 0;
    }

    private function cleanupDuplicates()
    {
        $this->info('🧹 Limpiando registros duplicados...');

        // Limpiar películas duplicadas por TMDB ID
        $duplicatedMovies = Movie::select('tmdb_id')
            ->whereNotNull('tmdb_id')
            ->groupBy('tmdb_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicatedMovies->count() > 0) {
            $this->warn("Encontradas {$duplicatedMovies->count()} películas duplicadas");
            
            foreach ($duplicatedMovies as $duplicate) {
                $movies = Movie::where('tmdb_id', $duplicate->tmdb_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mantener la primera, eliminar las demás
                $toKeep = $movies->first();
                $toDelete = $movies->skip(1);

                foreach ($toDelete as $movie) {
                    $this->line("Eliminando película duplicada: {$movie->title}");
                    $movie->delete();
                }
            }
        }

        // Limpiar personas duplicadas por TMDB ID
        $duplicatedPersons = Person::select('tmdb_id')
            ->whereNotNull('tmdb_id')
            ->groupBy('tmdb_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicatedPersons->count() > 0) {
            $this->warn("Encontradas {$duplicatedPersons->count()} personas duplicadas");
            
            foreach ($duplicatedPersons as $duplicate) {
                $persons = Person::where('tmdb_id', $duplicate->tmdb_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $toKeep = $persons->first();
                $toDelete = $persons->skip(1);

                foreach ($toDelete as $person) {
                    $this->line("Eliminando persona duplicada: {$person->name}");
                    
                    // Reasignar relaciones al registro que mantenemos
                    DB::table('movie_person')
                        ->where('person_id', $person->id)
                        ->update(['person_id' => $toKeep->id]);
                    
                    $person->delete();
                }
            }
        }

        $this->info('✅ Limpieza de duplicados completada');
    }

    private function reindexDatabase()
    {
        $this->info('📊 Reindexando base de datos...');
        
        try {
            // SQLite no tiene comandos OPTIMIZE/REINDEX específicos como MySQL
            // Pero podemos ejecutar VACUUM para optimizar
            DB::statement('VACUUM');
            DB::statement('ANALYZE');
            
            $this->info('✅ Base de datos optimizada');
        } catch (\Exception $e) {
            $this->error('Error optimizando base de datos: ' . $e->getMessage());
        }
    }

    private function showStats()
    {
        $this->info('📈 Estadísticas de la base de datos de películas:');
        $this->newLine();

        // Estadísticas de películas
        $totalMovies = Movie::count();
        $moviesWithPosters = Movie::whereNotNull('poster_path')->count();
        $moviesWithSpanishTitle = Movie::whereNotNull('display_title')->count();
        $avgRating = Movie::avg('vote_average');
        $topRatedMovie = Movie::orderBy('vote_average', 'desc')->first();

        $this->table(['Métrica', 'Valor'], [
            ['Total de Películas', number_format($totalMovies)],
            ['Con Poster', number_format($moviesWithPosters) . ' (' . round(($moviesWithPosters/$totalMovies)*100, 1) . '%)'],
            ['Con Título en Español', number_format($moviesWithSpanishTitle) . ' (' . round(($moviesWithSpanishTitle/$totalMovies)*100, 1) . '%)'],
            ['Calificación Promedio', round($avgRating, 2) . '/10'],
            ['Mejor Calificada', $topRatedMovie ? "{$topRatedMovie->display_title} ({$topRatedMovie->vote_average}/10)" : 'N/A'],
        ]);

        // Estadísticas de géneros
        $totalGenres = Genre::count();
        $genresWithMovies = Genre::whereHas('movies')->count();
        
        $this->newLine();
        $this->info('🎭 Géneros:');
        $this->table(['Métrica', 'Valor'], [
            ['Total de Géneros', $totalGenres],
            ['Géneros con Películas', $genresWithMovies],
        ]);

        // Top 5 géneros por cantidad de películas
        $topGenres = Genre::withCount('movies')
            ->whereHas('movies')
            ->orderBy('movies_count', 'desc')
            ->limit(5)
            ->get();

        if ($topGenres->count() > 0) {
            $this->newLine();
            $this->info('🏆 Top 5 Géneros por Cantidad de Películas:');
            $genreData = [];
            foreach ($topGenres as $genre) {
                $genreData[] = [
                    $genre->display_name ?: $genre->name,
                    $genre->movies_count . ' películas'
                ];
            }
            $this->table(['Género', 'Cantidad'], $genreData);
        }

        // Estadísticas de personas
        $totalPersons = Person::count();
        $personsWithMovies = Person::whereHas('movies')->count();
        
        $this->newLine();
        $this->info('👥 Personas (Actores/Director/etc):');
        $this->table(['Métrica', 'Valor'], [
            ['Total de Personas', number_format($totalPersons)],
            ['Con Películas Asociadas', number_format($personsWithMovies)],
        ]);

        // Distribución por años
        $moviesByYear = Movie::selectRaw('strftime("%Y", release_date) as year, COUNT(*) as count')
            ->whereNotNull('release_date')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit(5)
            ->get();

        if ($moviesByYear->count() > 0) {
            $this->newLine();
            $this->info('📅 Películas por Año (Top 5):');
            $yearData = [];
            foreach ($moviesByYear as $year) {
                $yearData[] = [$year->year, $year->count . ' películas'];
            }
            $this->table(['Año', 'Cantidad'], $yearData);
        }
    }
}