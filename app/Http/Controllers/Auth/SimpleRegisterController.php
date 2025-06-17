<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SimpleRegisterController extends Controller
{
    public function register(Request $request)
    {
        Log::info('SimpleRegister - Request received', [
            'method' => $request->method(),
            'has_name' => $request->has('name'),
            'has_email' => $request->has('email'),
            'has_password' => $request->has('password'),
            'session_id' => session()->getId()
        ]);

        try {
            // Validación simple
            if (empty($request->name) || empty($request->email) || empty($request->password)) {
                return back()->with('error', 'Todos los campos son requeridos');
            }

            // Verificar si el email ya existe
            if (User::where('email', $request->email)->exists()) {
                return back()->with('error', 'Este email ya está registrado');
            }

            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now()
            ]);

            Log::info('SimpleRegister - User created', ['user_id' => $user->id]);

            // Iniciar sesión
            Auth::login($user);

            Log::info('SimpleRegister - User logged in', [
                'user_id' => $user->id,
                'auth_check' => Auth::check()
            ]);

            return redirect()->route('home')->with('success', '¡Bienvenido a Dorasia! Tu cuenta ha sido creada.');

        } catch (\Exception $e) {
            Log::error('SimpleRegister - Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error al crear la cuenta: ' . $e->getMessage());
        }
    }
}