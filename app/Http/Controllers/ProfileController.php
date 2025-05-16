<?php
<<<<<<< HEAD

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
=======
// En app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user()->load('profile', 'preferences');
        
        return view('user.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user()->load('profile');
        $countries = Country::orderBy('name')->get();
        
        return view('user.edit-profile', compact('user', 'countries'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'birth_date' => 'nullable|date|before:today',
            'country_id' => 'nullable|exists:countries,id',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Actualizar usuario
        $user->name = $request->name;
        $user->username = $request->username;
        
        // Procesar avatar si se ha subido
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        
        $user->save();
        
        // Actualizar o crear perfil
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => $request->bio,
                'location' => $request->location,
                'website' => $request->website,
                'birth_date' => $request->birth_date,
                'country_id' => $request->country_id,
            ]
        );
        
        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }

    public function preferences()
    {
        $user = Auth::user()->load('preferences');
        $genres = Genre::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        
        return view('user.preferences', compact('user', 'genres', 'countries'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'favorite_genres' => 'nullable|array',
            'favorite_genres.*' => 'exists:genres,id',
            'favorite_countries' => 'nullable|array',
            'favorite_countries.*' => 'exists:countries,id',
            'email_notifications' => 'boolean',
            'dark_mode' => 'boolean',
            'content_language' => 'string|in:es,en,jp,kr,cn',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Actualizar o crear preferencias
        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'favorite_genres' => $request->favorite_genres ?? [],
                'favorite_countries' => $request->favorite_countries ?? [],
                'email_notifications' => $request->has('email_notifications'),
                'dark_mode' => $request->has('dark_mode'),
                'content_language' => $request->content_language ?? 'es',
            ]
        );
        
        return redirect()->route('profile.preferences')->with('success', 'Preferencias actualizadas correctamente.');
    }
}
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
