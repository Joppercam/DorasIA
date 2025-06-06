<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile
     */
    public function show(User $user = null)
    {
        $user = $user ?? Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            // Create profile if it doesn't exist
            $profile = Profile::create([
                'user_id' => $user->id,
                'bio' => 'Fanático de los K-Dramas 🎭',
                'favorite_genres' => json_encode(['Drama', 'Romance']),
                'privacy_settings' => json_encode([
                    'show_watchlist' => true,
                    'show_ratings' => true,
                    'show_comments' => true,
                ]),
            ]);
        }

        // Get user statistics
        $stats = [
            'series_watched' => $user->watchHistory()->distinct('series_id')->count(),
            'total_episodes' => $user->watchHistory()->sum('episodes_watched'),
            'ratings_given' => $user->ratings()->count(),
            'comments_made' => $user->comments()->count(),
            'watchlist_items' => $user->watchlist()->count(),
        ];

        // Get recent activity
        $recentWatched = $user->watchHistory()
            ->with('series')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $recentRatings = $user->ratings()
            ->with('series')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $isOwnProfile = Auth::id() === $user->id;

        return view('profile.show', compact('user', 'profile', 'stats', 'recentWatched', 'recentRatings', 'isOwnProfile'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $genres = Genre::orderBy('name_es')->get();

        return view('profile.edit', compact('user', 'profile', 'genres'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'favorite_genres' => 'nullable|array',
            'favorite_genres.*' => 'exists:genres,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'current_password' => 'nullable|string|min:6',
            'new_password' => 'nullable|string|min:6|confirmed',
            'privacy_show_watchlist' => 'boolean',
            'privacy_show_ratings' => 'boolean',
            'privacy_show_comments' => 'boolean',
        ], [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'Ingresa un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está en uso',
            'bio.max' => 'La biografía no puede tener más de 500 caracteres',
            'location.max' => 'La ubicación no puede tener más de 100 caracteres',
            'avatar.image' => 'El avatar debe ser una imagen',
            'avatar.max' => 'El avatar no puede ser mayor a 2MB',
            'banner.image' => 'El banner debe ser una imagen',
            'banner.max' => 'El banner no puede ser mayor a 5MB',
            'current_password.min' => 'La contraseña actual debe tener al menos 6 caracteres',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
            'new_password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update user basic info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar_path = $avatarPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($profile->banner_path) {
                Storage::disk('public')->delete($profile->banner_path);
            }

            $bannerPath = $request->file('banner')->store('banners', 'public');
            $profile->banner_path = $bannerPath;
        }

        // Update profile
        $profile->update([
            'bio' => $request->bio,
            'location' => $request->location,
            'favorite_genres' => $request->favorite_genres ? json_encode($request->favorite_genres) : null,
            'privacy_settings' => json_encode([
                'show_watchlist' => $request->boolean('privacy_show_watchlist'),
                'show_ratings' => $request->boolean('privacy_show_ratings'),
                'show_comments' => $request->boolean('privacy_show_comments'),
            ]),
        ]);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Show user's watchlist
     */
    public function watchlist(User $user = null)
    {
        $user = $user ?? Auth::user();
        $profile = $user->profile;

        // Check privacy settings
        if (Auth::id() !== $user->id && $profile && !$profile->show_watchlist) {
            abort(403, 'Este usuario ha configurado su lista de seguimiento como privada.');
        }

        $watchlist = $user->watchlist()->with('series')->paginate(20);

        return view('profile.watchlist', compact('user', 'watchlist'));
    }

    /**
     * Show user's ratings
     */
    public function ratings(User $user = null)
    {
        $user = $user ?? Auth::user();
        $profile = $user->profile;

        // Check privacy settings
        if (Auth::id() !== $user->id && $profile && !$profile->show_ratings) {
            abort(403, 'Este usuario ha configurado sus calificaciones como privadas.');
        }

        $ratings = $user->ratings()
            ->with('series')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('profile.ratings', compact('user', 'ratings'));
    }
}
