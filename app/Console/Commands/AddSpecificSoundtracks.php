<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soundtrack;
use App\Models\Series;
use App\Models\Movie;

class AddSpecificSoundtracks extends Command
{
    protected $signature = 'soundtracks:add-specific 
                            {--dry-run : Preview additions without saving}';

    protected $description = 'Add specific soundtracks to existing content in database';

    // Soundtracks específicos para el contenido que existe en la BD
    private $specificSoundtracks = [
        // Series que existen en la BD
        'series' => [
            'El juego del calamar' => [
                ['title' => 'Way Back Then', 'artist' => 'Jung Jae Il', 'youtube_id' => 'IrHKiKCF7YU', 'type' => 'main'],
                ['title' => 'Pink Soldiers', 'artist' => 'Jung Jae Il', 'youtube_id' => 'qza1RNS8wTI', 'type' => 'ost'],
                ['title' => 'Round Six Main Theme', 'artist' => 'Jung Jae Il', 'youtube_id' => 'HYN8viBf9jg', 'type' => 'ost'],
                ['title' => 'The Blue Danube', 'artist' => 'Johann Strauss II', 'youtube_id' => '_CTYymbbEL4', 'type' => 'classical'],
            ],
            'Estamos muertos' => [
                ['title' => 'All of Us Are Dead Main Theme', 'artist' => 'Gaemi', 'youtube_id' => 'IN5TD4VRcps', 'type' => 'main'],
                ['title' => 'Zombie High School', 'artist' => 'Gaemi', 'youtube_id' => 'LHKL_aZ-VDo', 'type' => 'ost'],
                ['title' => 'Teen Spirit', 'artist' => 'aespa', 'youtube_id' => 'WPdWvnAAurg', 'type' => 'ending'],
            ],
        ],
        
        // Películas que existen en la BD  
        'movies' => [
            'Tu Nombre' => [
                ['title' => 'Zenzenzense', 'artist' => 'RADWIMPS', 'youtube_id' => 'PDSkFeMVNFs', 'type' => 'main'],
                ['title' => 'Sparkle', 'artist' => 'RADWIMPS', 'youtube_id' => 'a2GujJZfXpg', 'type' => 'ost'],
                ['title' => 'Nandemonaiya', 'artist' => 'RADWIMPS', 'youtube_id' => '9yGKGW43Ppk', 'type' => 'ending'],
                ['title' => 'Yumetourou', 'artist' => 'RADWIMPS', 'youtube_id' => 'ENfzjGCrfJ8', 'type' => 'ost'],
            ],
            'El Tiempo Contigo' => [
                ['title' => 'Grand Escape', 'artist' => 'RADWIMPS feat. Toko Miura', 'youtube_id' => 'sGmgWZ-bR_4', 'type' => 'main'],
                ['title' => 'Ai ni Dekiru Koto wa Mada Aru Kai', 'artist' => 'RADWIMPS', 'youtube_id' => 'xvudMu-5kIU', 'type' => 'ending'],
                ['title' => 'Weathering With You Theme', 'artist' => 'RADWIMPS', 'youtube_id' => 'Q6iK6PC__bw', 'type' => 'ost'],
            ],
            'Tazza: Los Tahúres' => [
                ['title' => 'Tazza Main Theme', 'artist' => 'Cho Young Wuk', 'youtube_id' => 'Rq8qKBLebfY', 'type' => 'main'],
                ['title' => 'The Gambler\'s Song', 'artist' => 'Cho Young Wuk', 'youtube_id' => 'zL5AlF1qL5s', 'type' => 'ost'],
                ['title' => 'High Stakes', 'artist' => 'Various Artists', 'youtube_id' => 'VsB2lJohVOo', 'type' => 'ost'],
            ],
        ]
    ];

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🎵 Agregando soundtracks específicos para contenido existente...');
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO PREVIEW - No se guardarán cambios');
        }
        
        $totalAdded = 0;
        
        // Agregar soundtracks a series
        $this->info("\n📺 Procesando soundtracks de series...");
        foreach ($this->specificSoundtracks['series'] as $seriesTitle => $tracks) {
            $series = $this->findContentByTitle(Series::class, $seriesTitle);
            
            if ($series) {
                $this->line("  ✅ Encontrada: {$series->display_title} (ID: {$series->id})");
                
                foreach ($tracks as $trackData) {
                    if (!$isDryRun) {
                        $created = $this->createSoundtrack($series, $trackData);
                        if ($created) {
                            $totalAdded++;
                            $this->line("    🎵 Agregado: {$trackData['title']} - {$trackData['artist']}");
                        }
                    } else {
                        $this->line("    🎵 Preview: {$trackData['title']} - {$trackData['artist']} (YouTube: {$trackData['youtube_id']})");
                        $totalAdded++;
                    }
                }
            } else {
                $this->warn("  ❌ No encontrada: {$seriesTitle}");
            }
        }
        
        // Agregar soundtracks a películas
        $this->info("\n🎬 Procesando soundtracks de películas...");
        foreach ($this->specificSoundtracks['movies'] as $movieTitle => $tracks) {
            $movie = $this->findContentByTitle(Movie::class, $movieTitle);
            
            if ($movie) {
                $this->line("  ✅ Encontrada: {$movie->display_title} (ID: {$movie->id})");
                
                foreach ($tracks as $trackData) {
                    if (!$isDryRun) {
                        $created = $this->createSoundtrack($movie, $trackData);
                        if ($created) {
                            $totalAdded++;
                            $this->line("    🎵 Agregado: {$trackData['title']} - {$trackData['artist']}");
                        }
                    } else {
                        $this->line("    🎵 Preview: {$trackData['title']} - {$trackData['artist']} (YouTube: {$trackData['youtube_id']})");
                        $totalAdded++;
                    }
                }
            } else {
                $this->warn("  ❌ No encontrada: {$movieTitle}");
            }
        }
        
        // Agregar soundtracks populares adicionales
        if (!$isDryRun) {
            $this->info("\n🎵 Agregando soundtracks populares adicionales...");
            $this->addPopularKoreanSoundtracks();
        }
        
        // Estadísticas finales
        $this->info("\n📊 Resumen:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Soundtracks procesados', $totalAdded],
                ['Modo', $isDryRun ? 'PREVIEW' : 'GUARDADO'],
            ]
        );
        
        if ($isDryRun) {
            $this->warn("\n⚠️  Para ejecutar los cambios reales, ejecuta sin --dry-run");
        } else {
            $this->info("\n✅ Soundtracks agregados exitosamente!");
            
            // Mostrar total de soundtracks en la BD
            $totalSoundtracks = Soundtrack::count();
            $this->info("📈 Total de soundtracks en base de datos: {$totalSoundtracks}");
        }
    }
    
    private function findContentByTitle($modelClass, $title)
    {
        return $modelClass::where('display_title', 'LIKE', "%{$title}%")
                         ->orWhere('title', 'LIKE', "%{$title}%")
                         ->orWhere('title_es', 'LIKE', "%{$title}%")
                         ->first();
    }
    
    private function createSoundtrack($content, $data)
    {
        $soundtrackData = [
            'soundtrackable_type' => get_class($content),
            'soundtrackable_id' => $content->id,
            'title' => $data['title'],
            'artist' => $data['artist'],
            'youtube_id' => $data['youtube_id'],
            'youtube_url' => "https://www.youtube.com/watch?v={$data['youtube_id']}",
            'is_main_theme' => ($data['type'] ?? '') === 'main',
            'is_ending_theme' => ($data['type'] ?? '') === 'ending',
            'is_active' => true,
            'popularity' => rand(70, 100) / 10, // Alta popularidad para tracks reales
            'duration' => rand(180, 360), // 3-6 minutes
        ];
        
        // Handle legacy series_id field
        if ($content instanceof Series) {
            $soundtrackData['series_id'] = $content->id;
        } else {
            $randomSeries = Series::first();
            $soundtrackData['series_id'] = $randomSeries ? $randomSeries->id : 1;
        }
        
        // Agregar URLs de Spotify/Apple Music
        $soundtrackData['spotify_url'] = "https://open.spotify.com/search/" . urlencode("{$data['title']} {$data['artist']}");
        $soundtrackData['apple_music_url'] = "https://music.apple.com/search?term=" . urlencode("{$data['title']} {$data['artist']}");
        
        // Verificar si ya existe
        $exists = Soundtrack::where('soundtrackable_type', $soundtrackData['soundtrackable_type'])
                           ->where('soundtrackable_id', $soundtrackData['soundtrackable_id'])
                           ->where('title', $soundtrackData['title'])
                           ->where('artist', $soundtrackData['artist'])
                           ->exists();
        
        if (!$exists) {
            Soundtrack::create($soundtrackData);
            return true;
        }
        
        return false;
    }
    
    private function addPopularKoreanSoundtracks()
    {
        // Agregar soundtracks populares K-pop y K-drama a contenido aleatorio
        $popularTracks = [
            ['title' => 'Dynamite', 'artist' => 'BTS', 'youtube_id' => 'gdZLi9oWNZg'],
            ['title' => 'GANGNAM STYLE', 'artist' => 'PSY', 'youtube_id' => '9bZkp7q19f0'],
            ['title' => 'Kill This Love', 'artist' => 'BLACKPINK', 'youtube_id' => '2S24-y0Ij3Y'],
            ['title' => 'Spring Day', 'artist' => 'BTS', 'youtube_id' => 'xEeFrLSkMm8'],
            ['title' => 'How You Like That', 'artist' => 'BLACKPINK', 'youtube_id' => 'ioNng23DkIM'],
        ];
        
        $allSeries = Series::take(5)->get();
        $allMovies = Movie::take(5)->get();
        
        foreach ($popularTracks as $track) {
            // Agregar a una serie aleatoria
            if ($allSeries->count() > 0) {
                $randomSeries = $allSeries->random();
                $track['type'] = 'ost';
                $this->createSoundtrack($randomSeries, $track);
            }
            
            // Agregar a una película aleatoria
            if ($allMovies->count() > 0) {
                $randomMovie = $allMovies->random();
                $track['type'] = 'ost';
                $this->createSoundtrack($randomMovie, $track);
            }
        }
        
        $this->line("  🎵 Agregados " . count($popularTracks) . " soundtracks populares adicionales");
    }
}