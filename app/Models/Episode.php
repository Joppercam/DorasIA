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
        'season_id',
        'name',
        'number',
        'overview',
        'still',
        'runtime',
        'air_date',
        'video_url',
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
    public function title()
    {
        return $this->season->title;
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