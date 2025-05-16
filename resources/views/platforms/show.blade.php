@extends('layouts.app')

@section('title', $platform->name . ' - Disponibilidad')

@section('meta_description', 'Descubre todo el contenido asiático disponible en ' . $platform->name . ': películas, doramas, series y anime de China, Japón y Corea.')

@section('hero')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-48 h-48 flex-shrink-0 bg-white rounded-xl p-4 flex items-center justify-center">
                <img 
                    src="{{ $platform->logo_path ? asset('storage/' . $platform->logo_path) : asset('images/platform-placeholder.png') }}" 
                    alt="{{ $platform->name }}" 
                    class="max-w-full max-h-full object-contain"
                >
            </div>
            
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $platform->name }}</h1>
                <p class="text-gray-300 text-lg max-w-3xl mb-6">
                    {{ $platform->description ?? 'Explora todo el contenido asiático disponible en ' . $platform->name . ': películas, doramas, series y anime de China, Japón y Corea.' }}
                </p>
                
                @if($platform->website_url)
                <a 
                    href="{{ $platform->website_url }}" 
                    target="_blank"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                    </svg>
                    Visitar sitio oficial
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Películas disponibles -->
    @if($movies->isNotEmpty())
    <section class="mb-16">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Películas disponibles en {{ $platform->name }}</h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($movies as $movie)
                <div class="content-card bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition group relative">
                    <!-- Poster Image -->
                    <div class="relative aspect-[2/3] overflow-hidden">
                        <img 
                            src="{{ $movie->poster_path }}" 
                            alt="{{ $movie->title }}"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                            loading="lazy"
                        >
                        
                        <!-- Availability Badge -->
                        @if(isset($movie->availability->type))
                            <div class="absolute top-2 right-2 rounded-full p-1.5 
                                    {{ $movie->availability->type == 'subscription' ? 'bg-blue-600' : 
                                      ($movie->availability->type == 'rent' ? 'bg-yellow-600' : 
                                      ($movie->availability->type == 'purchase' ? 'bg-green-600' : 'bg-gray-600')) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <div class="flex space-x-2">
                                <a 
                                    href="{{ $movie->link }}" 
                                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded py-1.5 text-center transition"
                                >
                                    Detalles
                                </a>
                                
                                @if(isset($movie->availability->url))
                                <a 
                                    href="{{ $movie->availability->url }}" 
                                    target="_blank"
                                    class="bg-green-600 hover:bg-green-700 text-white w-9 rounded flex items-center justify-center transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Info -->
                    <div class="p-4">
                        <!-- Badges -->
                        <div class="flex items-center space-x-2 mb-2">
                            @if($movie->origin_country)
                                <span class="text-xs font-semibold px-1.5 py-0.5 bg-indigo-600/70 rounded text-white">
                                    {{ $movie->origin_country }}
                                </span>
                            @endif
                            
                            @if($movie->year)
                                <span class="text-xs text-gray-400">{{ $movie->year }}</span>
                            @endif
                            
                            @if($movie->vote_average)
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-xs text-gray-400">{{ number_format($movie->vote_average, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Title -->
                        <h3 class="font-semibold text-white truncate">{{ $movie->title }}</h3>
                        
                        <!-- Availability Type/Price -->
                        @if(isset($movie->availability->type))
                            <p class="text-xs text-gray-400 mt-1">
                                @if($movie->availability->type == 'subscription')
                                    <span class="text-blue-400">Suscripción</span>
                                @elseif($movie->availability->type == 'rent')
                                    <span class="text-yellow-400">Alquiler{{ $movie->availability->price ? ': '.$movie->availability->price.'€' : '' }}</span>
                                @elseif($movie->availability->type == 'purchase')
                                    <span class="text-green-400">Compra{{ $movie->availability->price ? ': '.$movie->availability->price.'€' : '' }}</span>
                                @else
                                    <span>{{ ucfirst($movie->availability->type) }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
    
    <!-- Series disponibles -->
    @if($tvShows->isNotEmpty())
    <section>
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Series disponibles en {{ $platform->name }}</h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($tvShows as $tvShow)
                <div class="content-card bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition group relative">
                    <!-- Poster Image -->
                    <div class="relative aspect-[2/3] overflow-hidden">
                        <img 
                            src="{{ $tvShow->poster_path }}" 
                            alt="{{ $tvShow->title }}"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                            loading="lazy"
                        >
                        
                        <!-- Availability Badge -->
                        @if(isset($tvShow->availability->type))
                            <div class="absolute top-2 right-2 rounded-full p-1.5 
                                    {{ $tvShow->availability->type == 'subscription' ? 'bg-blue-600' : 
                                      ($tvShow->availability->type == 'rent' ? 'bg-yellow-600' : 
                                      ($tvShow->availability->type == 'purchase' ? 'bg-green-600' : 'bg-gray-600')) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <div class="flex space-x-2">
                                <a 
                                    href="{{ $tvShow->link }}" 
                                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded py-1.5 text-center transition"
                                >
                                    Detalles
                                </a>
                                
                                @if(isset($tvShow->availability->url))
                                <a 
                                    href="{{ $tvShow->availability->url }}" 
                                    target="_blank"
                                    class="bg-green-600 hover:bg-green-700 text-white w-9 rounded flex items-center justify-center transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Info -->
                    <div class="p-4">
                        <!-- Badges -->
                        <div class="flex items-center space-x-2 mb-2">
                            @if($tvShow->origin_country)
                                <span class="text-xs font-semibold px-1.5 py-0.5 bg-indigo-600/70 rounded text-white">
                                    {{ $tvShow->origin_country }}
                                </span>
                            @endif
                            
                            @if($tvShow->year)
                                <span class="text-xs text-gray-400">{{ $tvShow->year }}</span>
                            @endif
                            
                            @if($tvShow->vote_average)
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-xs text-gray-400">{{ number_format($tvShow->vote_average, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Title -->
                        <h3 class="font-semibold text-white truncate">{{ $tvShow->title }}</h3>
                        
                        <!-- Availability Type/Price -->
                        @if(isset($tvShow->availability->type))
                            <p class="text-xs text-gray-400 mt-1">
                                @if($tvShow->availability->type == 'subscription')
                                    <span class="text-blue-400">Suscripción</span>
                                @elseif($tvShow->availability->type == 'rent')
                                    <span class="text-yellow-400">Alquiler{{ $tvShow->availability->price ? ': '.$tvShow->availability->price.'€' : '' }}</span>
                                @elseif($tvShow->availability->type == 'purchase')
                                    <span class="text-green-400">Compra{{ $tvShow->availability->price ? ': '.$tvShow->availability->price.'€' : '' }}</span>
                                @else
                                    <span>{{ ucfirst($tvShow->availability->type) }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
    
    @if($movies->isEmpty() && $tvShows->isEmpty())
        <div class="bg-gray-800 rounded-lg p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
            </svg>
            <h3 class="text-xl font-semibold mb-2">No hay contenido disponible</h3>
            <p class="text-gray-400 mb-6">Actualmente no hay contenido asiático disponible en {{ $platform->name }} en nuestra base de datos.</p>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Explorar catálogo completo
            </a>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    /* Estilos específicos para la página de plataforma */
    .content-card {
        height: 100%;
    }
    
    .content-card .aspect-\[2\/3\] {
        height: 0;
        padding-bottom: 150%; /* Aspect ratio 2:3 */
    }
    
    @media (max-width: 640px) {
        .content-card .p-4 {
            padding: 0.75rem;
        }
    }
</style>
@endsection