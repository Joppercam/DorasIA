<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'display_title',
        'overview',
        'display_overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'runtime',
        'vote_average',
        'vote_count',
        'status',
        'original_language',
        'tmdb_id',
        'imdb_id',
        'budget',
        'revenue',
        'production_companies',
        'production_countries',
        'spoken_languages',
        'tagline',
        'adult',
        'popularity'
    ];

    protected $casts = [
        'release_date' => 'date',
        'production_companies' => 'array',
        'production_countries' => 'array',
        'spoken_languages' => 'array',
        'adult' => 'boolean',
        'vote_average' => 'decimal:1',
        'budget' => 'decimal:2',
        'revenue' => 'decimal:2',
        'popularity' => 'decimal:3'
    ];

    // Relaciones
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'movie_person')->withPivot(['character', 'order', 'department', 'job']);
    }

    // Scopes
    public function scopePopular($query)
    {
        return $query->orderBy('popularity', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('vote_average', 'desc');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('release_date', '>', now())
                    ->orderBy('release_date', 'asc');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    // Accessors
    public function getFormattedRuntimeAttribute()
    {
        if (!$this->runtime) return null;
        
        $hours = floor($this->runtime / 60);
        $minutes = $this->runtime % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }
        
        return $minutes . 'min';
    }

    public function getFormattedReleaseDateAttribute()
    {
        if (!$this->release_date) return null;
        
        return $this->release_date->format('d M Y');
    }

    public function getYearAttribute()
    {
        if (!$this->release_date) return null;
        
        return $this->release_date->format('Y');
    }
    
    // Rating methods
    public function userRating($userId)
    {
        return \DB::table('movie_ratings')
            ->where('user_id', $userId)
            ->where('movie_id', $this->id)
            ->first();
    }
    
    public function isInWatchlist($userId)
    {
        return \DB::table('movie_watchlist')
            ->where('user_id', $userId)
            ->where('movie_id', $this->id)
            ->exists();
    }
    
    public function getRatingCounts(): array
    {
        return [
            'love' => \DB::table('movie_ratings')->where('movie_id', $this->id)->where('rating_type', 'love')->count(),
            'like' => \DB::table('movie_ratings')->where('movie_id', $this->id)->where('rating_type', 'like')->count(),
            'dislike' => \DB::table('movie_ratings')->where('movie_id', $this->id)->where('rating_type', 'dislike')->count(),
        ];
    }
}
