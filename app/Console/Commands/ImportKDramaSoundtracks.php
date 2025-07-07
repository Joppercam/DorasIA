<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Soundtrack;

class ImportKDramaSoundtracks extends Command
{
    protected $signature = 'import:kdrama-soundtracks {--limit=20}';
    protected $description = 'Importa soundtracks populares de K-Dramas';

    public function handle()
    {
        $this->info('🎵 Importando soundtracks de K-Dramas populares...');
        
        $limit = $this->option('limit');
        $imported = 0;
        
        // Soundtracks famosos organizados por serie (usando nombres que podrían coincidir)
        $famousSoundtracks = [
            'Descendientes del Sol' => [
                [
                    'title' => 'Always',
                    'artist' => 'Yoon Mirae',
                    'album' => 'Descendants of the Sun OST',
                    'duration' => 245,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=9U8F6Z5tfwY'
                ],
                [
                    'title' => 'You Are',
                    'artist' => 'GOT7',
                    'album' => 'Descendants of the Sun OST',
                    'duration' => 208,
                    'youtube_url' => 'https://www.youtube.com/watch?v=ktc8XDBq93k'
                ]
            ],
            'Goblin' => [
                [
                    'title' => 'Stay With Me',
                    'artist' => 'Chanyeol (EXO) & Punch',
                    'album' => 'Goblin OST',
                    'duration' => 227,
                    'is_main_theme' => true,
                    'spotify_url' => 'https://open.spotify.com/track/7w7xyNTbD3Kg5SNfrcUb0y',
                    'youtube_url' => 'https://www.youtube.com/watch?v=pcKR0LPwoYs'
                ],
                [
                    'title' => 'Beautiful',
                    'artist' => 'Crush',
                    'album' => 'Goblin OST',
                    'duration' => 236,
                    'youtube_url' => 'https://www.youtube.com/watch?v=q6LE0HzSN8U'
                ]
            ],
            'Hotel del Luna' => [
                [
                    'title' => 'Remember Me',
                    'artist' => 'Oh My Girl',
                    'album' => 'Hotel Del Luna OST',
                    'duration' => 215,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=RrvdjyIL0fA'
                ]
            ],
            'Crash Landing on You' => [
                [
                    'title' => 'Give You My Heart',
                    'artist' => 'IU',
                    'album' => 'Crash Landing on You OST',
                    'duration' => 252,
                    'is_main_theme' => true,
                    'spotify_url' => 'https://open.spotify.com/track/5w5xKADtFJjl9PbGg0Nw3N',
                    'youtube_url' => 'https://www.youtube.com/watch?v=x8UJ_1M8Bps'
                ],
                [
                    'title' => 'Here I Am Again',
                    'artist' => 'Yerin Baek',
                    'album' => 'Crash Landing on You OST',
                    'duration' => 198,
                    'youtube_url' => 'https://www.youtube.com/watch?v=8J-lKRCUL_w'
                ]
            ],
            'What\'s Wrong with Secretary Kim' => [
                [
                    'title' => 'A Little Braver',
                    'artist' => 'New Empire',
                    'album' => 'What\'s Wrong with Secretary Kim OST',
                    'duration' => 207,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=wTn28Ybzz1Y'
                ]
            ],
            'Itaewon Class' => [
                [
                    'title' => 'Start',
                    'artist' => 'Gaho',
                    'album' => 'Itaewon Class OST',
                    'duration' => 240,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=8rpjMj-4Swg'
                ]
            ],
            'My Love from the Star' => [
                [
                    'title' => 'My Destiny',
                    'artist' => 'Lyn',
                    'album' => 'My Love from the Star OST',
                    'duration' => 234,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=6g2bHVogAvY'
                ]
            ],
            'Reply 1988' => [
                [
                    'title' => 'Youth',
                    'artist' => 'Park Bo Ram',
                    'album' => 'Reply 1988 OST',
                    'duration' => 225,
                    'is_main_theme' => true,
                    'youtube_url' => 'https://www.youtube.com/watch?v=U0tG74LLHZo'
                ]
            ]
        ];
        
        foreach ($famousSoundtracks as $seriesTitle => $soundtracks) {
            if ($imported >= $limit) break;
            
            // Buscar la serie por título (flexible)
            $series = Series::where(function($query) use ($seriesTitle) {
                $query->where('title', 'LIKE', '%' . $seriesTitle . '%')
                      ->orWhere('original_title', 'LIKE', '%' . $seriesTitle . '%')
                      ->orWhere('title_es', 'LIKE', '%' . $seriesTitle . '%');
            })->first();
            
            if (!$series) {
                // Si no existe, buscar por palabras clave
                $keywords = explode(' ', $seriesTitle);
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) > 3) {
                        $series = Series::where('title', 'LIKE', '%' . $keyword . '%')
                                       ->orWhere('original_title', 'LIKE', '%' . $keyword . '%')
                                       ->first();
                        if ($series) break;
                    }
                }
            }
            
            if ($series) {
                $this->info("📺 Agregando soundtracks para: {$series->title}");
                
                foreach ($soundtracks as $soundtrackData) {
                    if ($imported >= $limit) break;
                    
                    // Verificar que no exista ya
                    $exists = Soundtrack::where('series_id', $series->id)
                                       ->where('title', $soundtrackData['title'])
                                       ->exists();
                    
                    if (!$exists) {
                        $soundtrackData['series_id'] = $series->id;
                        $soundtrackData['track_number'] = $soundtrackData['track_number'] ?? 1;
                        
                        Soundtrack::create($soundtrackData);
                        $imported++;
                        
                        $this->line("  🎵 {$soundtrackData['title']} - {$soundtrackData['artist']}");
                    }
                }
            } else {
                $this->warn("⚠️  Serie no encontrada: {$seriesTitle}");
            }
        }
        
        // Agregar soundtracks genéricos para series existentes sin soundtrack
        $seriesWithoutSoundtrack = Series::whereDoesntHave('soundtracks')
                                        ->where('original_language', 'ko')
                                        ->limit(10)
                                        ->get();
        
        $genericSoundtracks = [
            [
                'title' => 'Opening Theme',
                'artist' => 'Original Soundtrack',
                'album' => 'K-Drama OST',
                'duration' => 90,
                'is_main_theme' => true,
                'track_number' => 1
            ],
            [
                'title' => 'Ending Theme',
                'artist' => 'Original Soundtrack',
                'album' => 'K-Drama OST',
                'duration' => 120,
                'is_ending_theme' => true,
                'track_number' => 2
            ]
        ];
        
        foreach ($seriesWithoutSoundtrack as $series) {
            if ($imported >= $limit) break;
            
            foreach ($genericSoundtracks as $soundtrack) {
                if ($imported >= $limit) break;
                
                $soundtrack['series_id'] = $series->id;
                $soundtrack['album'] = $series->title . ' OST';
                
                Soundtrack::create($soundtrack);
                $imported++;
            }
            
            $this->line("🎼 Soundtracks genéricos para: {$series->title}");
        }
        
        $this->newLine();
        $this->info("✅ Importación completada!");
        $this->info("📊 Soundtracks importados: {$imported}");
        $this->info("🎵 Total soundtracks en BD: " . Soundtrack::count());
        
        // Mostrar estadísticas por serie
        $seriesWithSoundtracks = Series::withCount('soundtracks')
                                      ->having('soundtracks_count', '>', 0)
                                      ->orderBy('soundtracks_count', 'desc')
                                      ->limit(10)
                                      ->get();
        
        if ($seriesWithSoundtracks->count() > 0) {
            $this->newLine();
            $this->info("🏆 Top series con más soundtracks:");
            foreach ($seriesWithSoundtracks as $series) {
                $this->line("  • {$series->title}: {$series->soundtracks_count} canciones");
            }
        }
    }
}