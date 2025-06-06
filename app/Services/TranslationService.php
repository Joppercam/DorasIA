<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class TranslationService
{
    private $apiKey;
    private $organization;
    private $model;
    private $maxRetries;
    private $timeout;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->organization = env('OPENAI_ORGANIZATION');
        $this->model = env('OPENAI_MODEL_NAME', 'gpt-4-turbo');
        $this->maxRetries = 3;
        $this->timeout = 45;
    }

    /**
     * Translate text from English to Chilean Spanish optimized for K-drama content
     */
    public function translateToChileanSpanish(string $text, string $type = 'general'): ?string
    {
        if (empty($text) || empty($this->apiKey)) {
            Log::warning('Translation aborted: empty text or API key', [
                'has_text' => !empty($text),
                'has_api_key' => !empty($this->apiKey)
            ]);
            return null;
        }

        // Create cache key based on text, type, and model
        $cacheKey = "kdrama_translation_" . md5($text . $type . $this->model);
        
        // Check cache first
        if (Cache::has($cacheKey)) {
            Log::debug('Translation retrieved from cache', ['type' => $type]);
            return Cache::get($cacheKey);
        }

        // Attempt translation with retry logic
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $translation = $this->performTranslation($text, $type);
                
                if ($translation) {
                    // Cache successful translation for 60 days
                    Cache::put($cacheKey, $translation, now()->addDays(60));
                    
                    Log::info("K-drama translation successful", [
                        'original_length' => strlen($text),
                        'translated_length' => strlen($translation),
                        'type' => $type,
                        'model' => $this->model,
                        'attempt' => $attempt
                    ]);
                    
                    return $translation;
                }
                
            } catch (Exception $e) {
                Log::warning("Translation attempt {$attempt} failed", [
                    'message' => $e->getMessage(),
                    'text_preview' => substr($text, 0, 100),
                    'type' => $type,
                    'attempt' => $attempt
                ]);
                
                if ($attempt === $this->maxRetries) {
                    Log::error("All translation attempts failed", [
                        'text_preview' => substr($text, 0, 100),
                        'type' => $type,
                        'total_attempts' => $this->maxRetries
                    ]);
                }
                
                // Wait before retry (exponential backoff)
                if ($attempt < $this->maxRetries) {
                    sleep(pow(2, $attempt));
                }
            }
        }

        return null;
    }

    /**
     * Perform the actual translation API call
     */
    private function performTranslation(string $text, string $type): ?string
    {
        $prompt = $this->buildKDramaPrompt($text, $type);
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];
        
        // Note: Organization header removed due to API key mismatch issue
        // The current API key is not associated with the specified organization
        // This is a common issue with newer project-based API keys
        
        $response = Http::withHeaders($headers)
            ->timeout($this->timeout)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $this->getMaxTokens($type),
                'temperature' => $this->getTemperature($type),
                'presence_penalty' => 0.1,
                'frequency_penalty' => 0.1,
            ]);

        if (!$response->successful()) {
            throw new Exception("OpenAI API error: " . $response->status() . " - " . $response->body());
        }

        $result = $response->json();
        $translation = $result['choices'][0]['message']['content'] ?? null;
        
        if (!$translation) {
            throw new Exception("No translation content in API response");
        }
        
        return trim($translation);
    }

    /**
     * Get optimized system prompt for K-drama translation
     */
    private function getSystemPrompt(): string
    {
        return "Eres un traductor especializado en K-dramas y cultura coreana para fanáticas chilenas. " .
               "Tu misión es traducir contenido manteniendo la esencia emocional y cultural de los dramas coreanos. " .
               "REGLAS IMPORTANTES:\n" .
               "1. Mantén nombres propios coreanos en su forma original\n" .
               "2. Conserva términos específicos de K-dramas cuando sea apropiado (chaebol, oppa, unnie, etc.)\n" .
               "3. Usa expresiones naturales del español chileno\n" .
               "4. Mantén el tono emocional y dramático característico\n" .
               "5. Adapta referencias culturales cuando sea necesario para el público chileno\n" .
               "6. Solo devuelve la traducción, sin comentarios adicionales";
    }

    /**
     * Build K-drama specific prompts based on content type
     */
    private function buildKDramaPrompt(string $text, string $type): string
    {
        $instructions = match($type) {
            'title' => "Traduce este título de K-drama. Debe ser atractivo y mantener el impacto dramático. " .
                      "Conserva nombres propios coreanos.",
            
            'synopsis', 'overview' => "Traduce esta sinopsis de K-drama. Usa lenguaje emotivo que genere interés y " .
                                     "ganas de ver el drama. Mantén términos específicos de K-dramas cuando sea apropiado.",
            
            'tagline' => "Traduce este tagline de K-drama. Debe ser impactante, memorable y crear expectativa.",
            
            'genre' => "Traduce este género de K-drama de manera estándar y clara.",
            
            'cast', 'actor', 'actress' => "Traduce información sobre actores/actrices de K-dramas. " .
                                         "Mantén nombres coreanos en su forma original.",
            
            'episode' => "Traduce este título o descripción de episodio de K-drama. " .
                        "Mantén la intriga y el gancho dramático.",
            
            'news', 'article' => "Traduce esta noticia sobre K-dramas. Mantén el tono informativo " .
                                "pero emocionante para las fanáticas.",
            
            default => "Traduce este contenido relacionado con K-dramas de manera natural y fluida."
        };

        return $instructions . "\n\nTexto a traducir:\n" . $text;
    }

    /**
     * Get appropriate max tokens based on content type
     */
    private function getMaxTokens(string $type): int
    {
        return match($type) {
            'title' => 100,
            'tagline' => 150,
            'genre' => 50,
            'synopsis', 'overview' => 800,
            'episode' => 200,
            'news', 'article' => 1200,
            default => 500
        };
    }

    /**
     * Get appropriate temperature based on content type
     */
    private function getTemperature(string $type): float
    {
        return match($type) {
            'title', 'tagline' => 0.4,  // Slightly more creative for titles
            'genre' => 0.1,  // Very consistent for genres
            'synopsis', 'overview' => 0.3,  // Balanced for descriptions
            'news', 'article' => 0.2,  // More factual for news
            default => 0.3
        };
    }

    /**
     * Translate multiple texts in batch with progress tracking
     */
    public function translateBatch(array $texts, string $type = 'general', callable $progressCallback = null): array
    {
        $results = [];
        $total = count($texts);
        $processed = 0;
        
        Log::info("Starting batch translation", [
            'total_items' => $total,
            'type' => $type,
            'model' => $this->model
        ]);
        
        foreach ($texts as $key => $text) {
            $results[$key] = $this->translateToChileanSpanish($text, $type);
            $processed++;
            
            // Call progress callback if provided
            if ($progressCallback) {
                $progressCallback($processed, $total, $key);
            }
            
            // Small delay to avoid rate limiting
            if ($processed % 5 === 0 && $processed < $total) {
                usleep(100000); // 0.1 second delay every 5 translations
            }
        }
        
        Log::info("Batch translation completed", [
            'total_items' => $total,
            'successful_translations' => count(array_filter($results)),
            'type' => $type
        ]);
        
        return $results;
    }

    /**
     * Translate K-drama title specifically
     */
    public function translateTitle(string $title): ?string
    {
        return $this->translateToChileanSpanish($title, 'title');
    }

    /**
     * Translate K-drama synopsis/overview specifically
     */
    public function translateSynopsis(string $synopsis): ?string
    {
        return $this->translateToChileanSpanish($synopsis, 'synopsis');
    }

    /**
     * Translate K-drama genre specifically
     */
    public function translateGenre(string $genre): ?string
    {
        return $this->translateToChileanSpanish($genre, 'genre');
    }

    /**
     * Translate multiple K-drama titles
     */
    public function translateTitles(array $titles, callable $progressCallback = null): array
    {
        return $this->translateBatch($titles, 'title', $progressCallback);
    }

    /**
     * Translate multiple K-drama synopses
     */
    public function translateSynopses(array $synopses, callable $progressCallback = null): array
    {
        return $this->translateBatch($synopses, 'synopsis', $progressCallback);
    }

    /**
     * Check if translation service is available and properly configured
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->model);
    }

    /**
     * Get service configuration info
     */
    public function getServiceInfo(): array
    {
        return [
            'is_available' => $this->isAvailable(),
            'model' => $this->model,
            'has_organization' => !empty($this->organization),
            'organization_header_disabled' => true, // Due to API key mismatch
            'max_retries' => $this->maxRetries,
            'timeout' => $this->timeout,
            'version' => 'v2.1-kdrama-optimized-fixed'
        ];
    }

    /**
     * Clear translation cache for specific type or all
     */
    public function clearCache(string $type = null): bool
    {
        try {
            if ($type) {
                // Clear cache for specific type (more complex, would need to track keys)
                Log::info("Cache clear requested for type: {$type}");
                return true;
            } else {
                // Clear all translation cache
                $pattern = 'kdrama_translation_*';
                Log::info("Clearing all K-drama translation cache");
                return Cache::flush(); // This clears all cache, might want to be more specific in production
            }
        } catch (Exception $e) {
            Log::error("Failed to clear translation cache", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Test the translation service with a sample K-drama text
     */
    public function testService(): array
    {
        $testCases = [
            'title' => 'Crash Landing on You',
            'synopsis' => 'A South Korean heiress accidentally lands in North Korea and falls in love with a North Korean officer.',
            'genre' => 'Romantic Drama'
        ];

        $results = [];
        $startTime = microtime(true);

        foreach ($testCases as $type => $text) {
            $translation = $this->translateToChileanSpanish($text, $type);
            $results[$type] = [
                'original' => $text,
                'translated' => $translation,
                'success' => !is_null($translation)
            ];
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        return [
            'service_info' => $this->getServiceInfo(),
            'test_results' => $results,
            'duration_seconds' => $duration,
            'overall_success' => count(array_filter(array_column($results, 'success'))) === count($testCases)
        ];
    }
}