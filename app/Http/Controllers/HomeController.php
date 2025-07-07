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
use App\Models\ActorContent;
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
        // Generar semilla basada en el día para rotación diaria
        $dailySeed = date('Y-m-d');
        $hourlySeed = date('Y-m-d-H'); // Para rotación cada hora
        
        // Cache de series candidatas para el hero (30 minutos) - Optimizado con bonus de recencia
        $featuredCandidates = Cache::remember('hero.featured_candidates', 1800, function () {
            $candidates = Series::where('vote_average', '>', 7.5)
                ->where('vote_count', '>=', 100)
                ->whereNotNull('backdrop_path')
                ->whereNotNull('spanish_overview')
                ->selectRaw('*, vote_average + (CASE WHEN first_air_date >= date("now", "-2 years") THEN 0.5 ELSE 0 END) as hero_score')
                ->orderBy('hero_score', 'desc')
                ->limit(50) // Aumentado para más variedad
                ->get();

            // Si no hay suficientes candidatos, ampliar criterios
            if ($candidates->count() < 20) {
                $candidates = Series::where('vote_average', '>', 7)
                    ->where('vote_count', '>=', 50)
                    ->whereNotNull('backdrop_path')
                    ->selectRaw('*, vote_average + (CASE WHEN first_air_date >= date("now", "-2 years") THEN 0.3 ELSE 0 END) as hero_score')
                    ->orderBy('hero_score', 'desc')
                    ->limit(50)
                    ->get();
            }

            return $candidates;
        });

        // Rotación del hero cada 2 horas usando una semilla temporal
        $heroRotationSeed = floor(time() / 7200); // Cambia cada 2 horas
        $featuredSeries = $this->getRotatedSelection($featuredCandidates, 1, $heroRotationSeed)->first();
        
        if (!$featuredSeries) {
            $featuredSeries = Series::orderBy('vote_average', 'desc')->first();
        }
        
        // Lista de candidatos para rotación en el frontend (10 diferentes cada vez)
        $heroSeriesList = $this->getRotatedSelection($featuredCandidates, 10, $heroRotationSeed);

        // Series más populares con rotación diaria
        $popularSeriesPool = Cache::remember('series.popular.pool', 3600, function () {
            return $this->getSeriesWithUserRatings()
                ->selectRaw('*, (popularity * 0.6 + vote_average * 0.2 + (CASE WHEN first_air_date >= date("now", "-6 months") THEN 2 ELSE 0 END)) as trending_score')
                ->where('vote_average', '>', 6)
                ->orderBy('trending_score', 'desc')
                ->limit(50) // Pool más grande
                ->get();
        });
        $popularSeries = $this->getRotatedSelection($popularSeriesPool, 25, $dailySeed);

        // Series mejor calificadas con rotación diaria
        $topRatedSeriesPool = Cache::remember('series.top_rated.pool', 3600, function () {
            return $this->getSeriesWithUserRatings()
                ->where('vote_average', '>', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('vote_average', 'desc')
                ->limit(50) // Pool más grande
                ->get();
        });
        $topRatedSeries = $this->getRotatedSelection($topRatedSeriesPool, 25, $dailySeed);

        // Series recientes con rotación por hora
        $recentSeriesPool = Cache::remember('series.recent.pool', 1800, function () {
            return $this->getSeriesWithUserRatings()
                ->whereNotNull('first_air_date')
                ->where('vote_average', '>', 6.5)
                ->orderBy('first_air_date', 'desc')
                ->limit(50) // Pool más grande
                ->get();
        });
        $recentSeries = $this->getRotatedSelection($recentSeriesPool, 25, $hourlySeed);

        // Series por género con rotación diaria
        $dramasSeriesPool = Cache::remember('series.genre.drama.pool', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Drama');
                })->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $dramasSeries = $this->getRotatedSelection($dramasSeriesPool, 25, $dailySeed);

        $comedySeriesPool = Cache::remember('series.genre.comedy.pool', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Comedy');
                })->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $comedySeries = $this->getRotatedSelection($comedySeriesPool, 25, $dailySeed);

        $romanceSeriesPool = Cache::remember('series.genre.romance.pool', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->whereIn('name', ['Romance', 'Romantic Comedy', 'Family']);
                })->where('vote_average', '>', 6)
                ->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $romanceSeries = $this->getRotatedSelection($romanceSeriesPool, 25, $dailySeed);

        $actionSeriesPool = Cache::remember('series.genre.action.pool', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Action & Adventure');
                })->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $actionSeries = $this->getRotatedSelection($actionSeriesPool, 25, $dailySeed);

        $mysterySeriesPool = Cache::remember('series.genre.mystery.pool', 7200, function () {
            return $this->getSeriesWithUserRatings()
                ->whereHas('genres', function($query) {
                    $query->where('name', 'Mystery');
                })->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $mysterySeries = $this->getRotatedSelection($mysterySeriesPool, 25, $dailySeed);

        $historicalSeriesPool = Cache::remember('series.genre.historical.pool', 7200, function () {
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
                ->orderBy('vote_average', 'desc')->limit(40)->get();
        });
        $historicalSeries = $this->getRotatedSelection($historicalSeriesPool, 25, $dailySeed);


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

        // Películas populares con rotación diaria
        $popularMoviesPool = Cache::remember('movies.popular.pool', 3600, function () {
            return Movie::with('genres')
                ->where('vote_average', '>', 6)
                ->orderBy('popularity', 'desc')
                ->limit(40)
                ->get();
        });
        $popularMovies = $this->getRotatedSelection($popularMoviesPool, 25, $dailySeed);

        // Películas mejor calificadas con rotación diaria
        $topRatedMoviesPool = Cache::remember('movies.top_rated.pool', 3600, function () {
            return Movie::with('genres')
                ->where('vote_average', '>', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('vote_average', 'desc')
                ->limit(40)
                ->get();
        });
        $topRatedMovies = $this->getRotatedSelection($topRatedMoviesPool, 25, $dailySeed);

        // Películas recientes con rotación por hora
        $recentMoviesPool = Cache::remember('movies.recent.pool', 1800, function () {
            return Movie::with('genres')
                ->whereNotNull('release_date')
                ->where('vote_average', '>', 6.5)
                ->orderBy('release_date', 'desc')
                ->limit(40)
                ->get();
        });
        $recentMovies = $this->getRotatedSelection($recentMoviesPool, 25, $hourlySeed);

        // ===== CONTENIDO EXCLUSIVO DE ACTORES =====
        
        // Contenido destacado de actores
        $featuredActorContent = Cache::remember('actor.content.featured', 1800, function () {
            return ActorContent::featured()
                ->published()
                ->with(['actor'])
                ->orderBy('published_at', 'desc')
                ->limit(6)
                ->get();
        });

        // Contenido reciente de actores (últimos 7 días)
        $recentActorContent = Cache::remember('actor.content.recent', 900, function () {
            return ActorContent::published()
                ->recent(7)
                ->with(['actor'])
                ->orderBy('published_at', 'desc')
                ->limit(8)
                ->get();
        });

        // Contenido más popular de actores (por vistas)
        $popularActorContent = Cache::remember('actor.content.popular', 3600, function () {
            return ActorContent::published()
                ->with(['actor'])
                ->where('view_count', '>', 100)
                ->orderBy('view_count', 'desc')
                ->limit(6)
                ->get();
        });

        // Actores con más contenido exclusivo
        $actorsWithContent = Cache::remember('actors.with.content', 7200, function () {
            return Person::whereHas('exclusiveContent', function($query) {
                $query->published();
            })
            ->withCount(['exclusiveContent as content_count' => function($query) {
                $query->published();
            }])
            ->with(['exclusiveContent' => function($query) {
                $query->published()->latest()->limit(3);
            }])
            ->orderBy('content_count', 'desc')
            ->limit(8)
            ->get();
        });

        // Contenido personalizado para usuarios autenticados
        $personalizedActorContent = collect();
        if (Auth::check()) {
            $followedActorIds = Auth::user()->followedActors()->pluck('person_id');
            
            if ($followedActorIds->isNotEmpty()) {
                $personalizedActorContent = ActorContent::whereIn('person_id', $followedActorIds)
                    ->published()
                    ->with(['actor'])
                    ->orderBy('published_at', 'desc')
                    ->limit(8)
                    ->get();
            }
        }

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
            'recentMovies',
            'featuredActorContent',
            'recentActorContent',
            'popularActorContent',
            'actorsWithContent',
            'personalizedActorContent'
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
            'professionalReviews',
            'soundtracks' => function($query) {
                $query->orderBy('is_main_theme', 'desc')
                      ->orderBy('track_number', 'asc');
            },
            'streamingSources'
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
        ])->find($id);
        
        if (!$series) {
            abort(404, 'Serie no encontrada');
        }
        
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

        $alreadyWatched = $watchHistory && $watchHistory->status === 'completed';

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
            'already_watched' => $alreadyWatched,
            'message' => $alreadyWatched ? '✅ Ya la tenías marcada como vista' : '🎬 ¡Marcada como vista!'
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

        // Search series - prioritize Spanish content
        $series = Series::where(function($q) use ($searchTerm) {
            $q->where('title_es', 'LIKE', $searchTerm)
              ->orWhere('spanish_title', 'LIKE', $searchTerm)
              ->orWhere('overview_es', 'LIKE', $searchTerm)
              ->orWhere('spanish_overview', 'LIKE', $searchTerm)
              ->orWhere('synopsis_es', 'LIKE', $searchTerm)
              ->orWhere('tagline_es', 'LIKE', $searchTerm)
              ->orWhere('title', 'LIKE', $searchTerm)
              ->orWhere('original_title', 'LIKE', $searchTerm)
              ->orWhere('overview', 'LIKE', $searchTerm);
        })
        ->select(['id', 'title', 'title_es', 'original_title', 'spanish_title', 'overview_es', 'spanish_overview', 'poster_path', 'first_air_date', 'vote_average'])
        ->orderByRaw('CASE 
            WHEN title_es LIKE ? THEN 1
            WHEN spanish_title LIKE ? THEN 2
            WHEN overview_es LIKE ? THEN 3
            WHEN spanish_overview LIKE ? THEN 4
            ELSE 5
        END, popularity DESC', [$searchTerm, $searchTerm, $searchTerm, $searchTerm])
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

    /**
     * Método para rotar selecciones basado en una semilla temporal
     * Asegura que el mismo seed siempre retorne la misma selección
     */
    private function getRotatedSelection($collection, $count, $seed)
    {
        if ($collection->isEmpty()) {
            return collect();
        }

        // Convertir la semilla en un número entero si es string
        if (is_string($seed)) {
            $seed = crc32($seed);
        }

        // Usar la semilla para generar un offset consistente
        $offset = abs($seed) % max(1, $collection->count() - $count + 1);
        
        // Si no hay suficientes elementos, duplicar la colección
        if ($collection->count() < $count) {
            $repeated = collect();
            while ($repeated->count() < $count) {
                $repeated = $repeated->concat($collection);
            }
            $collection = $repeated;
        }

        // Crear una "rotación" circular basada en el offset
        $rotated = $collection->skip($offset)->take($count);
        
        // Si no obtuvimos suficientes elementos, completar desde el inicio
        if ($rotated->count() < $count) {
            $needed = $count - $rotated->count();
            $rotated = $rotated->concat($collection->take($needed));
        }

        return $rotated;
    }

    /**
     * Endpoint para obtener carruseles rotados dinámicamente via AJAX
     */
    public function getRotatedCarousel(Request $request)
    {
        $type = $request->get('type');
        $seed = $request->get('seed', time());
        $count = $request->get('count', 25);

        switch ($type) {
            case 'popular':
                $pool = Cache::remember('series.popular.pool', 3600, function () {
                    return $this->getSeriesWithUserRatings()
                        ->selectRaw('*, (popularity * 0.6 + vote_average * 0.2 + (CASE WHEN first_air_date >= date("now", "-6 months") THEN 2 ELSE 0 END)) as trending_score')
                        ->where('vote_average', '>', 6)
                        ->orderBy('trending_score', 'desc')
                        ->limit(50)
                        ->get();
                });
                break;

            case 'top_rated':
                $pool = Cache::remember('series.top_rated.pool', 3600, function () {
                    return $this->getSeriesWithUserRatings()
                        ->where('vote_average', '>', 7)
                        ->where('vote_count', '>=', 50)
                        ->orderBy('vote_average', 'desc')
                        ->limit(50)
                        ->get();
                });
                break;

            case 'recent':
                $pool = Cache::remember('series.recent.pool', 1800, function () {
                    return $this->getSeriesWithUserRatings()
                        ->whereNotNull('first_air_date')
                        ->where('vote_average', '>', 6.5)
                        ->orderBy('first_air_date', 'desc')
                        ->limit(50)
                        ->get();
                });
                break;

            case 'romance':
                $pool = Cache::remember('series.genre.romance.pool', 7200, function () {
                    return $this->getSeriesWithUserRatings()
                        ->whereHas('genres', function($query) {
                            $query->whereIn('name', ['Romance', 'Romantic Comedy', 'Family']);
                        })->where('vote_average', '>', 6)
                        ->orderBy('vote_average', 'desc')->limit(40)->get();
                });
                break;

            case 'drama':
                $pool = Cache::remember('series.genre.drama.pool', 7200, function () {
                    return $this->getSeriesWithUserRatings()
                        ->whereHas('genres', function($query) {
                            $query->where('name', 'Drama');
                        })->orderBy('vote_average', 'desc')->limit(40)->get();
                });
                break;

            default:
                return response()->json(['error' => 'Invalid carousel type'], 400);
        }

        $rotatedSelection = $this->getRotatedSelection($pool, $count, $seed);

        return response()->json([
            'success' => true,
            'series' => $rotatedSelection,
            'type' => $type,
            'count' => $rotatedSelection->count()
        ]);
    }
}