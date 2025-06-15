<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function titleRatings()
    {
        return $this->hasMany(TitleRating::class);
    }

    // Alias for ratings
    public function ratings()
    {
        return $this->hasMany(TitleRating::class);
    }

    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function watchlist()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function episodeProgress()
    {
        return $this->hasMany(EpisodeProgress::class);
    }

    public function getEpisodeProgress($episodeId)
    {
        return $this->episodeProgress()->where('episode_id', $episodeId)->first();
    }

    public function getSeriesProgress($seriesId)
    {
        return $this->episodeProgress()
            ->where('series_id', $seriesId)
            ->with('episode')
            ->get();
    }

    public function actorFollows()
    {
        return $this->hasMany(ActorFollow::class);
    }

    public function followedActors()
    {
        return $this->belongsToMany(Person::class, 'actor_follows', 'user_id', 'person_id')
            ->withTimestamps();
    }

    public function isFollowingActor($personId)
    {
        return $this->actorFollows()->where('person_id', $personId)->exists();
    }
}
