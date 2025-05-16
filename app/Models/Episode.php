<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_id',
        'season_id',
        'name',
        'number',
        'overview',
        'still',
        'runtime',
        'air_date',
        'video_url',
        'tmdb_id',
    ];

    protected $casts = [
        'number' => 'integer',
        'runtime' => 'integer',
        'air_date' => 'date',
    ];

    /**
     * Get the season that this episode belongs to.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the title through the season.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }
    
    /**
     * Get title attribute through the relationship
     */
    public function getTitleAttribute()
    {
        return $this->title()->first();
    }

    /**
     * Get the comments for this episode.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get watch histories for this episode.
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
}