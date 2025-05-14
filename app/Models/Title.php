<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Title extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'synopsis',
        'poster',
        'backdrop',
        'release_year',
        'country',
        'type',
        'duration',
        'trailer_url',
        'slug',
        'featured',
        'vote_average',
        'vote_count',
        'popularity',
        'streaming_platforms',
        'category_id',
        'tmdb_id',
        'content_rating',
        'status',
        'original_language',
        'number_of_seasons',
        'number_of_episodes',
    ];

    protected $casts = [
        'release_year' => 'integer',
        'duration' => 'integer',
        'featured' => 'boolean',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'popularity' => 'float',
        'tmdb_id' => 'integer',
        'number_of_seasons' => 'integer',
        'number_of_episodes' => 'integer',
    ];

    /**
     * Get the category that the title belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the genres associated with the title.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'title_genre');
    }

    /**
     * Get the people associated with the title.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->withTimestamps();
    }

    /**
     * Get the actors for this title.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->wherePivot('role', 'actor')
            ->orderByPivot('order')
            ->withTimestamps();
    }

    /**
     * Get the directors for this title.
     */
    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role')
            ->wherePivot('role', 'director')
            ->withTimestamps();
    }

    /**
     * Get the seasons for this title.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Get the ratings for this title.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the comments for this title.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the watchlists that include this title.
     */
    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get the watch histories for this title.
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
}