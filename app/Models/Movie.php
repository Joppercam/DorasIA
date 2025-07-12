<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_es',
        'spanish_title',
        'original_title',
        'display_title',
        'overview',
        'overview_es',
        'spanish_overview',
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
        'popularity',
        'like_count',
        'love_count'
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

    public function ratings()
    {
        return $this->hasMany(MovieRating::class);
    }

    public function watchlistItems()
    {
        return $this->hasMany(MovieWatchlist::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
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

    // Accessors para contenido en español
    public function getDisplayTitleAttribute()
    {
        return $this->spanish_title ?: $this->title_es ?: $this->title;
    }

    public function getDisplayOverviewAttribute()
    {
        return $this->spanish_overview ?: $this->overview_es ?: $this->overview;
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

    // === MÉTODOS PARA IMÁGENES DE TMDB ===
    
    /**
     * Obtener URL del poster con tamaño específico
     */
    public function posterUrl($size = 'w500')
    {
        if (!$this->poster_path) {
            return 'https://via.placeholder.com/500x750/333/666?text=Película';
        }
        
        return "https://image.tmdb.org/t/p/{$size}{$this->poster_path}";
    }

    /**
     * Obtener URL del backdrop con tamaño específico
     */
    public function backdropUrl($size = 'original')
    {
        if (!$this->backdrop_path) {
            return 'https://via.placeholder.com/1920x1080/333/666?text=Película';
        }
        
        return "https://image.tmdb.org/t/p/{$size}{$this->backdrop_path}";
    }

    /**
     * Obtener URL del poster para vista de detalle
     */
    public function getDetailPosterUrlAttribute()
    {
        return $this->posterUrl('w500');
    }

    /**
     * Obtener URL del backdrop original
     */
    public function getOriginalBackdropUrlAttribute()
    {
        return $this->backdropUrl('original');
    }
}
