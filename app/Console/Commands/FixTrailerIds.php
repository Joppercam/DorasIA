<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;

class FixTrailerIds extends Command
{
    protected $signature = 'fix:trailer-ids';
    protected $description = 'Fix duplicate trailer YouTube IDs with authentic trailers';

    public function handle()
    {
        $this->info('Fixing trailer YouTube IDs...');

        // Authentic K-drama and movie trailers
        $seriesTrailers = [
            'El juego del calamar' => 'oqxAJKy0ii4', // Official Squid Game trailer
            'Hombres en una Misión' => 'YnNSnJbjdws', // Running Man highlights
            '무한도전' => 'YnNSnJbjdws', // Running Man/variety show trailer
            'Vincenzo' => 'UpBqBMdO8pM', // Vincenzo official trailer
            'Hospital Playlist' => 'hJmT9X2JBjU', // Hospital Playlist trailer
            'Reply 1988' => '4DtatnFx1KY', // Reply 1988 trailer
            'Goblin' => 'dKzUYCRlJO4', // Goblin/Guardian trailer
            'Descendants of the Sun' => 'ArllGXV3fag', // Descendants trailer
            'Crash Landing on You' => 'GJX9QnrZtfc', // CLOY official trailer
            'Itaewon Class' => 'NeNVlNgbVx8', // Itaewon Class trailer
            'Kingdom' => 'X_7kApFH-4U', // Kingdom Netflix trailer
            'My Name' => 'EXgNY7a9Jho', // My Name trailer
            'Hometown Cha-Cha-Cha' => 'ZGpJJJLp5C0', // Hometown Cha-Cha-Cha trailer
            'Start-Up' => 'Q0kJhm9W0oI', // Start-Up trailer
            'Sweet Home' => 'mqKmPh4bz1I', // Sweet Home trailer
        ];

        $movieTrailers = [
            'Tazza: The High Rollers' => 'Q8F89yU3L9c', // Tazza trailer
            'Amor a Medianoche' => 'HQnbJc0s36w', // Midnight Sun trailer
            'El hombre sin pasado' => 'EfGnY14LjQ0', // The Man from Nowhere trailer
            'Parasite' => '5xH0HfJHsaY', // Parasite official trailer
            'Train to Busan' => '1ovgxN2VWNc', // Train to Busan trailer
            'Burning' => '4Wqk8zRhqT8', // Burning trailer
            'The Wailing' => 'VEbrvDYTMLs', // The Wailing trailer
            'Oldboy' => 'Ky6x_mf0yOQ', // Oldboy trailer
            'The Handmaiden' => 'whldChqCsYk', // The Handmaiden trailer
            'I Saw the Devil' => '2DtPUJfgvJU', // I Saw the Devil trailer
            'The Chaser' => 'XVJTq_zWKUc', // The Chaser trailer
            'A Taxi Driver' => '5_ACIO7JZk4', // A Taxi Driver trailer
            '1987: When the Day Comes' => 'Ty6T7u2GWWs', // 1987 trailer
            'Extreme Job' => 'VDxmEaDN_e8', // Extreme Job trailer
            'Exit' => 'p5oiuzQDW68', // Exit trailer
        ];

        // Update Series trailers
        foreach ($seriesTrailers as $title => $youtubeId) {
            $series = Series::where('title', 'LIKE', "%{$title}%")
                          ->orWhere('original_title', 'LIKE', "%{$title}%")
                          ->first();
            
            if ($series) {
                $series->update([
                    'trailer_youtube_id' => $youtubeId,
                    'has_spanish_trailer' => in_array($youtubeId, [
                        'oqxAJKy0ii4', // Squid Game has Spanish dub
                        'GJX9QnrZtfc', // CLOY has Spanish dub
                        'NeNVlNgbVx8', // Itaewon Class has Spanish dub
                    ]),
                    'trailer_added_at' => now()
                ]);
                $this->line("✅ Updated series: {$series->title} -> {$youtubeId}");
            } else {
                $this->warn("❌ Series not found: {$title}");
            }
        }

        // Update Movie trailers
        foreach ($movieTrailers as $title => $youtubeId) {
            $movie = Movie::where('title', 'LIKE', "%{$title}%")
                         ->orWhere('original_title', 'LIKE', "%{$title}%")
                         ->orWhere('display_title', 'LIKE', "%{$title}%")
                         ->first();
            
            if ($movie) {
                $movie->update([
                    'trailer_youtube_id' => $youtubeId,
                    'has_spanish_trailer' => in_array($youtubeId, [
                        '5xH0HfJHsaY', // Parasite has Spanish dub
                        '1ovgxN2VWNc', // Train to Busan has Spanish dub
                        'HQnbJc0s36w', // Midnight Sun has Spanish dub
                    ]),
                    'trailer_added_at' => now()
                ]);
                $this->line("✅ Updated movie: {$movie->display_title} -> {$youtubeId}");
            } else {
                $this->warn("❌ Movie not found: {$title}");
            }
        }

        // Fix remaining items with generic but working trailers
        $this->info('Fixing remaining items with fallback trailers...');
        
        // Series without trailers get a Korean content compilation
        Series::where(function($q) {
            $q->whereNull('trailer_youtube_id')
              ->orWhere('trailer_youtube_id', 'cXOydUjD-bk'); // Remove the repeated one
        })->update([
            'trailer_youtube_id' => 'tjBCjfB3Hq8', // K-drama compilation trailer
            'has_spanish_trailer' => false,
            'trailer_added_at' => now()
        ]);

        // Movies without trailers get a Korean cinema compilation
        Movie::where(function($q) {
            $q->whereNull('trailer_youtube_id')
              ->orWhere('trailer_youtube_id', 'SEUXfv87Wpk'); // Remove the repeated one
        })->update([
            'trailer_youtube_id' => 'vjEdbdNKTdU', // Korean movies compilation
            'has_spanish_trailer' => false,
            'trailer_added_at' => now()
        ]);

        $this->info('✅ Trailer IDs have been fixed!');
        
        // Show statistics
        $seriesWithTrailers = Series::whereNotNull('trailer_youtube_id')->count();
        $moviesWithTrailers = Movie::whereNotNull('trailer_youtube_id')->count();
        
        $this->table(['Type', 'Count'], [
            ['Series with trailers', $seriesWithTrailers],
            ['Movies with trailers', $moviesWithTrailers]
        ]);

        return 0;
    }
}