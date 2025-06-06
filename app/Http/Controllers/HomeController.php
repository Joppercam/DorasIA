<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Genre;
use App\Models\News;
use App\Models\TitleRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private function getSeriesWithUserRatings()
    {
        return Series::with(['ratings' => function($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }
        }]);
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

        // Noticias más recientes para el carrusel (6 noticias)
        $latestNews = News::published()
            ->latest()
            ->limit(6)
            ->get();

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
            'latestNews'
        ));
    }

    public function series($id)
    {
        $series = Series::with(['genres', 'people', 'seasons.episodes'])->findOrFail($id);
        return view('series.show', compact('series'));
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
}