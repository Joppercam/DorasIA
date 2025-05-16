<?php

namespace App\Http\Controllers;

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
    }
}