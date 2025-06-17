@extends('layouts.app')

@section('title', 'Error del servidor - DORASIA')

@section('content')
<div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
    <div style="max-width: 600px;">
        <!-- Error Icon -->
        <div style="font-size: 6rem; margin-bottom: 1rem;">
            🚨
        </div>
        
        <!-- Error Title -->
        <h1 style="font-size: 3rem; font-weight: bold; color: white; margin-bottom: 1rem;">
            500
        </h1>
        
        <h2 style="font-size: 1.5rem; color: rgba(255,255,255,0.8); margin-bottom: 1.5rem;">
            Error interno del servidor
        </h2>
        
        <!-- Error Message -->
        <p style="font-size: 1.1rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 2rem;">
            Algo salió mal en nuestros servidores. Nuestro equipo técnico ha sido notificado 
            y está trabajando para solucionarlo lo antes posible.
        </p>
        
        <!-- Suggested Actions -->
        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: center;">
            <a href="{{ route('home') }}" 
               style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); 
                      color: white; 
                      padding: 1rem 2rem; 
                      border-radius: 8px; 
                      text-decoration: none; 
                      font-weight: 600; 
                      font-size: 1.1rem;
                      transition: all 0.3s ease;
                      display: inline-block;">
                🏠 Volver al Inicio
            </a>
            
            <button onclick="window.location.reload()" 
                    style="background: rgba(255,255,255,0.1); 
                           color: white; 
                           padding: 0.75rem 1.5rem; 
                           border: 1px solid rgba(255,255,255,0.2);
                           border-radius: 6px; 
                           cursor: pointer;
                           transition: background-color 0.3s ease;">
                🔄 Intentar de nuevo
            </button>
        </div>
        
        <!-- Error Details (if in debug mode) -->
        @if(config('app.debug') && isset($exception))
        <div style="margin-top: 2rem; padding: 1rem; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 8px; text-align: left;">
            <h3 style="color: #dc3545; margin-bottom: 0.5rem; font-size: 1rem;">Detalles del error (modo debug):</h3>
            <p style="color: rgba(255,255,255,0.8); font-family: monospace; font-size: 0.9rem; word-break: break-all;">
                {{ $exception->getMessage() ?? 'Error desconocido' }}
            </p>
        </div>
        @endif
    </div>
</div>
@endsection