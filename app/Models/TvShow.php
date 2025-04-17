<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TvShow extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'slug',
        'overview',
        'poster_path',
        'backdrop_path',
        'number_of_seasons',
        'number_of_episodes',
        'first_air_date',
        'last_air_date',
        'original_language',
        'country_of_origin',
        'in_production',
        'popularity',
        'vote_average',
        'vote_count',
        'status',
        'show_type',
        'api_id',
        'api_source',
    ];

    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'in_production' => 'boolean',
        'popularity' => 'float',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'number_of_seasons' => 'integer',
        'number_of_episodes' => 'integer',
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function cast(): HasMany
    {
        return $this->hasMany(TvShowCast::class);
    }

    public function crew(): HasMany
    {
        return $this->hasMany(TvShowCrew::class);
    }

    public function availability(): MorphMany
    {
        return $this->morphMany(Availability::class, 'content');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'content');
    }

    public function watchlistItems(): MorphMany
    {
        return $this->morphMany(WatchlistItem::class, 'content');
    }
    
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'content');
    }
}