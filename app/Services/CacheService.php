<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Cache durations in seconds
     */
    const DURATION_SHORT = 300; // 5 minutes
    const DURATION_MEDIUM = 3600; // 1 hour
    const DURATION_LONG = 86400; // 1 day
    const DURATION_WEEK = 604800; // 1 week
    
    /**
     * Cache key prefixes
     */
    const PREFIX_TITLE = 'title:';
    const PREFIX_CATALOG = 'catalog:';
    const PREFIX_RATINGS = 'ratings:';
    const PREFIX_COMMENTS = 'comments:';
    const PREFIX_PROFILE = 'profile:';
    const PREFIX_TRENDING = 'trending:';
    const PREFIX_SEARCH = 'search:';
    const PREFIX_NEWS = 'news:';
    
    /**
     * Get or set title data with cache
     */
    public static function rememberTitle($titleId, $callback, $duration = self::DURATION_MEDIUM)
    {
        $key = self::PREFIX_TITLE . $titleId;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Invalidate title cache
     */
    public static function forgetTitle($titleId)
    {
        $key = self::PREFIX_TITLE . $titleId;
        Cache::forget($key);
        
        // Also clear related caches
        self::forgetTitleRatings($titleId);
        self::forgetTitleComments($titleId);
    }
    
    /**
     * Get or set catalog page data
     */
    public static function rememberCatalog($page, $filters, $callback, $duration = self::DURATION_SHORT)
    {
        $key = self::PREFIX_CATALOG . md5(serialize(['page' => $page, 'filters' => $filters]));
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Get or set title ratings stats
     */
    public static function rememberTitleRatings($titleId, $callback, $duration = self::DURATION_MEDIUM)
    {
        $key = self::PREFIX_RATINGS . $titleId;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Invalidate title ratings cache
     */
    public static function forgetTitleRatings($titleId)
    {
        $key = self::PREFIX_RATINGS . $titleId;
        Cache::forget($key);
    }
    
    /**
     * Get or set title comments count
     */
    public static function rememberTitleComments($titleId, $callback, $duration = self::DURATION_SHORT)
    {
        $key = self::PREFIX_COMMENTS . $titleId;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Invalidate title comments cache
     */
    public static function forgetTitleComments($titleId)
    {
        $key = self::PREFIX_COMMENTS . $titleId;
        Cache::forget($key);
    }
    
    /**
     * Get or set profile statistics
     */
    public static function rememberProfileStats($profileId, $callback, $duration = self::DURATION_MEDIUM)
    {
        $key = self::PREFIX_PROFILE . 'stats:' . $profileId;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Invalidate profile cache
     */
    public static function forgetProfile($profileId)
    {
        $keys = [
            self::PREFIX_PROFILE . 'stats:' . $profileId,
            self::PREFIX_PROFILE . 'activity:' . $profileId,
            self::PREFIX_PROFILE . 'followers:' . $profileId,
            self::PREFIX_PROFILE . 'following:' . $profileId,
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
    
    /**
     * Get or set trending titles
     */
    public static function rememberTrending($type, $callback, $duration = self::DURATION_MEDIUM)
    {
        $key = self::PREFIX_TRENDING . $type;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Get or set search results
     */
    public static function rememberSearch($query, $filters, $callback, $duration = self::DURATION_SHORT)
    {
        $key = self::PREFIX_SEARCH . md5(serialize(['q' => $query, 'filters' => $filters]));
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Get or set news article
     */
    public static function rememberNews($newsId, $callback, $duration = self::DURATION_LONG)
    {
        $key = self::PREFIX_NEWS . $newsId;
        return Cache::remember($key, $duration, $callback);
    }
    
    /**
     * Clear all caches (for admin use)
     */
    public static function clearAll()
    {
        Cache::flush();
    }
    
    /**
     * Clear specific cache type
     */
    public static function clearType($type)
    {
        $prefix = '';
        
        switch ($type) {
            case 'catalog':
                $prefix = self::PREFIX_CATALOG;
                break;
            case 'titles':
                $prefix = self::PREFIX_TITLE;
                break;
            case 'profiles':
                $prefix = self::PREFIX_PROFILE;
                break;
            case 'trending':
                $prefix = self::PREFIX_TRENDING;
                break;
            case 'search':
                $prefix = self::PREFIX_SEARCH;
                break;
            case 'news':
                $prefix = self::PREFIX_NEWS;
                break;
        }
        
        if ($prefix) {
            $redis = Redis::connection();
            $keys = $redis->keys(config('cache.prefix') . ':' . $prefix . '*');
            
            foreach ($keys as $key) {
                $redis->del($key);
            }
        }
    }
    
    /**
     * Cache warming - pre-populate important caches
     */
    public static function warmCache()
    {
        // Warm trending titles cache
        $types = ['all', 'movies', 'series', 'korean', 'japanese'];
        foreach ($types as $type) {
            self::rememberTrending($type, function () use ($type) {
                return \App\Models\Title::trending($type)->limit(20)->get();
            });
        }
        
        // Warm popular genres
        $genres = \App\Models\Genre::popular()->get();
        foreach ($genres as $genre) {
            self::rememberCatalog(1, ['genre' => $genre->slug], function () use ($genre) {
                return \App\Models\Title::whereHas('genres', function ($q) use ($genre) {
                    $q->where('genres.id', $genre->id);
                })->paginate(24);
            });
        }
    }
    
    /**
     * Get cache statistics
     */
    public static function getStats()
    {
        $redis = Redis::connection();
        $prefixes = [
            'titles' => self::PREFIX_TITLE,
            'catalog' => self::PREFIX_CATALOG,
            'profiles' => self::PREFIX_PROFILE,
            'trending' => self::PREFIX_TRENDING,
            'search' => self::PREFIX_SEARCH,
            'news' => self::PREFIX_NEWS,
        ];
        
        $stats = [];
        
        foreach ($prefixes as $name => $prefix) {
            $keys = $redis->keys(config('cache.prefix') . ':' . $prefix . '*');
            $stats[$name] = [
                'count' => count($keys),
                'memory' => 0,
            ];
            
            foreach ($keys as $key) {
                $stats[$name]['memory'] += $redis->memory('usage', $key);
            }
        }
        
        return $stats;
    }
}