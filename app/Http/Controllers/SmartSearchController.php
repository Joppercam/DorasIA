<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Series;
use App\Models\Actor;
use App\Models\Movie;

class SmartSearchController extends Controller
{
    /**
     * Motor de Búsqueda Semántica Inteligente de Dorasia
     * Utiliza NLP, análisis de contexto y machine learning para búsquedas avanzadas
     */
    
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        $limit = $request->get('limit', 20);
        $filters = $request->get('filters', []);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query muy corto',
                'suggestions' => $this->getPopularSearches()
            ]);
        }
        
        try {
            // 1. Análisis semántico de la query
            $queryAnalysis = $this->analyzeQuery($query);
            
            // 2. Búsqueda multi-dimensional
            $results = $this->performMultiSearch($query, $queryAnalysis, $filters, $limit);
            
            // 3. Ranking inteligente
            $rankedResults = $this->rankResults($results, $query, $queryAnalysis);
            
            // 4. Sugerencias y correcciones
            $suggestions = $this->generateSuggestions($query, $rankedResults);
            
            // Log para analytics
            Log::info('Smart search performed', [
                'query' => $query,
                'analysis' => $queryAnalysis,
                'results_count' => count($rankedResults),
                'filters' => $filters
            ]);
            
            return response()->json([
                'success' => true,
                'query' => $query,
                'analysis' => $queryAnalysis,
                'results' => $rankedResults,
                'suggestions' => $suggestions,
                'total' => count($rankedResults),
                'filters_applied' => $filters
            ]);
            
        } catch (\Exception $e) {
            Log::error('Smart search error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            
            // Fallback a búsqueda simple
            return $this->fallbackSearch($query, $limit);
        }
    }
    
    /**
     * Análisis semántico de la query de búsqueda
     */
    private function analyzeQuery($query)
    {
        $analysis = [
            'original' => $query,
            'normalized' => $this->normalizeQuery($query),
            'tokens' => $this->tokenizeQuery($query),
            'intent' => $this->detectIntent($query),
            'entities' => $this->extractEntities($query),
            'mood' => $this->detectMood($query),
            'temporal' => $this->extractTemporal($query),
            'similarity_queries' => $this->findSimilarQueries($query)
        ];
        
        return $analysis;
    }
    
    /**
     * Normalizar query para búsqueda
     */
    private function normalizeQuery($query)
    {
        // Convertir a minúsculas
        $normalized = strtolower($query);
        
        // Remover acentos
        $normalized = $this->removeAccents($normalized);
        
        // Limpiar caracteres especiales
        $normalized = preg_replace('/[^\w\s]/', ' ', $normalized);
        
        // Normalizar espacios
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));
        
        return $normalized;
    }
    
    /**
     * Tokenizar query en palabras clave
     */
    private function tokenizeQuery($query)
    {
        $stopWords = [
            'el', 'la', 'los', 'las', 'de', 'del', 'en', 'un', 'una', 'y', 'o',
            'que', 'se', 'por', 'con', 'para', 'como', 'sobre', 'donde', 'cuando'
        ];
        
        $words = explode(' ', $this->normalizeQuery($query));
        $tokens = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) >= 2 && !in_array($word, $stopWords);
        });
        
        return array_values($tokens);
    }
    
    /**
     * Detectar intención de búsqueda
     */
    private function detectIntent($query)
    {
        $intents = [
            'series_by_genre' => ['romance', 'comedia', 'thriller', 'drama', 'accion', 'misterio', 'historico'],
            'series_by_year' => ['2023', '2022', '2021', '2020', 'reciente', 'nuevo', 'antiguo'],
            'series_by_length' => ['corto', 'largo', 'episodios', 'temporada', 'capitulos'],
            'series_by_mood' => ['triste', 'feliz', 'romantico', 'divertido', 'emocionante', 'relajante'],
            'actor_search' => ['actor', 'actriz', 'protagonista', 'elenco', 'reparto'],
            'recommendation' => ['recomiendan', 'sugieren', 'parecido', 'similar', 'como'],
            'trending' => ['popular', 'trending', 'moda', 'famoso', 'viral', 'actual']
        ];
        
        $query_lower = strtolower($query);
        $detected_intents = [];
        
        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($query_lower, $keyword) !== false) {
                    $detected_intents[] = $intent;
                    break;
                }
            }
        }
        
        return empty($detected_intents) ? ['general_search'] : array_unique($detected_intents);
    }
    
    /**
     * Extraer entidades (nombres, géneros, años, etc.)
     */
    private function extractEntities($query)
    {
        $entities = [
            'genres' => [],
            'years' => [],
            'actors' => [],
            'countries' => [],
            'emotions' => []
        ];
        
        // Extraer años
        preg_match_all('/\b(19|20)\d{2}\b/', $query, $years);
        $entities['years'] = $years[0];
        
        // Extraer géneros conocidos
        $genres = ['romance', 'comedia', 'drama', 'thriller', 'accion', 'misterio', 'historico', 'fantasia'];
        foreach ($genres as $genre) {
            if (stripos($query, $genre) !== false) {
                $entities['genres'][] = $genre;
            }
        }
        
        // Extraer nombres de actores (búsqueda en BD)
        $words = $this->tokenizeQuery($query);
        if (count($words) <= 3) { // Solo buscar actores si son pocas palabras
            $actors = Actor::where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'LIKE', "%{$word}%");
                }
            })->limit(5)->get(['id', 'name']);
            
            $entities['actors'] = $actors->toArray();
        }
        
        // Extraer emociones/mood
        $emotions = ['triste', 'alegre', 'romantico', 'emocionante', 'relajante', 'intenso'];
        foreach ($emotions as $emotion) {
            if (stripos($query, $emotion) !== false) {
                $entities['emotions'][] = $emotion;
            }
        }
        
        return $entities;
    }
    
    /**
     * Detectar mood/sentimiento de la búsqueda
     */
    private function detectMood($query)
    {
        $moodPatterns = [
            'happy' => ['feliz', 'alegre', 'divertido', 'comedia', 'risa'],
            'sad' => ['triste', 'llorar', 'melancolico', 'drama', 'dolor'],
            'romantic' => ['amor', 'romance', 'pareja', 'boda', 'corazon'],
            'exciting' => ['emocionante', 'accion', 'aventura', 'adrenalina'],
            'relaxing' => ['relajante', 'calma', 'tranquilo', 'paz'],
            'mysterious' => ['misterio', 'suspense', 'thriller', 'enigma']
        ];
        
        $query_lower = strtolower($query);
        $detectedMoods = [];
        
        foreach ($moodPatterns as $mood => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($query_lower, $keyword) !== false) {
                    $detectedMoods[] = $mood;
                    break;
                }
            }
        }
        
        return $detectedMoods;
    }
    
    /**
     * Extraer información temporal
     */
    private function extractTemporal($query)
    {
        $temporal = [
            'specific_years' => [],
            'periods' => [],
            'recency' => null
        ];
        
        // Años específicos
        preg_match_all('/\b(19|20)\d{2}\b/', $query, $years);
        $temporal['specific_years'] = $years[0];
        
        // Períodos
        if (preg_match('/\b(90s?|noventa)\b/i', $query)) $temporal['periods'][] = '1990s';
        if (preg_match('/\b(2000s?|dos mil)\b/i', $query)) $temporal['periods'][] = '2000s';
        if (preg_match('/\b(2010s?|dos mil diez)\b/i', $query)) $temporal['periods'][] = '2010s';
        
        // Recencia
        if (preg_match('/\b(nuevo|reciente|actual|2023|2024)\b/i', $query)) {
            $temporal['recency'] = 'recent';
        } elseif (preg_match('/\b(clasico|antiguo|viejo)\b/i', $query)) {
            $temporal['recency'] = 'classic';
        }
        
        return $temporal;
    }
    
    /**
     * Buscar queries similares previas
     */
    private function findSimilarQueries($query)
    {
        // En una implementación real, esto buscaría en logs de búsqueda
        // Por ahora, retornamos sugerencias estáticas basadas en patrones
        
        $similarQueries = [];
        $normalized = $this->normalizeQuery($query);
        
        // Patrones comunes de búsqueda
        $commonPatterns = [
            'romance' => ['dramas romanticos', 'series de amor', 'romances coreanos'],
            'comedia' => ['series divertidas', 'comedias coreanas', 'dramas comicos'],
            'thriller' => ['series de suspense', 'thrillers psicologicos', 'misterio'],
            'accion' => ['series de accion', 'dramas de accion', 'peleas'],
        ];
        
        foreach ($commonPatterns as $keyword => $suggestions) {
            if (strpos($normalized, $keyword) !== false) {
                $similarQueries = array_merge($similarQueries, $suggestions);
            }
        }
        
        return array_slice($similarQueries, 0, 3);
    }
    
    /**
     * Realizar búsqueda multi-dimensional
     */
    private function performMultiSearch($query, $analysis, $filters, $limit)
    {
        $results = [
            'series' => [],
            'movies' => [],
            'actors' => []
        ];
        
        // 1. Búsqueda exacta de títulos
        $results['series'] = array_merge($results['series'], $this->searchExactTitles($query, 'series'));
        $results['movies'] = array_merge($results['movies'], $this->searchExactTitles($query, 'movies'));
        
        // 2. Búsqueda semántica por contenido
        $results['series'] = array_merge($results['series'], $this->searchByContent($analysis, 'series'));
        
        // 3. Búsqueda por actores
        if (!empty($analysis['entities']['actors'])) {
            $results['actors'] = $this->searchActors($analysis['entities']['actors']);
            $results['series'] = array_merge($results['series'], $this->searchByActors($analysis['entities']['actors']));
        }
        
        // 4. Búsqueda contextual por intención
        $results['series'] = array_merge($results['series'], $this->searchByIntent($analysis['intent'], $analysis));
        
        // 5. Aplicar filtros
        if (!empty($filters)) {
            $results = $this->applyFilters($results, $filters);
        }
        
        // 6. Eliminar duplicados
        $results['series'] = $this->removeDuplicates($results['series']);
        $results['movies'] = $this->removeDuplicates($results['movies']);
        $results['actors'] = $this->removeDuplicates($results['actors']);
        
        return $results;
    }
    
    /**
     * Búsqueda exacta de títulos
     */
    private function searchExactTitles($query, $type)
    {
        $model = $type === 'series' ? Series::class : Movie::class;
        
        return $model::where('title', 'LIKE', "%{$query}%")
            ->orWhere('original_title', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($item) use ($type) {
                return $this->formatSearchResult($item, $type, 'exact_title', 1.0);
            })
            ->toArray();
    }
    
    /**
     * Búsqueda por contenido y descripción
     */
    private function searchByContent($analysis, $type)
    {
        $model = $type === 'series' ? Series::class : Movie::class;
        $query = $model::query();
        
        // Buscar en overview/descripción
        $tokens = $analysis['tokens'];
        $query->where(function($q) use ($tokens) {
            foreach ($tokens as $token) {
                $q->orWhere('overview', 'LIKE', "%{$token}%");
                $q->orWhere('tagline', 'LIKE', "%{$token}%");
            }
        });
        
        // Filtrar por géneros detectados
        if (!empty($analysis['entities']['genres'])) {
            $query->where(function($q) use ($analysis) {
                foreach ($analysis['entities']['genres'] as $genre) {
                    $q->orWhere('genres', 'LIKE', "%{$genre}%");
                }
            });
        }
        
        return $query->limit(8)
            ->get()
            ->map(function($item) use ($type) {
                return $this->formatSearchResult($item, $type, 'content', 0.7);
            })
            ->toArray();
    }
    
    /**
     * Búsqueda por actores
     */
    private function searchActors($actors)
    {
        $results = [];
        
        foreach ($actors as $actor) {
            $actorModel = Actor::find($actor['id']);
            if ($actorModel) {
                $results[] = $this->formatSearchResult($actorModel, 'actor', 'exact_actor', 0.9);
            }
        }
        
        return $results;
    }
    
    /**
     * Búsqueda de series por actores
     */
    private function searchByActors($actors)
    {
        if (empty($actors)) return [];
        
        $actorIds = array_column($actors, 'id');
        
        return Series::whereHas('actors', function($query) use ($actorIds) {
            $query->whereIn('actors.id', $actorIds);
        })
        ->limit(6)
        ->get()
        ->map(function($item) {
            return $this->formatSearchResult($item, 'series', 'by_actor', 0.8);
        })
        ->toArray();
    }
    
    /**
     * Búsqueda por intención detectada
     */
    private function searchByIntent($intents, $analysis)
    {
        $results = [];
        
        foreach ($intents as $intent) {
            switch ($intent) {
                case 'series_by_genre':
                    $results = array_merge($results, $this->searchByGenreIntent($analysis));
                    break;
                
                case 'series_by_year':
                    $results = array_merge($results, $this->searchByYearIntent($analysis));
                    break;
                
                case 'recommendation':
                    $results = array_merge($results, $this->searchSimilarContent($analysis));
                    break;
                
                case 'trending':
                    $results = array_merge($results, $this->searchTrending());
                    break;
            }
        }
        
        return $results;
    }
    
    private function searchByGenreIntent($analysis)
    {
        if (empty($analysis['entities']['genres'])) return [];
        
        $query = Series::query();
        $query->where(function($q) use ($analysis) {
            foreach ($analysis['entities']['genres'] as $genre) {
                $q->orWhere('genres', 'LIKE', "%{$genre}%");
            }
        });
        
        return $query->orderByDesc('rating')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return $this->formatSearchResult($item, 'series', 'genre_intent', 0.6);
            })
            ->toArray();
    }
    
    private function searchByYearIntent($analysis)
    {
        if (empty($analysis['entities']['years'])) return [];
        
        $query = Series::query();
        $query->whereIn(DB::raw('YEAR(first_air_date)'), $analysis['entities']['years']);
        
        return $query->orderByDesc('rating')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return $this->formatSearchResult($item, 'series', 'year_intent', 0.6);
            })
            ->toArray();
    }
    
    private function searchSimilarContent($analysis)
    {
        // Buscar contenido similar basado en tokens
        $tokens = $analysis['tokens'];
        if (empty($tokens)) return [];
        
        return Series::where(function($query) use ($tokens) {
            foreach ($tokens as $token) {
                $query->orWhere('title', 'LIKE', "%{$token}%");
                $query->orWhere('overview', 'LIKE', "%{$token}%");
            }
        })
        ->orderByDesc('rating')
        ->limit(4)
        ->get()
        ->map(function($item) {
            return $this->formatSearchResult($item, 'series', 'similar', 0.5);
        })
        ->toArray();
    }
    
    private function searchTrending()
    {
        return Series::select('series.*', DB::raw('COUNT(ratings.id) as recent_ratings'))
            ->leftJoin('ratings', function($join) {
                $join->on('series.id', '=', 'ratings.series_id')
                     ->where('ratings.created_at', '>=', now()->subDays(7));
            })
            ->groupBy('series.id')
            ->orderByDesc('recent_ratings')
            ->orderByDesc('rating')
            ->limit(6)
            ->get()
            ->map(function($item) {
                return $this->formatSearchResult($item, 'series', 'trending', 0.7);
            })
            ->toArray();
    }
    
    /**
     * Formatear resultado de búsqueda
     */
    private function formatSearchResult($item, $type, $reason, $baseScore)
    {
        $result = [
            'id' => $item->id,
            'type' => $type,
            'reason' => $reason,
            'score' => $baseScore,
            'title' => $item->title ?? $item->name,
            'url' => $this->generateUrl($item, $type)
        ];
        
        // Agregar campos específicos por tipo
        switch ($type) {
            case 'series':
                $result['poster_path'] = $item->poster_path;
                $result['rating'] = $item->rating;
                $result['year'] = $item->first_air_date ? date('Y', strtotime($item->first_air_date)) : null;
                $result['genres'] = $item->genres;
                $result['episodes'] = $item->number_of_episodes;
                $result['overview'] = substr($item->overview ?? '', 0, 120) . '...';
                break;
                
            case 'movies':
                $result['poster_path'] = $item->poster_path;
                $result['rating'] = $item->rating;
                $result['year'] = $item->release_date ? date('Y', strtotime($item->release_date)) : null;
                $result['genres'] = $item->genres;
                $result['overview'] = substr($item->overview ?? '', 0, 120) . '...';
                break;
                
            case 'actor':
                $result['profile_path'] = $item->profile_path;
                $result['known_for'] = $item->known_for_department;
                $result['popularity'] = $item->popularity;
                break;
        }
        
        return $result;
    }
    
    /**
     * Generar URL para el resultado
     */
    private function generateUrl($item, $type)
    {
        switch ($type) {
            case 'series':
                return "/series/{$item->id}";
            case 'movies':
                return "/peliculas/{$item->id}";
            case 'actor':
                return "/actores/{$item->id}";
            default:
                return '#';
        }
    }
    
    /**
     * Ranking inteligente de resultados
     */
    private function rankResults($results, $query, $analysis)
    {
        $allResults = [];
        
        // Combinar todos los resultados
        foreach ($results as $type => $items) {
            $allResults = array_merge($allResults, $items);
        }
        
        // Calcular score final para cada resultado
        foreach ($allResults as &$result) {
            $result['final_score'] = $this->calculateFinalScore($result, $query, $analysis);
        }
        
        // Ordenar por score final
        usort($allResults, function($a, $b) {
            return $b['final_score'] <=> $a['final_score'];
        });
        
        return array_slice($allResults, 0, 20);
    }
    
    /**
     * Calcular score final considerando múltiples factores
     */
    private function calculateFinalScore($result, $query, $analysis)
    {
        $score = $result['score'];
        
        // Boost por exactitud de título
        if (isset($result['title'])) {
            $titleSimilarity = $this->calculateStringSimilarity($query, $result['title']);
            $score += $titleSimilarity * 0.5;
        }
        
        // Boost por popularidad/rating
        if (isset($result['rating']) && $result['rating'] > 8.0) {
            $score += 0.2;
        }
        
        // Boost por recencia si se busca contenido nuevo
        if (in_array('recent', $analysis['temporal']['recency'] ?? []) && isset($result['year'])) {
            if ($result['year'] >= 2022) {
                $score += 0.3;
            }
        }
        
        // Penalty por resultados duplicados del mismo tipo
        if ($result['reason'] === 'similar' || $result['reason'] === 'content') {
            $score *= 0.9;
        }
        
        return round($score, 3);
    }
    
    /**
     * Calcular similitud entre strings
     */
    private function calculateStringSimilarity($str1, $str2)
    {
        $str1 = strtolower($this->removeAccents($str1));
        $str2 = strtolower($this->removeAccents($str2));
        
        return similar_text($str1, $str2) / max(strlen($str1), strlen($str2));
    }
    
    /**
     * Generar sugerencias de búsqueda
     */
    private function generateSuggestions($query, $results)
    {
        $suggestions = [
            'corrections' => [],
            'related' => [],
            'trending' => []
        ];
        
        // Sugerencias basadas en resultados encontrados
        foreach ($results as $result) {
            if (isset($result['genres'])) {
                $genres = explode(',', $result['genres']);
                foreach ($genres as $genre) {
                    $genre = trim($genre);
                    $suggestions['related'][] = "series de {$genre}";
                }
            }
        }
        
        // Limpiar duplicados y limitar
        $suggestions['related'] = array_unique($suggestions['related']);
        $suggestions['related'] = array_slice($suggestions['related'], 0, 3);
        
        // Sugerencias trending
        $suggestions['trending'] = [
            'romance coreano 2024',
            'thriller psicologico',
            'drama historico'
        ];
        
        return $suggestions;
    }
    
    /**
     * Aplicar filtros a los resultados
     */
    private function applyFilters($results, $filters)
    {
        foreach ($results as $type => &$items) {
            $items = array_filter($items, function($item) use ($filters) {
                return $this->itemMatchesFilters($item, $filters);
            });
        }
        
        return $results;
    }
    
    private function itemMatchesFilters($item, $filters)
    {
        // Filtro por tipo
        if (isset($filters['type']) && $item['type'] !== $filters['type']) {
            return false;
        }
        
        // Filtro por rating mínimo
        if (isset($filters['min_rating']) && isset($item['rating'])) {
            if ($item['rating'] < $filters['min_rating']) {
                return false;
            }
        }
        
        // Filtro por año
        if (isset($filters['year']) && isset($item['year'])) {
            if ($item['year'] != $filters['year']) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Remover duplicados de resultados
     */
    private function removeDuplicates($items)
    {
        $seen = [];
        $unique = [];
        
        foreach ($items as $item) {
            $key = $item['type'] . '_' . $item['id'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $item;
            }
        }
        
        return $unique;
    }
    
    /**
     * Búsqueda de respaldo simple
     */
    private function fallbackSearch($query, $limit)
    {
        $series = Series::where('title', 'LIKE', "%{$query}%")
            ->orderByDesc('rating')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $series->map(function($item) {
                return $this->formatSearchResult($item, 'series', 'fallback', 0.5);
            }),
            'fallback' => true
        ]);
    }
    
    /**
     * Obtener búsquedas populares
     */
    private function getPopularSearches()
    {
        return [
            'romance coreano',
            'thriller psicologico',
            'drama historico',
            'comedia romantica',
            'series 2024',
            'lee min ho',
            'park seo joon',
            'son ye jin'
        ];
    }
    
    /**
     * Remover acentos de texto
     */
    private function removeAccents($text)
    {
        $unwanted = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ā' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'ē' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'ī' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'ō' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'ū' => 'u',
            'ñ' => 'n', 'ç' => 'c'
        ];
        
        return strtr($text, $unwanted);
    }
    
    /**
     * Autocompletar búsqueda en tiempo real
     */
    public function autocomplete(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => $this->getPopularSearches()
            ]);
        }
        
        $suggestions = [];
        
        // Títulos de series
        $series = Series::where('title', 'LIKE', "{$query}%")
            ->orderByDesc('rating')
            ->limit(5)
            ->pluck('title')
            ->toArray();
        
        // Nombres de actores
        $actors = Actor::where('name', 'LIKE', "{$query}%")
            ->orderByDesc('popularity')
            ->limit(3)
            ->pluck('name')
            ->toArray();
        
        $suggestions = array_merge($series, $actors);
        
        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 8)
        ]);
    }
}