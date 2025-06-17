<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login.simple')->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        // Verificar si el usuario es administrador
        if (!Auth::user()->is_admin) {
            abort(403, 'No tienes permisos para acceder al panel de administración.');
        }

        return $next($request);
    }
}
