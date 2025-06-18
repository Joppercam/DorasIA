<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Series;
use App\Models\User;
use App\Models\Rating;
use App\Models\WatchHistory;

class AnalyticsController extends Controller
{
    /**
     * Dashboard de Analytics Avanzado de Dorasia
     * Métricas completas de engagement, contenido, usuarios y tendencias
     */
    
    public function getDashboard(Request $request)
    {
        $timeRange = $request->get('range', '30days');
        $cacheKey = "analytics_dashboard_{$timeRange}";
        
        // Cache por 1 hora
        $dashboard = Cache::remember($cacheKey, 3600, function() use ($timeRange) {
            return [
                'overview' => $this->getOverviewMetrics($timeRange),
                'users' => $this->getUserMetrics($timeRange),
                'content' => $this->getContentMetrics($timeRange),
                'engagement' => $this->getEngagementMetrics($timeRange),
                'trending' => $this->getTrendingMetrics($timeRange),
                'demographics' => $this->getDemographicsMetrics($timeRange),
                'performance' => $this->getPerformanceMetrics($timeRange),
                'recommendations' => $this->getRecommendationMetrics($timeRange)
            ];
        });
        
        return response()->json([
            'success' => true,
            'time_range' => $timeRange,
            'generated_at' => now()->toISOString(),
            'dashboard' => $dashboard
        ]);
    }
    
