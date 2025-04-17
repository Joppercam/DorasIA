@extends('layouts.app')

@section('title', 'Inicio')

@section('meta_description', 'Descubre el mejor contenido asiático: doramas coreanos, series chinas y japonesas, anime y películas asiáticas.')

@section('hero')
<div class="relative bg-black">
    <!-- Hero Slider -->
    <div class="hero-slider" x-data="{ activeSlide: 0 }" x-init="setInterval(() => { activeSlide = activeSlide === {{ count($featuredContent) - 1 }} ? 0 : activeSlide + 1 }, 8000)">
        @foreach($featuredContent as $index => $item)
            <div 
                x-show="activeSlide === {{ $index }}"
                x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 transform scale-105"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative h-[60vh] md:h-[80vh] bg-cover bg-center"
                style="background-image: url('{{ $item->backdrop_path }}')"
            >
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
                
                <div class="container mx-auto px-4 h-full flex items-end pb-16 md:pb-24 relative z-10">
                    <div class="max-w-2xl">
                        <div class="mb-4 flex items-center space-x-2">
                            @if($item->origin_country)
                                <span class="text-xs font-semibold px-2 py-1 bg-indigo-600 rounded-md">{{ $item->origin_country }}</span>
                            @endif
                            
                            @if($item->type == 'movie')
                                <span class="text-xs font-semibold px-2 py-1 bg-purple-600 rounded-md">Película</span>
                            @else
                                <span class="text-xs font-semibold px-2 py-1 bg-pink-600 rounded-md">Serie</span>
                            @endif
                            
                            @if($item->release_date)
                                <span class="text-gray-300 text-sm">{{ date('Y', strtotime($item->release_date)) }}</span>
                            @endif
                        </div>
                        
                        <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">{{ $item->title }}</h1>
                        
                        <p class="text-gray-300 text-lg mb-6 line-clamp-3">{{ $item->overview }}</p>
                        
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('movies.show', $item->id) }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-lg hover:shadow-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Ver detalles
                            </a>
                            
                            <button 
                                @click="$store.watchlist.toggle('{{ $item->id }}', '{{ $item->type }}')"
                                class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg transition"
                                x-data
                                x-bind:class="$store.watchlist.exists('{{ $item->id }}') ? 'bg-gray-700' : 'bg-gray-800'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-bind:stroke-width="$store.watchlist.exists('{{ $item->id }}') ? 2.5 : 1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" x-show="!$store.watchlist.exists('{{ $item->id }}')" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" x-show="$store.watchlist.exists('{{ $item->id }}')" />
                                </svg>
                                <span x-text="$store.watchlist.exists('{{ $item->id }}') ? 'Añadido' : 'Mi Lista'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Slider Controls -->
        <div class="absolute bottom-6 left-0 right-0 flex justify-center space-x-3 z-10">
            @foreach($featuredContent as $index => $item)
                <button 
                    @click="activeSlide = {{ $index }}"
                    class="w-3 h-3 rounded-full transition-all duration-300"
                    :class="{ 'bg-white': activeSlide === {{ $index }}, 'bg-gray-600': activeSlide !== {{ $index }} }"
                ></button>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('content')
    <!-- Tendencias -->
    <section class="mb-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Tendencias</h2>
            <a href="{{ route('trending') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver todo</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($trendingContent as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Series Coreanas -->
    <section class="mb-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Series Coreanas</h2>
            <a href="{{ route('catalog.index', ['country' => 'korea', 'type' => 'series']) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver todo</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($koreanSeries as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Películas Asiáticas -->
    <section class="mb-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Películas Asiáticas</h2>
            <a href="{{ route('catalog.movies') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver todo</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($asianMovies as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Series Japonesas -->
    <section class="mb-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Series Japonesas</h2>
            <a href="{{ route('catalog.index', ['country' => 'japan', 'type' => 'series']) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver todo</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($japaneseSeries as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Series Chinas -->
    <section class="mb-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold">Series Chinas</h2>
            <a href="{{ route('catalog.index', ['country' => 'china', 'type' => 'series']) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver todo</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($chineseSeries as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Géneros populares -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Explora por Géneros</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($genres as $genre)
                <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}" class="block bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-center hover:from-indigo-600 hover:to-purple-700 transition shadow-lg hover:shadow-xl">
                    <span class="text-white font-medium">{{ $genre->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
    
    <!-- Plataformas disponibles -->
    <section>
        <h2 class="text-2xl font-bold mb-6">Plataformas Disponibles</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
            @foreach($platforms as $platform)
                <a href="{{ route('platforms.show', $platform->slug) }}" class="flex flex-col items-center opacity-70 hover:opacity-100 transition">
                    <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="h-16 object-contain mb-3">
                    <span class="text-sm text-gray-300">{{ $platform->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endsection