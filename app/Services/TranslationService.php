<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Traduce texto del inglés al español chileno
     */
    public function translateToChileanSpanish(string $text, string $type = 'general'): ?string
    {
        if (empty($text) || empty($this->apiKey)) {
            return null;
        }

        // Cache key basado en el texto y tipo
        $cacheKey = "translation_" . md5($text . $type);
        
        // Verificar si ya está en cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $prompt = $this->buildPrompt($text, $type);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor especializado en K-dramas y cultura coreana. Tu audiencia son fanáticas chilenas de los dramas coreanos. Traduce siempre manteniendo el contexto cultural y usando expresiones naturales del español chileno.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $translation = $result['choices'][0]['message']['content'] ?? null;
                
                if ($translation) {
                    // Guardar en cache por 30 días
                    Cache::put($cacheKey, $translation, now()->addDays(30));
                    
                    Log::info("Traducción exitosa", [
                        'original' => substr($text, 0, 100),
                        'translated' => substr($translation, 0, 100),
                        'type' => $type
                    ]);
                    
                    return $translation;
                }
            }

            Log::error("Error en traducción OpenAI", [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

        } catch (\Exception $e) {
            Log::error("Excepción en traducción", [
                'message' => $e->getMessage(),
                'text' => substr($text, 0, 100)
            ]);
        }

        return null;
    }

    /**
     * Construye el prompt específico según el tipo de contenido
     */
    private function buildPrompt(string $text, string $type): string
    {
        $baseInstructions = "Traduce el siguiente texto del inglés al español, considerando que la audiencia son fanáticas chilenas de K-dramas. ";

        switch ($type) {
            case 'title':
                return $baseInstructions . "Este es el título de un K-drama. Mantén el estilo dramático y atractivo. Si hay nombres propios coreanos, manténlos en su forma original.\n\nTexto: " . $text;
            
            case 'synopsis':
            case 'overview':
                return $baseInstructions . "Esta es la sinopsis de un K-drama. Usa un lenguaje emotivo y atractivo que genere interés. Mantén términos específicos de K-dramas si es necesario (como 'chaebol', 'oppa', etc.). Usa expresiones naturales del español chileno.\n\nTexto: " . $text;
            
            case 'tagline':
                return $baseInstructions . "Este es un tagline o eslogan de un K-drama. Debe ser impactante y memorable.\n\nTexto: " . $text;
            
            case 'genre':
                return $baseInstructions . "Este es el nombre de un género de K-drama. Traduce de manera estándar.\n\nTexto: " . $text;
            
            default:
                return $baseInstructions . "Traduce de manera natural y fluida.\n\nTexto: " . $text;
        }
    }

    /**
     * Traduce múltiples textos en batch para mayor eficiencia
     */
    public function translateBatch(array $texts, string $type = 'general'): array
    {
        $results = [];
        
        foreach ($texts as $key => $text) {
            $results[$key] = $this->translateToChileanSpanish($text, $type);
        }
        
        return $results;
    }

    /**
     * Verifica si el servicio de traducción está disponible
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }
}