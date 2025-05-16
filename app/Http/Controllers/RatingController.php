<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'score' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Check if user has already rated this title
        $existingRating = Rating::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
            
        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'score' => $validated['score'],
                'review' => $validated['review'],
            ]);
            
            $rating = $existingRating;
        } else {
            // Create new rating
            $rating = Rating::create([
                'profile_id' => $profile->id,
                'title_id' => $validated['title_id'],
                'score' => $validated['score'],
                'review' => $validated['review'],
            ]);
        }
        
        // Update title's average rating
        $title = Title::find($validated['title_id']);
        $title->updateAverageRating();
        
        if ($request->wantsJson()) {
            return response()->json([
                'rating' => $rating,
                'message' => $existingRating ? 'Valoración actualizada.' : 'Valoración guardada.',
                'averageRating' => $title->average_rating,
                'totalRatings' => $title->ratings()->count()
            ]);
        }
        
        return redirect()->back()->with('success', 
            $existingRating ? 'Valoración actualizada.' : 'Valoración guardada.');
    }

    /**
     * Store or update a rating for a specific title (AJAX)
     */
    public function rate(Request $request, Title $title)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $profile = auth()->user()->getActiveProfile();
        
        // Scale the rating from 1-5 to 1-10 for database storage
        $scaledRating = $request->rating * 2;
        
        $rating = Rating::updateOrCreate(
            [
                'profile_id' => $profile->id,
                'title_id' => $title->id
            ],
            [
                'score' => $scaledRating
            ]
        );

        // Update title's average rating
        $title->updateAverageRating();

        return response()->json([
            'success' => true,
            'rating' => $request->rating, // Return the 1-5 scale rating
            'averageRating' => $title->average_rating / 2, // Convert back to 1-5 scale
            'totalRatings' => $title->ratings()->count()
        ]);
    }

    /**
     * Get rating statistics for a title
     */
    public function statistics(Title $title)
    {
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];

        // Get ratings and convert from 1-10 to 1-5 scale
        $ratings = $title->ratings()
            ->select(DB::raw('ROUND(score/2) as rating_scale'), DB::raw('count(*) as count'))
            ->groupBy('rating_scale')
            ->get();

        foreach ($ratings as $rating) {
            if (isset($distribution[$rating->rating_scale])) {
                $distribution[$rating->rating_scale] = $rating->count;
            }
        }

        $totalRatings = array_sum($distribution);
        $percentages = [];

        foreach ($distribution as $score => $count) {
            $percentages[$score] = $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0;
        }

        $userRating = null;
        if (Auth::check()) {
            $profile = auth()->user()->getActiveProfile();
            $userRatingValue = $title->ratings()
                ->where('profile_id', $profile->id)
                ->value('score');
            $userRating = $userRatingValue ? round($userRatingValue / 2) : null;
        }

        return response()->json([
            'distribution' => $distribution,
            'percentages' => $percentages,
            'averageRating' => $title->average_rating / 2, // Convert to 1-5 scale
            'totalRatings' => $totalRatings,
            'userRating' => $userRating
        ]);
    }

    /**
     * Update the specified rating in storage.
     */
    public function update(Request $request, string $id)
    {
        $rating = Rating::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($rating->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para editar esta valoración.',
            ], 403);
        }
        
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000',
        ]);
        
        $rating->update([
            'score' => $validated['score'],
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
    public function destroy(Title $title)
    {
        $profile = auth()->user()->getActiveProfile();
        $rating = $title->ratings()->where('profile_id', $profile->id)->first();

        if ($rating) {
            $rating->delete();
            
            // Update title's average rating
            $title->updateAverageRating();

            return response()->json([
                'success' => true,
                'averageRating' => $title->average_rating / 2, // Convert to 1-5 scale
                'totalRatings' => $title->ratings()->count()
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}