<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceSessionCookie
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Forzar la cookie de sesión
        if (!$request->hasCookie('dorasia_session')) {
            $response->withCookie(cookie('dorasia_session', session()->getId(), 480));
        }
        
        return $response;
    }
}