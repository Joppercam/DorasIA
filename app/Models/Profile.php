<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasMany;
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe

class Profile extends Model
{
    use HasFactory;

<<<<<<< HEAD
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
        'bio',
        'location',
        'favorite_genres',
        'is_public',
        'allow_messages',
        'followers_count',
        'following_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferences' => 'array',
        'favorite_genres' => 'array',
        'is_public' => 'boolean',
        'allow_messages' => 'boolean',
    ];
    
    /**
     * Get the profile's avatar URL.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('images/profiles/' . $this->avatar);
        }
        return asset('images/profiles/default.jpg');
    }

    /**
     * Get the user that owns the profile.
     */
=======
    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'website',
        'birth_date',
        'country_id',
        'social_links',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'social_links' => 'array',
    ];

>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
<<<<<<< HEAD
    
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
    
    /**
     * Get the followers of this profile.
     */
    public function followers()
    {
        return $this->belongsToMany(Profile::class, 'profile_followers', 'profile_id', 'follower_id')
            ->withTimestamps();
    }
    
    /**
     * Get the profiles this profile is following.
     */
    public function following()
    {
        return $this->belongsToMany(Profile::class, 'profile_followers', 'follower_id', 'profile_id')
            ->withTimestamps();
    }
    
    /**
     * Get messages sent by this profile.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    /**
     * Get messages received by this profile.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    
    /**
     * Check if this profile is following another profile.
     */
    public function isFollowing(Profile $profile): bool
    {
        return $this->following()->where('profile_id', $profile->id)->exists();
    }
    
    /**
     * Follow another profile.
     */
    public function follow(Profile $profile): void
    {
        if (!$this->isFollowing($profile) && $this->id !== $profile->id) {
            $this->following()->attach($profile);
            $this->increment('following_count');
            $profile->increment('followers_count');
            
            // Send notification
            $profile->user->notify(new \App\Notifications\NewFollower($this));
        }
    }
    
    /**
     * Unfollow another profile.
     */
    public function unfollow(Profile $profile): void
    {
        if ($this->isFollowing($profile)) {
            $this->following()->detach($profile);
            $this->decrement('following_count');
            $profile->decrement('followers_count');
        }
    }
    
    /**
     * Get unread messages count.
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
=======

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}