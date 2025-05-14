<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'has.active.profile']);
    }
    
    /**
     * Display a listing of ratings.
     */
    public function index()
    {
        // Not implemented - ratings are shown on the title page
    }

    /**
     * Store a newly created rating in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Check if user has already rated this title
        $existingRating = \App\Models\Rating::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
            
        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $validated['rating'],
                'review' => $validated['review'],
            ]);
            
            $rating = $existingRating;
        } else {
            // Create new rating
            $rating = \App\Models\Rating::create([
                'profile_id' => $profile->id,
                'title_id' => $validated['title_id'],
                'rating' => $validated['rating'],
                'review' => $validated['review'],
            ]);
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'rating' => $rating,
                'message' => $existingRating ? 'Valoración actualizada.' : 'Valoración guardada.',
            ]);
        }
        
        return redirect()->back()->with('success', 
            $existingRating ? 'Valoración actualizada.' : 'Valoración guardada.');
    }

    /**
     * Update the specified rating in storage.
     */
    public function update(Request $request, string $id)
    {
        $rating = \App\Models\Rating::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($rating->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para editar esta valoración.',
            ], 403);
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000',
        ]);
        
        $rating->update([
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'rating' => $rating->fresh(),
                'message' => 'Valoración actualizada correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Valoración actualizada correctamente.');
    }

    /**
     * Remove the specified rating from storage.
     */
    public function destroy(string $id)
    {
        $rating = \App\Models\Rating::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($rating->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para eliminar esta valoración.',
            ], 403);
        }
        
        $rating->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Valoración eliminada correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Valoración eliminada correctamente.');
    }
}
