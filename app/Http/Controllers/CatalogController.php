<?php

namespace App\Http\Controllers;

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
    }
}