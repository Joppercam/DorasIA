<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasActiveProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $activeProfile = $user->getActiveProfile();
            
            if (!$activeProfile) {
                return redirect()->route('user-profiles.create')
                    ->with('info', 'Por favor, crea un perfil primero.');
            }
        }
        
        return $next($request);
    }
}
