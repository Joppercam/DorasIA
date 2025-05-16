<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DebugAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('profiles/*/edit')) {
            Log::info('Debug Auth - Profile Edit Request', [
                'is_auth' => Auth::check(),
                'user_id' => Auth::id(),
                'url' => $request->url(),
                'profile_id' => $request->route('profile'),
                'headers' => $request->headers->all(),
            ]);
        }
        
        return $next($request);
    }
}