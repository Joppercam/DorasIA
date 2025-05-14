<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'user_id',
        'preferences',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the watchlist items for this profile.
     */
    public function watchlist(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
    
    /**
     * Get the ratings made by this profile.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
    
    /**
     * Get the comments made by this profile.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Get the watch history for this profile.
     */
    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
}