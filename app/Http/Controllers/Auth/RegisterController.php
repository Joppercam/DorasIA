<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'Ingresa un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password_confirmation.required' => 'Confirma tu contraseña',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('name', 'email'));
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify for now
        ]);

        // Create profile
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

        // Log in the user
        Auth::login($user);

        return redirect()->route('home')->with('success', '¡Bienvenido a DORASIA! Tu cuenta ha sido creada exitosamente.');
    }
}
