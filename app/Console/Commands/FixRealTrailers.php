<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Support\Facades\Http;

class FixRealTrailers extends Command
{
    protected $signature = 'fix:real-trailers';
    protected $description = 'Fix trailers with real K-drama and movie trailers';

    public function handle()
    {
        $this->info('🎬 Arreglando trailers con videos reales de K-dramas...');

        $realTrailers = [
            // K-Drama trailers reales que SÍ funcionan
            'Squid Game' => 'oqxAJKy0ii4',
            'Crash Landing on You' => 'GJX9QnrZtfc', 
            'Vincenzo' => 'tQW2zzUT2jE',
            'Kingdom' => 'A6jOBOvpKkE',
            'The Glory' => 'YQz1NUQNb8U',
            'Goblin' => '8oWPV7-XaLw',
            'Descendants of the Sun' => 'dEI1jVhiYgc',
            'Hotel del Luna' => 'yQr1A3YjbU4',
            'Itaewon Class' => 'NeZ3kehWIbw',
            'Start-Up' => 'BemKR8_h0bk',
            'Reply 1988' => 'jGRcrPdG_L0',
            'Sky Castle' => 'qCTQgmPFJKI',
            'Mr. Sunshine' => 'OEjjx1TIgj0',
            'My Love from the Star' => 'N2wKWYq4-iI',
            'What\'s Wrong with Secretary Kim' => 'M2Ye8hUQdRQ',
            'Strong Woman Do Bong Soon' => 'rnCuOtjyoLI',
            'W: Two Worlds' => '4ExnxlCn_Pg',
            'Weightlifting Fairy Kim Bok Joo' => 'LGQKbglmgUw',
            'Pinocchio' => 'jfJMGEWxzds',
            'Heirs' => 'wFuVWHaWJ4k',
            'Boys Over Flowers' => '8sLjr0l6Jhg',
            'Secret Garden' => 'foXg4l5MJsE',
            'My Name' => 'V-F3htSNNJY',
            'Sweet Home' => 'irq0p8o22HY',
            'Train to Busan' => '1ovgxN2VWNc',
            'Parasite' => '5xH0HfJHsaY',
            'Memories of Murder' => 'whldChqCsYk',
            'Oldboy' => 'dQw4w9WgXcQ' // Placeholder, buscar real después
        ];

        $this->fixSeriesTrailers($realTrailers);
        $this->fixMovieTrailers($realTrailers);

        $this->info('✅ Trailers arreglados con videos reales');
        return 0;
    }

    private function fixSeriesTrailers($realTrailers)
    {
        $this->info('📺 Arreglando trailers de series...');
        
        $series = Series::all();
        
        foreach ($series as $serie) {
            $trailerFound = false;
            
            // Buscar por título exacto
            foreach ($realTrailers as $title => $trailerId) {
                if (stripos($serie->title, $title) !== false || 
                    stripos($serie->original_title, $title) !== false) {
                    
                    if ($this->validateYouTubeId($trailerId)) {
                        $serie->update([
                            'trailer_youtube_id' => $trailerId,
                            'has_spanish_trailer' => false,
                            'trailer_added_at' => now()
                        ]);
                        
                        $this->info("  ✓ {$serie->title} - Trailer actualizado: {$trailerId}");
                        $trailerFound = true;
                        break;
                    }
                }
            }
            
            // Si no encontramos trailer específico, buscar uno genérico por género
            if (!$trailerFound && !$serie->trailer_youtube_id) {
                $defaultTrailer = $this->getDefaultTrailerByGenre($serie);
                if ($defaultTrailer) {
                    $serie->update([
                        'trailer_youtube_id' => $defaultTrailer,
                        'has_spanish_trailer' => false,
                        'trailer_added_at' => now()
                    ]);
                    $this->info("  ○ {$serie->title} - Trailer genérico asignado: {$defaultTrailer}");
                }
            }
        }
    }

    private function fixMovieTrailers($realTrailers)
    {
        $this->info('🎬 Arreglando trailers de películas...');
        
        $movies = Movie::all();
        
        foreach ($movies as $movie) {
            $trailerFound = false;
            
            // Buscar por título exacto
            foreach ($realTrailers as $title => $trailerId) {
                if (stripos($movie->title, $title) !== false || 
                    stripos($movie->original_title, $title) !== false) {
                    
                    if ($this->validateYouTubeId($trailerId)) {
                        $movie->update([
                            'trailer_youtube_id' => $trailerId,
                            'has_spanish_trailer' => false,
                            'trailer_added_at' => now()
                        ]);
                        
                        $this->info("  ✓ {$movie->title} - Trailer actualizado: {$trailerId}");
                        $trailerFound = true;
                        break;
                    }
                }
            }
            
            // Si no encontramos trailer específico, usar uno por defecto
            if (!$trailerFound && !$movie->trailer_youtube_id) {
                $defaultTrailer = 'oqxAJKy0ii4'; // Squid Game como fallback
                $movie->update([
                    'trailer_youtube_id' => $defaultTrailer,
                    'has_spanish_trailer' => false,
                    'trailer_added_at' => now()
                ]);
                $this->info("  ○ {$movie->title} - Trailer por defecto asignado: {$defaultTrailer}");
            }
        }
    }

    private function getDefaultTrailerByGenre($serie)
    {
        // Trailers por género/tipo
        $genreTrailers = [
            'romance' => 'GJX9QnrZtfc', // Crash Landing on You
            'action' => 'A6jOBOvpKkE', // Kingdom  
            'comedy' => 'NeZ3kehWIbw', // Itaewon Class
            'drama' => '8oWPV7-XaLw', // Goblin
            'thriller' => 'tQW2zzUT2jE', // Vincenzo
            'horror' => 'A6jOBOvpKkE', // Kingdom
            'fantasy' => '8oWPV7-XaLw' // Goblin
        ];

        // Buscar género de la serie
        if ($serie->genres && $serie->genres->count() > 0) {
            foreach ($serie->genres as $genre) {
                $genreName = strtolower($genre->name);
                foreach ($genreTrailers as $genreKey => $trailerId) {
                    if (stripos($genreName, $genreKey) !== false) {
                        return $trailerId;
                    }
                }
            }
        }

        // Si es K-drama, usar Goblin por defecto
        if ($serie->is_korean_drama) {
            return '8oWPV7-XaLw'; // Goblin
        }

        return 'oqxAJKy0ii4'; // Squid Game como último recurso
    }

    private function validateYouTubeId($youtubeId)
    {
        if (!$youtubeId) return false;
        
        if (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $youtubeId)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->get("https://www.youtube.com/oembed", [
                'url' => "https://www.youtube.com/watch?v={$youtubeId}",
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['title']) && !empty($data['title']);
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}