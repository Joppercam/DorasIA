<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Genre;

class AssignMissingGenres extends Command
{
    protected $signature = 'fix:missing-genres';
    protected $description = 'Assign genres to movies and series that don\'t have any';

    public function handle()
    {
        $this->info('🎭 Asignando géneros faltantes...');

        $this->assignGenresToSeries();
        $this->assignGenresToMovies();

        $this->info('✅ Géneros asignados exitosamente');
        return 0;
    }

    private function assignGenresToSeries()
    {
        $seriesWithoutGenres = Series::whereDoesntHave('genres')->get();
        
        if ($seriesWithoutGenres->count() === 0) {
            $this->info('📺 Todas las series ya tienen géneros asignados');
            return;
        }

        $this->info("📺 Asignando géneros a {$seriesWithoutGenres->count()} series...");

        // Get some common K-drama genres
        $dramaGenre = Genre::firstOrCreate(['name' => 'Drama'], ['name' => 'Drama']);
        $romanceGenre = Genre::firstOrCreate(['name' => 'Romance'], ['name' => 'Romance']);
        $comedyGenre = Genre::firstOrCreate(['name' => 'Comedy'], ['name' => 'Comedy']);
        $actionGenre = Genre::firstOrCreate(['name' => 'Action & Adventure'], ['name' => 'Action & Adventure']);
        $crimeGenre = Genre::firstOrCreate(['name' => 'Crime'], ['name' => 'Crime']);
        $mysteryGenre = Genre::firstOrCreate(['name' => 'Mystery'], ['name' => 'Mystery']);
        $familyGenre = Genre::firstOrCreate(['name' => 'Family'], ['name' => 'Family']);

        $defaultGenresByTitle = [
            // Romance dramas
            'Amor en la puerta de al lado' => [$dramaGenre->id, $romanceGenre->id],
            'Corredor Adorable' => [$dramaGenre->id, $romanceGenre->id, $comedyGenre->id],
            'Romance en Oficina' => [$dramaGenre->id, $romanceGenre->id],
            'Mi Primer Primer Amor' => [$dramaGenre->id, $romanceGenre->id],
            'Moonshine' => [$dramaGenre->id, $romanceGenre->id],
            
            // Thriller/Mystery
            'Dr. Brain' => [$dramaGenre->id, $mysteryGenre->id],
            'Light Shop' => [$dramaGenre->id, $mysteryGenre->id],
            
            // Family/Drama
            'La buena mala madre' => [$dramaGenre->id, $familyGenre->id],
            'Dear My Friends' => [$dramaGenre->id, $familyGenre->id],
        ];

        foreach ($seriesWithoutGenres as $series) {
            $genreIds = [];
            
            // Check if we have specific genres for this title
            if (isset($defaultGenresByTitle[$series->title])) {
                $genreIds = $defaultGenresByTitle[$series->title];
            } else {
                // Default assignment based on title analysis
                $title = strtolower($series->title . ' ' . ($series->original_title ?? ''));
                
                // Always assign Drama as it's the most common K-drama genre
                $genreIds[] = $dramaGenre->id;
                
                // Assign additional genres based on keywords
                if (str_contains($title, 'amor') || str_contains($title, 'love') || str_contains($title, 'romance')) {
                    $genreIds[] = $romanceGenre->id;
                }
                
                if (str_contains($title, 'familia') || str_contains($title, 'family') || str_contains($title, 'madre') || str_contains($title, 'padre')) {
                    $genreIds[] = $familyGenre->id;
                }
                
                if (str_contains($title, 'misterio') || str_contains($title, 'mystery') || str_contains($title, 'crime') || str_contains($title, 'detective')) {
                    $genreIds[] = $mysteryGenre->id;
                }
                
                if (str_contains($title, 'acción') || str_contains($title, 'action') || str_contains($title, 'fight') || str_contains($title, 'guerra')) {
                    $genreIds[] = $actionGenre->id;
                }
                
                // If only drama was assigned, add romance as second most common
                if (count($genreIds) === 1) {
                    $genreIds[] = $romanceGenre->id;
                }
            }
            
            // Remove duplicates and attach genres
            $genreIds = array_unique($genreIds);
            $series->genres()->sync($genreIds);
            
            $this->info("  ✓ {$series->title} - " . count($genreIds) . " géneros asignados");
        }

        $this->info("📺 Series procesadas: {$seriesWithoutGenres->count()}");
    }

