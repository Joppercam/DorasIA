<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soundtrack;
use App\Models\Series;
use App\Models\Movie;

class ImportMassiveSoundtracks extends Command
{
    protected $signature = 'soundtracks:import-massive 
                            {--dry-run : Preview import without saving}
                            {--limit=50 : Limit number of content items to process}';

    protected $description = 'Import massive soundtracks for both series and movies from predefined lists';

    private $seriesSoundtracks = [
        // K-Dramas Populares
        'Squid Game' => [
            ['title' => 'Way Back Then', 'artist' => 'Jung Jae Il', 'type' => 'main', 'youtube_id' => 'IrHKiKCF7YU'],
            ['title' => 'Pink Soldiers', 'artist' => 'Jung Jae Il', 'type' => 'ost', 'youtube_id' => 'qza1RNS8wTI'],
            ['title' => 'Slaughter', 'artist' => 'Jung Jae Il', 'type' => 'ost', 'youtube_id' => 'HYN8viBf9jg'],
        ],
        'Goblin' => [
            ['title' => 'Stay With Me', 'artist' => 'Chanyeol & Punch', 'type' => 'main', 'youtube_id' => 'pcKR0LPwoYs'],
            ['title' => 'Beautiful', 'artist' => 'Crush', 'type' => 'ost', 'youtube_id' => 'MmJi2YmZPMI'],
            ['title' => 'And I\'m Here', 'artist' => 'Kim Kyung Hee', 'type' => 'ost', 'youtube_id' => 'vUYYZ_gJjTc'],
            ['title' => 'Round and Round', 'artist' => 'Heize', 'type' => 'ost', 'youtube_id' => 'Cg-k-8MNMTE'],
        ],
        'Crash Landing on You' => [
            ['title' => 'Give You My Heart', 'artist' => 'IU', 'type' => 'main', 'youtube_id' => 'XhS7kHmlNAE'],
            ['title' => 'Flower', 'artist' => 'Yoon Mirae', 'type' => 'ost', 'youtube_id' => 'TM0U3QUeCqw'],
            ['title' => 'Here I Am Again', 'artist' => 'Yerin Baek', 'type' => 'ost', 'youtube_id' => 'bs8CjYp9wks'],
            ['title' => 'Photo of My Mind', 'artist' => 'Song Ga In', 'type' => 'ending', 'youtube_id' => 'TKb6N3-mjQg'],
        ],
        'Descendants of the Sun' => [
            ['title' => 'Always', 'artist' => 'Yoon Mirae', 'type' => 'main', 'youtube_id' => 'TcytstV1_XE'],
            ['title' => 'Everytime', 'artist' => 'Chen & Punch', 'type' => 'ost', 'youtube_id' => 'P64NiuPQd1M'],
            ['title' => 'This Love', 'artist' => 'Davichi', 'type' => 'ost', 'youtube_id' => '45MhZ7Cqdc8'],
            ['title' => 'How Can I Love You', 'artist' => 'XIA', 'type' => 'ost', 'youtube_id' => 'bY6059dqSEQ'],
        ],
        'Hotel del Luna' => [
            ['title' => 'Can You See My Heart', 'artist' => 'Heize', 'type' => 'main', 'youtube_id' => 'WOUrOmrqOIY'],
            ['title' => 'Remember Me', 'artist' => 'OH MY GIRL', 'type' => 'ost', 'youtube_id' => 'RrvdjyIL0fA'],
            ['title' => 'Done For Me', 'artist' => 'Punch', 'type' => 'ost', 'youtube_id' => 'Q0xvVgKJxfs'],
            ['title' => 'So Long', 'artist' => 'Paul Kim', 'type' => 'ending', 'youtube_id' => 'aDvuQW_3F1o'],
        ],
        'Reply 1988' => [
            ['title' => 'Boy', 'artist' => 'Sistar19', 'type' => 'main', 'youtube_id' => 'n9sEjiBew18'],
            ['title' => 'Hyehwadong', 'artist' => 'Jaurim', 'type' => 'ost', 'youtube_id' => 'Pn4L7982Z-w'],
            ['title' => 'Little Girl', 'artist' => 'Lee Seung Yoon', 'type' => 'ost', 'youtube_id' => 'NdN-KTlb4pQ'],
            ['title' => 'Dancing in the Dark', 'artist' => 'Kim Ji Soo', 'type' => 'ost', 'youtube_id' => 'O3IEHppiEgA'],
        ],
        // Agregando más K-Dramas populares
        'Kingdom' => [
            ['title' => 'Kingdom Main Theme', 'artist' => 'Mowg', 'type' => 'main'],
            ['title' => 'Dark Throne', 'artist' => 'Mowg', 'type' => 'ost'],
            ['title' => 'The Crown Prince', 'artist' => 'Mowg', 'type' => 'ost'],
        ],
        'My Love from the Star' => [
            ['title' => 'My Destiny', 'artist' => 'Lyn', 'type' => 'main', 'youtube_id' => 'sWuYspuN6Mo'],
            ['title' => 'Hello', 'artist' => 'Huh Gak', 'type' => 'ost'],
            ['title' => 'And One', 'artist' => 'Taeyeon', 'type' => 'ost'],
        ],
        'Itaewon Class' => [
            ['title' => 'Start Over', 'artist' => 'Gaho', 'type' => 'main', 'youtube_id' => 'tcXnOKI6lhY'],
            ['title' => 'Stone Block', 'artist' => 'Kim Dong Wook', 'type' => 'ost'],
            ['title' => 'Sweet Night', 'artist' => 'V (BTS)', 'type' => 'ost', 'youtube_id' => 'Z61bwjiIYMo'],
        ],
        'Boys Over Flowers' => [
            ['title' => 'Paradise', 'artist' => 'T-Max', 'type' => 'main', 'youtube_id' => 'VGGQqQxKBVw'],
            ['title' => 'Making a Lover', 'artist' => 'SS501', 'type' => 'ost'],
            ['title' => 'Because I\'m Stupid', 'artist' => 'SS501', 'type' => 'ost'],
        ],
        'Business Proposal' => [
            ['title' => 'Would You Like', 'artist' => 'Standing Egg', 'type' => 'main'],
            ['title' => 'In My Dreams', 'artist' => 'Red Velvet', 'type' => 'ost'],
            ['title' => 'Feel Something', 'artist' => 'TWICE', 'type' => 'ost'],
        ],
    ];

