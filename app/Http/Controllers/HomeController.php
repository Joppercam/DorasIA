<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Person;
use App\Models\Genre;
use App\Models\TitleRating;
use App\Models\Watchlist;
use App\Models\WatchHistory;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private function getSeriesWithUserRatings()
    {
        return Series::with([
            'genres',
            'people' => function($query) {
                $query->wherePivot('department', 'Acting')->take(4);
            },
            'ratings' => function($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            }, 
            'watchlistItems' => function($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            }
        ])->withCount([
            'ratings as like_count' => function ($query) {
                $query->where('rating_type', 'like');
            },
            'ratings as dislike_count' => function ($query) {
                $query->where('rating_type', 'dislike');
            },
            'ratings as love_count' => function ($query) {
                $query->where('rating_type', 'love');
            },
            'watchlistItems as watchlist_count',
            'watchlistItems as watched_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'polymorphicComments as comments_count' => function ($query) {
                $query->where('is_approved', true)->whereNull('parent_id');
            }
        ]);
    }

    public function index()
    {
        // Serie destacada para el hero (la mejor calificada)
        $featuredSeries = Series::where('vote_average', '>', 8)
            ->whereNotNull('backdrop_path')
            ->orderBy('vote_average', 'desc')
            ->first();

        // Si no hay series con backdrop, tomar cualquiera con buena calificación
        if (!$featuredSeries) {
            $featuredSeries = Series::where('vote_average', '>', 7)
                ->orderBy('vote_average', 'desc')
                ->first();
        }

        // Series más populares (25 para carrusel infinito)
        $popularSeries = $this->getSeriesWithUserRatings()
            ->orderBy('popularity', 'desc')
            ->limit(25)
            ->get();

        // Series mejor calificadas (25 para carrusel infinito)
        $topRatedSeries = $this->getSeriesWithUserRatings()
            ->where('vote_average', '>', 7)
            ->orderBy('vote_average', 'desc')
            ->limit(25)
            ->get();

        // Series recientes (25 para carrusel infinito)
        $recentSeries = $this->getSeriesWithUserRatings()
            ->whereNotNull('first_air_date')
            ->orderBy('first_air_date', 'desc')
            ->limit(25)
            ->get();

        // Series por género (25 para carrusel infinito)
        $dramasSeries = $this->getSeriesWithUserRatings()
            ->whereHas('genres', function($query) {
                $query->where('name', 'Drama');
            })->orderBy('vote_average', 'desc')->limit(25)->get();

        $comedySeries = $this->getSeriesWithUserRatings()
            ->whereHas('genres', function($query) {
                $query->where('name', 'Comedy');
            })->orderBy('vote_average', 'desc')->limit(25)->get();

        $romanceSeries = $this->getSeriesWithUserRatings()
            ->where(function($query) {
                $query->where('title', 'LIKE', '%love%')
                      ->orWhere('title', 'LIKE', '%romance%')
                      ->orWhere('title', 'LIKE', '%marry%')
                      ->orWhere('title', 'LIKE', '%wedding%')
                      ->orWhere('title', 'LIKE', '%heart%')
                      ->orWhere('overview', 'LIKE', '%romance%')
                      ->orWhere('overview', 'LIKE', '%love%')
                      ->orWhere('overview', 'LIKE', '%romantic%');
            })->orderBy('vote_average', 'desc')->limit(25)->get();

        $actionSeries = $this->getSeriesWithUserRatings()
            ->whereHas('genres', function($query) {
                $query->where('name', 'Action & Adventure');
            })->orderBy('vote_average', 'desc')->limit(25)->get();

        // Series de misterio (25 para carrusel infinito)
        $mysterySeries = $this->getSeriesWithUserRatings()
            ->whereHas('genres', function($query) {
                $query->where('name', 'Mystery');
            })->orderBy('vote_average', 'desc')->limit(25)->get();

        // Series históricos/sageuks (25 para carrusel infinito)
        $historicalSeries = $this->getSeriesWithUserRatings()
            ->where(function($query) {
                $query->where('title', 'LIKE', '%king%')
                      ->orWhere('title', 'LIKE', '%queen%')
                      ->orWhere('title', 'LIKE', '%emperor%')
                      ->orWhere('title', 'LIKE', '%prince%')
                      ->orWhere('title', 'LIKE', '%dynasty%')
                      ->orWhere('overview', 'LIKE', '%historical%')
                      ->orWhere('overview', 'LIKE', '%ancient%')
                      ->orWhere('overview', 'LIKE', '%palace%');
            })->orderBy('vote_average', 'desc')->limit(25)->get();


        // Series más vistas (recently watched by users) - 25 para carrusel infinito
        $watchedSeries = null;
        if (Auth::check()) {
            $watchedSeries = $this->getSeriesWithUserRatings()
                ->whereHas('watchlistItems', function($query) {
                    $query->where('user_id', Auth::id())
                          ->where('status', 'completed');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(25)
                ->get();
        }

        return view('home', compact(
            'featuredSeries',
            'popularSeries', 
            'topRatedSeries',
            'recentSeries',
            'dramasSeries',
            'comedySeries',
            'romanceSeries',
            'actionSeries',
            'mysterySeries',
            'historicalSeries',
            'watchedSeries'
        ));
    }

    public function series($id)
    {
        $series = Series::with([
            'genres', 
            'people', 
            'seasons.episodes', 
            'professionalReviews'
        ])->withCount([
            'ratings as like_count' => function ($query) {
                $query->where('rating_type', 'like');
            },
            'ratings as dislike_count' => function ($query) {
                $query->where('rating_type', 'dislike');
            },
            'ratings as love_count' => function ($query) {
                $query->where('rating_type', 'love');
            },
            'watchlistItems as watchlist_count',
            'watchlistItems as watched_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'polymorphicComments as comments_count' => function ($query) {
                $query->where('is_approved', true)->whereNull('parent_id');
            }
        ])->findOrFail($id);
        
        // Load comments with user info - visible to everyone
        $comments = $series->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get professional reviews
        $positiveReviews = $series->professionalReviews()->where('is_positive', true)->get();
        $negativeReviews = $series->professionalReviews()->where('is_positive', false)->get();

        return view('series.show', compact('series', 'comments', 'positiveReviews', 'negativeReviews'));
    }

    public function rateTitle(Request $request, Series $series)
    {
        $request->validate([
            'rating_type' => 'required|in:dislike,like,love'
        ]);

        $userId = Auth::id();
        $ratingType = $request->rating_type;
        $ratingValue = TitleRating::getRatingValue($ratingType);

        // Remove existing rating if any
        TitleRating::where('user_id', $userId)
                   ->where('series_id', $series->id)
                   ->delete();

        // Create new rating
        $rating = TitleRating::create([
            'user_id' => $userId,
            'series_id' => $series->id,
            'rating_type' => $ratingType,
            'rating_value' => $ratingValue
        ]);

        // Get updated counts
        $counts = $series->getRatingCounts();

        return response()->json([
            'success' => true,
            'rating_type' => $ratingType,
            'counts' => $counts,
            'message' => $this->getRatingMessage($ratingType)
        ]);
    }

    public function removeRating(Series $series)
    {
        $userId = Auth::id();
        
        TitleRating::where('user_id', $userId)
                   ->where('series_id', $series->id)
                   ->delete();

        // Get updated counts
        $counts = $series->getRatingCounts();

        return response()->json([
            'success' => true,
            'rating_type' => null,
            'counts' => $counts,
            'message' => 'Calificación eliminada'
        ]);
    }

    private function getRatingMessage($type)
    {
        return match($type) {
            'dislike' => '👎 No te gusta esta serie',
            'like' => '👍 Te gusta esta serie',
            'love' => '❤️ ¡Te encanta esta serie!',
            default => 'Calificación guardada'
        };
    }

    public function toggleWatchlist(Request $request, Series $series)
    {
        $request->validate([
            'status' => 'sometimes|in:want_to_watch,watching,completed,dropped,on_hold'
        ]);

        $userId = Auth::id();
        $status = $request->get('status', 'want_to_watch');

        // Check if already in watchlist
        $existingItem = Watchlist::where('user_id', $userId)
                                ->where('series_id', $series->id)
                                ->first();

        if ($existingItem) {
            // Remove from watchlist
            $existingItem->delete();
            $inWatchlist = false;
            $message = '📺 Eliminado de tu lista';
        } else {
            // Add to watchlist
            Watchlist::create([
                'user_id' => $userId,
                'series_id' => $series->id,
                'status' => $status,
                'priority' => 0
            ]);
            $inWatchlist = true;
            $message = $this->getWatchlistMessage($status);
        }

        return response()->json([
            'success' => true,
            'in_watchlist' => $inWatchlist,
            'status' => $status,
            'message' => $message
        ]);
    }

    public function updateWatchlistStatus(Request $request, Series $series)
    {
        $request->validate([
            'status' => 'required|in:want_to_watch,watching,completed,dropped,on_hold'
        ]);

        $userId = Auth::id();
        $status = $request->status;

        $watchlistItem = Watchlist::where('user_id', $userId)
                                 ->where('series_id', $series->id)
                                 ->first();

        if (!$watchlistItem) {
            return response()->json(['success' => false, 'message' => 'Serie no encontrada en tu lista'], 404);
        }

        $watchlistItem->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $this->getWatchlistMessage($status)
        ]);
    }

    private function getWatchlistMessage($status)
    {
        return match($status) {
            'want_to_watch' => '🎯 Agregado a "Pendientes"',
            'watching' => '👀 Marcado como "Viendo"',
            'completed' => '✅ Marcado como "Completada"',
            'dropped' => '❌ Marcado como "Abandonada"',
            'on_hold' => '⏸️ Marcado como "En Pausa"',
            default => '📺 Agregado a tu lista'
        };
    }

    public function markAsWatched(Series $series)
    {
        $userId = Auth::id();

        // Add to watchlist as completed if not already there
        $watchlistItem = Watchlist::where('user_id', $userId)
                                 ->where('series_id', $series->id)
                                 ->first();

        if ($watchlistItem) {
            $watchlistItem->update(['status' => 'completed']);
        } else {
            Watchlist::create([
                'user_id' => $userId,
                'series_id' => $series->id,
                'status' => 'completed',
                'priority' => 0
            ]);
        }

        // Add to watch history if not already there
        $watchHistory = WatchHistory::where('user_id', $userId)
                                   ->where('series_id', $series->id)
                                   ->first();

        if (!$watchHistory) {
            WatchHistory::create([
                'user_id' => $userId,
                'series_id' => $series->id,
                'episodes_watched' => $series->number_of_episodes ?: 1,
                'total_episodes' => $series->number_of_episodes ?: 1,
                'progress_percentage' => 100.00,
                'status' => 'completed',
                'last_watched_at' => now()
            ]);
        } else {
            $watchHistory->update([
                'status' => 'completed',
                'progress_percentage' => 100.00,
                'episodes_watched' => $series->number_of_episodes ?: $watchHistory->episodes_watched,
                'last_watched_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '🎬 ¡Marcada como vista!'
        ]);
    }

    public function storeComment(Request $request, Series $series)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'is_spoiler' => 'boolean'
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'series_id' => $series->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_spoiler' => $request->boolean('is_spoiler'),
            'is_approved' => true // Auto-approve for now
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => '¡Comentario agregado!',
            'comment' => $comment
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([
                'series' => [],
                'actors' => []
            ]);
        }

        // Search series - prioritize Spanish titles
        $series = Series::where(function($q) use ($query) {
            $q->where('spanish_title', 'LIKE', "%{$query}%")
              ->orWhere('title', 'LIKE', "%{$query}%")
              ->orWhere('display_title', 'LIKE', "%{$query}%")
              ->orWhere('original_title', 'LIKE', "%{$query}%")
              ->orWhere('spanish_overview', 'LIKE', "%{$query}%")
              ->orWhere('overview', 'LIKE', "%{$query}%")
              ->orWhere('display_overview', 'LIKE', "%{$query}%");
        })
        ->select(['id', 'title', 'display_title', 'original_title', 'spanish_title', 'poster_path', 'first_air_date', 'vote_average'])
        ->orderBy('popularity', 'desc')
        ->limit(5)
        ->get();

        // Search actors
        $actors = Person::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('display_name', 'LIKE', "%{$query}%");
        })
        ->select(['id', 'name', 'display_name', 'profile_path', 'birthday', 'popularity'])
        ->orderBy('popularity', 'desc')
        ->limit(5)
        ->get();

        return response()->json([
            'series' => $series,
            'actors' => $actors
        ]);
    }
}