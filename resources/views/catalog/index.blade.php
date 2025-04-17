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