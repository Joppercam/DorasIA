<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title_id',
        'episode_id',
        'watched_seconds',
        'progress',
        'season_number',
        'episode_number',
    ];

    protected $casts = [
        'watched_seconds' => 'integer',
        'progress' => 'float',
        'season_number' => 'integer',
        'episode_number' => 'integer',
    ];

    /**
     * Get the profile that owns the watch history.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the title that was watched.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Get the episode that was watched.
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
    
    /**
     * Calculate progress percentage based on watched_seconds and total duration
     * 
     * @return float Progress percentage (0-100)
     */
    public function calculateProgress(): float
    {
        if ($this->title_id) {
            $title = $this->title;
            if ($title && $title->duration > 0) {
                return min(100, ($this->watched_seconds / ($title->duration * 60)) * 100);
            }
        } elseif ($this->episode_id) {
            $episode = $this->episode;
            if ($episode && $episode->duration > 0) {
                return min(100, ($this->watched_seconds / ($episode->duration * 60)) * 100);
            }
        }
        
        return 0;
    }
    
    /**
     * Update the progress field based on watched_seconds
     * 
     * @return void
     */
    public function updateProgress(): void
    {
        $this->progress = $this->calculateProgress();
        $this->save();
    }
    
    /**
     * Get the resume time in format MM:SS
     * 
     * @return string
     */
    public function getFormattedResumeTime(): string
    {
        $minutes = floor($this->watched_seconds / 60);
        $seconds = $this->watched_seconds % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}