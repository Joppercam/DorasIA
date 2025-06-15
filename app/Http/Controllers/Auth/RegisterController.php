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
        return view('auth.register-simple');
    }

    /**
     * Handle a registration request
     */
    public function register(Request $request)
    {
        // Debug log
        \Log::info('Registration attempt', [
            'data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        try {
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
                \Log::info('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return back()
                    ->withErrors($validator)
                    ->withInput($request->only('name', 'email'));
            }
            
            \Log::info('Validation passed, creating user');

            // Create user (simplified)
            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(), // Auto-verify for now
                ]);
                
                \Log::info('User created successfully', ['user_id' => $user->id]);
            } catch (\Exception $userError) {
                \Log::error('User creation failed', [
                    'error' => $userError->getMessage(),
                    'trace' => $userError->getTraceAsString()
                ]);
                throw $userError;
            }

            // Log in the user
            Auth::login($user);
            \Log::info('User logged in after registration', [
                'user_id' => $user->id,
                'auth_check' => Auth::check()
            ]);

            \Log::info('Redirecting to home');
            return redirect()->route('home')->with('success', '¡Bienvenido a DORASIA! Tu cuenta ha sido creada exitosamente.');
            
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage(), [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['error' => 'Hubo un error al crear tu cuenta. Por favor, intenta nuevamente.'])
                ->withInput($request->only('name', 'email'));
        }
    }
}
