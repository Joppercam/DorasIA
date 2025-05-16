@extends('layouts.app')

@section('title', 'Recomendaciones basadas en ' . $title->title . ' | Dorasia')

@section('content')
    <!-- Hero Section with Title Info -->
    <section class="relative">
        <div class="w-full h-[50vh] bg-cover bg-center" style="background-image: url('{{ $title->backdrop_url }}');">
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative h-full">
                <div class="flex items-center h-full">
                    <div class="w-full md:w-3/5 lg:w-1/2">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ $title->title }}</h1>
                        <div class="flex items-center mb-4">
                            @if($title->vote_average > 0)
                                <div class="flex items-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-white">{{ number_format($title->vote_average, 1) }}</span>
                                </div>
                            @endif
                            <span class="text-gray-300">{{ $romanticSubgenres[$subgenre]['name'] ?? 'Romance' }}</span>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="text-gray-300">{{ $title->formatted_category }}</span>
                        </div>
                        <p class="text-gray-300 mb-6 line-clamp-3">{{ $title->description }}</p>
                        <div class="flex space-x-3">
                            <a href="{{ route('titles.show', $title->slug) }}" class="bg-white text-gray-900 hover:bg-gray-200 px-5 py-2 rounded-md font-semibold transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Ver detalles
                            </a>
                            <a href="{{ route('titles.watch', $title->slug) }}" class="bg-pink-600 text-white hover:bg-pink-700 px-5 py-2 rounded-md font-semibold transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Ver ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recommended Titles Section -->
    <section class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">
                Porque te gusta "{{ $title->title }}" - {{ $romanticSubgenres[$subgenre]['name'] ?? 'Romance' }}
            </h2>
            
            @if($recommendedTitles->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($recommendedTitles as $recommended)
                        @if($recommended->id !== $title->id)
                            <x-netflix-modern-card :title="$recommended" />
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-gray-400">No se encontraron recomendaciones basadas en este título.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Similar By Origin Section -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">
                Más Doramas Románticos {{ $title->formatted_category }}s
            </h2>
            
            @if($similarByOrigin->count() > 0)
                <x-netflix-carousel :titles="$similarByOrigin" />
            @else
                <div class="text-center py-6">
                    <p class="text-gray-400">No se encontraron títulos similares por origen.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Explore Other Categories Section -->
    <section class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Explorar Otras Categorías</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                
                <a href="{{ route('romantic-dramas.subgenre', 'office_romance') }}" class="relative overflow-hidden rounded-lg group">
                    <img src="{{ asset('images/categories/k-drama.jpg') }}" alt="Office Romance" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">Romances de Oficina</h3>
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