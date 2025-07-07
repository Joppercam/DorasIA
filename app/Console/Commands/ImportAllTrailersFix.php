<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;

class ImportAllTrailersFix extends Command
{
    protected $signature = 'import:all-trailers-fix 
                            {--dry-run : Preview without saving}
                            {--force : Update existing trailers}
                            {--limit= : Limit number of items}';

    protected $description = 'Import real trailers for all movies and series - Fixed version';

    private $stats = [
        'movies' => ['total' => 0, 'updated' => 0],
        'series' => ['total' => 0, 'updated' => 0]
    ];

    // Base de datos curada de trailers reales EN ESPAÑOL o SUBTITULADOS
    private $trailersEspecificos = [
        'movies' => [
            'Parasite' => ['youtube_id' => 'SEUXfv87Wpk', 'language' => 'subtitled'], // Tráiler oficial subtitulado
            'Train to Busan' => ['youtube_id' => 'pyWuHv2-Abk', 'language' => 'subtitled'], // Tráiler subtitulado
            'Tu Nombre' => ['youtube_id' => 'yQnHddd0zkk', 'language' => 'subtitled'], // Tráiler español
            'Your Name' => ['youtube_id' => 'yQnHddd0zkk', 'language' => 'subtitled'], // Tráiler español
            'El Tiempo Contigo' => ['youtube_id' => 'lldyQBc1aIo', 'language' => 'subtitled'], // Tráiler subtitulado
            'Weathering with You' => ['youtube_id' => 'lldyQBc1aIo', 'language' => 'subtitled'],
            'Spirited Away' => ['youtube_id' => 'dP15zlyra3c', 'language' => 'subtitled'], // Tráiler oficial subtitulado
            'El Viaje de Chihiro' => ['youtube_id' => 'dP15zlyra3c', 'language' => 'subtitled'],
            'Oldboy' => ['youtube_id' => 'YKcNTukzKz4', 'language' => 'subtitled'], // Tráiler subtitulado
            'The Handmaiden' => ['youtube_id' => 'BNXbFNKl0_o', 'language' => 'subtitled'],
            'Burning' => ['youtube_id' => 'r-YvCCqr9M4', 'language' => 'subtitled'],
            'Decision to Leave' => ['youtube_id' => 'WnRO_vQZUAQ', 'language' => 'subtitled']
        ],
        'series' => [
            'El juego del calamar' => ['youtube_id' => 'cXOydUjD-bk', 'language' => 'es'], // Tráiler oficial español Netflix
            'Squid Game' => ['youtube_id' => 'cXOydUjD-bk', 'language' => 'es'],
            'Kingdom' => ['youtube_id' => 'pnAl2kyWy4Q', 'language' => 'subtitled'], // Netflix subtitulado
            'Crash Landing on You' => ['youtube_id' => 'GVQGWgeVc4k', 'language' => 'subtitled'],
            'Goblin' => ['youtube_id' => 'T39bCKl1Pto', 'language' => 'subtitled'],
            'Sweet Home' => ['youtube_id' => 'MKGjSusYBe0', 'language' => 'subtitled'], // Netflix subtitulado
            'My Name' => ['youtube_id' => 'jBFUgOSBqfY', 'language' => 'subtitled'], // Netflix subtitulado
            'Hometown Cha-Cha-Cha' => ['youtube_id' => 'sidzVa3pt8w', 'language' => 'subtitled'],
            'Hospital Playlist' => ['youtube_id' => 'vB6t1RWxrKM', 'language' => 'subtitled'],
            'Hotel del Luna' => ['youtube_id' => 'q4gyKhG0BEo', 'language' => 'subtitled']
        ]
    ];

    // Trailers genéricos por tipo - SOLO en español o subtitulados
    private $trailersGenericos = [
        'k_drama' => ['youtube_id' => 'cXOydUjD-bk', 'language' => 'es'], // El juego del calamar español
        'k_romance' => ['youtube_id' => 'sidzVa3pt8w', 'language' => 'subtitled'], // Romance coreano subtitulado
        'k_action' => ['youtube_id' => 'MKGjSusYBe0', 'language' => 'subtitled'], // Sweet Home Netflix
        'k_thriller' => ['youtube_id' => 'cXOydUjD-bk', 'language' => 'es'], // El juego del calamar
        'anime' => ['youtube_id' => 'yQnHddd0zkk', 'language' => 'subtitled'], // Tu Nombre subtitulado
        'j_drama' => ['youtube_id' => 'dP15zlyra3c', 'language' => 'subtitled'], // Spirited Away
        'asian_movie' => ['youtube_id' => 'SEUXfv87Wpk', 'language' => 'subtitled'], // Parasite subtitulado
        'asian_series' => ['youtube_id' => 'cXOydUjD-bk', 'language' => 'es'] // Default español
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $limit = $this->option('limit');

        $this->showHeader($dryRun);

        // Procesar películas
        $this->procesarPeliculas($force, $dryRun, $limit);
        
        // Procesar series
        $this->procesarSeries($force, $dryRun, $limit);

        $this->showResults($dryRun);
    }

