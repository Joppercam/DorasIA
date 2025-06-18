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
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'manual.session.auth' => \App\Http\Middleware\ManualSessionAuth::class,
        ]);
        
        // Manual session auth disabled temporarily to reduce resource usage
        // $middleware->web(prepend: [
        //     \App\Http\Middleware\ManualSessionAuth::class,
        // ]);
        
        // CSRF verification enabled with specific exceptions
        $middleware->validateCsrfTokens(except: [
            // API routes that need CSRF exemption
            'api/*',
            'auth/google/callback',
            // Simple registration and login routes without CSRF - FUNCIONA SIEMPRE
            'registro',
            'registro-process',
            'login-process',
            // Rating and interaction routes (AJAX)
            'series/*/rate',
            'movies/*/rate',
            'series/*/watchlist',
            'movies/*/watchlist',
            'series/*/watched',
            'series/*/comments',
            'actors/*/comments',
            'episodes/*/watched',
            'episodes/*/progress',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
