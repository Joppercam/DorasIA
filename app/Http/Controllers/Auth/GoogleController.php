<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists by Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->route('home')->with('success', '¡Bienvenido de vuelta!');
            }
            
            // Check if user exists by email
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
                
                Auth::login($existingUser);
                return redirect()->route('home')->with('success', '¡Tu cuenta de Google ha sido vinculada exitosamente!');
            }
            
            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'email_verified_at' => now(),
                'password' => null, // No password for OAuth users
            ]);
            
            // Create profile for new user
            Profile::create([
                'user_id' => $user->id,
                'bio' => 'Fanático de los K-Dramas 🎭',
                'favorite_genres' => json_encode(['Drama', 'Romance']),
                'privacy_settings' => json_encode([
                    'show_watchlist' => true,
                    'show_ratings' => true,
                    'show_comments' => true,
                ]),
            ]);
            
            Auth::login($user);
            
            return redirect()->route('home')->with('success', '¡Bienvenido a DORASIA! Tu cuenta ha sido creada exitosamente.');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Hubo un error al iniciar sesión con Google. Por favor, intenta de nuevo.');
        }
    }
}