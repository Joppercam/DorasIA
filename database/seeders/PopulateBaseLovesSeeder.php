<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Series;

class PopulateBaseLovesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Populating base_loves for movies...');
        
        // Update movies with random base_loves (50-200, less than base_likes)
        Movie::chunk(100, function ($movies) {
            foreach ($movies as $movie) {
                // Generate random love count that's 40-70% of likes count
                $maxLoves = min(200, max(50, round($movie->base_likes * 0.7)));
                $minLoves = min($maxLoves, max(50, round($movie->base_likes * 0.4)));
                
                $baseLoves = rand($minLoves, $maxLoves);
                
                $movie->update(['base_loves' => $baseLoves]);
            }
        });
        
        $this->command->info('Populating base_loves for series...');
        
        // Update series with random base_loves (80-300, less than base_likes)
        Series::chunk(100, function ($series) {
            foreach ($series as $seriesItem) {
                // Generate random love count that's 40-70% of likes count
                $maxLoves = min(300, max(80, round($seriesItem->base_likes * 0.7)));
                $minLoves = min($maxLoves, max(80, round($seriesItem->base_likes * 0.4)));
                
                $baseLoves = rand($minLoves, $maxLoves);
                
                $seriesItem->update(['base_loves' => $baseLoves]);
            }
        });
        
        $moviesCount = Movie::count();
        $seriesCount = Series::count();
        
        $this->command->info("Successfully populated base_loves for {$moviesCount} movies and {$seriesCount} series!");
        
        // Show some statistics
        $avgMovieLikes = Movie::avg('base_likes');
        $avgMovieLoves = Movie::avg('base_loves');
        $avgSeriesLikes = Series::avg('base_likes');
        $avgSeriesLoves = Series::avg('base_loves');
        
        $this->command->info("Movies - Avg Likes: " . round($avgMovieLikes, 2) . ", Avg Loves: " . round($avgMovieLoves, 2));
        $this->command->info("Series - Avg Likes: " . round($avgSeriesLikes, 2) . ", Avg Loves: " . round($avgSeriesLoves, 2));
    }
}