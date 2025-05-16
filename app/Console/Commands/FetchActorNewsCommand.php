<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchActorNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:fetch-news {--source=newsapi : Source to use (newsapi, ai)} 
                            {--limit=10 : Max number of news articles to fetch}
                            {--days=7 : How many days back to search for news}
                            {--actor= : Fetch news for a specific actor by name or ID}
                            {--add-images : Try to find and download images for news articles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch recent news about actors, movies, and TV shows from external APIs';

    /**
     * NewsAPI.org API key
     * 
     * @var string
     */
    protected $newsApiKey;
    
    /**
     * OpenAI API key
     * 
     * @var string
     */
    protected $openaiApiKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->newsApiKey = config('services.newsapi.key');
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = $this->option('source');
        $limit = (int) $this->option('limit');
        $days = (int) $this->option('days');
        $actorOption = $this->option('actor');
        $addImages = $this->option('add-images');
        
        // Validate required API keys
        if ($source === 'newsapi' && empty($this->newsApiKey)) {
            $this->error('NewsAPI key is not set. Please add NEWSAPI_KEY to your .env file.');
            return 1;
        }
        
        if ($source === 'ai' && empty($this->openaiApiKey)) {
            $this->error('OpenAI API key is not set. Please add OPENAI_API_KEY to your .env file.');
            return 1;
        }
        
        $this->info("Fetching news from {$source}...");
        
        // Get actors to search for
        $actors = $this->getActorsForSearch($actorOption);
        
        if ($actors->isEmpty()) {
            $this->error('No actors found to search for.');
            return 1;
        }
        
        $this->info('Found ' . $actors->count() . ' actors to search for news.');
        
        $newsCount = 0;
        $progressBar = $this->output->createProgressBar($actors->count());
        $progressBar->start();
        
        foreach ($actors as $actor) {
            $results = $this->fetchNewsForActor($actor, $source, $days, $limit);
            
            if (!empty($results)) {
                $newsCount += count($results);
                
                foreach ($results as $result) {
                    $this->saveNewsArticle($result, $actor, $addImages);
                }
            }
            
            // Avoid rate limiting
            if ($source === 'newsapi') {
                sleep(1);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Successfully imported {$newsCount} news articles for actors.");
        
        // Ejecutar validación de imágenes de actores
        if ($newsCount > 0) {
            $this->newLine();
            $this->info("Validando imágenes de actores en noticias...");
            $this->call('news:validate-actor-images', ['--fix' => true]);
        }
        
        return 0;
    }
    
    /**
     * Get actors to search for
     */
    protected function getActorsForSearch($actorOption)
    {
        if (!empty($actorOption)) {
            // Try to find by ID first
            if (is_numeric($actorOption)) {
                $actor = Person::find($actorOption);
                if ($actor) {
                    return collect([$actor]);
                }
            }
            
            // Try to find by name
            $actors = Person::where('name', 'like', "%{$actorOption}%")
                ->orWhere('original_name', 'like', "%{$actorOption}%")
                ->orderBy('popularity', 'desc')
                ->take(1)
                ->get();
                
            if ($actors->isNotEmpty()) {
                return $actors;
            }
            
            $this->warn("Actor '{$actorOption}' not found.");
            return collect();
        }
        
        // Get top popular actors
        return Person::orderBy('popularity', 'desc')
            ->take(20)
            ->get();
    }
    
    /**
     * Fetch news for a specific actor
     */
    protected function fetchNewsForActor(Person $actor, $source, $days, $limit)
    {
        $this->info("  Searching for news about: {$actor->name}");
        
        if ($source === 'newsapi') {
            return $this->fetchNewsFromNewsApi($actor, $days, $limit);
        } elseif ($source === 'ai') {
            return $this->generateNewsWithAI($actor, $limit);
        }
        
        return [];
    }
    
    /**
     * Fetch news from NewsAPI.org
     */
    protected function fetchNewsFromNewsApi(Person $actor, $days, $limit)
    {
        try {
            $fromDate = Carbon::now()->subDays($days)->format('Y-m-d');
            
            // Crear queries más específicas para el entretenimiento asiático
            $queries = [];
            
            // Incluir el nombre del actor con términos de entretenimiento
            $queries[] = '"' . $actor->name . '" AND (drama OR movie OR serie OR actor OR kdrama OR cdrama OR jdrama)';
            
            // Si el actor es asiático, incluir términos específicos
            if (in_array($actor->nationality, ['Korean', 'Japanese', 'Chinese', 'Taiwanese', 'Thai']) || 
                preg_match('/[가-힣ぁ-んァ-ヶー一-龯]/u', $actor->name)) {
                $queries[] = '"' . $actor->name . '" AND (korea OR korean OR japan OR japanese OR china OR chinese OR taiwan OR thai OR asia)';
            }
            
            // Buscar con múltiples queries para mejores resultados
            $allResults = [];
            
            foreach ($queries as $query) {
                $response = Http::get('https://newsapi.org/v2/everything', [
                    'apiKey' => $this->newsApiKey,
                    'q' => $query,
                    'from' => $fromDate,
                    'language' => 'es',  // Cambiar a español
                    'sortBy' => 'publishedAt',
                    'pageSize' => ceil($limit / count($queries)),
                    'domains' => 'soompi.com,dramabeans.com,asianwiki.com,mydramalist.com,koreatimes.co.kr'
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'ok' && !empty($data['articles'])) {
                        $allResults = array_merge($allResults, $data['articles']);
                    }
                }
            }
            
            if (!empty($allResults)) {
                // Prepare results
                $results = [];
                
                // Filtrar y validar resultados
                foreach ($allResults as $article) {
                    // Validar que el artículo sea relevante (contiene palabras clave asiáticas)
                    $relevantKeywords = ['drama', 'kdrama', 'cdrama', 'jdrama', 'serie', 'película', 'actor', 'actriz', 
                                       'korea', 'korean', 'japan', 'japanese', 'china', 'chinese', 'taiwan', 'thai', 'asia'];
                    
                    $isRelevant = false;
                    $articleText = strtolower($article['title'] . ' ' . ($article['description'] ?? ''));
                    
                    foreach ($relevantKeywords as $keyword) {
                        if (str_contains($articleText, $keyword)) {
                            $isRelevant = true;
                            break;
                        }
                    }
                    
                    if ($isRelevant) {
                        $results[] = [
                            'title' => $article['title'],
                            'content' => $article['content'] ?? $article['description'],
                            'source_name' => $article['source']['name'] ?? 'NewsAPI',
                            'source_url' => $article['url'] ?? null,
                            'image_url' => $article['urlToImage'] ?? null,
                            'published_at' => Carbon::parse($article['publishedAt'])->toDateTimeString(),
                            'actor_id' => $actor->id,
                            'is_primary_subject' => true
                        ];
                    }
                }
                
                return array_slice($results, 0, $limit);
            }
            
            $this->warn("Failed to fetch news for {$actor->name} from NewsAPI.");
            return [];
        } catch (\Exception $e) {
            $this->error("Error fetching news from NewsAPI: " . $e->getMessage());
            Log::error("NewsAPI Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate news with AI (OpenAI)
     */
    protected function generateNewsWithAI(Person $actor, $limit)
    {
        try {
            // Skip if no OpenAI key is set
            if (empty($this->openaiApiKey)) {
                return [];
            }
            
            // Get actor details to provide context
            $actingTitles = $actor->actingTitles()->orderBy('first_air_date', 'desc')->take(3)->get();
            $titleNames = $actingTitles->pluck('title')->implode(', ');
            
            $prompt = "Genera {$limit} noticias recientes en español sobre el actor/actriz {$actor->name}";
            
            if (!empty($titleNames)) {
                $prompt .= ", conocido/a por su participación en {$titleNames}";
            }
            
            $prompt .= ". Las noticias deben ser únicamente sobre doramas asiáticos, películas asiáticas, premios de la industria asiática del entretenimiento, o proyectos relacionados con la industria del entretenimiento de Asia (Corea, Japón, China, Tailandia, etc). NO incluyas noticias sobre baloncesto, universidades americanas u otros temas no relacionados. Las noticias deben sonar auténticas y profesionales. Formato JSON requerido: {\"title\": \"Titular en español\", \"content\": \"Artículo completo en español (mínimo 150 palabras)\", \"source_name\": \"Nombre de fuente (ej: Drama News Asia, K-Entertainment Weekly, Asian Cinema Today)\", \"published_days_ago\": número entre 1-30}";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un periodista especializado en entretenimiento asiático. Escribe noticias en español sobre actores de doramas coreanos, japoneses, chinos y tailandeses. Enfócate únicamente en contenido relacionado con la industria del entretenimiento asiático.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $aiContent = $data['choices'][0]['message']['content'] ?? '';
                
                // Parse JSON from response
                // Sometimes GPT wraps the response in ```json ``` code blocks
                $aiContent = preg_replace('/```json\s(.*?)```/s', '$1', $aiContent);
                $aiContent = trim($aiContent);
                
                try {
                    $articles = json_decode($aiContent, true);
                    
                    // Fix if GPT returns a single article instead of an array
                    if (isset($articles['title'])) {
                        $articles = [$articles];
                    }
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($articles)) {
                        $results = [];
                        
                        foreach ($articles as $article) {
                            $daysAgo = $article['published_days_ago'] ?? rand(1, 7);
                            $publishedAt = Carbon::now()->subDays($daysAgo);
                            
                            $results[] = [
                                'title' => $article['title'],
                                'content' => $article['content'],
                                'source_name' => $article['source_name'] ?? 'Drama News Asia',
                                'source_url' => null,
                                'image_url' => null,
                                'published_at' => $publishedAt->toDateTimeString(),
                                'actor_id' => $actor->id,
                                'is_primary_subject' => true,
                                'ai_generated' => true
                            ];
                        }
                        
                        return $results;
                    }
                } catch (\Exception $jsonEx) {
                    $this->warn("Failed to parse AI response for {$actor->name}");
                    Log::error("AI JSON Parse Error: " . $jsonEx->getMessage() . "\nContent: " . $aiContent);
                }
            }
            
            $this->warn("Failed to generate news for {$actor->name} using AI.");
            return [];
        } catch (\Exception $e) {
            $this->error("Error generating news with AI: " . $e->getMessage());
            Log::error("OpenAI Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Save a news article to the database
     */
    protected function saveNewsArticle($article, Person $actor, $downloadImage = false)
    {
        try {
            // Check if a similar article already exists
            $existingNews = News::where('title', 'like', $article['title'])
                ->orWhere('source_url', $article['source_url'])
                ->first();
                
            if ($existingNews) {
                // If the article exists but not connected to this actor, add the connection
                if (!$existingNews->people()->where('person_id', $actor->id)->exists()) {
                    $existingNews->people()->attach($actor->id, [
                        'primary_subject' => $article['is_primary_subject'] ?? false
                    ]);
                    $this->info("  Added existing article '{$article['title']}' to {$actor->name}");
                }
                return;
            }
            
            // Generate slug
            $slug = Str::slug($article['title']);
            
            // Download image if available and requested
            $imagePath = null;
            if ($downloadImage && !empty($article['image_url'])) {
                $imagePath = $this->downloadImage($article['image_url'], $slug);
            }
            
            // Create the news article
            $news = News::create([
                'title' => $article['title'],
                'slug' => $slug,
                'content' => $article['content'],
                'image' => $imagePath,
                'source_url' => $article['source_url'] ?? null,
                'source_name' => $article['source_name'] ?? null,
                'featured' => false,
                'published_at' => $article['published_at'] ?? now(),
            ]);
            
            // Connect to the actor
            $news->people()->attach($actor->id, [
                'primary_subject' => $article['is_primary_subject'] ?? true
            ]);
            
            $this->info("  Added news article: {$article['title']}");
        } catch (\Exception $e) {
            $this->error("Error saving news article: " . $e->getMessage());
            Log::error("Error saving news: " . $e->getMessage());
        }
    }
    
    /**
     * Download an image from URL
     */
    protected function downloadImage($url, $slug)
    {
        try {
            $response = Http::get($url);
            
            if ($response->successful()) {
                $filename = $slug . '-' . time() . '.jpg';
                $path = storage_path('app/public/news');
                
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                file_put_contents("{$path}/{$filename}", $response->body());
                return "news/{$filename}";
            }
        } catch (\Exception $e) {
            Log::error("Image download error: " . $e->getMessage());
        }
        
        return null;
    }
}
