<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChileanLocalization
{
    public function handle(Request $request, Closure $next)
    {
        // Detectar si el usuario es de Chile
        $isChilean = $this->detectChileanUser($request);
        
        if ($isChilean) {
            // Establecer configuraciones específicas para Chile
            App::setLocale('es_CL');
            Session::put('user_country', 'CL');
            Session::put('is_chilean', true);
            
            // Configurar timezone
            config(['app.timezone' => 'America/Santiago']);
        }
        
        return $next($request);
    }
    
    private function detectChileanUser(Request $request): bool
    {
        // Verificar IP (simplificado - en producción usar servicio geolocation)
        $userAgent = $request->header('User-Agent');
        $acceptLanguage = $request->header('Accept-Language');
        
        // Detectar por idioma preferido
        if (str_contains($acceptLanguage, 'es-CL') || str_contains($acceptLanguage, 'es_CL')) {
            return true;
        }
        
        // Verificar si ya se detectó previamente
        if (Session::get('is_chilean')) {
            return true;
        }
        
        // Por defecto asumir que es chileno para esta demo
        return true;
    }
}