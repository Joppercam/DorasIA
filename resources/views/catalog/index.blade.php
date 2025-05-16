<<<<<<< HEAD
<x-app-layout>
    <x-slot name="title">Catálogo</x-slot>
    
    <!-- Header del catálogo -->
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold">Catálogo completo</h1>
            
            @if(request('search'))
                <p class="mt-2 text-gray-400">Resultados para: "{{ request('search') }}"</p>
            @elseif(request('category'))
                <p class="mt-2 text-gray-400">Filtrando por categoría: {{ $categories->where('id', request('category'))->first()?->name ?? 'Desconocida' }}</p>
            @elseif(request('genre'))
                <p class="mt-2 text-gray-400">Filtrando por género: {{ $genres->where('id', request('genre'))->first()?->name ?? 'Desconocido' }}</p>
            @elseif(request('country'))
                <p class="mt-2 text-gray-400">Filtrando por país: {{ request('country') }}</p>
            @endif
        </div>
    </div>
    
    <!-- Filtros rápidos de país -->
    <div class="bg-gray-950 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-3 overflow-x-auto pb-2">
                <span class="text-gray-400 text-sm whitespace-nowrap">Filtrar por país:</span>
                
                @php
                    $countries = [
                        'Corea del Sur' => '🇰🇷',
                        'Japón' => '🇯🇵',
                        'China' => '🇨🇳',
                        'Tailandia' => '🇹🇭',
                        'Taiwán' => '🇹🇼',
                        'Indonesia' => '🇮🇩',
                        'Filipinas' => '🇵🇭',
                        'Vietnam' => '🇻🇳'
                    ];
                    
                    $currentParams = request()->query();
                @endphp
                
                <!-- Botón "Todos" -->
                @php
                    $allParams = $currentParams;
                    unset($allParams['country']);
                @endphp
                <a href="{{ route('catalog.index', $allParams) }}"
                   class="flex items-center space-x-2 px-3 py-1.5 rounded-full text-sm transition whitespace-nowrap
                          {{ !request('country') ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    <span>🌏</span>
                    <span>Todos</span>
                </a>
                
                @foreach($countries as $country => $flag)
                    @php
                        $countryParams = array_merge($currentParams, ['country' => $country]);
                    @endphp
                    <a href="{{ route('catalog.index', $countryParams) }}"
                       class="flex items-center space-x-1 px-3 py-1.5 rounded-full text-sm transition whitespace-nowrap
                              {{ request('country') == $country ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <span>{{ $flag }}</span>
                        <span>{{ $country }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Filtros y resultados -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Sidebar con filtros (versión desktop) -->
            <div class="hidden md:block md:col-span-3 bg-gray-900 rounded-lg p-4 h-min sticky top-20">
                <h2 class="text-xl font-bold mb-4">Filtros</h2>
                
                <form action="{{ route('catalog.index') }}" method="GET">
                    <!-- Preservar la búsqueda actual si existe -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <!-- Preservar el país actual si existe -->
                    @if(request('country'))
                        <input type="hidden" name="country" value="{{ request('country') }}">
                    @endif
                    
                    <!-- Tipo -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Tipo</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input id="type-all" type="radio" name="type" value="" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ !request('type') ? 'checked' : '' }}>
                                <label for="type-all" class="ml-2 text-sm text-gray-300">Todos</label>
                            </div>
                            <div class="flex items-center">
                                <input id="type-movie" type="radio" name="type" value="movie" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'movie' ? 'checked' : '' }}>
                                <label for="type-movie" class="ml-2 text-sm text-gray-300">Películas</label>
                            </div>
                            <div class="flex items-center">
                                <input id="type-series" type="radio" name="type" value="series" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'series' ? 'checked' : '' }}>
                                <label for="type-series" class="ml-2 text-sm text-gray-300">Series</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categorías -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Categorías</h3>
                        <select name="category" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Géneros -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Géneros</h3>
                        <select name="genre" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Todos los géneros</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Ordenar por -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Ordenar por</h3>
                        <select name="sort" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Alfabético</option>
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full">
                            Aplicar filtros
                        </button>
                        <a href="{{ route('catalog.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Filtros móviles (toggle) -->
            <div class="md:hidden w-full mb-4">
                <div x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center justify-between w-full bg-gray-900 rounded-lg p-4">
                        <span class="font-semibold">Filtros</span>
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" class="mt-2 bg-gray-900 rounded-lg p-4">
                        <form action="{{ route('catalog.index') }}" method="GET">
                            <!-- Preservar la búsqueda actual si existe -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <!-- Preservar el país actual si existe -->
                            @if(request('country'))
                                <input type="hidden" name="country" value="{{ request('country') }}">
                            @endif
                            
                            <!-- Tipo -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Tipo</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input id="mobile-type-all" type="radio" name="type" value="" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ !request('type') ? 'checked' : '' }}>
                                        <label for="mobile-type-all" class="ml-2 text-sm text-gray-300">Todos</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="mobile-type-movie" type="radio" name="type" value="movie" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'movie' ? 'checked' : '' }}>
                                        <label for="mobile-type-movie" class="ml-2 text-sm text-gray-300">Películas</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="mobile-type-series" type="radio" name="type" value="series" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'series' ? 'checked' : '' }}>
                                        <label for="mobile-type-series" class="ml-2 text-sm text-gray-300">Series</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Categorías -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Categorías</h3>
                                <select name="category" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Géneros -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Géneros</h3>
                                <select name="genre" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="">Todos los géneros</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                            {{ $genre->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Ordenar por -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Ordenar por</h3>
                                <select name="sort" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Alfabético</option>
                                </select>
                            </div>
                            
                            <!-- Botones -->
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full">
                                    Aplicar filtros
                                </button>
                                <a href="{{ route('catalog.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Grid de títulos -->
            <div class="md:col-span-9">
                @if($titles->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($titles as $title)
                            <x-netflix-card 
                                :title="$title" 
                                :watchHistory="isset($titleWatchHistory[$title->id]) ? $titleWatchHistory[$title->id] : null" 
                            />
                        @endforeach
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-8">
                        {{ $titles->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="bg-gray-900 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-xl font-semibold">No se encontraron resultados</h3>
                        <p class="mt-1 text-gray-400">Prueba a ajustar los filtros o realizar otra búsqueda.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                            Ver todo el catálogo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
=======
@extends('layouts.app')

@section('title', $pageTitle)

@section('meta_description', $pageDescription)

@section('hero')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">{{ $pageTitle }}</h1>
        <p class="text-gray-300 text-lg max-w-3xl">{{ $pageDescription }}</p>
        
        @if($filters && isset($activeFilters) && count($activeFilters) > 0)
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach($activeFilters as $key => $value)
                    <div class="inline-flex items-center bg-indigo-800 rounded-full px-3 py-1">
                        <span class="text-xs text-gray-300 mr-1">{{ ucfirst($key) }}:</span>
                        <span class="text-sm text-white">{{ $value }}</span>
                        <a href="{{ route('catalog.index', array_merge($filters, [$key => null])) }}" class="ml-2 text-gray-300 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                @endforeach
                
                @if(count($activeFilters) > 1)
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center bg-gray-700 hover:bg-gray-600 rounded-full px-3 py-1 text-sm text-white transition">
                        Limpiar filtros
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="flex flex-col md:flex-row md:space-x-8">
    <!-- Filter Sidebar -->
    <div class="w-full md:w-1/4 lg:w-1/5 mb-6 md:mb-0">
        <div class="bg-gray-800 rounded-lg p-5">
            <h2 class="text-xl font-semibold mb-4">Filtros</h2>
            
            <form action="{{ route('catalog.index') }}" method="GET" class="space-y-6">
                <!-- Hidden inputs for current filters -->
                @foreach($filters as $key => $value)
                    @if($value && !in_array($key, ['country', 'genre', 'year', 'platform', 'sort']))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                
                <!-- Country Filter -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">País</label>
                    <select name="country" class="bg-gray-700 text-white rounded-lg w-full px-3 py-2 focus:ring focus:ring-indigo-500 focus:outline-none">
                        <option value="">Todos los países</option>
                        <option value="korea" {{ isset($filters['country']) && $filters['country'] == 'korea' ? 'selected' : '' }}>Corea</option>
                        <option value="japan" {{ isset($filters['country']) && $filters['country'] == 'japan' ? 'selected' : '' }}>Japón</option>
                        <option value="china" {{ isset($filters['country']) && $filters['country'] == 'china' ? 'selected' : '' }}>China</option>
                        <option value="thailand" {{ isset($filters['country']) && $filters['country'] == 'thailand' ? 'selected' : '' }}>Tailandia</option>
                        <option value="taiwan" {{ isset($filters['country']) && $filters['country'] == 'taiwan' ? 'selected' : '' }}>Taiwán</option>
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Tipo</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="type" value="" class="text-indigo-500 focus:ring-indigo-500" {{ !isset($filters['type']) || $filters['type'] == '' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-300">Todos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="movie" class="text-indigo-500 focus:ring-indigo-500" {{ isset($filters['type']) && $filters['type'] == 'movie' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-300">Películas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="series" class="text-indigo-500 focus:ring-indigo-500" {{ isset($filters['type']) && $filters['type'] == 'series' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-300">Series</span>
                        </label>
                    </div>
                </div>
                
                <!-- Genre Filter -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Género</label>
                    <select name="genre" class="bg-gray-700 text-white rounded-lg w-full px-3 py-2 focus:ring focus:ring-indigo-500 focus:outline-none">
                        <option value="">Todos los géneros</option>
                        @foreach($genresList as $genreItem)
                            <option value="{{ $genreItem->slug }}" {{ isset($filters['genre']) && $filters['genre'] == $genreItem->slug ? 'selected' : '' }}>{{ $genreItem->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Year Filter -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Año</label>
                    <select name="year" class="bg-gray-700 text-white rounded-lg w-full px-3 py-2 focus:ring focus:ring-indigo-500 focus:outline-none">
                        <option value="">Todos los años</option>
                        @foreach($yearsList as $year)
                            <option value="{{ $year }}" {{ isset($filters['year']) && $filters['year'] == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Platform Filter -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Plataforma</label>
                    <select name="platform" class="bg-gray-700 text-white rounded-lg w-full px-3 py-2 focus:ring focus:ring-indigo-500 focus:outline-none">
                        <option value="">Todas las plataformas</option>
                        @foreach($platformsList as $platformItem)
                            <option value="{{ $platformItem->slug }}" {{ isset($filters['platform']) && $filters['platform'] == $platformItem->slug ? 'selected' : '' }}>{{ $platformItem->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sort Order -->
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Ordenar por</label>
                    <select name="sort" class="bg-gray-700 text-white rounded-lg w-full px-3 py-2 focus:ring focus:ring-indigo-500 focus:outline-none">
                        <option value="popularity" {{ isset($filters['sort']) && $filters['sort'] == 'popularity' ? 'selected' : '' }}>Popularidad</option>
                        <option value="newest" {{ isset($filters['sort']) && $filters['sort'] == 'newest' ? 'selected' : '' }}>Más recientes</option>
                        <option value="oldest" {{ isset($filters['sort']) && $filters['sort'] == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                        <option value="rating" {{ isset($filters['sort']) && $filters['sort'] == 'rating' ? 'selected' : '' }}>Mejor valorados</option>
                        <option value="title_asc" {{ isset($filters['sort']) && $filters['sort'] == 'title_asc' ? 'selected' : '' }}>Título A-Z</option>
                        <option value="title_desc" {{ isset($filters['sort']) && $filters['sort'] == 'title_desc' ? 'selected' : '' }}>Título Z-A</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg px-4 py-2.5 transition">
                    Aplicar filtros
                </button>
            </form>
        </div>
    </div>
    
    <!-- Content Grid -->
    <div class="w-full md:w-3/4 lg:w-4/5">
        @if(count($content) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($content as $item)
                    <div class="content-card bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition group relative">
                        <!-- Poster Image -->
                        <div class="relative aspect-[2/3] overflow-hidden">
                            <img 
                                src="{{ $item->poster_path ?? asset('images/poster-placeholder.jpg') }}" 
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                                loading="lazy"
                            >
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                <!-- Platform Badges (if available) -->
                                @if(!empty($item->available_on) && count($item->available_on) > 0)
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($item->available_on as $platform)
                                            <img 
                                                src="{{ $platform->logo_url }}" 
                                                alt="{{ $platform->name }}" 
                                                class="h-5 w-auto rounded"
                                                title="{{ $platform->name }}"
                                            >
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a 
                                        href="{{ route($item->type === 'movie' ? 'movies.show' : 'series.show', $item->id) }}" 
                                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded py-1.5 text-center transition"
                                    >
                                        Detalles
                                    </a>
                                    <button 
                                        @click.stop="$store.watchlist.toggle('{{ $item->id }}', '{{ $item->type }}')" 
                                        class="bg-gray-700 hover:bg-gray-600 text-white w-9 rounded flex items-center justify-center transition"
                                        x-data
                                        x-bind:class="$store.watchlist.exists('{{ $item->id }}') ? 'bg-gray-600' : 'bg-gray-700'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-bind:stroke-width="$store.watchlist.exists('{{ $item->id }}') ? 2.5 : 1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" x-show="!$store.watchlist.exists('{{ $item->id }}')" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" x-show="$store.watchlist.exists('{{ $item->id }}')" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Info -->
                        <div class="p-4">
                            <!-- Badges -->
                            <div class="flex items-center space-x-2 mb-2">
                                @if($item->origin_country)
                                    <span class="text-xs font-semibold px-1.5 py-0.5 bg-indigo-600/70 rounded text-white">
                                        {{ $item->origin_country }}
                                    </span>
                                @endif
                                
                                @if($item->year)
                                    <span class="text-xs text-gray-400">{{ $item->year }}</span>
                                @endif
                                
                                @if($item->rating)
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="ml-1 text-xs text-gray-400">{{ $item->rating }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Title -->
                            <h3 class="font-semibold text-white truncate">{{ $item->title }}</h3>
                            
                            <!-- Genres -->
                            @if(!empty($item->genres) && count($item->genres) > 0)
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ collect($item->genres)->pluck('name')->join(', ') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-10">
                {{ $content->links() }}
            </div>
        @else
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">No se encontraron resultados</h3>
                <p class="text-gray-400 mb-4">No hay contenido que coincida con los filtros seleccionados.</p>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Limpiar filtros
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
