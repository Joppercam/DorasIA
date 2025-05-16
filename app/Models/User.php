<?php

namespace App\Models;

<<<<<<< HEAD
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
=======
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe

    /**
     * The attributes that are mass assignable.
     *
<<<<<<< HEAD
     * @var list<string>
=======
     * @var array<int, string>
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
     */
    protected $fillable = [
        'name',
        'email',
<<<<<<< HEAD
        'password',
        'provider',
        'provider_id',
        'avatar',
        'is_admin',
=======
        'username',
        'avatar',
        'role',
        'is_active',
        'password',
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
<<<<<<< HEAD
     * @var list<string>
=======
     * @var array<int, string>
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
<<<<<<< HEAD
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
    
    /**
     * Get the profiles that belong to the user.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
    
    /**
     * Get the user's active profile.
     */
    public function getActiveProfile(): ?Profile
    {
        $profileId = Session::get('active_profile_id');
        
        if (!$profileId) {
            // If no active profile in session, try to get the first profile
            $profile = $this->profiles()->first();
            
            if ($profile) {
                // Set this profile as active
                Session::put('active_profile_id', $profile->id);
                return $profile;
            }
            
            return null;
        }
        
        return $this->profiles()->find($profileId);
    }

    /**
     * Set the user's active profile.
     */
    public function setActiveProfile(Profile $profile): void
    {
        if ($profile->user_id === $this->id) {
            Session::put('active_profile_id', $profile->id);
        }
    }

    /**
     * Get all comments from all user's profiles.
     */
    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Profile::class);
    }

    /**
     * Get all ratings from all user's profiles.
     */
    public function ratings(): HasManyThrough
    {
        return $this->hasManyThrough(Rating::class, Profile::class);
    }
}
=======
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(Preference::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' || $this->role === 'admin';
    }
}
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