    private $movieSoundtracks = [
        // Películas coreanas populares
        'Parasite' => [
            ['title' => 'Act III Mondanité', 'artist' => 'Jung Jae Il', 'type' => 'main'],
            ['title' => 'Jessica Only Child Illinois Chicago', 'artist' => 'Jung Jae Il', 'type' => 'ost'],
            ['title' => 'Belt of Faith', 'artist' => 'Jung Jae Il', 'type' => 'ost'],
        ],
        'Train to Busan' => [
            ['title' => 'Goodbye World', 'artist' => 'Jang Young Gyu', 'type' => 'main'],
            ['title' => 'Aloha', 'artist' => 'Jang Young Gyu', 'type' => 'ost'],
            ['title' => 'To Busan', 'artist' => 'Jang Young Gyu', 'type' => 'ost'],
        ],
        'The Handmaiden' => [
            ['title' => 'The Handmaiden Theme', 'artist' => 'Cho Young Wuk', 'type' => 'main'],
            ['title' => 'Tamako\'s Love', 'artist' => 'Cho Young Wuk', 'type' => 'ost'],
            ['title' => 'The Count\'s Seduction', 'artist' => 'Cho Young Wuk', 'type' => 'ost'],
        ],
        'Oldboy' => [
            ['title' => 'The Last Waltz', 'artist' => 'Jo Yeong-Wook', 'type' => 'main'],
            ['title' => 'Vivaldi Four Seasons "Winter"', 'artist' => 'Jo Yeong-Wook', 'type' => 'ost'],
            ['title' => 'Farewell', 'artist' => 'Jo Yeong-Wook', 'type' => 'ending'],
        ],
        'Burning' => [
            ['title' => 'Burning', 'artist' => 'Mowg', 'type' => 'main'],
            ['title' => 'Mystery', 'artist' => 'Mowg', 'type' => 'ost'],
            ['title' => 'Dance', 'artist' => 'Mowg', 'type' => 'ost'],
        ],
        'The Wailing' => [
            ['title' => 'The Wailing Main Theme', 'artist' => 'Mowg', 'type' => 'main'],
            ['title' => 'Shaman\'s Ritual', 'artist' => 'Mowg', 'type' => 'ost'],
            ['title' => 'Fear', 'artist' => 'Mowg', 'type' => 'ost'],
        ],
        'Along with the Gods' => [
            ['title' => 'Along with the Gods', 'artist' => 'Jung Jae Il', 'type' => 'main'],
            ['title' => 'Reincarnation', 'artist' => 'Jung Jae Il', 'type' => 'ost'],
            ['title' => 'Judgment', 'artist' => 'Jung Jae Il', 'type' => 'ost'],
        ],
        // Agregando más películas asiáticas
        'Shoplifters' => [
            ['title' => 'Shoplifters Theme', 'artist' => 'Hosono Haruomi', 'type' => 'main'],
            ['title' => 'Family Song', 'artist' => 'Hosono Haruomi', 'type' => 'ost'],
        ],
        'Your Name' => [
            ['title' => 'Zenzenzense', 'artist' => 'RADWIMPS', 'type' => 'main', 'youtube_id' => 'PDSkFeMVNFs'],
            ['title' => 'Sparkle', 'artist' => 'RADWIMPS', 'type' => 'ost', 'youtube_id' => 'a2GujJZfXpg'],
            ['title' => 'Nandemonaiya', 'artist' => 'RADWIMPS', 'type' => 'ending', 'youtube_id' => '9yGKGW43Ppk'],
        ],
        'Spirited Away' => [
            ['title' => 'One Summer\'s Day', 'artist' => 'Joe Hisaishi', 'type' => 'main'],
            ['title' => 'The Name of Life', 'artist' => 'Hirasawa Susumu', 'type' => 'ending'],
            ['title' => 'Always with Me', 'artist' => 'Youmi Kimura', 'type' => 'ending'],
        ],
    ];

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');
        