    private function assignGenresToMovies()
    {
        $moviesWithoutGenres = Movie::whereDoesntHave('genres')->get();
        
        if ($moviesWithoutGenres->count() === 0) {
            $this->info('🎬 Todas las películas ya tienen géneros asignados');
            return;
        }

        $this->info("🎬 Asignando géneros a {$moviesWithoutGenres->count()} películas...");

        // Get common movie genres
        $dramaGenre = Genre::firstOrCreate(['name' => 'Drama'], ['name' => 'Drama']);
        $actionGenre = Genre::firstOrCreate(['name' => 'Action'], ['name' => 'Action']);
        $thrillerGenre = Genre::firstOrCreate(['name' => 'Thriller'], ['name' => 'Thriller']);
        $romanceGenre = Genre::firstOrCreate(['name' => 'Romance'], ['name' => 'Romance']);
        $comedyGenre = Genre::firstOrCreate(['name' => 'Comedy'], ['name' => 'Comedy']);
        $crimeGenre = Genre::firstOrCreate(['name' => 'Crime'], ['name' => 'Crime']);
        $horrorGenre = Genre::firstOrCreate(['name' => 'Horror'], ['name' => 'Horror']);
        $animationGenre = Genre::firstOrCreate(['name' => 'Animation'], ['name' => 'Animation']);

        $defaultGenresByTitle = [
            // Animation
            'Your Name.' => [$animationGenre->id, $romanceGenre->id, $dramaGenre->id],
            'Weathering with You' => [$animationGenre->id, $romanceGenre->id, $dramaGenre->id],
            'Suzume' => [$animationGenre->id, $dramaGenre->id],
            
            // Romance
            'Amor a Medianoche' => [$romanceGenre->id, $dramaGenre->id],
            'Un Momento Para Recordar' => [$romanceGenre->id, $dramaGenre->id],
            
            // Thriller/Crime
            'Tazza: The High Rollers' => [$thrillerGenre->id, $crimeGenre->id, $dramaGenre->id],
            'El hombre sin pasado' => [$thrillerGenre->id, $actionGenre->id],
            
            // Action
            'Misión Posible' => [$actionGenre->id, $comedyGenre->id],
            'Steel Rain 2' => [$actionGenre->id, $thrillerGenre->id],
        ];

        foreach ($moviesWithoutGenres as $movie) {
            $genreIds = [];
            
            // Check if we have specific genres for this title
            if (isset($defaultGenresByTitle[$movie->title])) {
                $genreIds = $defaultGenresByTitle[$movie->title];
            } else {
                // Default assignment based on title analysis
                $title = strtolower($movie->title . ' ' . ($movie->original_title ?? ''));
                
                // Always assign Drama as base genre
                $genreIds[] = $dramaGenre->id;
                
                // Assign additional genres based on keywords
                if (str_contains($title, 'amor') || str_contains($title, 'love') || str_contains($title, 'romance')) {
                    $genreIds[] = $romanceGenre->id;
                }
                
                if (str_contains($title, 'acción') || str_contains($title, 'action') || str_contains($title, 'fight') || str_contains($title, 'guerra') || str_contains($title, 'battle')) {
                    $genreIds[] = $actionGenre->id;
                }
                
                if (str_contains($title, 'thriller') || str_contains($title, 'suspense') || str_contains($title, 'mystery')) {
                    $genreIds[] = $thrillerGenre->id;
                }
                
                if (str_contains($title, 'comedy') || str_contains($title, 'comedia') || str_contains($title, 'funny')) {
                    $genreIds[] = $comedyGenre->id;
                }
                
                if (str_contains($title, 'horror') || str_contains($title, 'terror') || str_contains($title, 'zombie')) {
                    $genreIds[] = $horrorGenre->id;
                }
                
                if (str_contains($title, 'animation') || str_contains($title, 'anime') || str_contains($title, 'animación')) {
                    $genreIds[] = $animationGenre->id;
                }
                
                if (str_contains($title, 'crime') || str_contains($title, 'criminal') || str_contains($title, 'mafia') || str_contains($title, 'gang')) {
                    $genreIds[] = $crimeGenre->id;
                }
                
                // If only drama was assigned, add action as common second genre
                if (count($genreIds) === 1) {
                    $genreIds[] = $actionGenre->id;
                }
            }
            
            // Remove duplicates and attach genres
            $genreIds = array_unique($genreIds);
            $movie->genres()->sync($genreIds);
            
            $this->info("  ✓ {$movie->title} - " . count($genreIds) . " géneros asignados");
        }

        $this->info("🎬 Películas procesadas: {$moviesWithoutGenres->count()}");
    }
}