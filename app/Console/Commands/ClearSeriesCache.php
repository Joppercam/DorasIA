<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearSeriesCache extends Command
{
    protected $signature = 'cache:clear-series {--pattern=* : Specific cache patterns to clear}';
    protected $description = 'Clear series-related cache for better performance';

    public function handle()
    {
        $patterns = $this->option('pattern');
        
        if (empty($patterns)) {
            // Clear all series-related cache
            $this->clearAllSeriesCache();
        } else {
            // Clear specific patterns
            foreach ($patterns as $pattern) {
                $this->clearCachePattern($pattern);
            }
        }

        $this->info('Series cache cleared successfully!');
    }

    private function clearAllSeriesCache()
    {
        $cacheKeys = [
            'hero.featured_candidates',
            'series.popular',
            'series.top_rated',
            'series.recent',
            'series.genre.drama',
            'series.genre.comedy',
            'series.genre.romance',
            'series.genre.action',
            'series.genre.mystery',
            'series.genre.historical',
            'genres.all',
            'series.years',
            'series.cast.*'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            $this->line("Cleared cache: {$key}");
        }
    }

    private function clearCachePattern($pattern)
    {
        switch ($pattern) {
            case 'genre':
            case 'genres':
                $genreKeys = [
                    'series.genre.drama',
                    'series.genre.comedy',
                    'series.genre.romance',
                    'series.genre.action',
                    'series.genre.mystery',
                    'series.genre.historical',
                    'genres.all'
                ];
                foreach ($genreKeys as $key) {
                    Cache::forget($key);
                    $this->line("Cleared cache: {$key}");
                }
                break;

            case 'hero':
                Cache::forget('hero.featured_candidates');
                $this->line("Cleared cache: hero.featured_candidates");
                break;

            case 'popular':
                Cache::forget('series.popular');
                $this->line("Cleared cache: series.popular");
                break;

            case 'rated':
            case 'top':
                Cache::forget('series.top_rated');
                $this->line("Cleared cache: series.top_rated");
                break;

            case 'recent':
                Cache::forget('series.recent');
                $this->line("Cleared cache: series.recent");
                break;

            default:
                Cache::forget($pattern);
                $this->line("Cleared cache: {$pattern}");
                break;
        }
    }
}