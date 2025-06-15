<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $key = 'api', int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $limiterKey = $key . ':' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($limiterKey);
            
            return response()->json([
                'error' => 'Demasiadas solicitudes. Intenta de nuevo en ' . $seconds . ' segundos.',
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($limiterKey, $decayMinutes * 60);
        
        $response = $next($request);
        
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($limiterKey, $maxAttempts));
        
        return $response;
    }
}