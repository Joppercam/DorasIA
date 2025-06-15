<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StaticAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Autenticar automáticamente al primer usuario para bypass del problema de sesiones
        if (!Auth::check()) {
            $user = User::first();
            if ($user) {
                Auth::login($user);
            }
        }
        
        return $next($request);
    }
}