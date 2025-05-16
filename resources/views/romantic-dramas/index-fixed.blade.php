@extends('layouts.app')

@section('title', 'Doramas Románticos | Dorasia')

@section('content')
    <!-- Hero Section -->
    <section class="relative">
        <x-netflix-carousel-hero :titles="$featuredTitles" />
    </section>

    <!-- Featured Titles Section -->
    <section class="py-6 bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-white mb-4">Títulos Destacados</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($featuredTitles as $title)
                    <div class="bg-gray-800 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300">
                        <a href="{{ route('titles.show', $title->slug) }}">
                            <img src="{{ $title->poster_url }}" 
                                 alt="{{ $title->title }}" 
                                 class="w-full h-64 object-cover"
                                 onerror="this.src='/posters/placeholder.jpg'">
                            <div class="p-3">
                                <h3 class="text-white font-medium">{{ $title->title }}</h3>
                                <p class="text-gray-400 text-sm">{{ $title->release_date?->year ?? 'N/A' }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Subgenre Sections -->
    @foreach($subgenreSections as $section)
        <section class="py-4">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $section['name'] }}
                    </h2>
                    <a href="{{ route('romantic-dramas.subgenre', $section['key']) }}" class="text-sm text-gray-400 hover:text-white transition">
                        Ver más <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($section['titles']->take(6) as $title)
                        <x-netflix-modern-card :title="$title" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <!-- Popular K-Dramas Section -->
    <section class="py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white">
                    Doramas Románticos Coreanos Populares
                </h2>
                <a href="{{ route('romantic-dramas.origin', 'korean') }}" class="text-sm text-gray-400 hover:text-white transition">
                    Ver más <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($popularKdramas->take(6) as $title)
                    <x-netflix-modern-card :title="$title" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular J-Dramas Section -->
    <section class="py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white">
                    Doramas Románticos Japoneses Populares
                </h2>
                <a href="{{ route('romantic-dramas.origin', 'japanese') }}" class="text-sm text-gray-400 hover:text-white transition">
                    Ver más <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($popularJdramas->take(6) as $title)
                    <x-netflix-modern-card :title="$title" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular C-Dramas Section (if any) -->
    @if($popularCdramas->count() > 0)
        <section class="py-4">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-white">
                        Doramas Románticos Chinos Populares
                    </h2>
                    <a href="{{ route('romantic-dramas.origin', 'chinese') }}" class="text-sm text-gray-400 hover:text-white transition">
                        Ver más <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($popularCdramas->take(6) as $title)
                        <x-netflix-modern-card :title="$title" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- New Romantic Dramas Section -->
    <section class="py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white">
                    Nuevos Doramas Románticos
                </h2>
                <a href="{{ route('romantic-dramas.search') }}?sort=newest" class="text-sm text-gray-400 hover:text-white transition">
                    Ver más <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($newRomanticDramas->take(6) as $title)
                    <x-netflix-modern-card :title="$title" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Browse by Category Banner -->
    <section class="py-8 bg-gradient-to-r from-pink-900 to-purple-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-4">Descubre Tu Tipo de Romance Favorito</h2>
                    <p class="text-gray-200 mb-6">
                        Explora nuestra colección de doramas románticos categorizados por subgéneros, 
                        desde romances históricos hasta comedias románticas, doramas de oficina 
                        y mucho más.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($romanticSubgenres as $key => $subgenre)
                            <a href="{{ route('romantic-dramas.subgenre', $key) }}" class="px-4 py-2 bg-pink-700 hover:bg-pink-600 rounded-full text-white transition">
                                {{ $subgenre['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-lg overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/categories/k-drama.jpg') }}" alt="Romance Categories" class="w-full h-auto"
                         onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug info
        console.log('Romantic dramas page loaded');
        
        // Highlight romantic dramas in the navigation
        const romanticDramasLink = document.querySelector('a[href="{{ route("romantic-dramas.index") }}"]');
        if (romanticDramasLink) {
            romanticDramasLink.classList.add('text-pink-500', 'border-b-2', 'border-pink-500');
        }
    });
</script>
@endpush