<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

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
        'provider',
        'provider_id',
        'avatar',
        'is_admin',
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
}
