<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ], [
                'email.required' => 'El email es requerido',
                'email.email' => 'Ingresa un email válido',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput($request->only('email'));
            }

            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember');

            // Intentar autenticación
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();
                
                // Configurar cookies manuales como respaldo
                setcookie('user_logged_in', $user->id, time() + (480 * 60), '/', '', false, false);
                setcookie('user_auth_token', hash('sha256', $user->id . $user->email), time() + (480 * 60), '/', '', false, false);
                
                $request->session()->regenerate();
                
                Log::info('Usuario logueado exitosamente', [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email
                ]);

                // Redirigir a la página solicitada o home
                $intended = $request->session()->get('url.intended', route('home'));
                return redirect($intended)->with('success', '¡Bienvenido de vuelta!');
            }

            // Login fallido
            Log::warning('Intento de login fallido', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return back()
                ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros'])
                ->withInput($request->only('email'));

        } catch (\Exception $e) {
            Log::error('Error en login', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown'
            ]);

            return back()
                ->withErrors(['error' => 'Error interno. Por favor intenta nuevamente.'])
                ->withInput($request->only('email'));
        }
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        try {
            // Validación completa
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|string|min:6',
            ], [
                'name.required' => 'El nombre es requerido',
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'name.max' => 'El nombre no puede tener más de 255 caracteres',
                'email.required' => 'El email es requerido',
                'email.email' => 'Ingresa un email válido',
                'email.unique' => 'Este email ya está registrado',
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

            // Crear usuario
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verificar por ahora
            ]);

            Log::info('Usuario registrado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            // Autenticar automáticamente
            Auth::login($user);
            
            // Configurar cookies manuales como respaldo
            setcookie('user_logged_in', $user->id, time() + (480 * 60), '/', '', false, false);
            setcookie('user_auth_token', hash('sha256', $user->id . $user->email), time() + (480 * 60), '/', '', false, false);
            
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', '¡Bienvenido a Dorasia! Tu cuenta ha sido creada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error en registro', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Error al crear la cuenta. Por favor intenta nuevamente.'])
                ->withInput($request->only('name', 'email'));
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        try {
            $userId = Auth::id();
            
            // Limpiar cookies manuales
            setcookie('user_logged_in', '', time() - 3600, '/', '', false, false);
            setcookie('user_auth_token', '', time() - 3600, '/', '', false, false);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('Usuario cerró sesión', ['user_id' => $userId]);

            return redirect()->route('home')->with('success', 'Has cerrado sesión exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en logout', ['error' => $e->getMessage()]);
            
            return redirect()->route('home')->with('error', 'Error al cerrar sesión');
        }
    }

    /**
     * Registro simple sin CSRF
     */
    public function registerSimple(Request $request)
    {
        try {
            // Log del intento
            Log::info('Intento de registro simple', [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Validación básica
            $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6|same:password',
            ], [
                'name.required' => 'El nombre es requerido',
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'email.required' => 'El email es requerido',
                'email.email' => 'Ingresa un email válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
                'password_confirmation.required' => 'Confirma tu contraseña',
                'password_confirmation.same' => 'Las contraseñas no coinciden',
            ]);

            // Crear usuario
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            Log::info('Usuario registrado exitosamente (simple)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            // Autenticar automáticamente
            Auth::login($user);
            
            // Configurar cookies manuales como respaldo
            setcookie('user_logged_in', $user->id, time() + (480 * 60), '/', '', false, false);
            setcookie('user_auth_token', hash('sha256', $user->id . $user->email), time() + (480 * 60), '/', '', false, false);
            
            // Regenerar la sesión para asegurar persistencia
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', '¡Bienvenido a Dorasia! Tu cuenta ha sido creada exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en registro simple', [
                'errors' => $e->errors(),
                'email' => $request->email ?? 'unknown'
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('name', 'email'));

        } catch (\Exception $e) {
            Log::error('Error en registro simple', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Error al crear la cuenta. Por favor intenta nuevamente.')
                ->withInput($request->only('name', 'email'));
        }
    }

    /**
     * Login simple sin CSRF
     */
    public function loginSimple(Request $request)
    {
        try {
            // Log del intento
            Log::info('Intento de login simple', [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Validación básica
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ], [
                'email.required' => 'El email es requerido',
                'email.email' => 'Ingresa un email válido',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            ]);

            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember');

            // Buscar usuario manualmente
            $user = User::where('email', $credentials['email'])->first();
            
            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Usar cookies manuales como respaldo a las sesiones de Laravel
                setcookie('user_logged_in', $user->id, time() + (480 * 60), '/', '', false, false);
                setcookie('user_auth_token', hash('sha256', $user->id . $user->email), time() + (480 * 60), '/', '', false, false);
                
                // Login normal de Laravel también
                Auth::login($user, $remember);
                
                Log::info('Usuario logueado exitosamente (simple)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'remember' => $remember
                ]);

                // Redirigir al admin manual que funciona con cookies
                return redirect('/admin-manual')->with('success', '¡Bienvenido de vuelta!');
            }

            // Login fallido
            Log::warning('Intento de login fallido (simple)', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return back()
                ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros'])
                ->withInput($request->only('email'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en login simple', [
                'errors' => $e->errors(),
                'email' => $request->email ?? 'unknown'
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));

        } catch (\Exception $e) {
            Log::error('Error en login simple', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Error interno. Por favor intenta nuevamente.')
                ->withInput($request->only('email'));
        }
    }

    /**
     * Verificar si el usuario está autenticado (para AJAX)
     */
    public function check()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user()->only(['id', 'name', 'email']) : null
        ]);
    }
}