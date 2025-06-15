<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TitleRating;
use App\Models\Watchlist;
use App\Models\WatchHistory;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\EpisodeProgress;
use App\Models\UpcomingSeries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        // Cache de series candidatas para el hero (30 minutos) - Optimizado con bonus de recencia
        $featuredCandidates = Cache::remember('hero.featured_candidates', 1800, function () {
            $candidates = Series::where('vote_average', '>', 7.5)
                ->where('vote_count', '>=', 100)
                ->whereNotNull('backdrop_path')
                ->whereNotNull('spanish_overview')
                ->selectRaw('*, vote_average + (CASE WHEN first_air_date >= date("now", "-2 years") THEN 0.5 ELSE 0 END) as hero_score')
                ->orderBy('hero_score', 'desc')
                ->limit(20)
                ->get();

            // Si no hay suficientes candidatos, ampliar criterios
            if ($candidates->count() < 10) {
                $candidates = Series::where('vote_average', '>', 7)
                    ->where('vote_count', '>=', 50)
                    ->whereNotNull('backdrop_path')
                    ->selectRaw('*, vote_average + (CASE WHEN first_air_date >= date("now", "-2 years") THEN 0.3 ELSE 0 END) as hero_score')
                    ->orderBy('hero_score', 'desc')
                    ->limit(20)
                    ->get();
            }

            return $candidates;
        });

        // Seleccionar una serie aleatoria del grupo de candidatas
        $featuredSeries = $featuredCandidates->count() > 0 
            ? $featuredCandidates->random() 
            : Series::orderBy('vote_average', 'desc')->first();
        
        // Pasar también las series candidatas para rotación en el frontend
        $heroSeriesList = $featuredCandidates->take(10);

        // Series más populares con caché (1 hora) - Algoritmo mejorado
        $popularSeries = Cache::remember('series.popular', 3600, function () {
            return $this->getSeriesWithUserRatings()
                ->selectRaw('*, (popularity * 0.6 + vote_average * 0.2 + (CASE WHEN first_air_date >= date("now", "-6 months") THEN 2 ELSE 0 END)) as trending_score')
                ->where('vote_average', '>', 6)
                ->orderBy('trending_score', 'desc')
                ->limit(25)
                ->get();
        });

        // Series mejor calificadas con caché (1 hora) - Agregado mínimo de votos
        $topRatedSeries = Cache::remember('series.top_rated', 3600, function () {
            return $this->getSeriesWithUserRatings()
                ->where('vote_average', '>', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('vote_average', 'desc')
                ->limit(25)
                ->get();
        });

        // Series recientes con caché (30 minutos) - Agregado filtro de calidad mínima
        $recentSeries = Cache::remember('series.recent', 1800, function () {
            return $this->getSeriesWithUserRatings()
                ->whereNotNull('first_air_date')
                ->where('vote_average', '>', 6.5)
                ->orderBy('first_air_date', 'desc')
                ->limit(25)
                ->get();
        });

        // Series por género con caché (2 horas - cambian menos frecuentemente)
        $dramasSeries = Cache::remember('series.genre.drama', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Drama');
                })->orderBy('vote_average', 'desc')->limit(25)->get();
        });

        $comedySeries = Cache::remember('series.genre.comedy', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Comedy');
                })->orderBy('vote_average', 'desc')->limit(25)->get();
        });

        $romanceSeries = Cache::remember('series.genre.romance', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->whereIn('name', ['Romance', 'Romantic Comedy', 'Family']);
                })->where('vote_average', '>', 6)
                ->orderBy('vote_average', 'desc')->limit(25)->get();
        });

        $actionSeries = Cache::remember('series.genre.action', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Action & Adventure');
                })->orderBy('vote_average', 'desc')->limit(25)->get();
        });

        $mysterySeries = Cache::remember('series.genre.mystery', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Mystery');
                })->orderBy('vote_average', 'desc')->limit(25)->get();
        });

        $historicalSeries = Cache::remember('series.genre.historical', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->whereIn('name', ['War & Politics', 'History', 'Period Drama']);
                })
                ->orWhere(function($query) {
                    $query->where('spanish_overview', 'LIKE', '%palacio%')
                          ->orWhere('spanish_overview', 'LIKE', '%rey%')
                          ->orWhere('spanish_overview', 'LIKE', '%reina%')
                          ->orWhere('spanish_overview', 'LIKE', '%emperador%')
                          ->orWhere('spanish_overview', 'LIKE', '%dinastía%')
                          ->orWhere('spanish_overview', 'LIKE', '%históric%')
                          ->orWhere('spanish_overview', 'LIKE', '%antiguo%')
                          ->orWhere('spanish_overview', 'LIKE', '%tradicional%');
                })
                ->where('vote_average', '>', 6)
                ->orderBy('vote_average', 'desc')->limit(25)->get();
        });


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

        // Próximos estrenos con caché (30 minutos)
        $upcomingSeries = Cache::remember('upcoming.widget', 1800, function () {
            return UpcomingSeries::upcoming()
                ->orderBy('release_date')
                ->limit(6)
                ->get();
        });

        // Películas populares con caché (1 hora)
        $popularMovies = Cache::remember('movies.popular', 3600, function () {
            return Movie::with('genres')
                ->where('vote_average', '>', 6)
                ->orderBy('popularity', 'desc')
                ->limit(25)
                ->get();
        });

        // Películas mejor calificadas con caché (1 hora)
        $topRatedMovies = Cache::remember('movies.top_rated', 3600, function () {
            return Movie::with('genres')
                ->where('vote_average', '>', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('vote_average', 'desc')
                ->limit(25)
                ->get();
        });

        // Películas recientes con caché (30 minutos)
        $recentMovies = Cache::remember('movies.recent', 1800, function () {
            return Movie::with('genres')
                ->whereNotNull('release_date')
                ->where('vote_average', '>', 6.5)
                ->orderBy('release_date', 'desc')
                ->limit(25)
                ->get();
        });

        return view('home', compact(
            'featuredSeries',
            'heroSeriesList',
            'popularSeries', 
            'topRatedSeries',
            'recentSeries',
            'dramasSeries',
            'comedySeries',
            'romanceSeries',
            'actionSeries',
            'mysterySeries',
            'historicalSeries',
            'watchedSeries',
            'upcomingSeries',
            'popularMovies',
            'topRatedMovies',
            'recentMovies'
        ));
    }

    public function series($id)
    {
        $series = Series::with([
            'genres', 
            'people' => function($query) {
                $query->orderBy('series_person.order')
                      ->orderBy('series_person.department', 'desc'); // Acting first
            }, 
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

        // Get following status for actors if user is authenticated
        $actorFollowingStatus = [];
        if (auth()->check()) {
            $actorIds = $series->people->pluck('id')->toArray();
            $followedActorIds = auth()->user()->actorFollows()
                ->whereIn('person_id', $actorIds)
                ->pluck('person_id')
                ->toArray();
            
            foreach ($actorIds as $actorId) {
                $actorFollowingStatus[$actorId] = in_array($actorId, $followedActorIds);
            }
        }

        return view('series.show', compact('series', 'comments', 'positiveReviews', 'negativeReviews', 'actorFollowingStatus'));
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

        // Sanitize search query
        $searchTerm = '%' . $query . '%';

        // Search series - prioritize Spanish titles
        $series = Series::where(function($q) use ($searchTerm) {
            $q->where('spanish_title', 'LIKE', $searchTerm)
              ->orWhere('title', 'LIKE', $searchTerm)
              ->orWhere('display_title', 'LIKE', $searchTerm)
              ->orWhere('original_title', 'LIKE', $searchTerm)
              ->orWhere('spanish_overview', 'LIKE', $searchTerm)
              ->orWhere('overview', 'LIKE', $searchTerm)
              ->orWhere('display_overview', 'LIKE', $searchTerm);
        })
        ->select(['id', 'title', 'display_title', 'original_title', 'spanish_title', 'poster_path', 'first_air_date', 'vote_average'])
        ->orderBy('popularity', 'desc')
        ->limit(5)
        ->get();

        // Search actors
        $actors = Person::where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', $searchTerm)
              ->orWhere('display_name', 'LIKE', $searchTerm);
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

    public function browse(Request $request)
    {
        $query = Series::with(['genres'])
            ->withCount(['titleRatings as likes_count' => function($q) {
                $q->where('rating', 'like');
            }])
            ->withCount(['titleRatings as loves_count' => function($q) {
                $q->where('rating', 'love');
            }])
            ->withCount(['titleRatings as dislikes_count' => function($q) {
                $q->where('rating', 'dislike');
            }])
            ->withCount('comments');

        // Filtros
        if ($request->filled('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('name', $request->genre);
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('first_air_date', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popularity':
                    $query->orderBy('popularity', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('vote_average', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('first_air_date', 'desc');
                    break;
                case 'alphabetical':
                    $query->orderBy('display_title');
                    break;
                default:
                    $query->orderBy('popularity', 'desc');
            }
        } else {
            $query->orderBy('popularity', 'desc');
        }

        $series = $query->paginate(24);

        // Datos para filtros con caché (24 horas - cambian muy poco)
        $genres = Cache::remember('genres.all', 86400, function () {
            return \App\Models\Genre::orderBy('spanish_name')->get();
        });

        $years = Cache::remember('series.years', 86400, function () {
            return Series::selectRaw('YEAR(first_air_date) as year')
                ->whereNotNull('first_air_date')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        });

        return view('browse', compact('series', 'genres', 'years'));
    }

    // Episode tracking methods
    public function markEpisodeAsWatched(Request $request, Episode $episode)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $progress = EpisodeProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'episode_id' => $episode->id,
            ],
            [
                'series_id' => $episode->series_id,
                'status' => 'completed',
                'progress_percentage' => 100,
                'total_minutes' => $episode->runtime,
                'progress_minutes' => $episode->runtime,
                'completed_at' => now()
            ]
        );

        // Clear cache related to user progress
        Cache::forget("user.series.progress." . Auth::id() . "." . $episode->series_id);

        return response()->json([
            'success' => true,
            'message' => 'Episodio marcado como visto',
            'progress' => $progress
        ]);
    }

    public function markEpisodeAsUnwatched(Request $request, Episode $episode)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $progress = EpisodeProgress::where([
            'user_id' => Auth::id(),
            'episode_id' => $episode->id,
        ])->first();

        if ($progress) {
            $progress->delete();
            Cache::forget("user.series.progress." . Auth::id() . "." . $episode->series_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Episodio marcado como no visto'
        ]);
    }

    public function updateEpisodeProgress(Request $request, Episode $episode)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'progress_minutes' => 'required|integer|min:0'
        ]);

        $progress = EpisodeProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'episode_id' => $episode->id,
            ],
            [
                'series_id' => $episode->series_id,
                'total_minutes' => $episode->runtime,
                'started_at' => now()
            ]
        );

        $progress->updateProgress($request->progress_minutes);

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh()
        ]);
    }

    public function getSeriesProgress(Request $request, $seriesId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $series = Series::findOrFail($seriesId);
        $progressData = Cache::remember(
            "user.series.progress." . Auth::id() . "." . $seriesId,
            900, // 15 minutos
            function () use ($series) {
                return $series->getUserSeriesProgress(Auth::id());
            }
        );

        return response()->json([
            'success' => true,
            'progress' => $progressData
        ]);
    }

    public function getEpisodesList(Request $request, $seriesId)
    {
        $series = Series::with(['episodes' => function($query) {
            $query->orderBy('season_number')->orderBy('episode_number');
        }])->findOrFail($seriesId);

        $episodes = $series->episodes->map(function ($episode) {
            $userProgress = null;
            if (Auth::check()) {
                $userProgress = $episode->getUserProgress(Auth::id());
            }

            return [
                'id' => $episode->id,
                'episode_number' => $episode->episode_number,
                'season_number' => $episode->season_number,
                'name' => $episode->name,
                'overview' => $episode->overview,
                'air_date' => $episode->air_date?->format('Y-m-d'),
                'runtime' => $episode->runtime,
                'formatted_runtime' => $episode->getFormattedRuntime(),
                'still_path' => $episode->still_path,
                'user_progress' => $userProgress ? [
                    'status' => $userProgress->status,
                    'progress_percentage' => $userProgress->progress_percentage,
                    'completed_at' => $userProgress->completed_at?->format('Y-m-d H:i:s')
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'episodes' => $episodes
        ]);
    }
}