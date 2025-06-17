<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ManualSessionAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for our manual cookies (try both Laravel and PHP methods)
        $userId = $request->cookie('user_logged_in') ?? $_COOKIE['user_logged_in'] ?? null;
        $authToken = $request->cookie('user_auth_token') ?? $_COOKIE['user_auth_token'] ?? null;
        
        // Note: Using $_COOKIE because Laravel's request->cookie() doesn't read manual cookies properly
        
        if ($userId && $authToken && !Auth::check()) {
            $user = User::find($userId);
            if ($user && hash('sha256', $user->id . $user->email) === $authToken) {
                // Log in the user for this request
                Auth::login($user);
            }
        }
        
        return $next($request);
    }
}