<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Genre;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
        $popularSeries = Series::orderBy('popularity', 'desc')
            ->limit(25)
            ->get();

        // Series mejor calificadas (25 para carrusel infinito)
        $topRatedSeries = Series::where('vote_average', '>', 7)
            ->orderBy('vote_average', 'desc')
            ->limit(25)
            ->get();

        // Series recientes (25 para carrusel infinito)
        $recentSeries = Series::whereNotNull('first_air_date')
            ->orderBy('first_air_date', 'desc')
            ->limit(25)
            ->get();

        // Series por género (25 para carrusel infinito)
        $dramasSeries = Series::whereHas('genres', function($query) {
            $query->where('name', 'Drama');
        })->orderBy('vote_average', 'desc')->limit(25)->get();

        $comedySeries = Series::whereHas('genres', function($query) {
            $query->where('name', 'Comedy');
        })->orderBy('vote_average', 'desc')->limit(25)->get();

        $romanceSeries = Series::where(function($query) {
            $query->where('title', 'LIKE', '%love%')
                  ->orWhere('title', 'LIKE', '%romance%')
                  ->orWhere('title', 'LIKE', '%marry%')
                  ->orWhere('title', 'LIKE', '%wedding%')
                  ->orWhere('title', 'LIKE', '%heart%')
                  ->orWhere('overview', 'LIKE', '%romance%')
                  ->orWhere('overview', 'LIKE', '%love%')
                  ->orWhere('overview', 'LIKE', '%romantic%');
        })->orderBy('vote_average', 'desc')->limit(25)->get();

        $actionSeries = Series::whereHas('genres', function($query) {
            $query->where('name', 'Action & Adventure');
        })->orderBy('vote_average', 'desc')->limit(25)->get();

        // Series de misterio (25 para carrusel infinito)
        $mysterySeries = Series::whereHas('genres', function($query) {
            $query->where('name', 'Mystery');
        })->orderBy('vote_average', 'desc')->limit(25)->get();

        // Series históricos/sageuks (25 para carrusel infinito)
        $historicalSeries = Series::where(function($query) {
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
}