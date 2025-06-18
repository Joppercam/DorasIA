<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Series;
use App\Models\Movie;

class AITaggingController extends Controller
{
    /**
     * Sistema de Tags Automáticos por IA de Dorasia
     * Analiza contenido y genera etiquetas inteligentes basadas en múltiples factores
     */
    
    /**
     * Generar tags automáticos para una serie
     */
    public function generateSeriesTags(Request $request)
    {
        $seriesId = $request->get('series_id');
        $forceRegenerate = $request->get('force', false);
        
        if (!$seriesId) {
            return response()->json(['error' => 'Series ID required'], 400);
        }
        
        try {
            $series = Series::findOrFail($seriesId);
            
            // Verificar si ya tiene tags y no forzar regeneración
            if (!$forceRegenerate && !empty($series->ai_tags)) {
                return response()->json([
                    'success' => true,
                    'series_id' => $seriesId,
                    'tags' => json_decode($series->ai_tags, true),
                    'cached' => true
                ]);
            }
            
            // Generar tags con IA
            $tags = $this->analyzeAndGenerateTags($series);
            
            // Guardar tags en la serie
            $series->update(['ai_tags' => json_encode($tags)]);
            
            Log::info('AI tags generated for series', [
                'series_id' => $seriesId,
                'tags_count' => count($tags['all']),
                'title' => $series->title
            ]);
            
            return response()->json([
                'success' => true,
                'series_id' => $seriesId,
                'tags' => $tags,
                'cached' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating AI tags', [
                'series_id' => $seriesId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error generating tags'
            ], 500);
        }
    }
    
