<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }

        // Check if the user already exists in the database
        $user = User::where('provider_id', $socialUser->getId())
                    ->where('provider', $provider)
                    ->first();

        // If not, create a new user record
        if (!$user) {
            // Check if a user with this email already exists
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // Link the social account to the existing user
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
                
                $user = $existingUser;
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => null, // Password is not needed for social auth
                    'email_verified_at' => now(), // Consider emails from social providers as verified
                ]);
            }
        }

        // Login the user
        Auth::login($user, true);

        // Check if the user has any profiles, redirect to create one if not
        if ($user->profiles()->count() === 0) {
            return redirect()->route('user-profiles.create')
                ->with('info', 'Por favor, crea un perfil para personalizar tu experiencia.');
        }

        // Set the first profile as active if none is active
        $activeProfile = $user->getActiveProfile();
        if (!$activeProfile) {
            $firstProfile = $user->profiles()->first();
            if ($firstProfile) {
                $user->setActiveProfile($firstProfile);
            }
        }

        return redirect()->route('dashboard');
    }
}