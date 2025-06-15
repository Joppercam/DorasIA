<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Movie::with('genres');
        
        // Filtros
        if ($request->has('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }
        
        if ($request->has('year')) {
            $query->whereRaw('strftime("%Y", release_date) = ?', [$request->year]);
        }
        
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('display_title', 'like', "%{$searchTerm}%");
            });
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'popularity');
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'release_date':
                $query->orderBy('release_date', 'desc');
                break;
            case 'rating':
                $query->orderBy('vote_average', 'desc');
                break;
            default:
                $query->orderBy('popularity', 'desc');
        }
        
        $movies = $query->paginate(20);
        $genres = Genre::orderBy('display_name')->get();
        
        // Años disponibles (compatible con SQLite)
        $years = Movie::selectRaw('strftime("%Y", release_date) as year')
                     ->whereNotNull('release_date')
                     ->groupBy('year')
                     ->orderBy('year', 'desc')
                     ->pluck('year');
        
        return view('movies.index', compact('movies', 'genres', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $movie->load(['genres', 'people']);
        
        // Películas relacionadas (mismo género)
        $relatedMovies = Movie::whereHas('genres', function ($query) use ($movie) {
            $query->whereIn('genres.id', $movie->genres->pluck('id'));
        })
        ->where('id', '!=', $movie->id)
        ->popular()
        ->take(8)
        ->get();
        
        return view('movies.show', compact('movie', 'relatedMovies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
    
    /**
     * Rate a movie
     */
    public function rate(Request $request, Movie $movie)
    {
        $request->validate([
            'rating_type' => 'required|in:dislike,like,love'
        ]);
        
        $user = auth()->user();
        
        // Check if user already has a rating
        $existingRating = \DB::table('movie_ratings')
            ->where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->first();
        
        if ($existingRating) {
            // If same rating, remove it (toggle off)
            if ($existingRating->rating_type === $request->rating_type) {
                \DB::table('movie_ratings')
                    ->where('user_id', $user->id)
                    ->where('movie_id', $movie->id)
                    ->delete();
                    
                return response()->json([
                    'success' => true,
                    'message' => 'Calificación eliminada',
                    'rating_type' => null,
                    'rating_counts' => $movie->getRatingCounts()
                ]);
            }
            
            // Update existing rating
            \DB::table('movie_ratings')
                ->where('user_id', $user->id)
                ->where('movie_id', $movie->id)
                ->update([
                    'rating_type' => $request->rating_type,
                    'updated_at' => now()
                ]);
        } else {
            // Create new rating
            \DB::table('movie_ratings')->insert([
                'user_id' => $user->id,
                'movie_id' => $movie->id,
                'rating_type' => $request->rating_type,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        $messages = [
            'dislike' => 'No te gustó esta película',
            'like' => '¡Te gustó esta película!',
            'love' => '¡Te encantó esta película!'
        ];
        
        return response()->json([
            'success' => true,
            'message' => $messages[$request->rating_type] ?? 'Calificación guardada',
            'rating_type' => $request->rating_type,
            'rating_counts' => $movie->getRatingCounts()
        ]);
    }
    
    /**
     * Remove movie rating
     */
    public function removeRating(Movie $movie)
    {
        $user = auth()->user();
        
        \DB::table('movie_ratings')
            ->where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->delete();
            
        return response()->json([
            'success' => true,
            'message' => 'Calificación eliminada'
        ]);
    }
    
    /**
     * Toggle movie watchlist
     */
    public function toggleWatchlist(Request $request, Movie $movie)
    {
        $user = auth()->user();
        
        // Check if movie is in watchlist
        $inWatchlist = \DB::table('movie_watchlist')
            ->where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->exists();
            
        if ($inWatchlist) {
            // Remove from watchlist
            \DB::table('movie_watchlist')
                ->where('user_id', $user->id)
                ->where('movie_id', $movie->id)
                ->delete();
                
            return response()->json([
                'success' => true,
                'message' => 'Película eliminada de tu lista',
                'in_watchlist' => false
            ]);
        } else {
            // Add to watchlist
            \DB::table('movie_watchlist')->insert([
                'user_id' => $user->id,
                'movie_id' => $movie->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Película agregada a tu lista',
                'in_watchlist' => true
            ]);
        }
    }
}
