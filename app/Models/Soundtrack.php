<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Soundtrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'title',
        'artist',
        'album',
        'lyrics',
        'spotify_url',
        'youtube_url',
        'apple_music_url',
        'duration',
        'is_main_theme',
        'is_ending_theme',
        'track_number'
    ];

    protected $casts = [
        'is_main_theme' => 'boolean',
        'is_ending_theme' => 'boolean'
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
