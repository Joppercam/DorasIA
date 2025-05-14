<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WatchHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'has.active.profile']);
    }
    
    /**
     * Display a listing of the user's watch history.
     */
    public function index()
    {
        $profile = auth()->user()->getActiveProfile();
        
        $watchHistory = \App\Models\WatchHistory::where('profile_id', $profile->id)
            ->with(['title', 'episode.season'])
            ->orderBy('updated_at', 'desc')
            ->paginate(24);
        
        return view('watch-history.index', [
            'watchHistory' => $watchHistory,
            'profile' => $profile,
        ]);
    }

    /**
     * Store a newly created watch history record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required_without:episode_id|exists:titles,id',
            'episode_id' => 'required_without:title_id|exists:episodes,id',
            'watched_seconds' => 'required|integer|min:0',
            'season_number' => 'nullable|integer|min:1',
            'episode_number' => 'nullable|integer|min:1',
            'completed' => 'boolean',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        $data = [
            'profile_id' => $profile->id,
            'watched_seconds' => $validated['watched_seconds'],
            'completed' => $validated['completed'] ?? false,
        ];
        
        // For movies
        if ($request->has('title_id')) {
            $data['title_id'] = $validated['title_id'];
            $data['episode_id'] = null;
            $data['season_number'] = null;
            $data['episode_number'] = null;
            
            $title = \App\Models\Title::find($validated['title_id']);
            if ($title && $title->duration > 0) {
                $data['progress'] = min(100, ($validated['watched_seconds'] / ($title->duration * 60)) * 100);
            }
            
            $watchHistory = \App\Models\WatchHistory::updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'title_id' => $validated['title_id'],
                    'episode_id' => null,
                ],
                $data
            );
        } 
        // For episodes
        else {
            $episode = \App\Models\Episode::findOrFail($validated['episode_id']);
            $season = $episode->season;
            
            $data['episode_id'] = $validated['episode_id'];
            $data['title_id'] = null;
            $data['season_number'] = $validated['season_number'] ?? $season->number;
            $data['episode_number'] = $validated['episode_number'] ?? $episode->number;
            
            if ($episode->duration > 0) {
                $data['progress'] = min(100, ($validated['watched_seconds'] / ($episode->duration * 60)) * 100);
            }
            
            $watchHistory = \App\Models\WatchHistory::updateOrCreate(
                [
                    'profile_id' => $profile->id,
                    'episode_id' => $validated['episode_id'],
                    'title_id' => null,
                ],
                $data
            );
        }
        
        return response()->json([
            'watch_history' => $watchHistory,
            'message' => 'Progreso de visualización guardado.',
        ]);
    }

    /**
     * Update the specified watch history record in storage.
     */
    public function update(Request $request, string $id)
    {
        $watchHistory = \App\Models\WatchHistory::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($watchHistory->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para actualizar este historial.',
            ], 403);
        }
        
        $validated = $request->validate([
            'watched_seconds' => 'required|integer|min:0',
            'season_number' => 'nullable|integer|min:1',
            'episode_number' => 'nullable|integer|min:1',
            'completed' => 'boolean',
        ]);
        
        $data = [
            'watched_seconds' => $validated['watched_seconds'],
            'completed' => $validated['completed'] ?? $watchHistory->completed,
        ];
        
        // Update season and episode numbers if provided
        if (isset($validated['season_number'])) {
            $data['season_number'] = $validated['season_number'];
        }
        
        if (isset($validated['episode_number'])) {
            $data['episode_number'] = $validated['episode_number'];
        }
        
        // Calculate progress based on watched_seconds
        if ($watchHistory->title_id) {
            $title = $watchHistory->title;
            if ($title && $title->duration > 0) {
                $data['progress'] = min(100, ($validated['watched_seconds'] / ($title->duration * 60)) * 100);
            }
        } elseif ($watchHistory->episode_id) {
            $episode = $watchHistory->episode;
            if ($episode && $episode->duration > 0) {
                $data['progress'] = min(100, ($validated['watched_seconds'] / ($episode->duration * 60)) * 100);
            }
        }
        
        $watchHistory->update($data);
        
        return response()->json([
            'watch_history' => $watchHistory->fresh(),
            'message' => 'Progreso de visualización actualizado.',
        ]);
    }

    /**
     * Remove the specified watch history record from storage.
     */
    public function destroy(string $id)
    {
        $watchHistory = \App\Models\WatchHistory::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($watchHistory->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para eliminar este historial.',
            ], 403);
        }
        
        $watchHistory->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Historial eliminado correctamente.',
            ]);
        }
        
        return redirect()->route('watch-history.index')
            ->with('success', 'Historial eliminado correctamente.');
    }
}