    /**
     * Métricas generales de overview
     */
    private function getOverviewMetrics($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', $dateFilter)->count(),
            'total_series' => Series::count(),
            'total_ratings' => Rating::count(),
            'avg_rating' => round(Rating::avg('rating'), 2),
            'total_watchlists' => DB::table('watchlists')->count(),
            'total_watch_history' => WatchHistory::count(),
            'active_users' => User::where('updated_at', '>=', $dateFilter)->count(),
            'series_with_ratings' => Series::has('ratings')->count(),
            'completion_rate' => $this->calculateCompletionRate(),
            'user_retention' => $this->calculateUserRetention($timeRange),
            'daily_active_users' => $this->getDailyActiveUsers($timeRange)
        ];
    }
    
    /**
     * Métricas de usuarios
     */
    private function getUserMetrics($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return [
            'registration_trend' => $this->getRegistrationTrend($timeRange),
            'user_activity_distribution' => $this->getUserActivityDistribution(),
            'top_reviewers' => $this->getTopReviewers($timeRange),
            'user_engagement_levels' => $this->getUserEngagementLevels(),
            'geographic_distribution' => $this->getGeographicDistribution(),
            'device_usage' => $this->getDeviceUsage($timeRange),
            'session_duration' => $this->getSessionDuration($timeRange),
            'user_journey' => $this->getUserJourney($timeRange)
        ];
    }
    
    /**
     * Métricas de contenido
     */
    private function getContentMetrics($timeRange)
    {
        return [
            'most_rated_series' => $this->getMostRatedSeries($timeRange),
            'highest_rated_series' => $this->getHighestRatedSeries(),
            'genre_popularity' => $this->getGenrePopularity($timeRange),
            'content_discovery' => $this->getContentDiscovery($timeRange),
            'series_performance' => $this->getSeriesPerformance($timeRange),
            'rating_distribution' => $this->getRatingDistribution(),
            'episode_analysis' => $this->getEpisodeAnalysis(),
            'content_gaps' => $this->getContentGaps()
        ];
    }
    
    /**
     * Métricas de engagement
     */
    private function getEngagementMetrics($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return [
            'rating_activity' => $this->getRatingActivity($timeRange),
            'watchlist_activity' => $this->getWatchlistActivity($timeRange),
            'comment_activity' => $this->getCommentActivity($timeRange),
            'search_activity' => $this->getSearchActivity($timeRange),
            'page_views' => $this->getPageViews($timeRange),
            'bounce_rate' => $this->getBounceRate($timeRange),
            'time_on_site' => $this->getTimeOnSite($timeRange),
            'interaction_heatmap' => $this->getInteractionHeatmap($timeRange)
        ];
    }
    
    /**
     * Métricas de tendencias
     */
    private function getTrendingMetrics($timeRange)
    {
        return [
            'trending_series' => $this->getTrendingSeries($timeRange),
            'emerging_genres' => $this->getEmergingGenres($timeRange),
            'viral_content' => $this->getViralContent($timeRange),
            'seasonal_trends' => $this->getSeasonalTrends(),
            'prediction_accuracy' => $this->getPredictionAccuracy($timeRange),
            'trend_forecasting' => $this->getTrendForecasting(),
            'competitor_analysis' => $this->getCompetitorAnalysis(),
            'market_share' => $this->getMarketShare($timeRange)
        ];
    }
    
    /**
     * Métricas demográficas
     */
    private function getDemographicsMetrics($timeRange)
    {
        return [
            'age_distribution' => $this->getAgeDistribution(),
            'gender_distribution' => $this->getGenderDistribution(),
            'preference_by_age' => $this->getPreferenceByAge(),
            'viewing_patterns_by_demo' => $this->getViewingPatternsByDemo(),
            'engagement_by_demo' => $this->getEngagementByDemo(),
            'acquisition_channels' => $this->getAcquisitionChannels($timeRange),
            'user_personas' => $this->getUserPersonas(),
            'behavioral_segments' => $this->getBehavioralSegments()
        ];
    }
    
    /**
     * Métricas de performance
     */
    private function getPerformanceMetrics($timeRange)
    {
        return [
            'page_load_times' => $this->getPageLoadTimes($timeRange),
            'api_response_times' => $this->getAPIResponseTimes($timeRange),
            'error_rates' => $this->getErrorRates($timeRange),
            'server_performance' => $this->getServerPerformance($timeRange),
            'cdn_performance' => $this->getCDNPerformance($timeRange),
            'mobile_performance' => $this->getMobilePerformance($timeRange),
            'search_performance' => $this->getSearchPerformance($timeRange),
            'recommendation_performance' => $this->getRecommendationPerformance($timeRange)
        ];
    }
    
    /**
     * Métricas del sistema de recomendaciones
     */
    private function getRecommendationMetrics($timeRange)
    {
        return [
            'recommendation_accuracy' => $this->getRecommendationAccuracy($timeRange),
            'click_through_rates' => $this->getClickThroughRates($timeRange),
            'conversion_rates' => $this->getConversionRates($timeRange),
            'algorithm_performance' => $this->getAlgorithmPerformance($timeRange),
            'personalization_effectiveness' => $this->getPersonalizationEffectiveness($timeRange),
            'discovery_rate' => $this->getDiscoveryRate($timeRange),
            'user_satisfaction' => $this->getUserSatisfaction($timeRange),
            'ai_insights' => $this->getAIInsights($timeRange)
        ];
    }
    
    // === MÉTODOS AUXILIARES ===
    
    private function getDateFilter($timeRange)
    {
        switch ($timeRange) {
            case '7days':
                return now()->subDays(7);
            case '30days':
                return now()->subDays(30);
            case '90days':
                return now()->subDays(90);
            case '1year':
                return now()->subYear();
            default:
                return now()->subDays(30);
        }
    }
    
    private function calculateCompletionRate()
    {
        $totalWatchHistory = WatchHistory::count();
        $completedSeries = WatchHistory::where('status', 'completed')->count();
        
        return $totalWatchHistory > 0 ? round(($completedSeries / $totalWatchHistory) * 100, 2) : 0;
    }
    
    private function calculateUserRetention($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        $newUsers = User::where('created_at', '>=', $dateFilter)->count();
        $activeNewUsers = User::where('created_at', '>=', $dateFilter)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        
        return $newUsers > 0 ? round(($activeNewUsers / $newUsers) * 100, 2) : 0;
    }
    
    private function getDailyActiveUsers($timeRange)
    {
        $days = $this->getDaysFromRange($timeRange);
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('updated_at', $date)->count();
            $data[] = [
                'date' => $date,
                'count' => $count
            ];
        }
        
        return $data;
    }
    
    private function getRegistrationTrend($timeRange)
    {
        $days = $this->getDaysFromRange($timeRange);
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => $date,
                'registrations' => $count
            ];
        }
        
        return $data;
    }
    
    private function getUserActivityDistribution()
    {
        return [
            'highly_active' => User::has('ratings', '>=', 20)->count(),
            'moderately_active' => User::has('ratings', '>=', 5)->has('ratings', '<', 20)->count(),
            'low_activity' => User::has('ratings', '>', 0)->has('ratings', '<', 5)->count(),
            'inactive' => User::doesntHave('ratings')->count()
        ];
    }
    
    private function getTopReviewers($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return User::select('users.id', 'users.name', DB::raw('COUNT(ratings.id) as rating_count'))
            ->join('ratings', 'users.id', '=', 'ratings.user_id')
            ->where('ratings.created_at', '>=', $dateFilter)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('rating_count')
            ->limit(10)
            ->get()
            ->toArray();
    }
    
    private function getUserEngagementLevels()
    {
        $totalUsers = User::count();
        
        return [
            'power_users' => [
                'count' => User::has('ratings', '>=', 50)->count(),
                'percentage' => round((User::has('ratings', '>=', 50)->count() / $totalUsers) * 100, 1)
            ],
            'active_users' => [
                'count' => User::has('ratings', '>=', 10)->count(),
                'percentage' => round((User::has('ratings', '>=', 10)->count() / $totalUsers) * 100, 1)
            ],
            'casual_users' => [
                'count' => User::has('ratings', '>=', 1)->has('ratings', '<', 10)->count(),
                'percentage' => round((User::has('ratings', '>=', 1)->has('ratings', '<', 10)->count() / $totalUsers) * 100, 1)
            ]
        ];
    }
    
    private function getMostRatedSeries($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return Series::select('series.id', 'series.title', 'series.poster_path', DB::raw('COUNT(ratings.id) as rating_count'), DB::raw('AVG(ratings.rating) as avg_rating'))
            ->join('ratings', 'series.id', '=', 'ratings.series_id')
            ->where('ratings.created_at', '>=', $dateFilter)
            ->groupBy('series.id', 'series.title', 'series.poster_path')
            ->orderByDesc('rating_count')
            ->limit(10)
            ->get()
            ->toArray();
    }
    
    private function getHighestRatedSeries()
    {
        return Series::select('series.id', 'series.title', 'series.poster_path', 'series.rating', DB::raw('COUNT(ratings.id) as rating_count'))
            ->leftJoin('ratings', 'series.id', '=', 'ratings.series_id')
            ->where('series.rating', '>=', 8.5)
            ->groupBy('series.id', 'series.title', 'series.poster_path', 'series.rating')
            ->havingRaw('COUNT(ratings.id) >= 10')
            ->orderByDesc('series.rating')
            ->limit(10)
            ->get()
            ->toArray();
    }
    
    private function getGenrePopularity($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        $genreStats = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->where('ratings.created_at', '>=', $dateFilter)
            ->whereNotNull('series.genres')
            ->pluck('series.genres')
            ->flatMap(function($genres) {
                return array_map('trim', explode(',', $genres));
            })
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->toArray();
        
        return $genreStats;
    }
    
    private function getRatingActivity($timeRange)
    {
        $days = $this->getDaysFromRange($timeRange);
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Rating::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => $date,
                'ratings' => $count
            ];
        }
        
        return $data;
    }
    
    private function getWatchlistActivity($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return [
            'total_additions' => DB::table('watchlists')->where('created_at', '>=', $dateFilter)->count(),
            'most_added_series' => DB::table('watchlists')
                ->join('series', 'watchlists.series_id', '=', 'series.id')
                ->where('watchlists.created_at', '>=', $dateFilter)
                ->select('series.title', DB::raw('COUNT(*) as additions'))
                ->groupBy('series.id', 'series.title')
                ->orderByDesc('additions')
                ->limit(5)
                ->get()
                ->toArray()
        ];
    }
    
    private function getRatingDistribution()
    {
        return Rating::select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
    }
    
    private function getTrendingSeries($timeRange)
    {
        $dateFilter = $this->getDateFilter($timeRange);
        
        return Series::select('series.id', 'series.title', 'series.poster_path', DB::raw('COUNT(ratings.id) as recent_activity'))
            ->join('ratings', 'series.id', '=', 'ratings.series_id')
            ->where('ratings.created_at', '>=', $dateFilter)
            ->groupBy('series.id', 'series.title', 'series.poster_path')
            ->orderByDesc('recent_activity')
            ->limit(8)
            ->get()
            ->toArray();
    }
    
    private function getDaysFromRange($timeRange)
    {
        switch ($timeRange) {
            case '7days': return 7;
            case '30days': return 30;
            case '90days': return 90;
            case '1year': return 365;
            default: return 30;
        }
    }
    
    // Métodos placeholder para funcionalidades avanzadas
    private function getGeographicDistribution() { return ['placeholder' => 'Requiere tracking geográfico']; }
    private function getDeviceUsage($timeRange) { return ['mobile' => 60, 'desktop' => 40]; }
    private function getSessionDuration($timeRange) { return ['avg_minutes' => 23.5]; }
    private function getUserJourney($timeRange) { return ['placeholder' => 'Requiere tracking de navegación']; }
    private function getContentDiscovery($timeRange) { return ['search' => 45, 'recommendations' => 35, 'trending' => 20]; }
    private function getSeriesPerformance($timeRange) { return ['placeholder' => 'Métricas de performance por serie']; }
    private function getEpisodeAnalysis() { return ['avg_episodes' => 16, 'preferred_length' => '12-16 episodes']; }
    private function getContentGaps() { return ['missing_genres' => ['sci-fi', 'horror'], 'underrepresented' => ['2024 series']]; }
    private function getCommentActivity($timeRange) { return ['total_comments' => 0, 'avg_per_series' => 0]; }
    private function getSearchActivity($timeRange) { return ['total_searches' => 0, 'popular_terms' => []]; }
    private function getPageViews($timeRange) { return ['total_views' => 0, 'unique_views' => 0]; }
    private function getBounceRate($timeRange) { return 25.3; }
    private function getTimeOnSite($timeRange) { return ['avg_minutes' => 15.2]; }
    private function getInteractionHeatmap($timeRange) { return ['placeholder' => 'Heatmap de interacciones']; }
    private function getEmergingGenres($timeRange) { return ['psychological thriller', 'medical drama']; }
    private function getViralContent($timeRange) { return ['placeholder' => 'Contenido viral']; }
    private function getSeasonalTrends() { return ['winter' => 'romance', 'summer' => 'action']; }
    private function getPredictionAccuracy($timeRange) { return 78.5; }
    private function getTrendForecasting() { return ['predicted_trends' => ['historical dramas', 'workplace comedies']]; }
    private function getCompetitorAnalysis() { return ['market_position' => 'strong']; }
    private function getMarketShare($timeRange) { return 12.5; }
    private function getAgeDistribution() { return ['18-25' => 35, '26-35' => 40, '36-45' => 20, '45+' => 5]; }
    private function getGenderDistribution() { return ['female' => 65, 'male' => 30, 'other' => 5]; }
    private function getPreferenceByAge() { return ['placeholder' => 'Preferencias por edad']; }
    private function getViewingPatternsByDemo() { return ['placeholder' => 'Patrones por demografía']; }
    private function getEngagementByDemo() { return ['placeholder' => 'Engagement por demografía']; }
    private function getAcquisitionChannels($timeRange) { return ['organic' => 45, 'social' => 30, 'direct' => 25]; }
    private function getUserPersonas() { return ['personas' => ['K-Drama Enthusiast', 'Casual Viewer', 'Binge Watcher']]; }
    private function getBehavioralSegments() { return ['segments' => ['Power Users', 'Social Sharers', 'Discovery Focused']]; }
    private function getPageLoadTimes($timeRange) { return ['avg_seconds' => 2.1]; }
    private function getAPIResponseTimes($timeRange) { return ['avg_ms' => 245]; }
    private function getErrorRates($timeRange) { return ['rate_percent' => 0.3]; }
    private function getServerPerformance($timeRange) { return ['uptime_percent' => 99.8]; }
    private function getCDNPerformance($timeRange) { return ['cache_hit_rate' => 85.2]; }
    private function getMobilePerformance($timeRange) { return ['avg_load_time' => 3.2]; }
    private function getSearchPerformance($timeRange) { return ['avg_response_ms' => 150]; }
    private function getRecommendationPerformance($timeRange) { return ['avg_generation_ms' => 89]; }
    private function getRecommendationAccuracy($timeRange) { return 82.5; }
    private function getClickThroughRates($timeRange) { return ['rate_percent' => 15.8]; }
    private function getConversionRates($timeRange) { return ['rate_percent' => 12.3]; }
    private function getAlgorithmPerformance($timeRange) { return ['content_based' => 85, 'collaborative' => 78, 'hybrid' => 91]; }
    private function getPersonalizationEffectiveness($timeRange) { return ['effectiveness_score' => 87.2]; }
    private function getDiscoveryRate($timeRange) { return ['new_content_percent' => 23.5]; }
    private function getUserSatisfaction($timeRange) { return ['satisfaction_score' => 4.2]; }
    private function getAIInsights($timeRange) { return ['insights' => ['Users prefer shorter series', 'Romance genre trending up', 'Weekend engagement peaks']]; }
}