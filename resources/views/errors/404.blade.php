@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        <h1 class="text-6xl font-bold text-red-600 mb-4">404</h1>
        <h2 class="text-3xl font-semibold mb-6">Página no encontrada</h2>
        <p class="text-gray-400 mb-8">
            Lo sentimos, la página que estás buscando no existe o ha sido movida.
        </p>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-md font-medium hover:bg-red-700 transition-colors">
                Volver al inicio
            </a>
            
            <div class="mt-4">
                <a href="{{ route('news.index') }}" class="text-red-600 hover:text-red-500 mr-4">
                    Ver todas las noticias
                </a>
                <a href="{{ route('catalog.index') }}" class="text-red-600 hover:text-red-500">
                    Explorar catálogo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection