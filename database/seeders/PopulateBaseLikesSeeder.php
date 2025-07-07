<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Support\Facades\DB;

class PopulateBaseLikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Populating base likes for movies...');
        $this->populateMovieBaseLikes();
        
        $this->command->info('Populating base likes for series...');
        $this->populateSeriesBaseLikes();
        
        $this->command->info('Base likes population completed!');
    }

    /**
     * Populate base likes for movies
     */
    private function populateMovieBaseLikes(): void
    {
        // Get movies that don't have base_likes set
        $movies = Movie::where('base_likes', 0)->orWhereNull('base_likes')->get();
        
        $updateData = [];
        
        foreach ($movies as $movie) {
            // Calculate base likes based on movie popularity and rating
            $baseLikes = $this->calculateMovieBaseLikes($movie);
            
            $updateData[] = [
                'id' => $movie->id,
                'base_likes' => $baseLikes
            ];
            
            // Update in batches of 100
            if (count($updateData) >= 100) {
                $this->batchUpdateMovies($updateData);
                $updateData = [];
                $this->command->info('Updated ' . count($updateData) . ' movies...');
            }
        }
        
        // Update remaining movies
        if (!empty($updateData)) {
            $this->batchUpdateMovies($updateData);
        }
        
        $this->command->info('Updated ' . $movies->count() . ' movies total');
    }

    /**
     * Populate base likes for series
     */
    private function populateSeriesBaseLikes(): void
    {
        // Get series that don't have base_likes set
        $series = Series::where('base_likes', 0)->orWhereNull('base_likes')->get();
        
        $updateData = [];
        
        foreach ($series as $seriesItem) {
            // Calculate base likes based on series popularity and rating
            $baseLikes = $this->calculateSeriesBaseLikes($seriesItem);
            
            $updateData[] = [
                'id' => $seriesItem->id,
                'base_likes' => $baseLikes
            ];
            
            // Update in batches of 100
            if (count($updateData) >= 100) {
                $this->batchUpdateSeries($updateData);
                $updateData = [];
                $this->command->info('Updated ' . count($updateData) . ' series...');
            }
        }
        
        // Update remaining series
        if (!empty($updateData)) {
            $this->batchUpdateSeries($updateData);
        }
        
        $this->command->info('Updated ' . $series->count() . ' series total');
    }

    /**
     * Calculate base likes for a movie
     */
    private function calculateMovieBaseLikes(Movie $movie): int
    {
        $baseLikes = 100; // Minimum base likes
        
        // Factor in popularity (TMDB popularity score)
        if ($movie->popularity > 0) {
            $popularityBonus = min(200, (int)($movie->popularity * 2));
            $baseLikes += $popularityBonus;
        }
        
        // Factor in rating
        if ($movie->vote_average > 0) {
            $ratingMultiplier = max(0.5, $movie->vote_average / 10);
            $baseLikes = (int)($baseLikes * $ratingMultiplier);
        }
        
        // Factor in vote count (more votes = more popular)
        if ($movie->vote_count > 0) {
            $voteBonus = min(150, (int)($movie->vote_count / 10));
            $baseLikes += $voteBonus;
        }
        
        // Bonus for newer movies (within last 5 years)
        if ($movie->release_date && $movie->release_date->year >= (now()->year - 5)) {
            $baseLikes += 50;
        }
        
        // Add some randomness to make it feel organic
        $randomFactor = rand(-50, 100);
        $baseLikes += $randomFactor;
        
        return max(100, min(2000, $baseLikes)); // Cap between 100 and 2000
    }

    /**
     * Calculate base likes for a series
     */
    private function calculateSeriesBaseLikes(Series $series): int
    {
        $baseLikes = 120; // Slightly higher base for series
        
        // Factor in popularity
        if ($series->popularity > 0) {
            $popularityBonus = min(250, (int)($series->popularity * 2.5));
            $baseLikes += $popularityBonus;
        }
        
        // Factor in rating
        if ($series->vote_average > 0) {
            $ratingMultiplier = max(0.5, $series->vote_average / 10);
            $baseLikes = (int)($baseLikes * $ratingMultiplier);
        }
        
        // Factor in vote count
        if ($series->vote_count > 0) {
            $voteBonus = min(200, (int)($series->vote_count / 8));
            $baseLikes += $voteBonus;
        }
        
        // Bonus for series with more seasons (more content = more engagement)
        if ($series->number_of_seasons > 1) {
            $seasonBonus = min(100, ($series->number_of_seasons - 1) * 15);
            $baseLikes += $seasonBonus;
        }
        
        // Bonus for K-dramas (popular on the platform)
        if ($series->is_korean_drama || $series->country_code === 'KR') {
            $baseLikes += 80;
        }
        
        // Bonus for newer series
        if ($series->first_air_date && \Carbon\Carbon::parse($series->first_air_date)->year >= (now()->year - 3)) {
            $baseLikes += 60;
        }
        
        // Add randomness
        $randomFactor = rand(-60, 120);
        $baseLikes += $randomFactor;
        
        return max(120, min(2500, $baseLikes)); // Cap between 120 and 2500
    }

    /**
     * Batch update movies
     */
    private function batchUpdateMovies(array $updateData): void
    {
        foreach ($updateData as $data) {
            DB::table('movies')
                ->where('id', $data['id'])
                ->update(['base_likes' => $data['base_likes']]);
        }
    }

    /**
     * Batch update series
     */
    private function batchUpdateSeries(array $updateData): void
    {
        foreach ($updateData as $data) {
            DB::table('series')
                ->where('id', $data['id'])
                ->update(['base_likes' => $data['base_likes']]);
        }
    }
}
