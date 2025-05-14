<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartir variables globales con todas las vistas
        View::composer('*', function ($view) {
            // Solo agregar estas variables si no están ya definidas en la vista
            if (!$view->offsetExists('metaTitle')) {
                $view->with('metaTitle', config('app.name', 'Dorasia'));
            }
            
            if (!$view->offsetExists('metaDescription')) {
                $view->with('metaDescription', 'Dorasia - La mejor plataforma de streaming de contenido asiático: K-Dramas, C-Dramas, J-Dramas y películas asiáticas.');
            }
            
            if (!$view->offsetExists('metaImage')) {
                $view->with('metaImage', asset('images/heroes/hero-bg.jpg'));
            }
        });
    }
}