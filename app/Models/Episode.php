<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
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
=======
        'tv_show_id',
        'season_id',
        'episode_number',
        'name',
        'overview',
        'still_path',
        'runtime',
        'air_date',
        'api_id',
    ];

    protected $casts = [
        'air_date' => 'date',
        'episode_number' => 'integer',
        'runtime' => 'integer',
    ];

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
<<<<<<< HEAD

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
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
}