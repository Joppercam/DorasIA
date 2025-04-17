<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ModeratorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isModerator()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Moderator access required.'], 403);
            }
            
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área.');
        }

        return $next($request);
    }
}