    /**
     * Generar tags masivos para todas las series
     */
    public function generateBulkTags(Request $request)
    {
        $limit = $request->get('limit', 50);
        $offset = $request->get('offset', 0);
        
        try {
            // Obtener series sin tags o con tags antiguos
            $series = Series::whereNull('ai_tags')
                ->orWhere('updated_at', '<', now()->subDays(30))
                ->offset($offset)
                ->limit($limit)
                ->get();
            
            $processed = 0;
            $errors = 0;
            
            foreach ($series as $serie) {
                try {
                    $tags = $this->analyzeAndGenerateTags($serie);
                    $serie->update(['ai_tags' => json_encode($tags)]);
                    $processed++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('Error in bulk tagging', [
                        'series_id' => $serie->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'processed' => $processed,
                'errors' => $errors,
                'total_series' => $series->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in bulk tag generation', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error in bulk processing'
            ], 500);
        }
    }
    
    /**
     * Analizar serie y generar tags inteligentes
     */
    private function analyzeAndGenerateTags($series)
    {
        $tags = [
            'mood' => [],
            'themes' => [],
            'audience' => [],
            'style' => [],
            'emotions' => [],
            'pace' => [],
            'complexity' => [],
            'time_period' => [],
            'setting' => [],
            'relationship' => [],
            'ai_analysis' => [],
            'all' => []
        ];
        
        // 1. Análisis de géneros
        $genreTags = $this->analyzeGenres($series->genres);
        $tags = array_merge_recursive($tags, $genreTags);
        
        // 2. Análisis de descripción/overview
        $contentTags = $this->analyzeContent($series->overview);
        $tags = array_merge_recursive($tags, $contentTags);
        
        // 3. Análisis de título
        $titleTags = $this->analyzeTitle($series->title);
        $tags = array_merge_recursive($tags, $titleTags);
        
        // 4. Análisis de métricas
        $metricTags = $this->analyzeMetrics($series);
        $tags = array_merge_recursive($tags, $metricTags);
        
        // 5. Análisis del cast
        $castTags = $this->analyzeCast($series);
        $tags = array_merge_recursive($tags, $castTags);
        
        // 6. Análisis temporal
        $temporalTags = $this->analyzeTemporal($series);
        $tags = array_merge_recursive($tags, $temporalTags);
        
        // 7. Análisis de popularidad y tendencias
        $trendTags = $this->analyzeTrends($series);
        $tags = array_merge_recursive($tags, $trendTags);
        
        // 8. Tags de recomendación automática
        $recommendationTags = $this->generateRecommendationTags($series);
        $tags = array_merge_recursive($tags, $recommendationTags);
        
        // Consolidar todos los tags únicos
        $allTags = [];
        foreach ($tags as $category => $categoryTags) {
            if ($category !== 'all') {
                $allTags = array_merge($allTags, $categoryTags);
            }
        }
        $tags['all'] = array_unique($allTags);
        
        // Limpiar y optimizar tags
        $tags = $this->cleanAndOptimizeTags($tags);
        
        return $tags;
    }
    
    /**
     * Analizar géneros y generar tags correspondientes
     */
    private function analyzeGenres($genres)
    {
        $tags = [
            'mood' => [],
            'themes' => [],
            'audience' => [],
            'emotions' => []
        ];
        
        if (!$genres) return $tags;
        
        $genreList = array_map('trim', explode(',', strtolower($genres)));
        
        $genreMapping = [
            'romance' => [
                'mood' => ['romántico', 'dulce', 'emocional'],
                'themes' => ['amor', 'relaciones', 'corazón'],
                'audience' => ['para parejas', 'románticos'],
                'emotions' => ['amor', 'ternura', 'felicidad']
            ],
            'drama' => [
                'mood' => ['intenso', 'profundo', 'reflexivo'],
                'themes' => ['vida real', 'emociones', 'conflictos'],
                'audience' => ['adultos', 'emocionales'],
                'emotions' => ['drama', 'tristeza', 'esperanza']
            ],
            'comedy' => [
                'mood' => ['divertido', 'ligero', 'alegre'],
                'themes' => ['humor', 'entretenimiento', 'diversión'],
                'audience' => ['familiar', 'todos'],
                'emotions' => ['risa', 'alegría', 'diversión']
            ],
            'thriller' => [
                'mood' => ['tenso', 'emocionante', 'suspense'],
                'themes' => ['misterio', 'peligro', 'investigación'],
                'audience' => ['adultos', 'suspense'],
                'emotions' => ['tensión', 'ansiedad', 'emoción']
            ],
            'action' => [
                'mood' => ['dinámico', 'emocionante', 'intenso'],
                'themes' => ['aventura', 'acción', 'peleas'],
                'audience' => ['adrenalina', 'acción'],
                'emotions' => ['emoción', 'adrenalina', 'intensidad']
            ],
            'fantasy' => [
                'mood' => ['mágico', 'imaginativo', 'sobrenatural'],
                'themes' => ['fantasía', 'magia', 'otros mundos'],
                'audience' => ['fantasy lovers', 'imaginativos'],
                'emotions' => ['asombro', 'magia', 'wonder']
            ],
            'historical' => [
                'mood' => ['épico', 'nostálgico', 'cultural'],
                'themes' => ['historia', 'tradición', 'cultura'],
                'audience' => ['historia', 'cultura'],
                'emotions' => ['nostalgia', 'orgullo', 'respeto']
            ],
            'mystery' => [
                'mood' => ['intrigante', 'misterioso', 'enigmático'],
                'themes' => ['misterio', 'secretos', 'investigación'],
                'audience' => ['detectives', 'misterio'],
                'emotions' => ['curiosidad', 'intriga', 'suspense']
            ]
        ];
        
        foreach ($genreList as $genre) {
            if (isset($genreMapping[$genre])) {
                foreach ($genreMapping[$genre] as $category => $categoryTags) {
                    $tags[$category] = array_merge($tags[$category], $categoryTags);
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * Analizar contenido/descripción de la serie
     */
    private function analyzeContent($overview)
    {
        $tags = [
            'themes' => [],
            'emotions' => [],
            'style' => [],
            'setting' => [],
            'relationship' => []
        ];
        
        if (!$overview) return $tags;
        
        $content = strtolower($overview);
        
        // Análisis de temas
        $themePatterns = [
            'familia' => ['familia', 'family', 'padre', 'madre', 'hijo', 'hermano'],
            'trabajo' => ['trabajo', 'office', 'empresa', 'jefe', 'carrera'],
            'escuela' => ['escuela', 'school', 'estudiante', 'universidad', 'clase'],
            'hospital' => ['hospital', 'doctor', 'médico', 'enfermera', 'medicina'],
            'legal' => ['abogado', 'lawyer', 'corte', 'justicia', 'ley'],
            'cocina' => ['cocina', 'chef', 'restaurant', 'comida', 'cooking'],
            'música' => ['música', 'singer', 'cantante', 'banda', 'idol'],
            'deporte' => ['deporte', 'sport', 'fútbol', 'baseball', 'atleta'],
            'venganza' => ['venganza', 'revenge', 'traición', 'betrayal'],
            'secreto' => ['secreto', 'secret', 'oculto', 'hidden', 'misterio']
        ];
        
        foreach ($themePatterns as $theme => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) !== false) {
                    $tags['themes'][] = $theme;
                    break;
                }
            }
        }
        
        // Análisis de emociones
        $emotionPatterns = [
            'lágrimas' => ['llorar', 'cry', 'tears', 'sad', 'triste'],
            'risa' => ['reír', 'laugh', 'funny', 'divertido', 'gracioso'],
            'amor' => ['amor', 'love', 'romantic', 'heart', 'corazón'],
            'miedo' => ['miedo', 'fear', 'scary', 'terror', 'frightening'],
            'ira' => ['ira', 'anger', 'angry', 'furious', 'rage'],
            'esperanza' => ['esperanza', 'hope', 'hopeful', 'optimistic']
        ];
        
        foreach ($emotionPatterns as $emotion => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) !== false) {
                    $tags['emotions'][] = $emotion;
                    break;
                }
            }
        }
        
        // Análisis de configuración/setting
        $settingPatterns = [
            'seoul' => ['seoul', 'seúl'],
            'pueblo' => ['village', 'pueblo', 'rural', 'countryside'],
            'oficina' => ['office', 'oficina', 'corporate', 'empresa'],
            'hospital' => ['hospital', 'clinic', 'medical'],
            'escuela' => ['school', 'university', 'college', 'academia'],
            'café' => ['café', 'coffee shop', 'restaurant'],
            'casa' => ['home', 'house', 'family house']
        ];
        
        foreach ($settingPatterns as $setting => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) !== false) {
                    $tags['setting'][] = $setting;
                    break;
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * Analizar título de la serie
     */
    private function analyzeTitle($title)
    {
        $tags = [
            'style' => [],
            'themes' => []
        ];
        
        if (!$title) return $tags;
        
        $titleLower = strtolower($title);
        
        // Palabras clave en títulos
        $titleKeywords = [
            'love' => ['style' => ['romántico'], 'themes' => ['amor']],
            'secret' => ['style' => ['misterioso'], 'themes' => ['secreto']],
            'family' => ['style' => ['familiar'], 'themes' => ['familia']],
            'doctor' => ['style' => ['profesional'], 'themes' => ['medicina']],
            'school' => ['style' => ['juvenil'], 'themes' => ['educación']],
            'king' => ['style' => ['histórico'], 'themes' => ['realeza']],
            'queen' => ['style' => ['histórico'], 'themes' => ['realeza']],
            'my' => ['style' => ['personal', 'íntimo'], 'themes' => ['personal']]
        ];
        
        foreach ($titleKeywords as $keyword => $mappings) {
            if (strpos($titleLower, $keyword) !== false) {
                foreach ($mappings as $category => $categoryTags) {
                    $tags[$category] = array_merge($tags[$category], $categoryTags);
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * Analizar métricas de la serie
     */
    private function analyzeMetrics($series)
    {
        $tags = [
            'audience' => [],
            'pace' => [],
            'complexity' => [],
            'ai_analysis' => []
        ];
        
        // Análisis basado en rating
        if ($series->rating >= 9.0) {
            $tags['audience'][] = 'obra maestra';
            $tags['ai_analysis'][] = 'altamente recomendado';
        } elseif ($series->rating >= 8.0) {
            $tags['audience'][] = 'excelente';
            $tags['ai_analysis'][] = 'muy buena';
        } elseif ($series->rating >= 7.0) {
            $tags['audience'][] = 'buena';
        } else {
            $tags['audience'][] = 'nicho';
        }
        
        // Análisis basado en número de episodios
        $episodes = $series->number_of_episodes ?? 16;
        
        if ($episodes <= 8) {
            $tags['pace'][] = 'ritmo rápido';
            $tags['complexity'][] = 'formato corto';
            $tags['audience'][] = 'fácil de ver';
        } elseif ($episodes <= 16) {
            $tags['pace'][] = 'ritmo medio';
            $tags['complexity'][] = 'formato estándar';
        } else {
            $tags['pace'][] = 'ritmo pausado';
            $tags['complexity'][] = 'formato largo';
            $tags['audience'][] = 'para maratonear';
        }
        
        // Análisis temporal
        $year = $series->first_air_date ? date('Y', strtotime($series->first_air_date)) : date('Y');
        
        if ($year >= 2023) {
            $tags['ai_analysis'][] = 'muy reciente';
            $tags['audience'][] = 'trending';
        } elseif ($year >= 2020) {
            $tags['ai_analysis'][] = 'reciente';
        } elseif ($year >= 2010) {
            $tags['ai_analysis'][] = 'moderno';
        } else {
            $tags['ai_analysis'][] = 'clásico';
            $tags['audience'][] = 'nostálgico';
        }
        
        return $tags;
    }
    
    /**
     * Analizar cast/reparto
     */
    private function analyzeCast($series)
    {
        $tags = [
            'audience' => [],
            'style' => []
        ];
        
        try {
            // Obtener actores principales
            $mainActors = $series->actors()
                ->wherePivot('character_type', 'main')
                ->orWhere('popularity', '>', 10)
                ->limit(3)
                ->get();
            
            foreach ($mainActors as $actor) {
                // Actores muy populares
                if ($actor->popularity > 20) {
                    $tags['audience'][] = 'star power';
                    $tags['style'][] = 'actor popular';
                }
                
                // Análisis por tipo de actor
                if (strpos(strtolower($actor->known_for_department), 'action') !== false) {
                    $tags['style'][] = 'actor de acción';
                } elseif (strpos(strtolower($actor->known_for_department), 'comedy') !== false) {
                    $tags['style'][] = 'actor cómico';
                }
            }
            
        } catch (\Exception $e) {
            // Si no hay información de cast, no pasa nada
        }
        
        return $tags;
    }
    
    /**
     * Análisis temporal y de época
     */
    private function analyzeTemporal($series)
    {
        $tags = [
            'time_period' => [],
            'ai_analysis' => []
        ];
        
        $year = $series->first_air_date ? date('Y', strtotime($series->first_air_date)) : date('Y');
        
        // Períodos por década
        if ($year >= 2020) {
            $tags['time_period'][] = '2020s';
            $tags['ai_analysis'][] = 'era actual';
        } elseif ($year >= 2010) {
            $tags['time_period'][] = '2010s';
            $tags['ai_analysis'][] = 'era moderna';
        } elseif ($year >= 2000) {
            $tags['time_period'][] = '2000s';
            $tags['ai_analysis'][] = 'era digital';
        } else {
            $tags['time_period'][] = 'clásico';
            $tags['ai_analysis'][] = 'era vintage';
        }
        
        // Análisis de época en el contenido
        $overview = strtolower($series->overview ?? '');
        
        $historicalPeriods = [
            'joseon' => ['joseon', 'dynasty', 'king', 'queen', 'palace'],
            'modern' => ['modern', 'contemporary', 'actual'],
            'past' => ['past', 'history', 'historical', 'period'],
            'future' => ['future', 'sci-fi', 'futuristic']
        ];
        
        foreach ($historicalPeriods as $period => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($overview, $keyword) !== false) {
                    $tags['time_period'][] = $period;
                    break;
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * Analizar tendencias y popularidad
     */
    private function analyzeTrends($series)
    {
        $tags = [
            'audience' => [],
            'ai_analysis' => []
        ];
        
        try {
            // Popularidad basada en ratings recientes
            $recentRatings = DB::table('ratings')
                ->where('series_id', $series->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            if ($recentRatings > 50) {
                $tags['audience'][] = 'muy popular';
                $tags['ai_analysis'][] = 'trending ahora';
            } elseif ($recentRatings > 20) {
                $tags['audience'][] = 'popular';
                $tags['ai_analysis'][] = 'ganando popularidad';
            }
            
            // Análisis de watchlist
            $watchlistCount = DB::table('watchlists')
                ->where('series_id', $series->id)
                ->count();
            
            if ($watchlistCount > 100) {
                $tags['audience'][] = 'muy deseada';
                $tags['ai_analysis'][] = 'alta demanda';
            } elseif ($watchlistCount > 50) {
                $tags['audience'][] = 'deseada';
            }
            
        } catch (\Exception $e) {
            // Si no hay datos de tendencias, continuar
        }
        
        return $tags;
    }
    
    /**
     * Generar tags para sistema de recomendaciones
     */
    private function generateRecommendationTags($series)
    {
        $tags = [
            'ai_analysis' => []
        ];
        
        // Tags para ayudar al sistema de recomendaciones
        $genres = strtolower($series->genres ?? '');
        $rating = $series->rating ?? 0;
        $episodes = $series->number_of_episodes ?? 16;
        
        // Combinaciones inteligentes
        if (strpos($genres, 'romance') !== false && strpos($genres, 'comedy') !== false) {
            $tags['ai_analysis'][] = 'rom-com';
        }
        
        if (strpos($genres, 'thriller') !== false && strpos($genres, 'drama') !== false) {
            $tags['ai_analysis'][] = 'thriller dramático';
        }
        
        if ($rating >= 8.5 && $episodes <= 12) {
            $tags['ai_analysis'][] = 'joya compacta';
        }
        
        if ($rating >= 9.0) {
            $tags['ai_analysis'][] = 'imperdible';
        }
        
        return $tags;
    }
    
    /**
     * Limpiar y optimizar tags generados
     */
    private function cleanAndOptimizeTags($tags)
    {
        // Remover duplicados en cada categoría
        foreach ($tags as $category => &$categoryTags) {
            if (is_array($categoryTags)) {
                $categoryTags = array_unique($categoryTags);
                $categoryTags = array_filter($categoryTags); // Remover valores vacíos
                $categoryTags = array_values($categoryTags); // Reindexar
            }
        }
        
        // Limitar número de tags por categoría
        $limits = [
            'mood' => 3,
            'themes' => 4,
            'audience' => 3,
            'style' => 3,
            'emotions' => 3,
            'pace' => 2,
            'complexity' => 2,
            'time_period' => 2,
            'setting' => 3,
            'relationship' => 3,
            'ai_analysis' => 5
        ];
        
        foreach ($limits as $category => $limit) {
            if (isset($tags[$category]) && count($tags[$category]) > $limit) {
                $tags[$category] = array_slice($tags[$category], 0, $limit);
            }
        }
        
        return $tags;
    }
    
    /**
     * Buscar series por tags
     */
    public function searchByTags(Request $request)
    {
        $requestedTags = $request->get('tags', []);
        $limit = $request->get('limit', 20);
        
        if (empty($requestedTags)) {
            return response()->json([
                'error' => 'No tags provided'
            ], 400);
        }
        
        try {
            $series = Series::whereNotNull('ai_tags')
                ->get()
                ->filter(function($serie) use ($requestedTags) {
                    $serieTags = json_decode($serie->ai_tags, true);
                    if (!$serieTags) return false;
                    
                    $allTags = $serieTags['all'] ?? [];
                    
                    // Verificar si tiene al menos uno de los tags solicitados
                    foreach ($requestedTags as $tag) {
                        if (in_array(strtolower($tag), array_map('strtolower', $allTags))) {
                            return true;
                        }
                    }
                    
                    return false;
                })
                ->take($limit)
                ->values();
            
            return response()->json([
                'success' => true,
                'tags_searched' => $requestedTags,
                'results' => $series->map(function($serie) {
                    return [
                        'id' => $serie->id,
                        'title' => $serie->title,
                        'poster_path' => $serie->poster_path,
                        'rating' => $serie->rating,
                        'genres' => $serie->genres,
                        'ai_tags' => json_decode($serie->ai_tags, true)
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching by tags', [
                'tags' => $requestedTags,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error searching by tags'
            ], 500);
        }
    }
    
    /**
     * Obtener estadísticas de tags
     */
    public function getTagsStats()
    {
        try {
            $series = Series::whereNotNull('ai_tags')->get();
            
            $tagStats = [];
            $categoryStats = [];
            
            foreach ($series as $serie) {
                $tags = json_decode($serie->ai_tags, true);
                if (!$tags) continue;
                
                foreach ($tags as $category => $categoryTags) {
                    if ($category === 'all') continue;
                    
                    if (!isset($categoryStats[$category])) {
                        $categoryStats[$category] = 0;
                    }
                    $categoryStats[$category]++;
                    
                    foreach ($categoryTags as $tag) {
                        if (!isset($tagStats[$tag])) {
                            $tagStats[$tag] = 0;
                        }
                        $tagStats[$tag]++;
                    }
                }
            }
            
            // Ordenar por popularidad
            arsort($tagStats);
            arsort($categoryStats);
            
            return response()->json([
                'success' => true,
                'total_series_with_tags' => $series->count(),
                'most_popular_tags' => array_slice($tagStats, 0, 20, true),
                'category_distribution' => $categoryStats,
                'total_unique_tags' => count($tagStats)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting tags stats', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error getting stats'
            ], 500);
        }
    }
}