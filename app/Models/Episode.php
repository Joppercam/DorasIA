<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'season_id',
        'episode_number',
        'season_number',
        'name',
        'overview',
        'detailed_summary',
        'air_date',
        'runtime',
        'still_path',
        'vote_average',
        'vote_count',
        'tmdb_id',
        'guest_stars',
        'crew'
    ];

    protected $casts = [
        'air_date' => 'date',
        'vote_average' => 'decimal:1',
        'guest_stars' => 'array',
        'crew' => 'array'
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function stills(): MorphMany
    {
        return $this->images()->where('type', 'still');
    }

    public function progress()
    {
        return $this->hasMany(EpisodeProgress::class);
    }

    public function getUserProgress($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->progress()->where('user_id', $userId)->first();
    }

    public function getFormattedRuntime()
    {
        if (!$this->runtime) return 'N/A';
        
        $hours = floor($this->runtime / 60);
        $minutes = $this->runtime % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }
}
