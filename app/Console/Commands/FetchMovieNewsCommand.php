<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Person;
use App\Models\Title;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchMovieNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:fetch-movie-news {--source=newsapi : Source to use (newsapi, tmdb, ai)} 
                           {--limit=10 : Max number of news articles to fetch}
                           {--days=7 : How many days back to search for news}
                           {--title= : Fetch news for a specific title by name or ID}
                           {--genre= : Fetch news for a specific genre}
                           {--add-images : Try to find and download images for news articles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch recent news about movies and TV shows from external APIs';

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
     * TMDB API key
     * 
     * @var string
     */
    protected $tmdbApiKey;
    
    /**
     * TMDB Access Token
     * 
     * @var string
     */
    protected $tmdbAccessToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->newsApiKey = config('services.newsapi.key');
        $this->openaiApiKey = config('services.openai.api_key');
        $this->tmdbApiKey = config('services.tmdb.api_key');
        $this->tmdbAccessToken = config('services.tmdb.access_token');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = $this->option('source');
        $limit = (int) $this->option('limit');
        $days = (int) $this->option('days');
        $titleOption = $this->option('title');
        $genreOption = $this->option('genre');
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
        
        if ($source === 'tmdb' && (empty($this->tmdbApiKey) || empty($this->tmdbAccessToken))) {
            $this->error('TMDB API credentials are not set. Please check your TMDB_API_KEY and TMDB_ACCESS_TOKEN in .env file.');
            return 1;
        }
        
        $this->info("Fetching movie/TV news from {$source}...");
        
        // Get titles to search for
        $titles = $this->getTitlesForSearch($titleOption, $genreOption);
        
        if ($titles->isEmpty()) {
            $this->error('No titles found to search for.');
            return 1;
        }
        
        $this->info('Found ' . $titles->count() . ' titles to search for news.');
        
        $newsCount = 0;
        $progressBar = $this->output->createProgressBar($titles->count());
        $progressBar->start();
        
        foreach ($titles as $title) {
            $results = $this->fetchNewsForTitle($title, $source, $days, $limit);
            
            if (!empty($results)) {
                $newsCount += count($results);
                
                foreach ($results as $result) {
                    $this->saveNewsArticle($result, $title, $addImages);
                }
            }
            
            // Avoid rate limiting
            if ($source === 'newsapi' || $source === 'tmdb') {
                sleep(1);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Successfully imported {$newsCount} news articles for movies/TV shows.");
        
        // Ejecutar validación de imágenes de actores
        if ($newsCount > 0) {
            $this->newLine();
            $this->info("Validando imágenes de actores en noticias...");
            $this->call('news:validate-actor-images', ['--fix' => true]);
        }
        
        return 0;
    }
    
    /**
     * Get titles to search for
     */
    protected function getTitlesForSearch($titleOption, $genreOption)
    {
        if (!empty($titleOption)) {
            // Try to find by ID first
            if (is_numeric($titleOption)) {
                $title = Title::find($titleOption);
                if ($title) {
                    return collect([$title]);
                }
            }
            
            // Try to find by name
            $titles = Title::where('name', 'like', "%{$titleOption}%")
                ->orWhere('original_name', 'like', "%{$titleOption}%")
                ->orderBy('vote_average', 'desc')
                ->take(1)
                ->get();
                
            if ($titles->isNotEmpty()) {
                return $titles;
            }
            
            $this->warn("Title '{$titleOption}' not found.");
            return collect();
        }
        
        if (!empty($genreOption)) {
            // Find titles by genre
            return Title::whereHas('genres', function($query) use ($genreOption) {
                $query->where('name', 'like', "%{$genreOption}%");
            })
            ->orderBy('vote_average', 'desc')
            ->take(10)
            ->get();
        }
        
        // Get top rated/popular titles
        return Title::orderBy('vote_average', 'desc')
            ->where('vote_average', '>', 7.0)
            ->orderBy('popularity', 'desc')
            ->take(15)
            ->get();
    }
    
    /**
     * Fetch news for a specific title
     */
    protected function fetchNewsForTitle(Title $title, $source, $days, $limit)
    {
        $this->info("  Searching for news about: {$title->name}");
        
        if ($source === 'newsapi') {
            return $this->fetchNewsFromNewsApi($title, $days, $limit);
        } elseif ($source === 'tmdb') {
            return $this->fetchNewsFromTMDB($title, $limit);
        } elseif ($source === 'ai') {
            return $this->generateNewsWithAI($title, $limit);
        }
        
        return [];
    }
    
    /**
     * Fetch news from NewsAPI.org
     */
    protected function fetchNewsFromNewsApi(Title $title, $days, $limit)
    {
        try {
            $fromDate = Carbon::now()->subDays($days)->format('Y-m-d');
            
            // Build a search query with title name and media type
            $searchQuery = "\"{$title->name}\" AND (drama OR dorama OR serie OR movie OR película)";
            if (!empty($title->original_name) && $title->original_name !== $title->name) {
                $searchQuery .= " OR (\"{$title->original_name}\" AND (drama OR dorama OR movie))";
            }
            
            // Agregar términos específicos si es contenido asiático
            if ($title->production_countries && in_array($title->production_countries[0]['iso_3166_1'] ?? '', ['KR', 'JP', 'CN', 'TW', 'TH'])) {
                $searchQuery .= " AND (korea OR korean OR japan OR japanese OR china OR chinese OR taiwan OR thai OR asia)";
            }
            
            $response = Http::get('https://newsapi.org/v2/everything', [
                'apiKey' => $this->newsApiKey,
                'q' => $searchQuery,
                'from' => $fromDate,
                'language' => 'es',  // Cambiar a español
                'sortBy' => 'publishedAt',
                'pageSize' => $limit,
                'domains' => 'soompi.com,dramabeans.com,asianwiki.com,mydramalist.com'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'ok' && !empty($data['articles'])) {
                    // Prepare results
                    $results = [];
                    
                    foreach ($data['articles'] as $article) {
                        $results[] = [
                            'title' => $article['title'],
                            'content' => $article['content'] ?? $article['description'],
                            'source_name' => $article['source']['name'] ?? 'NewsAPI',
                            'source_url' => $article['url'] ?? null,
                            'image_url' => $article['urlToImage'] ?? null,
                            'published_at' => Carbon::parse($article['publishedAt'])->toDateTimeString(),
                            'title_id' => $title->id,
                            'is_primary_subject' => true
                        ];
                    }
                    
                    return $results;
                }
            }
            
            $this->warn("Failed to fetch news for {$title->name} from NewsAPI.");
            return [];
        } catch (\Exception $e) {
            $this->error("Error fetching news from NewsAPI: " . $e->getMessage());
            Log::error("NewsAPI Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Fetch news from TMDB
     */
    protected function fetchNewsFromTMDB(Title $title, $limit)
    {
        try {
            if (empty($title->tmdb_id)) {
                $this->warn("Title {$title->name} has no TMDB ID.");
                return [];
            }
            
            // TMDB doesn't have a news API, but we can use their changes endpoint
            // for movies or TV shows
            $mediaType = ($title->type === 'series') ? 'tv' : 'movie';
            $endpoint = "https://api.themoviedb.org/3/{$mediaType}/{$title->tmdb_id}/changes";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->tmdbAccessToken,
                'Content-Type' => 'application/json',
            ])->get($endpoint);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Also get credits/cast information for possible updates
                $creditsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->tmdbAccessToken,
                    'Content-Type' => 'application/json',
                ])->get("https://api.themoviedb.org/3/{$mediaType}/{$title->tmdb_id}/credits");
                
                // Also get current details for the title
                $detailsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->tmdbAccessToken,
                    'Content-Type' => 'application/json',
                ])->get("https://api.themoviedb.org/3/{$mediaType}/{$title->tmdb_id}");
                
                // Combine info into news items
                $results = [];
                
                if ($detailsResponse->successful()) {
                    $details = $detailsResponse->json();
                    
                    // Create a news item about the title details
                    $releaseInfo = '';
                    if ($mediaType === 'movie' && !empty($details['release_date'])) {
                        $releaseDate = Carbon::parse($details['release_date']);
                        $releaseInfo = " released on {$releaseDate->format('F j, Y')}";
                    } elseif ($mediaType === 'tv' && !empty($details['first_air_date'])) {
                        $airDate = Carbon::parse($details['first_air_date']);
                        $releaseInfo = " first aired on {$airDate->format('F j, Y')}";
                    }
                    
                    $overview = !empty($details['overview']) ? $details['overview'] : "No overview available.";
                    
                    $results[] = [
                        'title' => "Everything You Need to Know About {$title->name}",
                        'content' => "{$title->name}{$releaseInfo} is a {$mediaType} with a rating of {$details['vote_average']} out of 10 on TMDB. {$overview}",
                        'source_name' => 'TMDB',
                        'source_url' => "https://www.themoviedb.org/{$mediaType}/{$title->tmdb_id}",
                        'image_url' => !empty($details['poster_path']) ? "https://image.tmdb.org/t/p/w500{$details['poster_path']}" : null,
                        'published_at' => now()->toDateTimeString(),
                        'title_id' => $title->id,
                        'is_primary_subject' => true
                    ];
                }
                
                if ($creditsResponse->successful() && count($results) < $limit) {
                    $credits = $creditsResponse->json();
                    
                    if (!empty($credits['cast'])) {
                        $castNames = collect($credits['cast'])->take(5)->pluck('name')->implode(', ');
                        
                        $results[] = [
                            'title' => "Meet the Cast of {$title->name}",
                            'content' => "{$title->name} features a talented cast including {$castNames}. Each actor brings their unique talent to make this {$mediaType} an unforgettable experience.",
                            'source_name' => 'TMDB',
                            'source_url' => "https://www.themoviedb.org/{$mediaType}/{$title->tmdb_id}/cast",
                            'image_url' => null,
                            'published_at' => now()->subDays(rand(1, 7))->toDateTimeString(),
                            'title_id' => $title->id,
                            'is_primary_subject' => true
                        ];
                    }
                }
                
                if (!empty($data['changes']) && count($results) < $limit) {
                    // Create a news item about recent changes
                    $results[] = [
                        'title' => "Recent Updates for {$title->name}",
                        'content' => "The TMDB database entry for {$title->name} has been recently updated with new information. This could include cast changes, new images, or updated details about the {$mediaType}.",
                        'source_name' => 'TMDB Updates',
                        'source_url' => "https://www.themoviedb.org/{$mediaType}/{$title->tmdb_id}",
                        'image_url' => null,
                        'published_at' => now()->subDays(rand(1, 3))->toDateTimeString(),
                        'title_id' => $title->id,
                        'is_primary_subject' => true
                    ];
                }
                
                return $results;
            }
            
            $this->warn("Failed to fetch news for {$title->name} from TMDB.");
            return [];
        } catch (\Exception $e) {
            $this->error("Error fetching news from TMDB: " . $e->getMessage());
            Log::error("TMDB Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate news with AI (OpenAI)
     */
    protected function generateNewsWithAI(Title $title, $limit)
    {
        try {
            // Skip if no OpenAI key is set
            if (empty($this->openaiApiKey)) {
                return [];
            }
            
            // Get title details to provide context
            $genres = $title->genres->pluck('name')->implode(', ');
            $mediaType = ($title->type === 'series') ? 'serie' : 'película';
            
            $prompt = "Genera {$limit} artículos de noticias recientes en español sobre la {$mediaType} \"{$title->name}\"";
            
            if (!empty($title->original_name) && $title->original_name !== $title->name) {
                $prompt .= " (título original: {$title->original_name})";
            }
            
            if (!empty($genres)) {
                $prompt .= ", que pertenece a los géneros {$genres}";
            }
            
            $prompt .= ". Las noticias deben ser ÚNICAMENTE sobre doramas asiáticos, producciones asiáticas, premios asiáticos, disponibilidad en plataformas de streaming especializadas en contenido asiático, o información de la industria del entretenimiento asiático. NO incluyas referencias a contenido occidental o no relacionado con Asia. Formatea la respuesta como JSON con la siguiente estructura para cada noticia: {\"title\": \"Titular en español\", \"content\": \"Artículo completo en español (mínimo 150 palabras)\", \"source_name\": \"Nombre de fuente (ej: K-Drama News, Asian Entertainment Weekly, Dorama Channel)\", \"published_days_ago\": número entre 1-30}";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un periodista especializado en entretenimiento asiático. Escribe únicamente noticias en español sobre doramas, películas asiáticas, actores asiáticos, y la industria del entretenimiento de Asia Oriental (Corea, Japón, China, Tailandia, etc). NO incluyas contenido sobre deportes americanos, universidades, o temas no relacionados con el entretenimiento asiático.'],
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
                                'source_name' => $article['source_name'] ?? 'Entertainment Weekly',
                                'source_url' => null,
                                'image_url' => null,
                                'published_at' => $publishedAt->toDateTimeString(),
                                'title_id' => $title->id,
                                'is_primary_subject' => true,
                                'ai_generated' => true
                            ];
                        }
                        
                        return $results;
                    }
                } catch (\Exception $jsonEx) {
                    $this->warn("Failed to parse AI response for {$title->name}");
                    Log::error("AI JSON Parse Error: " . $jsonEx->getMessage() . "\nContent: " . $aiContent);
                }
            }
            
            $this->warn("Failed to generate news for {$title->name} using AI.");
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
    protected function saveNewsArticle($article, Title $title, $downloadImage = false)
    {
        try {
            // Check if a similar article already exists
            $existingNews = News::where('title', 'like', $article['title'])
                ->orWhere('source_url', $article['source_url'])
                ->first();
                
            if ($existingNews) {
                // Connect the actors from this title to the news article too
                $mainActors = $this->getMainActorsForTitle($title);
                
                foreach ($mainActors as $actor) {
                    if (!$existingNews->people()->where('person_id', $actor->id)->exists()) {
                        $existingNews->people()->attach($actor->id, [
                            'primary_subject' => false
                        ]);
                    }
                }
                
                $this->info("  Added existing article '{$article['title']}' to {$title->name} actors");
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
            
            // Connect to the main actors of the title
            $mainActors = $this->getMainActorsForTitle($title);
            
            foreach ($mainActors as $actor) {
                $news->people()->attach($actor->id, [
                    'primary_subject' => false
                ]);
            }
            
            $this->info("  Added news article: {$article['title']}");
        } catch (\Exception $e) {
            $this->error("Error saving news article: " . $e->getMessage());
            Log::error("Error saving news: " . $e->getMessage());
        }
    }
    
    /**
     * Get main actors for a title
     */
    protected function getMainActorsForTitle(Title $title)
    {
        return $title->people()
            ->wherePivot('role', 'actor')
            ->orderBy('popularity', 'desc')
            ->take(3)
            ->get();
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
