<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'avatar_path',
        'banner_path',
        'favorite_genres',
        'privacy_settings'
    ];

    protected $casts = [
        'favorite_genres' => 'array',
        'privacy_settings' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods for privacy settings
    public function getShowWatchlistAttribute()
    {
        return $this->privacy_settings['show_watchlist'] ?? true;
    }

    public function getShowRatingsAttribute()
    {
        return $this->privacy_settings['show_ratings'] ?? true;
    }

    public function getShowCommentsAttribute()
    {
        return $this->privacy_settings['show_comments'] ?? true;
    }
}
