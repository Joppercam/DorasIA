<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar el certificado SSL para todas las peticiones HTTP
        \Illuminate\Support\Facades\Http::globalOptions([
            'verify' => storage_path('app/private/cert/cacert-2025-02-25.pem')
        ]);
    }
}
