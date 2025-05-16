@extends('layouts.app')

@section('content')
    <!-- Hero existente -->
    <x-hero-section :featured-title="$featuredTitle" />
    
    <!-- Sección de lo más comentado (NUEVA) -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Lo más discutido -->
                <div class="lg:col-span-2">
                    <x-most-discussed-titles :period="request('period', 'week')" />
                </div>
                
                <!-- Comentarios recientes -->
                <div>
                    <x-recent-comments :limit="5" />
                </div>
            </div>
        </div>
    </section>
    
    <!-- Continuar viendo con comentarios -->
    @auth
        @if($continueWatching && $continueWatching->count() > 0)
            <section class="py-6">
                <div class="container mx-auto px-4">
                    <h2 class="text-2xl font-bold mb-4 text-white">Continuar viendo</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($continueWatching as $history)
                            <x-netflix-card-with-comments :title="$history->title" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endauth
    
    <!-- Secciones existentes de géneros pero con las nuevas tarjetas -->
    @foreach($genres as $genre)
        @if($genre->titles->count() > 0)
            <section class="py-6">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-white">{{ $genre->name }}</h2>
                        <a href="{{ route('catalog.genre', $genre->slug) }}" 
                           class="text-red-500 hover:text-red-400 transition-colors">
                            Ver más →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($genre->titles->take(12) as $title)
                            <x-netflix-card-with-comments :title="$title" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endsection