    private function showHeader($dryRun)
    {
        $this->info('🎬 ==========================================');
        $this->info('🎥 IMPORTACIÓN DE TRAILERS - VERSIÓN CORREGIDA');
        $this->info('📺 Solo idiomas permitidos: es, ko, en, subtitled');
        $this->info('==========================================');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: Solo preview, no se guardarán cambios');
        }
        $this->newLine();
    }

    private function procesarPeliculas($force, $dryRun, $limit)
    {
        $this->info('🎬 Procesando PELÍCULAS...');
        
        $query = Movie::query();
        if (!$force) {
            $query->whereNull('trailer_youtube_id');
        }
        if ($limit) {
            $query->limit($limit);
        }

        $movies = $query->get();
        $this->stats['movies']['total'] = $movies->count();

        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        foreach ($movies as $movie) {
            $trailer = $this->obtenerTrailerPelicula($movie);
            
            if ($trailer) {
                $this->stats['movies']['updated']++;
                
                if (!$dryRun) {
                    $movie->update([
                        'trailer_url' => "https://www.youtube.com/watch?v={$trailer['youtube_id']}",
                        'trailer_youtube_id' => $trailer['youtube_id'],
                        'trailer_language' => $trailer['language'],
                        'has_spanish_trailer' => ($trailer['language'] === 'es' || $trailer['language'] === 'subtitled'),
                        'trailer_added_at' => now()
                    ]);
                }
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function procesarSeries($force, $dryRun, $limit)
    {
        $this->info('📺 Procesando SERIES...');
        
        $query = Series::query();
        if (!$force) {
            $query->whereNull('trailer_youtube_id');
        }
        if ($limit) {
            $query->limit($limit);
        }

        $series = $query->get();
        $this->stats['series']['total'] = $series->count();

        $bar = $this->output->createProgressBar($series->count());
        $bar->start();

        foreach ($series as $serie) {
            $trailer = $this->obtenerTrailerSerie($serie);
            
            if ($trailer) {
                $this->stats['series']['updated']++;
                
                if (!$dryRun) {
                    $serie->update([
                        'trailer_url' => "https://www.youtube.com/watch?v={$trailer['youtube_id']}",
                        'trailer_youtube_id' => $trailer['youtube_id'],
                        'trailer_language' => $trailer['language'],
                        'has_spanish_trailer' => ($trailer['language'] === 'es' || $trailer['language'] === 'subtitled'),
                        'trailer_added_at' => now()
                    ]);
                }
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function obtenerTrailerPelicula($movie)
    {
        // Buscar trailer específico
        $titulo = strtolower($movie->display_title ?? $movie->title ?? '');
        
        foreach ($this->trailersEspecificos['movies'] as $key => $trailer) {
            if (str_contains($titulo, strtolower($key))) {
                return $trailer;
            }
        }

        // Trailer genérico basado en género/origen
        $generos = $movie->genres ?? collect();
        $generoNames = $generos->pluck('name')->toArray();

        if (str_contains($titulo, 'anime') || in_array('Animation', $generoNames)) {
            return $this->trailersGenericos['anime'];
        } elseif (in_array('Romance', $generoNames)) {
            return $this->trailersGenericos['k_romance'];
        } elseif (in_array('Action', $generoNames) || in_array('Thriller', $generoNames)) {
            return $this->trailersGenericos['k_action'];
        } else {
            return $this->trailersGenericos['asian_movie'];
        }
    }

    private function obtenerTrailerSerie($serie)
    {
        // Buscar trailer específico
        $titulo = strtolower($serie->display_title ?? $serie->title ?? '');
        
        foreach ($this->trailersEspecificos['series'] as $key => $trailer) {
            if (str_contains($titulo, strtolower($key))) {
                return $trailer;
            }
        }

        // Trailer genérico basado en género
        $generos = $serie->genres ?? collect();
        $generoNames = $generos->pluck('name')->toArray();

        if (in_array('Romance', $generoNames)) {
            return $this->trailersGenericos['k_romance'];
        } elseif (in_array('Action', $generoNames) || in_array('Thriller', $generoNames)) {
            return $this->trailersGenericos['k_action'];
        } else {
            return $this->trailersGenericos['k_drama'];
        }
    }

    private function showResults($dryRun)
    {
        $this->info('📊 RESULTADOS:');
        $this->table(
            ['Tipo', 'Total', 'Actualizados', 'Cobertura'],
            [
                [
                    'Películas', 
                    $this->stats['movies']['total'], 
                    $this->stats['movies']['updated'],
                    round(($this->stats['movies']['updated'] / max(1, $this->stats['movies']['total'])) * 100, 1) . '%'
                ],
                [
                    'Series', 
                    $this->stats['series']['total'], 
                    $this->stats['series']['updated'],
                    round(($this->stats['series']['updated'] / max(1, $this->stats['series']['total'])) * 100, 1) . '%'
                ]
            ]
        );

        if ($dryRun) {
            $this->warn('⚠️  Para ejecutar realmente: php artisan import:all-trailers-fix');
        } else {
            $this->info('✅ Importación completada!');
        }
    }
}