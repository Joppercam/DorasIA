<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar la página principal
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener contenido destacado para el slider principal (mezcla de películas y series mejor valoradas)
        $featuredContent = collect([])
            ->merge(Movie::where('vote_average', '>=', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('popularity', 'desc')
                ->take(3)
                ->get())
            ->merge(TvShow::where('vote_average', '>=', 7)
                ->where('vote_count', '>=', 50)
                ->orderBy('popularity', 'desc')
                ->take(3)
                ->get())
            ->sortByDesc('popularity')
            ->take(5)
            ->map(function($item) {
                $type = $item instanceof Movie ? 'movie' : 'tv-show';
                $item->type = $type;
                $item->backdrop_path = asset('storage/' . $item->backdrop_path);
                return $item;
            });
        
        // Contenido en tendencia (más populares recientemente)
        $trendingContent = collect([])
            ->merge(Movie::orderBy('popularity', 'desc')->take(8)->get())
            ->merge(TvShow::orderBy('popularity', 'desc')->take(8)->get())
            ->sortByDesc('popularity')
            ->take(12)
            ->map(function($item) {
                return $this->formatContent($item);
            });
        
        // Series coreanas (doramas)
        $koreanSeries = TvShow::where('country_of_origin', 'KR')
            ->orderBy('popularity', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return $this->formatContent($item);
            });
        
        // Películas asiáticas populares
        $asianMovies = Movie::whereIn('country_of_origin', ['JP', 'KR', 'CN', 'TW', 'HK', 'TH'])
            ->orderBy('popularity', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return $this->formatContent($item);
            });
        
        // Series japonesas (incluye anime)
        $japaneseSeries = TvShow::where('country_of_origin', 'JP')
            ->orderBy('popularity', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return $this->formatContent($item);
            });
        
        // Series chinas (C-dramas)
        $chineseSeries = TvShow::whereIn('country_of_origin', ['CN', 'TW', 'HK'])
            ->orderBy('popularity', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return $this->formatContent($item);
            });
        
        // Géneros para exploración
        $genres = Genre::orderBy('name')
            ->take(12)
            ->get();
        
        // Plataformas disponibles
        $platforms = Platform::orderBy('name')
            ->take(6)
            ->get();
            
        return view('home.index', compact(
            'featuredContent',
            'trendingContent',
            'koreanSeries',
            'asianMovies',
            'japaneseSeries',
            'chineseSeries',
            'genres',
            'platforms'
        ));
    }
    
    /**
     * Mostrar la página Acerca de
     */
    public function about()
    {
        return view('about');
    }
    
    /**
     * Mostrar la página de contacto
     */
    public function contact()
    {
        return view('contact');
    }
    
    /**
     * Formatear elementos de contenido para carruseles
     */
    private function formatContent($item)
    {
        $type = $item instanceof Movie ? 'movie' : 'tv-show';
        $posterPath = $item->poster_path 
            ? asset('storage/' . $item->poster_path) 
            : asset('images/poster-placeholder.jpg');
            
        $year = null;
        if ($type === 'movie' && $item->release_date) {
            $year = date('Y', strtotime($item->release_date));
        } elseif ($type === 'tv-show' && $item->first_air_date) {
            $year = date('Y', strtotime($item->first_air_date));
        }
        
        return (object)[
            'id' => $item->id,
            'title' => $item->title,
            'poster_path' => $posterPath,
            'backdrop_path' => $item->backdrop_path ? asset('storage/' . $item->backdrop_path) : null,
            'vote_average' => $item->vote_average,
            'overview' => $item->overview,
            'type' => $type,
            'show_type' => $type === 'tv-show' ? $item->show_type : null,
            'origin_country' => $item->country_of_origin,
            'year' => $year,
            'genres' => $item->genres,
            'slug' => $item->slug,
            'link' => $type === 'movie' 
                ? route('catalog.movie-detail', $item->slug) 
                : route('catalog.tv-show-detail', $item->slug)
        ];
    }
}