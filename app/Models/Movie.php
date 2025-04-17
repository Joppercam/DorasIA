<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'slug',
        'overview',
        'poster_path',
        'backdrop_path',
        'runtime',
        'release_date',
        'original_language',
        'country_of_origin',
        'popularity',
        'vote_average',
        'vote_count',
        'status',
        'api_id',
        'api_source',
    ];

    protected $casts = [
        'release_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'runtime' => 'integer',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function cast(): HasMany
    {
        return $this->hasMany(MovieCast::class);
    }

    public function crew(): HasMany
    {
        return $this->hasMany(MovieCrew::class);
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