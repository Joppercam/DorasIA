<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\ProfessionalReview;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class ImportMovieReviews extends Command
{
    protected $signature = 'movies:import-reviews {--translate : Traducir reseñas existentes}';
    protected $description = 'Importar reseñas de películas desde TMDB y traducirlas al español';

    private $tmdbApiKey;
    private $openAiAvailable = false;

    public function __construct()
    {
        parent::__construct();
        $this->tmdbApiKey = config('services.tmdb.api_key');
        
        // Verificar si OpenAI está disponible
        try {
            $this->openAiAvailable = !empty(config('openai.api_key'));
        } catch (Exception $e) {
            $this->openAiAvailable = false;
        }
    }

    public function handle()
    {
        if ($this->option('translate')) {
            return $this->translateExistingReviews();
        }

        if (!$this->tmdbApiKey) {
            $this->error('TMDB API key no configurada');
            return 1;
        }

        $this->info('🎬 Importando reseñas de películas coreanas...');
        
        $movies = Movie::whereNotNull('tmdb_id')->get();
        $this->info("📊 Encontradas {$movies->count()} películas para procesar");

        $importedCount = 0;
        $totalReviews = 0;

        $progressBar = $this->output->createProgressBar($movies->count());
        $progressBar->start();

        foreach ($movies as $movie) {
            try {
                $reviewsImported = $this->importMovieReviews($movie);
                $importedCount += $reviewsImported > 0 ? 1 : 0;
                $totalReviews += $reviewsImported;
                
                // Pequeña pausa para no sobrecargar la API
                usleep(250000); // 0.25 segundos
                
            } catch (Exception $e) {
                Log::error("Error importando reseñas para película {$movie->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        
        $this->info("✅ Importación completada!");
        $this->info("   📽️ Películas procesadas: {$movies->count()}");
        $this->info("   📝 Películas con reseñas: {$importedCount}");
        $this->info("   📋 Total reseñas importadas: {$totalReviews}");

        return 0;
    }

    private function importMovieReviews(Movie $movie)
    {
        if (!$movie->tmdb_id) {
            return 0;
        }

        try {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$movie->tmdb_id}/reviews", [
                'api_key' => $this->tmdbApiKey,
                'language' => 'en-US',
                'page' => 1
            ]);

            if (!$response->successful()) {
                return 0;
            }

            $data = $response->json();
            $reviews = $data['results'] ?? [];

            if (empty($reviews)) {
                return 0;
            }

            $importedCount = 0;

            foreach ($reviews as $reviewData) {
                $existingReview = ProfessionalReview::where('tmdb_review_id', $reviewData['id'])
                    ->where('movie_id', $movie->id)
                    ->first();

                if ($existingReview) {
                    continue;
                }

                $content = $reviewData['content'] ?? '';
                $excerpt = $this->generateExcerpt($content);
                
                // Traducir contenido al español si OpenAI está disponible
                $contentEs = null;
                $excerptEs = null;
                
                if ($this->openAiAvailable && !empty($content)) {
                    try {
                        $contentEs = $this->translateToSpanish($content);
                        $excerptEs = $this->translateToSpanish($excerpt);
                    } catch (Exception $e) {
                        Log::warning("Error traduciendo reseña {$reviewData['id']}: " . $e->getMessage());
                    }
                }

                // Determinar sentiment
                $rating = $reviewData['author_details']['rating'] ?? null;
                $isPositive = $this->determineSentiment($content, $rating);

                ProfessionalReview::create([
                    'movie_id' => $movie->id,
                    'reviewable_type' => 'movie',
                    'source' => 'TMDB',
                    'source_url' => "https://www.themoviedb.org/review/{$reviewData['id']}",
                    'author' => $reviewData['author'] ?? 'Usuario TMDB',
                    'author_url' => $reviewData['author_details']['username'] 
                        ? "https://www.themoviedb.org/u/{$reviewData['author_details']['username']}" 
                        : null,
                    'rating' => $rating,
                    'max_rating' => 10,
                    'content' => $content,
                    'content_es' => $contentEs,
                    'excerpt' => $excerpt,
                    'excerpt_es' => $excerptEs,
                    'review_date' => $reviewData['created_at'] ? date('Y-m-d', strtotime($reviewData['created_at'])) : now(),
                    'is_positive' => $isPositive,
                    'language' => 'en',
                    'tmdb_review_id' => $reviewData['id']
                ]);

                $importedCount++;
            }

            return $importedCount;

        } catch (Exception $e) {
            Log::error("Error obteniendo reseñas de TMDB para película {$movie->id}: " . $e->getMessage());
            return 0;
        }
    }

    private function translateExistingReviews()
    {
        if (!$this->openAiAvailable) {
            $this->error('OpenAI no está configurado');
            return 1;
        }

        $this->info('🔄 Traduciendo reseñas existentes al español...');

        $untranslatedReviews = ProfessionalReview::where('reviewable_type', 'movie')
            ->whereNull('content_es')
            ->whereNotNull('content')
            ->get();

        if ($untranslatedReviews->isEmpty()) {
            $this->info('✅ Todas las reseñas de películas ya están traducidas');
            return 0;
        }

        $this->info("📝 Encontradas {$untranslatedReviews->count()} reseñas para traducir");

        $progressBar = $this->output->createProgressBar($untranslatedReviews->count());
        $progressBar->start();

        $translatedCount = 0;

        foreach ($untranslatedReviews as $review) {
            try {
                $contentEs = $this->translateToSpanish($review->content);
                $excerptEs = null;
                
                if ($review->excerpt) {
                    $excerptEs = $this->translateToSpanish($review->excerpt);
                }

                $review->update([
                    'content_es' => $contentEs,
                    'excerpt_es' => $excerptEs
                ]);

                $translatedCount++;
                
                // Pequeña pausa entre traducciones
                usleep(500000); // 0.5 segundos

            } catch (Exception $e) {
                Log::error("Error traduciendo reseña {$review->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Traducción completada!");
        $this->info("   📝 Reseñas traducidas: {$translatedCount}");

        return 0;
    }

    private function translateToSpanish($text)
    {
        if (empty($text) || !$this->openAiAvailable) {
            return null;
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional especializado en contenido audiovisual. Traduce el siguiente texto al español de manera natural y fluida, manteniendo el tono y contexto de una reseña de película. Si el texto ya está en español, devuélvelo sin cambios.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
                'max_tokens' => 1500,
                'temperature' => 0.3
            ]);

            return trim($response->choices[0]->message->content);

        } catch (Exception $e) {
            Log::error("Error en traducción OpenAI: " . $e->getMessage());
            return null;
        }
    }

    private function generateExcerpt($content, $maxLength = 200)
    {
        if (empty($content)) {
            return '';
        }

        $content = strip_tags($content);
        
        if (strlen($content) <= $maxLength) {
            return $content;
        }

        $excerpt = substr($content, 0, $maxLength);
        $lastSpace = strrpos($excerpt, ' ');
        
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }

    private function determineSentiment($content, $rating = null)
    {
        if ($rating !== null) {
            return $rating >= 6; // 6/10 o más es positivo
        }

        // Análisis básico de sentimiento por palabras clave
        $content = strtolower($content);
        
        $positiveWords = ['excellent', 'amazing', 'great', 'fantastic', 'wonderful', 'brilliant', 'masterpiece', 'outstanding', 'perfect', 'love'];
        $negativeWords = ['terrible', 'awful', 'horrible', 'disappointing', 'boring', 'waste', 'bad', 'worst', 'hate', 'disaster'];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($content, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($content, $word);
        }
        
        if ($positiveCount > $negativeCount) {
            return true;
        } elseif ($negativeCount > $positiveCount) {
            return false;
        }
        
        // Si empate, asumir neutral/positivo
        return true;
    }
}