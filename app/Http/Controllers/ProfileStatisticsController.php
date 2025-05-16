<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileStatisticsController extends Controller
{
    /**
     * Show the statistics page for the authenticated user's profile
     */
    public function index()
    {
        $profile = Auth::user()->getActiveProfile();
        
        if (!$profile) {
            return redirect()->route('user-profiles.index')
                ->with('error', 'Debes seleccionar un perfil primero.');
        }

        return $this->show($profile);
    }

    /**
     * Show the statistics page for a specific profile
     */
    public function show(Profile $profile)
    {
        // Basic statistics
        $stats = [
            'total_comments' => $profile->comments()->count(),
            'total_ratings' => $profile->ratings()->count(),
            'total_watchlist' => $profile->watchlist()->count(),
            'average_rating' => $profile->ratings()->avg('score') ?? 0,
        ];

        // Most watched genres
        $genreStats = DB::table('ratings')
            ->join('titles', 'ratings.title_id', '=', 'titles.id')
            ->join('title_genre', 'titles.id', '=', 'title_genre.title_id')
            ->join('genres', 'title_genre.genre_id', '=', 'genres.id')
            ->where('ratings.profile_id', $profile->id)
            ->select('genres.name', DB::raw('COUNT(*) as count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Rating distribution
        $ratingDistribution = DB::table('ratings')
            ->where('profile_id', $profile->id)
            ->select(DB::raw('FLOOR(score/2) as rating_group'), DB::raw('COUNT(*) as count'))
            ->groupBy('rating_group')
            ->orderBy('rating_group')
            ->get()
            ->mapWithKeys(function ($item) {
                $stars = $item->rating_group;
                return [$stars => $item->count];
            });

        // Monthly activity (last 12 months)
        $monthlyActivity = DB::table('ratings')
            ->where('profile_id', $profile->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw(DatabaseHelper::yearFunction('created_at') . ' as year'),
                DB::raw(DatabaseHelper::monthFunction('created_at') . ' as month'),
                DB::raw('COUNT(*) as ratings_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => sprintf('%d-%02d', $item->year, $item->month),
                    'count' => $item->ratings_count,
                ];
            });

        // Countries distribution
        $countryStats = DB::table('ratings')
            ->join('titles', 'ratings.title_id', '=', 'titles.id')
            ->where('ratings.profile_id', $profile->id)
            ->select('titles.country', DB::raw('COUNT(*) as count'))
            ->groupBy('titles.country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Recent activity
        $recentActivity = [
            'ratings' => $profile->ratings()
                ->with('title')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'comments' => $profile->comments()
                ->with('commentable')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];

        return view('profile.statistics', compact(
            'profile',
            'stats',
            'genreStats',
            'ratingDistribution',
            'monthlyActivity',
            'countryStats',
            'recentActivity'
        ));
    }
}