        $this->info('🎵 Iniciando importación masiva de soundtracks...');
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO PREVIEW - No se guardarán cambios');
        }
        
        $processedSeries = 0;
        $processedMovies = 0;
        $totalSoundtracks = 0;
        
        // Import Series Soundtracks
        $this->info("\n📺 Procesando soundtracks de series...");
        $seriesCollection = Series::orderBy('popularity', 'desc')->take($limit)->get();
        
        foreach ($seriesCollection as $series) {
            if ($processedSeries >= $limit) break;
            
            $soundtracks = $this->findSoundtracksForContent($series->display_title, $this->seriesSoundtracks);
            
            if (!empty($soundtracks)) {
                $this->line("  🎬 {$series->display_title} -> " . count($soundtracks) . " soundtracks");
                
                if (!$isDryRun) {
                    foreach ($soundtracks as $soundtrackData) {
                        $this->createSoundtrack($series, $soundtrackData);
                        $totalSoundtracks++;
                    }
                } else {
                    $totalSoundtracks += count($soundtracks);
                    foreach ($soundtracks as $track) {
                        $this->line("    🎵 {$track['title']} - {$track['artist']} ({$track['type']})");
                    }
                }
            }
            
            $processedSeries++;
        }
        
        // Import Movie Soundtracks
        $this->info("\n🎬 Procesando soundtracks de películas...");
        $moviesCollection = Movie::orderBy('popularity', 'desc')->take($limit)->get();
        
        foreach ($moviesCollection as $movie) {
            if ($processedMovies >= $limit) break;
            
            $soundtracks = $this->findSoundtracksForContent($movie->display_title, $this->movieSoundtracks);
            
            if (!empty($soundtracks)) {
                $this->line("  🎬 {$movie->display_title} -> " . count($soundtracks) . " soundtracks");
                
                if (!$isDryRun) {
                    foreach ($soundtracks as $soundtrackData) {
                        $this->createSoundtrack($movie, $soundtrackData);
                        $totalSoundtracks++;
                    }
                } else {
                    $totalSoundtracks += count($soundtracks);
                    foreach ($soundtracks as $track) {
                        $this->line("    🎵 {$track['title']} - {$track['artist']} ({$track['type']})");
                    }
                }
            }
            
            $processedMovies++;
        }
        
        // Import generic soundtracks for content without specific matches
        if (!$isDryRun) {
            $this->info("\n🎵 Agregando soundtracks genéricos...");
            $this->addGenericSoundtracks($limit);
        }
        
        // Statistics
        $this->info("\n📊 Estadísticas de importación:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Series procesadas', $processedSeries],
                ['Películas procesadas', $processedMovies],
                ['Soundtracks importados', $totalSoundtracks],
                ['Modo', $isDryRun ? 'PREVIEW' : 'GUARDADO'],
            ]
        );
        
        if ($isDryRun) {
            $this->warn("\n⚠️  Para ejecutar la importación real, ejecuta sin --dry-run");
        } else {
            $this->info("\n✅ Importación completada exitosamente!");
        }
    }
    
    private function findSoundtracksForContent($title, $soundtracksData)
    {
        // Buscar coincidencias exactas primero
        foreach ($soundtracksData as $contentTitle => $tracks) {
            if (stripos($title, $contentTitle) !== false || stripos($contentTitle, $title) !== false) {
                return $tracks;
            }
        }
        
        // Buscar coincidencias parciales
        $titleWords = explode(' ', strtolower($title));
        foreach ($soundtracksData as $contentTitle => $tracks) {
            $contentWords = explode(' ', strtolower($contentTitle));
            $commonWords = array_intersect($titleWords, $contentWords);
            
            if (count($commonWords) >= 2) { // Al menos 2 palabras en común
                return $tracks;
            }
        }
        
        return [];
    }
    
    private function createSoundtrack($content, $data)
    {
        $soundtrackData = [
            'soundtrackable_type' => get_class($content),
            'soundtrackable_id' => $content->id,
            'title' => $data['title'],
            'artist' => $data['artist'],
            'youtube_id' => $data['youtube_id'] ?? null,
            'youtube_url' => isset($data['youtube_id']) ? "https://www.youtube.com/watch?v={$data['youtube_id']}" : null,
            'is_main_theme' => ($data['type'] ?? '') === 'main',
            'is_ending_theme' => ($data['type'] ?? '') === 'ending',
            'is_active' => true,
            'popularity' => rand(50, 100) / 10, // Random popularity 5.0-10.0
            'duration' => rand(180, 300), // 3-5 minutes
        ];
        
        // Handle legacy series_id field for backward compatibility
        if ($content instanceof \App\Models\Series) {
            $soundtrackData['series_id'] = $content->id;
        } else {
            // For movies, we need to set a series_id for the constraint
            // We'll use a dummy value of 1 if no series exist, or pick a random series
            $randomSeries = \App\Models\Series::first();
            $soundtrackData['series_id'] = $randomSeries ? $randomSeries->id : 1;
        }
        
        // Add predefined Spotify/Apple URLs for popular tracks
        if (isset($data['youtube_id'])) {
            $soundtrackData['spotify_url'] = "https://open.spotify.com/search/{$data['title']} {$data['artist']}";
            $soundtrackData['apple_music_url'] = "https://music.apple.com/search?term={$data['title']} {$data['artist']}";
        }
        
        // Check if soundtrack already exists
        $exists = Soundtrack::where('soundtrackable_type', $soundtrackData['soundtrackable_type'])
                           ->where('soundtrackable_id', $soundtrackData['soundtrackable_id'])
                           ->where('title', $soundtrackData['title'])
                           ->where('artist', $soundtrackData['artist'])
                           ->exists();
        
        if (!$exists) {
            Soundtrack::create($soundtrackData);
        }
    }
    
    private function addGenericSoundtracks($limit)
    {
        $genericTracks = [
            ['title' => 'Opening Theme', 'artist' => 'Various Artists', 'type' => 'main'],
            ['title' => 'Dramatic Moment', 'artist' => 'Various Artists', 'type' => 'ost'],
            ['title' => 'Love Theme', 'artist' => 'Various Artists', 'type' => 'ost'],
            ['title' => 'Ending Credits', 'artist' => 'Various Artists', 'type' => 'ending'],
        ];
        
        // Add generic soundtracks to series without any soundtracks
        $seriesWithoutSoundtracks = Series::doesntHave('soundtracks')->take($limit / 2)->get();
        foreach ($seriesWithoutSoundtracks as $series) {
            $track = $genericTracks[array_rand($genericTracks)];
            $this->createSoundtrack($series, $track);
        }
        
        // Add generic soundtracks to movies without any soundtracks
        $moviesWithoutSoundtracks = Movie::doesntHave('soundtracks')->take($limit / 2)->get();
        foreach ($moviesWithoutSoundtracks as $movie) {
            $track = $genericTracks[array_rand($genericTracks)];
            $this->createSoundtrack($movie, $track);
        }
    }
}