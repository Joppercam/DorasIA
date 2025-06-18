<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Series;
use App\Models\User;
use App\Models\Rating;
use App\Models\WatchHistory;

class RecommendationController extends Controller
{
    /**
     * Motor de Recomendaciones IA de Dorasia
     * Utiliza múltiples algoritmos para generar recomendaciones personalizadas
     */
    
    public function getRecommendations(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->get('limit', 12);
        
        if (!$userId) {
            // Recomendaciones para usuarios no logueados
            return $this->getPopularRecommendations($limit);
        }
        
        try {
            // 1. Análisis de perfil del usuario
            $userProfile = $this->analyzeUserProfile($userId);
            
            // 2. Recomendaciones basadas en contenido
            $contentBased = $this->getContentBasedRecommendations($userId, $userProfile);
            
            // 3. Filtrado colaborativo
            $collaborative = $this->getCollaborativeRecommendations($userId);
            
            // 4. Trending personalizado
            $trending = $this->getPersonalizedTrending($userProfile);
            
            // 5. Combinar y rankear todas las recomendaciones
            $recommendations = $this->combineRecommendations([
                'content' => $contentBased,
                'collaborative' => $collaborative,
                'trending' => $trending
            ], $limit);
            
            // Log para analytics
            Log::info('Recommendations generated', [
                'user_id' => $userId,
                'profile' => $userProfile,
                'count' => count($recommendations)
            ]);
            
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'user_profile' => $userProfile,
                'debug' => config('app.debug') ? [
                    'content_based' => count($contentBased),
                    'collaborative' => count($collaborative),
                    'trending' => count($trending)
                ] : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating recommendations', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return $this->getPopularRecommendations($limit);
        }
    }
    
