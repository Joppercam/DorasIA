<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'series_id',
        'episodes_watched',
        'total_episodes',
        'progress_percentage',
        'status',
        'last_watched_at',
    ];

    protected $casts = [
        'last_watched_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }
}
