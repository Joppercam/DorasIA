<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
<<<<<<< HEAD
use Illuminate\Support\Facades\Gate;
use App\Models\Profile;
use App\Policies\ProfilePolicy;
=======
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe

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
<<<<<<< HEAD
        // Register policies
        Gate::policy(Profile::class, ProfilePolicy::class);
=======
        // Configurar el certificado SSL para todas las peticiones HTTP
        \Illuminate\Support\Facades\Http::globalOptions([
            'verify' => storage_path('app/private/cert/cacert-2025-02-25.pem')
        ]);
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}
