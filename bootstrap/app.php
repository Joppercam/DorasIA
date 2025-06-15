<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'chilean.localization' => \App\Http\Middleware\ChileanLocalization::class,
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);
        
        // Auto-authenticate for development
        // UNCOMMENT FOR PRODUCTION TO ENABLE AUTO-LOGIN
        // $middleware->web(append: [
        //     \App\Http\Middleware\StaticAuth::class,
        // ]);
        
        // CSRF verification disabled for development
        // ENABLE FOR PRODUCTION SECURITY
        $middleware->validateCsrfTokens(except: [
            '*'  // Disable for ALL routes - CHANGE IN PRODUCTION
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
