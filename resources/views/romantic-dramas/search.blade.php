@extends('layouts.app')

@section('title', 'Búsqueda Avanzada de Doramas Románticos | Dorasia')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-12 bg-gradient-to-r from-pink-900 to-purple-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-white mb-4">Búsqueda Avanzada de Doramas Románticos</h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Encuentra el dorama romántico perfecto con nuestras opciones de búsqueda avanzada.
                </p>
            </div>
        </div>
    </section>

    <!-- Search Form -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('romantic-dramas.search') }}" method="GET" class="bg-gray-800 p-6 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Buscar por título</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="bg-gray-700 text-white rounded-md border-gray-600 w-full py-2 px-3" placeholder="Buscar por título...">
                    </div>
                    
                    <div>
                        <label for="origin" class="block text-sm font-medium text-gray-400 mb-1">Origen</label>
                        <select name="origin" id="origin" class="bg-gray-700 text-white rounded-md border-gray-600 w-full py-2 px-3">
                            <option value="all" @if(!request('origin') || request('origin') == 'all') selected @endif>Todos</option>
                            <option value="korean" @if(request('origin') == 'korean') selected @endif>Corea</option>
                            <option value="japanese" @if(request('origin') == 'japanese') selected @endif>Japón</option>
                            <option value="chinese" @if(request('origin') == 'chinese') selected @endif>China</option>
                            <option value="thai" @if(request('origin') == 'thai') selected @endif>Tailandia</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="subgenre" class="block text-sm font-medium text-gray-400 mb-1">Subgénero</label>
                        <select name="subgenre" id="subgenre" class="bg-gray-700 text-white rounded-md border-gray-600 w-full py-2 px-3">
                            <option value="all" @if(!request('subgenre') || request('subgenre') == 'all') selected @endif>Todos</option>
                            @foreach($romanticSubgenres as $key => $subgenre)
                                <option value="{{ $key }}" @if(request('subgenre') == $key) selected @endif>{{ $subgenre['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-400 mb-1">Plataforma</label>
                        <select name="platform" id="platform" class="bg-gray-700 text-white rounded-md border-gray-600 w-full py-2 px-3">
                            <option value="all" @if(!request('platform') || request('platform') == 'all') selected @endif>Todas</option>
                            @foreach($streamingPlatforms as $key => $platform)
                                <option value="{{ $key }}" @if(request('platform') == $key) selected @endif>{{ $platform }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-400 mb-1">Ordenar por</label>
                        <select name="sort" id="sort" class="bg-gray-700 text-white rounded-md border-gray-600 w-full py-2 px-3">
                            <option value="popularity" @if(!request('sort') || request('sort') == 'popularity') selected @endif>Popularidad</option>
                            <option value="newest" @if(request('sort') == 'newest') selected @endif>Más reciente</option>
                            <option value="rating" @if(request('sort') == 'rating') selected @endif>Mejor calificación</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="bg-pink-700 hover:bg-pink-600 text-white py-2 px-6 rounded-md transition w-full">
                            Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <section class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-white">Resultados de búsqueda</h2>
                <p class="text-gray-400">{{ $titles->total() }} resultados encontrados</p>
            </div>
            
            @if($titles->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($titles as $title)
                        <x-netflix-modern-card :title="$title" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $titles->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-gray-800 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-xl text-gray-400 mb-4">No se encontraron resultados</h3>
                    <p class="text-gray-500 mb-6">Intente con otros términos de búsqueda o filtros</p>
                    <a href="{{ route('romantic-dramas.index') }}" class="inline-block bg-pink-700 hover:bg-pink-600 text-white py-2 px-6 rounded-md transition">
                        Ver todos los doramas románticos
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Categorías Populares</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('romantic-dramas.origin', 'korean') }}" class="relative overflow-hidden rounded-lg group">
                    <img src="{{ asset('images/categories/k-drama.jpg') }}" alt="K-Drama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">K-Dramas Románticos</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.subgenre', 'historical_romance') }}" class="relative overflow-hidden rounded-lg group">
                    <img src="{{ asset('images/categories/c-drama.jpg') }}" alt="Historical Romance" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">Romances Históricos</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.subgenre', 'romantic_comedy') }}" class="relative overflow-hidden rounded-lg group">
                    <img src="{{ asset('images/categories/j-drama.jpg') }}" alt="Romantic Comedy" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">Comedias Románticas</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.subgenre', 'melodrama') }}" class="relative overflow-hidden rounded-lg group">
                    <img src="{{ asset('images/categories/dorasia-originals.jpg') }}" alt="Melodrama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">Melodramas</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection