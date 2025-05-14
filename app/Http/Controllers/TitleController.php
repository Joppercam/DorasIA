<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Season;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TitleController extends Controller
{
    /**
     * Display a listing of all titles.
     */
    public function index()
    {
        // Redirecting to catalog page with all titles
        return redirect()->route('catalog.index');
    }

    /**
     * Show the form for creating a new title.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        
        return view('titles.create', [
            'categories' => $categories,
            'genres' => $genres,
        ]);
    }

    /**
     * Store a newly created title in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'synopsis' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 3),
            'country' => 'nullable|string|max:255',
            'type' => 'required|in:movie,series',
            'duration' => 'nullable|integer|min:1',
            'trailer_url' => 'nullable|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'genre_ids' => 'nullable|array',
            'genre_ids.*' => 'exists:genres,id',
            'poster' => 'nullable|image|max:2048',
            'backdrop' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
        ]);
        
        // Generate slug
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        
        // Handle file uploads
        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        
        if ($request->hasFile('backdrop')) {
            $validated['backdrop'] = $request->file('backdrop')->store('backdrops', 'public');
        }
        
        $title = Title::create($validated);
        
        // Attach genres
        if ($request->has('genre_ids')) {
            $title->genres()->attach($request->genre_ids);
        }
        
        return redirect()->route('titles.show', $title->slug)
            ->with('success', 'Título creado correctamente.');
    }

    /**
     * Display the specified title.
     */
    public function show(string $slug)
    {
        $title = Title::where('slug', $slug)
            ->with(['genres', 'category', 'directors', 'actors' => function ($query) {
                $query->take(10);
            }])
            ->firstOrFail();
        
        // Load seasons and episodes if it's a series
        if ($title->type === 'series') {
            $title->load(['seasons' => function ($query) {
                $query->with(['episodes' => function ($q) {
                    $q->orderBy('number');
                }])->orderBy('number');
            }]);
        }
        
        // Get user's watch status
        $watchStatus = null;
        $userRating = null;
        $watchHistory = null;
        
        if (auth()->check()) {
            $profile = auth()->user()->getActiveProfile();
            
            if ($profile) {
                $watchStatus = $profile->watchlist()
                    ->where('title_id', $title->id)
                    ->exists();
                
                $userRating = $profile->ratings()
                    ->where('title_id', $title->id)
                    ->first();
                
                // Get watch history for movie
                if ($title->type === 'movie') {
                    $watchHistory = $profile->watchHistory()
                        ->where('title_id', $title->id)
                        ->first();
                } 
                // For series, get the most recently watched episode
                else {
                    $watchHistory = $profile->watchHistory()
                        ->whereHas('episode', function($query) use ($title) {
                            $query->whereHas('season', function($q) use ($title) {
                                $q->where('title_id', $title->id);
                            });
                        })
                        ->orderBy('updated_at', 'desc')
                        ->first();
                }
            }
        }
        
        // Get similar titles
        $genreIds = $title->genres->pluck('id')->toArray();
        $similarTitles = Title::whereHas('genres', function ($query) use ($genreIds) {
                $query->whereIn('genres.id', $genreIds);
            })
            ->where('id', '!=', $title->id)
            ->with('genres')
            ->inRandomOrder()
            ->take(6)
            ->get();
        
        // Preparar metadatos para SEO y compartir en redes sociales
        $metaTitle = $title->title;
        $metaDescription = Str::limit($title->synopsis, 160) ?: "Ver {$title->title} en Dorasia - La mejor plataforma de streaming de contenido asiático";
        $metaImage = $title->backdrop ? asset('storage/' . $title->backdrop) : asset('images/heroes/hero-bg.jpg');
        
        return view('titles.show', [
            'title' => $title,
            'similarTitles' => $similarTitles,
            'watchStatus' => $watchStatus,
            'userRating' => $userRating,
            'watchHistory' => $watchHistory,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage' => $metaImage,
        ]);
    }

    /**
     * Show the form for editing the specified title.
     */
    public function edit(string $slug)
    {
        $title = Title::where('slug', $slug)->firstOrFail();
        $categories = Category::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        
        return view('titles.edit', [
            'title' => $title,
            'categories' => $categories,
            'genres' => $genres,
        ]);
    }

    /**
     * Update the specified title in storage.
     */
    public function update(Request $request, string $slug)
    {
        $title = Title::where('slug', $slug)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'synopsis' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 3),
            'country' => 'nullable|string|max:255',
            'type' => 'required|in:movie,series',
            'duration' => 'nullable|integer|min:1',
            'trailer_url' => 'nullable|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'genre_ids' => 'nullable|array',
            'genre_ids.*' => 'exists:genres,id',
            'poster' => 'nullable|image|max:2048',
            'backdrop' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
        ]);
        
        // Update slug if title changed
        if ($title->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }
        
        // Handle file uploads
        if ($request->hasFile('poster')) {
            // Delete old file if exists
            if ($title->poster) {
                Storage::disk('public')->delete($title->poster);
            }
            
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        
        if ($request->hasFile('backdrop')) {
            // Delete old file if exists
            if ($title->backdrop) {
                Storage::disk('public')->delete($title->backdrop);
            }
            
            $validated['backdrop'] = $request->file('backdrop')->store('backdrops', 'public');
        }
        
        $title->update($validated);
        
        // Sync genres
        if ($request->has('genre_ids')) {
            $title->genres()->sync($request->genre_ids);
        } else {
            $title->genres()->detach();
        }
        
        return redirect()->route('titles.show', $title->slug)
            ->with('success', 'Título actualizado correctamente.');
    }

    /**
     * Remove the specified title from storage.
     */
    public function destroy(string $slug)
    {
        $title = Title::where('slug', $slug)->firstOrFail();
        
        // Clean up files
        if ($title->poster) {
            Storage::disk('public')->delete($title->poster);
        }
        
        if ($title->backdrop) {
            Storage::disk('public')->delete($title->backdrop);
        }
        
        $title->delete();
        
        return redirect()->route('catalog')
            ->with('success', 'Título eliminado correctamente.');
    }
    
    /**
     * Display the player for watching a title.
     */
    public function watch(string $slug, ?string $seasonNumber = null, ?string $episodeNumber = null, ?int $startTime = null)
    {
        $title = Title::where('slug', $slug)->firstOrFail();
        
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver este contenido.');
        }
        
        $profile = auth()->user()->getActiveProfile();
        
        if (!$profile) {
            return redirect()->route('user-profiles.create')
                ->with('info', 'Debes crear un perfil primero.');
        }
        
        // Update watch history
        if ($seasonNumber && $episodeNumber && $title->type === 'series') {
            // Find the season and episode
            $season = Season::where('title_id', $title->id)
                ->where('number', (int) $seasonNumber)
                ->firstOrFail();
                
            $episode = Episode::where('season_id', $season->id)
                ->where('number', (int) $episodeNumber)
                ->firstOrFail();
            
            // Get or create watch history record
            $watchHistory = $profile->watchHistory()->updateOrCreate(
                [
                    'episode_id' => $episode->id,
                    'title_id' => null,
                ],
                [
                    'watched_seconds' => $startTime ?? 0,
                    'season_number' => (int) $seasonNumber,
                    'episode_number' => (int) $episodeNumber,
                    'progress' => 0,
                ]
            );
            
            // Find next episode for autoplay
            $nextEpisode = Episode::where('season_id', $season->id)
                ->where('number', '>', $episode->number)
                ->orderBy('number')
                ->first();
                
            if (!$nextEpisode) {
                // Check if there's a next season
                $nextSeason = Season::where('title_id', $title->id)
                    ->where('number', '>', $season->number)
                    ->orderBy('number')
                    ->first();
                    
                if ($nextSeason) {
                    $nextEpisode = Episode::where('season_id', $nextSeason->id)
                        ->orderBy('number')
                        ->first();
                }
            }
            
            return view('titles.player', [
                'title' => $title,
                'season' => $season,
                'episode' => $episode,
                'nextEpisode' => $nextEpisode ?? null,
                'prevEpisode' => null, // Could be implemented similar to nextEpisode
                'startTime' => $startTime,
                'watchHistory' => $watchHistory,
            ]);
        } else {
            // It's a movie
            if ($title->type !== 'movie') {
                return redirect()->route('titles.show', $title->slug)
                    ->with('error', 'Debes seleccionar un episodio para ver esta serie.');
            }
            
            // Create or update watch history for the movie
            $watchHistory = $profile->watchHistory()->updateOrCreate(
                [
                    'title_id' => $title->id,
                    'episode_id' => null,
                ],
                [
                    'watched_seconds' => $startTime ?? 0,
                    'season_number' => null,
                    'episode_number' => null,
                    'progress' => 0,
                ]
            );
            
            // If the movie has a fixed duration and progress is saved, update progress percentage
            if ($title->duration > 0 && $watchHistory && $watchHistory->watched_seconds > 0) {
                $watchHistory->updateProgress();
            }
            
            return view('titles.player', [
                'title' => $title,
                'episode' => null,
                'startTime' => $startTime,
                'watchHistory' => $watchHistory,
            ]);
        }
    }
}