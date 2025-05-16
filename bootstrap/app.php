<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
<<<<<<< HEAD
        api: __DIR__.'/../routes/api.php',
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
<<<<<<< HEAD
        $middleware->alias([
            'has.profile' => \App\Http\Middleware\HasActiveProfile::class,
            'has.active.profile' => \App\Http\Middleware\HasActiveProfile::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
    ])
    ->create();
=======
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
