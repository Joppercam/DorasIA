<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the profile.
     */
    public function view(User $user, Profile $profile): bool
    {
        // Everyone can view public profiles
        if ($profile->is_public ?? true) {
            return true;
        }
        
        // Users can view their own profiles
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function update(User $user, Profile $profile): bool
    {
        // Users can only update their own profiles
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can delete the profile.
     */
    public function delete(User $user, Profile $profile): bool
    {
        // Users can only delete their own profiles
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can create profiles.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create profiles
        return true;
    }
}