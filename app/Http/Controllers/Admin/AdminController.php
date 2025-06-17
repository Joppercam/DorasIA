<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Movie;
use App\Models\User;
use App\Models\Comment;
use App\Models\TitleRating;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'total_series' => Series::count(),
            'total_movies' => Movie::count(),
            'total_users' => User::count(),
            'total_comments' => Comment::count(),
            'total_ratings' => TitleRating::count(),
            'total_watchlist_items' => Watchlist::count(),
        ];

        // Series más populares (últimos 30 días)
        $popularSeries = Series::withCount(['ratings', 'watchlistItems', 'comments'])
            ->orderByDesc('ratings_count')
            ->take(5)
            ->get();

        // Películas más populares
        $popularMovies = Movie::withCount(['ratings', 'watchlistItems'])
            ->orderByDesc('ratings_count')
            ->take(5)
            ->get();

        // Usuarios más activos
        $activeUsers = User::withCount(['titleRatings', 'watchlists', 'comments'])
            ->orderByDesc('title_ratings_count')
            ->take(10)
            ->get()
            ->filter(function($user) {
                return $user->title_ratings_count > 0;
            })
            ->take(5);

        // Actividad reciente (últimos comentarios)
        $recentComments = Comment::with(['user', 'series', 'commentable'])
            ->whereNotNull('user_id')
            ->latest()
            ->take(10)
            ->get();

        // Doramas por país
        $dramasByCountry = Series::select('country_code', 'country_name', DB::raw('count(*) as total'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'popularSeries',
            'popularMovies',
            'activeUsers',
            'recentComments',
            'dramasByCountry'
        ));
    }

    /**
     * Gestión de series
     */
    public function series(Request $request)
    {
        $query = Series::with(['genres']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_es', 'like', "%{$search}%")
                  ->orWhere('original_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('drama_type')) {
            $query->where('drama_type', $request->get('drama_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $series = $query->orderByDesc('vote_average')
                       ->paginate(20)
                       ->withQueryString();

        $dramaTypes = Series::select('drama_type')->distinct()->pluck('drama_type');
        $statuses = Series::select('status')->distinct()->pluck('status');

        return view('admin.series', compact('series', 'dramaTypes', 'statuses'));
    }

    /**
     * Gestión de películas
     */
    public function movies(Request $request)
    {
        $query = Movie::with(['genres']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_es', 'like', "%{$search}%")
                  ->orWhere('spanish_title', 'like', "%{$search}%");
            });
        }

        $movies = $query->orderByDesc('vote_average')
                       ->paginate(20)
                       ->withQueryString();

        return view('admin.movies', compact('movies'));
    }

    /**
     * Gestión de usuarios
     */
    public function users(Request $request)
    {
        $query = User::withCount(['titleRatings', 'watchlists', 'comments']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_admin')) {
            $query->where('is_admin', $request->get('is_admin') === '1');
        }

        $users = $query->orderByDesc('created_at')
                      ->paginate(20)
                      ->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Hacer/quitar admin a un usuario
     */
    public function toggleAdmin(User $user)
    {
        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        $message = $user->is_admin 
            ? "Usuario {$user->name} ahora es administrador"
            : "Usuario {$user->name} ya no es administrador";

        return back()->with('success', $message);
    }

    /**
     * Comentarios pendientes de moderación
     */
    public function comments(Request $request)
    {
        $query = Comment::with(['user', 'series', 'commentable'])
                       ->whereNotNull('user_id');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('content', 'like', "%{$search}%");
        }

        $comments = $query->orderByDesc('created_at')
                         ->paginate(20)
                         ->withQueryString();

        return view('admin.comments', compact('comments'));
    }

    /**
     * Eliminar comentario
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comentario eliminado correctamente');
    }

    /**
     * Mostrar formulario de edición de serie
     */
    public function editSeries(Series $series)
    {
        return view('admin.series-edit', compact('series'));
    }

    /**
     * Actualizar serie
     */
    public function updateSeries(Request $request, Series $series)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_es' => 'nullable|string|max:255',
            'overview' => 'nullable|string',
            'overview_es' => 'nullable|string',
            'status' => 'required|string',
            'drama_type' => 'required|string',
            'country_code' => 'required|string|max:5',
            'country_name' => 'required|string|max:50',
            'number_of_episodes' => 'nullable|integer|min:1',
            'number_of_seasons' => 'nullable|integer|min:1',
            'vote_average' => 'nullable|numeric|min:0|max:10',
        ]);

        $series->update($request->only([
            'title', 'title_es', 'overview', 'overview_es', 'status', 
            'drama_type', 'country_code', 'country_name', 
            'number_of_episodes', 'number_of_seasons', 'vote_average'
        ]));

        return redirect()->route('admin.series')->with('success', 'Serie actualizada correctamente');
    }

    /**
     * Eliminar serie
     */
    public function deleteSeries(Series $series)
    {
        // Eliminar relaciones dependientes
        $series->ratings()->delete();
        $series->watchlistItems()->delete();
        $series->comments()->delete();
        $series->episodeProgress()->delete();
        
        // Eliminar la serie
        $series->delete();
        
        return back()->with('success', 'Serie eliminada correctamente');
    }

    /**
     * Mostrar formulario de edición de película
     */
    public function editMovie(Movie $movie)
    {
        return view('admin.movies-edit', compact('movie'));
    }

    /**
     * Actualizar película
     */
    public function updateMovie(Request $request, Movie $movie)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_es' => 'nullable|string|max:255',
            'spanish_title' => 'nullable|string|max:255',
            'overview' => 'nullable|string',
            'overview_es' => 'nullable|string',
            'spanish_overview' => 'nullable|string',
            'runtime' => 'nullable|integer|min:1',
            'vote_average' => 'nullable|numeric|min:0|max:10',
        ]);

        $movie->update($request->only([
            'title', 'title_es', 'spanish_title', 'overview', 
            'overview_es', 'spanish_overview', 'runtime', 'vote_average'
        ]));

        return redirect()->route('admin.movies')->with('success', 'Película actualizada correctamente');
    }

    /**
     * Eliminar película
     */
    public function deleteMovie(Movie $movie)
    {
        // Eliminar relaciones dependientes
        $movie->ratings()->delete();
        $movie->watchlistItems()->delete();
        
        // Eliminar la película
        $movie->delete();
        
        return back()->with('success', 'Película eliminada correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser(User $user)
    {
        // No permitir eliminar el usuario actual
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta');
        }

        // Eliminar relaciones dependientes
        $user->titleRatings()->delete();
        $user->watchlists()->delete();
        $user->comments()->delete();
        $user->episodeProgress()->delete();
        $user->actorFollows()->delete();
        
        // Eliminar el usuario
        $user->delete();
        
        return back()->with('success', 'Usuario eliminado correctamente');
    }
}