<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Category;
use App\Models\Genre;
use App\Models\Title;
use App\Models\News;
use App\Services\CacheService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the home page with featured content.
     */
    public function home()
    {
        // Get featured titles for the hero section with valid backdrop images
        $featuredTitles = CacheService::rememberTrending('featured', function() {
            return Title::where('is_featured', true)
                ->whereNotNull('backdrop_path')
                ->where('backdrop_path', '!=', '')
                ->whereRaw('LENGTH(backdrop_path) > 5') // Asegurarnos que la ruta tenga una longitud razonable
                ->where(function($query) {
                    $query->where('vote_average', '>=', 7.0)
                          ->orWhereNull('vote_average');
                })
                ->with(['genres'])
                ->inRandomOrder()
                ->take(5)
                ->get();
        }, CacheService::DURATION_MEDIUM);
            
        // Si no hay títulos destacados con imágenes, buscar cualquier título con buena valoración e imagen
        if ($featuredTitles->count() == 0) {
            $featuredTitles = Title::whereNotNull('backdrop_path')
                ->where('backdrop_path', '!=', '')
                ->whereRaw('LENGTH(backdrop_path) > 5')
                ->where('vote_average', '>=', 7.0)
                ->with(['genres'])
                ->inRandomOrder()
                ->take(5)
                ->get();
        }

        // Get titles by categories for carousels, ensuring they have poster images
        $categories = Category::with(['titles' => function ($query) {
            $query->whereNotNull('poster_path')
                  ->where('poster_path', '!=', '')
                  ->whereRaw('LENGTH(poster_path) > 5')
                  ->with('genres')
                  ->orderBy('created_at', 'desc')
                  ->take(10);
        }])
        ->orderBy('id')  // Cambio a id ya que display_order podría no existir
        ->get();
        
        // Obtener los títulos más valorados (con imágenes válidas)
        $topRatedTitles = CacheService::rememberTrending('top_rated', function() {
            return Title::whereNotNull('vote_average')
                ->whereNotNull('poster_path')
                ->where('poster_path', '!=', '')
                ->whereRaw('LENGTH(poster_path) > 5')
                ->with('genres')
                ->orderByDesc('vote_average')
                ->take(10)
                ->get();
        }, CacheService::DURATION_MEDIUM);
        
        // Obtener los títulos más vistos (basado en entradas de historial)
        $mostWatchedTitles = CacheService::rememberTrending('most_watched', function() {
            return Title::whereNotNull('poster_path')
                ->where('poster_path', '!=', '')
                ->whereRaw('LENGTH(poster_path) > 5')
                ->withCount('watchHistories')
                ->with('genres')
                ->orderByDesc('watch_histories_count')
                ->take(10)
                ->get();
        }, CacheService::DURATION_SHORT);
            
        // Obtener los títulos más comentados (los que generan más discusión)
        $mostCommentedTitles = CacheService::rememberTrending('most_commented', function() {
            return Title::whereNotNull('poster_path')
                ->where('poster_path', '!=', '')
                ->whereRaw('LENGTH(poster_path) > 5')
                ->withCount('comments')
                ->with('genres')
                ->orderByDesc('comments_count')
                ->take(10)
                ->get();
        }, CacheService::DURATION_SHORT);
            
        // Obtener las valoraciones más recientes para mostrar en el sidebar
        $recentRatings = \App\Models\Rating::with(['title', 'profile'])
            ->whereHas('title', function($query) {
                $query->whereNotNull('poster_path')
                      ->where('poster_path', '!=', '')
                      ->whereRaw('LENGTH(poster_path) > 5');
            })
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
            
        // Si no hay suficientes títulos con historial o comentarios, obtener los añadidos más recientemente
        if ($mostWatchedTitles->count() < 5 || $mostCommentedTitles->count() < 5) {
            $recentTitles = Title::whereNotNull('poster_path')
                ->where('poster_path', '!=', '')
                ->whereRaw('LENGTH(poster_path) > 5')
                ->with('genres')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();
        } else {
            $recentTitles = collect([]);
        }
        
        // Get categories with counts for display in the featured categories section
        $featuredCategories = Category::withCount('titles')
            ->orderBy('id')
            ->get();
        
        // Get popular genres for the genre showcase section
        $popularGenres = Genre::withCount('titles')
            ->orderByDesc('titles_count')
            ->take(12)
            ->get();
            
        // Get latest news for the news section
        $latestNews = News::with(['people' => function($query) {
                $query->limit(3);
            }])
            ->where(function($query) {
                $query->where('featured', true)
                      ->orWhere('published_at', '>=', now()->subDays(7));
            })
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        return view('home', [
            'featuredTitles' => $featuredTitles,
            'categories' => $categories,
            'topRatedTitles' => $topRatedTitles,
            'mostWatchedTitles' => $mostWatchedTitles,
            'mostCommentedTitles' => $mostCommentedTitles,
            'recentTitles' => $recentTitles,
            'recentRatings' => $recentRatings,
            'featuredCategories' => $featuredCategories,
            'popularGenres' => $popularGenres,
            'latestNews' => $latestNews,
        ]);
    }

    /**
     * Display the catalog page with all titles.
     */
    public function index(Request $request)
    {
        $query = Title::with(['genres', 'category']);

        // Apply filters
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('original_title', 'like', "%{$search}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('country')) {
            $query->where('origin_country', $request->country);
        }

        // Apply sorting
        $sort = $request->sort ?? 'latest';
        switch ($sort) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'oldest':
                $query->orderBy('release_year');
                break;
            case 'latest':
            default:
                $query->orderBy('release_year', 'desc');
                break;
        }

        $titles = $query->paginate(24);
        $categories = Category::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        
        // Get user's watch history for each title if logged in
        $titleWatchHistory = [];
        if (auth()->check()) {
            $profile = auth()->user()->getActiveProfile();
            
            if ($profile) {
                // Get watch history for movies
                $movieHistories = $profile->watchHistory()
                    ->whereNotNull('title_id')
                    ->with('title')
                    ->get();
                
                foreach ($movieHistories as $history) {
                    $titleWatchHistory[$history->title_id] = $history;
                }
                
                // Get watch history for episodes (most recent episode per title)
                $episodeHistories = $profile->watchHistory()
                    ->whereNotNull('episode_id')
                    ->with(['episode.season.title'])
                    ->get()
                    ->sortByDesc('updated_at')
                    ->groupBy(function($item) {
                        return $item->episode->season->title_id;
                    })
                    ->map(function($group) {
                        return $group->first(); // Get most recent episode watched for each title
                    });
                
                foreach ($episodeHistories as $history) {
                    $titleId = $history->episode->season->title_id;
                    $titleWatchHistory[$titleId] = $history;
                }
            }
        }

        return view('catalog.index', [
            'titles' => $titles,
            'categories' => $categories,
            'genres' => $genres,
            'filters' => $request->all(),
            'titleWatchHistory' => $titleWatchHistory,
        ]);
    }

    /**
     * Display titles in a specific category.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $titles = Title::where('category_id', $category->id)
            ->with('genres')
            ->orderBy('release_year', 'desc')
            ->paginate(24);

        return view('catalog.category', [
            'category' => $category,
            'titles' => $titles,
        ]);
    }

    /**
     * Display titles with a specific genre.
     */
    public function genre(string $slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();
        $titles = $genre->titles()
            ->with('genres')
            ->orderBy('release_year', 'desc')
            ->paginate(24);

        return view('catalog.genre', [
            'genre' => $genre,
            'titles' => $titles,
        ]);
=======
use App\Models\Genre;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Watchlist;
use App\Models\WatchlistItem;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    protected $tmdbService;
    
    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }
    
    /**
     * Mostrar la página principal de descubrimiento
     */
    public function discover(Request $request)
    {
        // Obtener todas las categorías de contenido
        $genres = Genre::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        
        // Películas más recientes
        $latestMovies = Movie::with('genres')
            ->orderBy('release_date', 'desc')
            ->take(12)
            ->get()
            ->map(function($movie) {
                return $this->formatContentForVue($movie, 'movie');
            });
            
        // Series más populares
        $popularTvShows = TvShow::with('genres')
            ->where('vote_count', '>', 100)
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get()
            ->map(function($tvShow) {
                return $this->formatContentForVue($tvShow, 'tv-show');
            });
            
        // Películas mejor valoradas
        $topRatedMovies = Movie::with('genres')
            ->where('vote_count', '>', 50)
            ->orderBy('vote_average', 'desc')
            ->take(6)
            ->get()
            ->map(function($movie) {
                return $this->formatContentForVue($movie, 'movie');
            });
            
        // Anime destacado (asumiendo que hay un género 'anime')
        $animeShows = TvShow::with('genres')
            ->where('show_type', 'anime')
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get()
            ->map(function($tvShow) {
                return $this->formatContentForVue($tvShow, 'tv-show');
            });
            
        // Películas chinas destacadas
        $chineseMovies = Movie::with('genres')
            ->where('country_of_origin', 'CN')
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get()
            ->map(function($movie) {
                return $this->formatContentForVue($movie, 'movie');
            });
            
        // Series coreanas destacadas
        $koreanDramas = TvShow::with('genres')
            ->where('country_of_origin', 'KR')
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get()
            ->map(function($tvShow) {
                return $this->formatContentForVue($tvShow, 'tv-show');
            });
            
        return view('catalog.discover', compact(
            'genres', 
            'countries', 
            'latestMovies', 
            'popularTvShows', 
            'topRatedMovies', 
            'animeShows',
            'chineseMovies',
            'koreanDramas'
        ));
    }
    
    /**
     * Mostrar listado de películas con filtros
     */
    public function movies(Request $request)
    {
        $query = Movie::with('genres');
        
        // Aplicar filtros si existen
        if ($request->has('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }
        
        if ($request->has('country')) {
            $query->where('country_of_origin', $request->country);
        }
        
        if ($request->has('year')) {
            $query->whereYear('release_date', $request->year);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'popularity');
        $direction = 'desc';
        
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', 'asc');
                $direction = 'asc';
                break;
            case 'release_date':
                $query->orderBy('release_date', 'desc');
                break;
            case 'rating':
                $query->orderBy('vote_average', 'desc');
                break;
            default:
                $query->orderBy('popularity', 'desc');
                break;
        }
        
        // Paginación
        $movies = $query->paginate(24)->withQueryString();
        
        // Obtener géneros y países para filtros
        $genres = Genre::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        
        return view('catalog.movies', compact('movies', 'genres', 'countries', 'sortBy', 'direction'));
    }
    
    /**
     * Mostrar listado de series/doramas con filtros
     */
    public function tvShows(Request $request)
    {
        $query = TvShow::with('genres');
        
        // Aplicar filtros si existen
        if ($request->has('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }
        
        if ($request->has('country')) {
            $query->where('country_of_origin', $request->country);
        }
        
        if ($request->has('year')) {
            $query->whereYear('first_air_date', $request->year);
        }
        
        if ($request->has('type')) {
            $query->where('show_type', $request->type);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'popularity');
        $direction = 'desc';
        
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', 'asc');
                $direction = 'asc';
                break;
            case 'first_air_date':
                $query->orderBy('first_air_date', 'desc');
                break;
            case 'rating':
                $query->orderBy('vote_average', 'desc');
                break;
            default:
                $query->orderBy('popularity', 'desc');
                break;
        }
        
        // Paginación
        $tvShows = $query->paginate(24)->withQueryString();
        
        // Obtener géneros y países para filtros
        $genres = Genre::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        
        // Tipos de series
        $showTypes = [
            'drama' => 'Drama/Dorama',
            'anime' => 'Anime',
            'variety' => 'Variety Show'
        ];
        
        return view('catalog.tv-shows', compact('tvShows', 'genres', 'countries', 'showTypes', 'sortBy', 'direction'));
    }
    
    /**
     * Mostrar detalle de una película
     */
    public function movieDetail($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'cast.person', 'crew.person', 'availability.platform', 'availability.country'])
            ->firstOrFail();
            
        // Verificar si está en favoritos/watchlist para usuarios autenticados
        $isFavorite = false;
        $inWatchlist = false;
        
        if (Auth::check()) {
            $userId = Auth::id();
            
            $isFavorite = Favorite::where([
                'user_id' => $userId,
                'content_type' => 'App\\Models\\Movie',
                'content_id' => $movie->id
            ])->exists();
            
            $watchlistItems = WatchlistItem::whereHas('watchlist', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where([
                'content_type' => 'App\\Models\\Movie',
                'content_id' => $movie->id
            ])
            ->exists();
            
            $inWatchlist = $watchlistItems;
        }
            
        // Obtener películas relacionadas (mismo género o país)
        $relatedMovies = Movie::where('id', '!=', $movie->id)
            ->where(function($query) use ($movie) {
                $query->whereHas('genres', function($q) use ($movie) {
                    $q->whereIn('genre_id', $movie->genres->pluck('id'));
                })
                ->orWhere('country_of_origin', $movie->country_of_origin);
            })
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get()
            ->map(function($relatedMovie) {
                return $this->formatContentForVue($relatedMovie, 'movie');
            });
            
        return view('catalog.movie-detail', compact('movie', 'relatedMovies', 'isFavorite', 'inWatchlist'));
    }
    
    /**
     * Mostrar detalle de una serie
     */
    public function tvShowDetail($slug)
    {
        $tvShow = TvShow::where('slug', $slug)
            ->with(['genres', 'seasons.episodes', 'cast.person', 'crew.person', 'availability.platform', 'availability.country'])
            ->firstOrFail();
            
        // Verificar si está en favoritos/watchlist para usuarios autenticados
        $isFavorite = false;
        $inWatchlist = false;
        
        if (Auth::check()) {
            $userId = Auth::id();
            
            $isFavorite = Favorite::where([
                'user_id' => $userId,
                'content_type' => 'App\\Models\\TvShow',
                'content_id' => $tvShow->id
            ])->exists();
            
            $watchlistItems = WatchlistItem::whereHas('watchlist', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where([
                'content_type' => 'App\\Models\\TvShow',
                'content_id' => $tvShow->id
            ])
            ->exists();
            
            $inWatchlist = $watchlistItems;
        }
            
        // Obtener series relacionadas (mismo género o país)
        $relatedTvShows = TvShow::where('id', '!=', $tvShow->id)
            ->where(function($query) use ($tvShow) {
                $query->whereHas('genres', function($q) use ($tvShow) {
                    $q->whereIn('genre_id', $tvShow->genres->pluck('id'));
                })
                ->orWhere('country_of_origin', $tvShow->country_of_origin);
            })
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get()
            ->map(function($relatedTvShow) {
                return $this->formatContentForVue($relatedTvShow, 'tv-show');
            });
            
        return view('catalog.tv-show-detail', compact('tvShow', 'relatedTvShows', 'isFavorite', 'inWatchlist'));
    }
    
    /**
     * Página de categorías por género
     */
    public function genre($slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();
        
        $movies = Movie::whereHas('genres', function($query) use ($genre) {
            $query->where('genres.id', $genre->id);
        })
        ->orderBy('popularity', 'desc')
        ->take(12)
        ->get()
        ->map(function($movie) {
            return $this->formatContentForVue($movie, 'movie');
        });
        
        $tvShows = TvShow::whereHas('genres', function($query) use ($genre) {
            $query->where('genres.id', $genre->id);
        })
        ->orderBy('popularity', 'desc')
        ->take(12)
        ->get()
        ->map(function($tvShow) {
            return $this->formatContentForVue($tvShow, 'tv-show');
        });
        
        return view('catalog.genre', compact('genre', 'movies', 'tvShows'));
    }
    
    /**
     * Página de categorías por país
     */
    public function country($code)
    {
        $country = Country::where('code', $code)->firstOrFail();
        
        $movies = Movie::where('country_of_origin', $code)
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get()
            ->map(function($movie) {
                return $this->formatContentForVue($movie, 'movie');
            });
        
        $tvShows = TvShow::where('country_of_origin', $code)
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get()
            ->map(function($tvShow) {
                return $this->formatContentForVue($tvShow, 'tv-show');
            });
        
        return view('catalog.country', compact('country', 'movies', 'tvShows'));
    }
    
    /**
     * Búsqueda de contenido
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('discover');
        }
        
        $movies = Movie::where('title', 'like', "%{$query}%")
            ->orWhere('original_title', 'like', "%{$query}%")
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get()
            ->map(function($movie) {
                return $this->formatContentForVue($movie, 'movie');
            });
            
        $tvShows = TvShow::where('title', 'like', "%{$query}%")
            ->orWhere('original_title', 'like', "%{$query}%")
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get()
            ->map(function($tvShow) {
                return $this->formatContentForVue($tvShow, 'tv-show');
            });
            
        return view('catalog.search-results', compact('query', 'movies', 'tvShows'));
    }
    
    /**
     * API de búsqueda con autocompletado
     */
    public function apiSearch(Request $request)
    {
        $query = $request->get('q');
        $limit = $request->get('limit', 5);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }
        
        // Búsqueda en películas
        $movies = Movie::where('title', 'like', "%{$query}%")
            ->orWhere('original_title', 'like', "%{$query}%")
            ->orderBy('popularity', 'desc')
            ->take($limit)
            ->get()
            ->map(function($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'poster' => $movie->poster_path ? asset('storage/' . $movie->poster_path) : asset('images/placeholder-poster.jpg'),
                    'year' => $movie->release_date ? $movie->release_date->format('Y') : 'N/A',
                    'type' => 'Película',
                    'url' => route('catalog.movie-detail', $movie->slug)
                ];
            });
            
        // Búsqueda en series
        $tvShows = TvShow::where('title', 'like', "%{$query}%")
            ->orWhere('original_title', 'like', "%{$query}%")
            ->orderBy('popularity', 'desc')
            ->take($limit)
            ->get()
            ->map(function($tvShow) {
                return [
                    'id' => $tvShow->id,
                    'title' => $tvShow->title,
                    'poster' => $tvShow->poster_path ? asset('storage/' . $tvShow->poster_path) : asset('images/placeholder-poster.jpg'),
                    'year' => $tvShow->first_air_date ? $tvShow->first_air_date->format('Y') : 'N/A',
                    'type' => $tvShow->show_type === 'anime' ? 'Anime' : 'Serie',
                    'url' => route('catalog.tv-show-detail', $tvShow->slug)
                ];
            });
            
        $results = $movies->concat($tvShows)->sortByDesc('popularity')->take($limit)->values();
        
        return response()->json(['results' => $results]);
    }
    
    /**
     * Formatear contenido para componentes Vue
     */
    protected function formatContentForVue($content, $type)
    {
        $formattedContent = [
            'id' => $content->id,
            'title' => $content->title,
            'poster_path' => $content->poster_path ? asset('storage/' . $content->poster_path) : asset('images/placeholder-poster.jpg'),
            'vote_average' => $content->vote_average,
            'country' => $content->country_of_origin,
            'type' => $type === 'movie' ? 'Película' : ($content->show_type === 'anime' ? 'Anime' : 'Serie'),
            'slug' => $content->slug
        ];
        
        if ($type === 'movie') {
            $formattedContent['year'] = $content->release_date ? $content->release_date->format('Y') : 'N/A';
            $formattedContent['link'] = route('catalog.movie-detail', $content->slug);
        } else {
            $formattedContent['year'] = $content->first_air_date ? $content->first_air_date->format('Y') : 'N/A';
            $formattedContent['link'] = route('catalog.tv-show-detail', $content->slug);
        }
        
        return $formattedContent;
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}