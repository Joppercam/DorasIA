<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class TranslateMoviesSpanish extends Command
{
    protected $signature = 'movies:translate-spanish {--retranslate : Re-traducir películas ya traducidas}';
    protected $description = 'Traducir información de películas coreanas al español usando OpenAI';

    private $openAiAvailable = false;

    public function __construct()
    {
        parent::__construct();
        
        // Verificar si OpenAI está disponible
        try {
            $this->openAiAvailable = !empty(config('openai.api_key'));
        } catch (Exception $e) {
            $this->openAiAvailable = false;
        }
    }

    public function handle()
    {
        if (!$this->openAiAvailable) {
            $this->error('OpenAI no está configurado. No se puede proceder con la traducción.');
            return 1;
        }

        $this->info('🎬 Traduciendo información de películas coreanas al español...');

        // Determinar qué películas traducir
        if ($this->option('retranslate')) {
            $movies = Movie::all();
            $this->info("🔄 Modo re-traducción: procesando todas las {$movies->count()} películas");
        } else {
            $movies = Movie::where(function($query) {
                $query->whereNull('title_es')
                      ->orWhereNull('spanish_title')
                      ->orWhereNull('overview_es')
                      ->orWhereNull('spanish_overview');
            })->get();
            $this->info("📝 Encontradas {$movies->count()} películas sin traducir completamente");
        }

        if ($movies->isEmpty()) {
            $this->info('✅ Todas las películas ya están traducidas al español');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($movies->count());
        $progressBar->start();

        $translatedCount = 0;
        $errorCount = 0;

        foreach ($movies as $movie) {
            try {
                $updated = $this->translateMovie($movie);
                if ($updated) {
                    $translatedCount++;
                }
                
                // Pequeña pausa para no sobrecargar OpenAI
                usleep(300000); // 0.3 segundos
                
            } catch (Exception $e) {
                $errorCount++;
                Log::error("Error traduciendo película {$movie->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        
        $this->info("✅ Traducción completada!");
        $this->info("   🎬 Películas procesadas: {$movies->count()}");
        $this->info("   ✅ Películas traducidas: {$translatedCount}");
        $this->info("   ❌ Errores: {$errorCount}");

        return 0;
    }

    private function translateMovie(Movie $movie)
    {
        $updated = false;
        $retranslate = $this->option('retranslate');

        // Traducir título
        if ($retranslate || empty($movie->title_es) || empty($movie->spanish_title)) {
            $titleTranslations = $this->translateTitle($movie->title, $movie->original_title);
            if ($titleTranslations) {
                $movie->title_es = $titleTranslations['title_es'];
                $movie->spanish_title = $titleTranslations['spanish_title'];
                $updated = true;
            }
        }

        // Traducir sinopsis/overview
        if ($retranslate || empty($movie->overview_es) || empty($movie->spanish_overview)) {
            if (!empty($movie->overview)) {
                $overviewTranslations = $this->translateOverview($movie->overview);
                if ($overviewTranslations) {
                    $movie->overview_es = $overviewTranslations['overview_es'];
                    $movie->spanish_overview = $overviewTranslations['spanish_overview'];
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $movie->save();
        }

        return $updated;
    }

    private function translateTitle($title, $originalTitle = null)
    {
        if (empty($title)) {
            return null;
        }

        try {
            $titleForTranslation = $originalTitle ?: $title;
            
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional especializado en títulos de películas coreanas. Tu tarea es proporcionar DOS traducciones al español para el título de película que te proporcionen:

1. title_es: Una traducción literal y profesional del título
2. spanish_title: Una adaptación comercial en español que suene natural y atractiva para el público hispanohablante

Responde SOLO en formato JSON con estas dos claves. Si el título ya está en español, mantenlo igual en ambas versiones. Para títulos en coreano, proporciona traducciones apropiadas que capturen el significado y el tono de la película.

Ejemplo de respuesta:
{"title_es": "Parásitos", "spanish_title": "Parásitos"}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Título de película coreana: {$titleForTranslation}"
                    ]
                ],
                'max_tokens' => 200,
                'temperature' => 0.3
            ]);

            $translationText = trim($response->choices[0]->message->content);
            
            // Limpiar y parsear JSON
            $translationText = str_replace(['```json', '```'], '', $translationText);
            $translation = json_decode($translationText, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($translation['title_es']) && isset($translation['spanish_title'])) {
                return [
                    'title_es' => trim($translation['title_es']),
                    'spanish_title' => trim($translation['spanish_title'])
                ];
            } else {
                // Fallback: usar traducción simple
                $simpleTranslation = $this->getSimpleTranslation($titleForTranslation, 'título de película');
                return [
                    'title_es' => $simpleTranslation,
                    'spanish_title' => $simpleTranslation
                ];
            }

        } catch (Exception $e) {
            Log::error("Error traduciendo título '{$title}': " . $e->getMessage());
            return null;
        }
    }

    private function translateOverview($overview)
    {
        if (empty($overview)) {
            return null;
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional especializado en sinopsis de películas coreanas. Tu tarea es proporcionar DOS traducciones al español para la sinopsis que te proporcionen:

1. overview_es: Una traducción directa y fiel al texto original
2. spanish_overview: Una adaptación más libre y atractiva para el público hispanohablante, manteniendo el contenido pero con un lenguaje más natural en español

Responde SOLO en formato JSON con estas dos claves. Mantén el tono dramático, emocional o de suspenso según corresponda al género de la película.

Ejemplo de respuesta:
{"overview_es": "Una familia pobre se infiltra...", "spanish_overview": "Una familia sin recursos idea un plan..."}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Sinopsis de película coreana: {$overview}"
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.4
            ]);

            $translationText = trim($response->choices[0]->message->content);
            
            // Limpiar y parsear JSON
            $translationText = str_replace(['```json', '```'], '', $translationText);
            $translation = json_decode($translationText, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($translation['overview_es']) && isset($translation['spanish_overview'])) {
                return [
                    'overview_es' => trim($translation['overview_es']),
                    'spanish_overview' => trim($translation['spanish_overview'])
                ];
            } else {
                // Fallback: usar traducción simple
                $simpleTranslation = $this->getSimpleTranslation($overview, 'sinopsis');
                return [
                    'overview_es' => $simpleTranslation,
                    'spanish_overview' => $simpleTranslation
                ];
            }

        } catch (Exception $e) {
            Log::error("Error traduciendo sinopsis: " . $e->getMessage());
            return null;
        }
    }

    private function getSimpleTranslation($text, $type = 'texto')
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Traduce el siguiente {$type} al español de manera natural y fluida. Si ya está en español, devuélvelo sin cambios."
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.3
            ]);

            return trim($response->choices[0]->message->content);

        } catch (Exception $e) {
            Log::error("Error en traducción simple: " . $e->getMessage());
            return $text; // Devolver original si falla
        }
    }
}