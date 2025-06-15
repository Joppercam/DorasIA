<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeProgress extends Model
{
    protected $fillable = [
        'user_id',
        'series_id', 
        'episode_id',
        'status',
        'progress_minutes',
        'total_minutes',
        'progress_percentage',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'watching',
            'started_at' => now()
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100.00
        ]);
    }

    public function updateProgress(int $minutes): void
    {
        $percentage = $this->total_minutes ? ($minutes / $this->total_minutes) * 100 : 0;
        
        $this->update([
            'progress_minutes' => $minutes,
            'progress_percentage' => min(100, $percentage),
            'status' => $percentage >= 90 ? 'completed' : 'watching',
            'completed_at' => $percentage >= 90 ? now() : null
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isWatching(): bool
    {
        return $this->status === 'watching';
    }

    public function getProgressPercentageAttribute($value): float
    {
        return round($value, 2);
    }
}