    /**
     * Analizar perfil del usuario basado en su actividad
     */
    private function analyzeUserProfile($userId)
    {
        // Géneros favoritos basados en ratings
        $favoriteGenres = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.rating', '>=', 4)
            ->selectRaw('series.genres, COUNT(*) as count, AVG(ratings.rating) as avg_rating')
            ->groupBy('series.genres')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $genres = explode(',', $item->genres ?? '');
                return array_map('trim', $genres);
            })
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take(3)
            ->keys()
            ->toArray();
        
        // Años favoritos
        $favoriteYears = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.rating', '>=', 4)
            ->selectRaw('YEAR(series.first_air_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderByDesc('count')
            ->limit(3)
            ->pluck('year')
            ->toArray();
        
        // Rating promedio del usuario
        $avgRating = DB::table('ratings')
            ->where('user_id', $userId)
            ->avg('rating') ?? 3.5;
        
        // Preferencia por series largas o cortas
        $episodePreference = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.rating', '>=', 4)
            ->selectRaw('
                AVG(series.number_of_episodes) as avg_episodes,
                COUNT(CASE WHEN series.number_of_episodes <= 16 THEN 1 END) as short_series,
                COUNT(CASE WHEN series.number_of_episodes > 16 THEN 1 END) as long_series
            ')
            ->first();
        
        // Actores favoritos
        $favoriteActors = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->join('series_actors', 'series.id', '=', 'series_actors.series_id')
            ->join('actors', 'series_actors.actor_id', '=', 'actors.id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.rating', '>=', 4)
            ->select('actors.id', 'actors.name')
            ->groupBy('actors.id', 'actors.name')
            ->havingRaw('COUNT(*) >= 2')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('actors.id')
            ->toArray();
        
        return [
            'favorite_genres' => $favoriteGenres,
            'favorite_years' => $favoriteYears,
            'avg_rating' => round($avgRating, 1),
            'prefers_short_series' => ($episodePreference->short_series ?? 0) > ($episodePreference->long_series ?? 0),
            'favorite_actors' => $favoriteActors,
            'total_ratings' => DB::table('ratings')->where('user_id', $userId)->count(),
            'personality' => $this->determineUserPersonality($avgRating, $favoriteGenres)
        ];
    }
    
    /**
     * Determinar personalidad del usuario para recomendaciones
     */
    private function determineUserPersonality($avgRating, $genres)
    {
        $personality = [];
        
        // Crítico vs Generoso
        if ($avgRating < 3.0) {
            $personality[] = 'crítico';
        } elseif ($avgRating > 4.0) {
            $personality[] = 'generoso';
        } else {
            $personality[] = 'equilibrado';
        }
        
        // Preferencias de género
        foreach ($genres as $genre) {
            switch (strtolower($genre)) {
                case 'romance':
                    $personality[] = 'romántico';
                    break;
                case 'thriller':
                case 'mystery':
                    $personality[] = 'suspense';
                    break;
                case 'comedy':
                    $personality[] = 'divertido';
                    break;
                case 'drama':
                    $personality[] = 'emocional';
                    break;
                case 'action':
                    $personality[] = 'acción';
                    break;
            }
        }
        
        return $personality;
    }
    
    /**
     * Recomendaciones basadas en contenido similar
     */
    private function getContentBasedRecommendations($userId, $userProfile)
    {
        // Series que el usuario NO ha visto
        $watchedSeries = DB::table('ratings')
            ->where('user_id', $userId)
            ->pluck('series_id')
            ->toArray();
        
        $watchlistSeries = DB::table('watchlists')
            ->where('user_id', $userId)
            ->pluck('series_id')
            ->toArray();
        
        $excludedSeries = array_merge($watchedSeries, $watchlistSeries);
        
        $query = Series::whereNotIn('id', $excludedSeries);
        
        // Filtrar por géneros favoritos
        if (!empty($userProfile['favorite_genres'])) {
            $query->where(function($q) use ($userProfile) {
                foreach ($userProfile['favorite_genres'] as $genre) {
                    $q->orWhere('genres', 'LIKE', "%{$genre}%");
                }
            });
        }
        
        // Filtrar por años favoritos
        if (!empty($userProfile['favorite_years'])) {
            $query->whereIn(DB::raw('YEAR(first_air_date)'), $userProfile['favorite_years']);
        }
        
        // Preferencia por duración
        if (isset($userProfile['prefers_short_series'])) {
            if ($userProfile['prefers_short_series']) {
                $query->where('number_of_episodes', '<=', 16);
            } else {
                $query->where('number_of_episodes', '>', 16);
            }
        }
        
        // Filtrar por actores favoritos
        if (!empty($userProfile['favorite_actors'])) {
            $query->whereHas('actors', function($q) use ($userProfile) {
                $q->whereIn('actors.id', $userProfile['favorite_actors']);
            });
        }
        
        return $query->with(['ratings'])
            ->orderByDesc('rating')
            ->limit(8)
            ->get()
            ->map(function($series) {
                return $this->formatSeriesRecommendation($series, 'content-based');
            })
            ->toArray();
    }
    
    /**
     * Filtrado colaborativo - usuarios con gustos similares
     */
    private function getCollaborativeRecommendations($userId)
    {
        // Encontrar usuarios con gustos similares
        $similarUsers = DB::select("
            SELECT 
                r2.user_id,
                COUNT(*) as common_ratings,
                AVG(ABS(r1.rating - r2.rating)) as avg_diff
            FROM ratings r1
            JOIN ratings r2 ON r1.series_id = r2.series_id
            WHERE r1.user_id = ? 
                AND r2.user_id != ?
                AND ABS(r1.rating - r2.rating) <= 1
            GROUP BY r2.user_id
            HAVING common_ratings >= 3
            ORDER BY common_ratings DESC, avg_diff ASC
            LIMIT 10
        ", [$userId, $userId]);
        
        if (empty($similarUsers)) {
            return [];
        }
        
        $similarUserIds = array_column($similarUsers, 'user_id');
        
        // Series bien valoradas por usuarios similares
        $recommendations = DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->whereIn('ratings.user_id', $similarUserIds)
            ->where('ratings.rating', '>=', 4)
            ->whereNotIn('series.id', function($query) use ($userId) {
                $query->select('series_id')
                    ->from('ratings')
                    ->where('user_id', $userId);
            })
            ->select('series.*', DB::raw('AVG(ratings.rating) as avg_rating'), DB::raw('COUNT(*) as recommendation_count'))
            ->groupBy('series.id')
            ->orderByDesc('recommendation_count')
            ->orderByDesc('avg_rating')
            ->limit(6)
            ->get();
        
        return $recommendations->map(function($series) {
            return $this->formatSeriesRecommendation($series, 'collaborative');
        })->toArray();
    }
    
    /**
     * Trending personalizado basado en perfil
     */
    private function getPersonalizedTrending($userProfile)
    {
        $query = Series::select('series.*', DB::raw('COUNT(ratings.id) as recent_ratings'))
            ->leftJoin('ratings', function($join) {
                $join->on('series.id', '=', 'ratings.series_id')
                     ->where('ratings.created_at', '>=', now()->subDays(30));
            })
            ->where('series.rating', '>=', 7.0)
            ->groupBy('series.id');
        
        // Aplicar filtros del perfil del usuario
        if (!empty($userProfile['favorite_genres'])) {
            $query->where(function($q) use ($userProfile) {
                foreach ($userProfile['favorite_genres'] as $genre) {
                    $q->orWhere('genres', 'LIKE', "%{$genre}%");
                }
            });
        }
        
        return $query->orderByDesc('recent_ratings')
            ->orderByDesc('rating')
            ->limit(4)
            ->get()
            ->map(function($series) {
                return $this->formatSeriesRecommendation($series, 'trending');
            })
            ->toArray();
    }
    
    /**
     * Combinar todas las recomendaciones con pesos
     */
    private function combineRecommendations($recommendations, $limit)
    {
        $combined = [];
        $weights = [
            'content' => 0.5,      // 50% - Basado en contenido
            'collaborative' => 0.3, // 30% - Filtrado colaborativo
            'trending' => 0.2       // 20% - Trending
        ];
        
        foreach ($recommendations as $type => $items) {
            foreach ($items as $index => $item) {
                $seriesId = $item['id'];
                $score = (count($items) - $index) * $weights[$type];
                
                if (isset($combined[$seriesId])) {
                    $combined[$seriesId]['score'] += $score;
                    $combined[$seriesId]['reasons'][] = $type;
                } else {
                    $item['score'] = $score;
                    $item['reasons'] = [$type];
                    $combined[$seriesId] = $item;
                }
            }
        }
        
        // Ordenar por score y limitar
        $sorted = collect($combined)->sortByDesc('score')->take($limit);
        
        return $sorted->values()->toArray();
    }
    
    /**
     * Formatear serie para recomendación
     */
    private function formatSeriesRecommendation($series, $reason)
    {
        return [
            'id' => $series->id,
            'title' => $series->title,
            'poster_path' => $series->poster_path,
            'rating' => $series->rating,
            'year' => $series->first_air_date ? date('Y', strtotime($series->first_air_date)) : null,
            'genres' => $series->genres,
            'episodes' => $series->number_of_episodes,
            'overview' => substr($series->overview, 0, 150) . '...',
            'reason' => $reason,
            'recommendation_score' => $series->avg_rating ?? $series->rating
        ];
    }
    
    /**
     * Recomendaciones populares para usuarios no logueados
     */
    private function getPopularRecommendations($limit)
    {
        $popular = Series::select('series.*', DB::raw('COUNT(ratings.id) as rating_count'))
            ->leftJoin('ratings', 'series.id', '=', 'ratings.series_id')
            ->where('series.rating', '>=', 7.5)
            ->groupBy('series.id')
            ->orderByDesc('rating_count')
            ->orderByDesc('series.rating')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'recommendations' => $popular->map(function($series) {
                return $this->formatSeriesRecommendation($series, 'popular');
            }),
            'user_profile' => null,
            'message' => 'Recomendaciones populares - Inicia sesión para recomendaciones personalizadas'
        ]);
    }
    
    /**
     * Análisis de patrones de usuario para insights
     */
    public function getUserInsights(Request $request)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        
        $insights = [
            'viewing_patterns' => $this->getViewingPatterns($userId),
            'rating_behavior' => $this->getRatingBehavior($userId),
            'genre_evolution' => $this->getGenreEvolution($userId),
            'discovery_sources' => $this->getDiscoverySources($userId),
            'recommendations_accuracy' => $this->getRecommendationAccuracy($userId)
        ];
        
        return response()->json([
            'success' => true,
            'insights' => $insights
        ]);
    }
    
    private function getViewingPatterns($userId)
    {
        return [
            'total_watched' => WatchHistory::where('user_id', $userId)->count(),
            'avg_episodes_per_series' => DB::table('watch_histories')
                ->join('series', 'watch_histories.series_id', '=', 'series.id')
                ->where('watch_histories.user_id', $userId)
                ->avg('series.number_of_episodes'),
            'completion_rate' => DB::table('watch_histories')
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->count() / max(1, WatchHistory::where('user_id', $userId)->count()) * 100,
            'most_active_day' => $this->getMostActiveDay($userId),
            'binge_behavior' => $this->getBingeBehavior($userId)
        ];
    }
    
    private function getRatingBehavior($userId)
    {
        $ratings = DB::table('ratings')->where('user_id', $userId);
        
        return [
            'total_ratings' => $ratings->count(),
            'avg_rating' => round($ratings->avg('rating'), 1),
            'rating_distribution' => DB::table('ratings')
                ->where('user_id', $userId)
                ->select('rating', DB::raw('COUNT(*) as count'))
                ->groupBy('rating')
                ->orderBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
            'rating_consistency' => $this->getRatingConsistency($userId)
        ];
    }
    
    private function getMostActiveDay($userId)
    {
        return DB::table('watch_histories')
            ->where('user_id', $userId)
            ->select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->orderByDesc('count')
            ->first()
            ->day ?? 'N/A';
    }
    
    private function getBingeBehavior($userId)
    {
        // Detectar patrones de maratón basado en timestamps
        $sessions = DB::table('watch_histories')
            ->where('user_id', $userId)
            ->select('series_id', 'created_at')
            ->orderBy('created_at')
            ->get();
        
        $bingeCount = 0;
        $currentBinge = [];
        
        foreach ($sessions as $session) {
            if (empty($currentBinge)) {
                $currentBinge = [$session];
            } else {
                $lastSession = end($currentBinge);
                $timeDiff = strtotime($session->created_at) - strtotime($lastSession->created_at);
                
                if ($timeDiff < 86400 && $session->series_id === $lastSession->series_id) { // Mismo día, misma serie
                    $currentBinge[] = $session;
                } else {
                    if (count($currentBinge) >= 3) {
                        $bingeCount++;
                    }
                    $currentBinge = [$session];
                }
            }
        }
        
        return [
            'binge_sessions' => $bingeCount,
            'avg_session_length' => count($sessions) > 0 ? count($sessions) / max(1, $bingeCount) : 0
        ];
    }
    
    private function getRatingConsistency($userId)
    {
        // Calcular varianza en ratings para determinar consistencia
        $ratings = DB::table('ratings')->where('user_id', $userId)->pluck('rating');
        
        if ($ratings->count() < 2) return 'N/A';
        
        $mean = $ratings->avg();
        $variance = $ratings->map(function($rating) use ($mean) {
            return pow($rating - $mean, 2);
        })->avg();
        
        $stdDev = sqrt($variance);
        
        if ($stdDev < 0.5) return 'Muy consistente';
        if ($stdDev < 1.0) return 'Consistente';
        if ($stdDev < 1.5) return 'Moderado';
        return 'Variado';
    }
    
    private function getGenreEvolution($userId)
    {
        // Analizar cómo han cambiado las preferencias de género a lo largo del tiempo
        return DB::table('ratings')
            ->join('series', 'ratings.series_id', '=', 'series.id')
            ->where('ratings.user_id', $userId)
            ->select(
                DB::raw('YEAR(ratings.created_at) as year'),
                'series.genres',
                DB::raw('AVG(ratings.rating) as avg_rating')
            )
            ->groupBy('year', 'series.genres')
            ->orderBy('year')
            ->get()
            ->groupBy('year')
            ->map(function($yearData) {
                return $yearData->sortByDesc('avg_rating')->first();
            })
            ->toArray();
    }
    
    private function getDiscoverySources($userId)
    {
        // Analizar cómo descubre nuevas series (trending, recommendations, search, etc.)
        // Esto requeriría tracking adicional, por ahora retornamos placeholder
        return [
            'trending' => 30,
            'recommendations' => 25,
            'search' => 20,
            'social' => 15,
            'direct' => 10
        ];
    }
    
    private function getRecommendationAccuracy($userId)
    {
        // Medir precisión de recomendaciones pasadas
        // Placeholder - requiere tracking de clicks y ratings en recomendaciones
        return [
            'click_through_rate' => 0.15,
            'avg_rating_recommended' => 4.2,
            'completion_rate_recommended' => 0.78
        ];
    }
}