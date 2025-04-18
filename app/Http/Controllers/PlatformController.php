<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Availability;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Mostrar información de una plataforma específica
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Buscar la plataforma por slug
        $platform = Platform::where('slug', $slug)->firstOrFail();
        
        // Obtener películas disponibles en esta plataforma
        $availableMovies = Availability::where('platform_id', $platform->id)
            ->where('content_type', 'App\\Models\\Movie')
            ->paginate(12);
            
        $movies = collect();
        foreach ($availableMovies as $availability) {
            $movie = Movie::find($availability->content_id);
            if ($movie) {
                $movies->push($this->formatMovieForDisplay($movie, $availability));
            }
        }
        
        // Obtener series disponibles en esta plataforma
        $availableTvShows = Availability::where('platform_id', $platform->id)
            ->where('content_type', 'App\\Models\\TvShow')
            ->paginate(12);
            
        $tvShows = collect();
        foreach ($availableTvShows as $availability) {
            $tvShow = TvShow::find($availability->content_id);
            if ($tvShow) {
                $tvShows->push($this->formatTvShowForDisplay($tvShow, $availability));
            }
        }
        
        return view('platforms.show', compact('platform', 'movies', 'tvShows'));
    }
    
    /**
     * Dar formato a una película para mostrarla
     * 
     * @param Movie $movie
     * @param Availability $availability
     * @return object
     */
    private function formatMovieForDisplay(Movie $movie, Availability $availability)
    {
        return (object)[
            'id' => $movie->id,
            'title' => $movie->title,
            'poster_path' => $movie->poster_path ? asset('storage/' . $movie->poster_path) : asset('images/poster-placeholder.jpg'),
            'vote_average' => $movie->vote_average,
            'type' => 'movie',
            'release_date' => $movie->release_date,
            'year' => $movie->release_date ? date('Y', strtotime($movie->release_date)) : null,
            'origin_country' => $movie->country_of_origin,
            'genres' => $movie->genres,
            'availability' => $availability,
            'link' => route('catalog.movie-detail', $movie->slug)
        ];
    }
    
    /**
     * Dar formato a una serie para mostrarla
     * 
     * @param TvShow $tvShow
     * @param Availability $availability
     * @return object
     */
    private function formatTvShowForDisplay(TvShow $tvShow, Availability $availability)
    {
        return (object)[
            'id' => $tvShow->id,
            'title' => $tvShow->title,
            'poster_path' => $tvShow->poster_path ? asset('storage/' . $tvShow->poster_path) : asset('images/poster-placeholder.jpg'),
            'vote_average' => $tvShow->vote_average,
            'type' => 'tv-show',
            'first_air_date' => $tvShow->first_air_date,
            'year' => $tvShow->first_air_date ? date('Y', strtotime($tvShow->first_air_date)) : null,
            'origin_country' => $tvShow->country_of_origin,
            'genres' => $tvShow->genres,
            'availability' => $availability,
            'link' => route('catalog.tv-show-detail', $tvShow->slug)
        ];
    }
}