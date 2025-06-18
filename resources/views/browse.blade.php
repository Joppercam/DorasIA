@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#141414] text-white pt-20">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-[#00d4ff] via-[#7b68ee] to-[#9d4edd] bg-clip-text text-transparent">
                Explorar K-Dramas
            </h1>
            <p class="text-gray-400">Descubre nuevas series coreanas</p>
        </div>

        <!-- Filtros -->
        <div class="bg-[#1a1a1a] rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('browse') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtro por Género -->
                <div>
                    <label class="block text-sm font-medium mb-2">Género</label>
                    <select name="genre" class="w-full bg-[#2a2a2a] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#00d4ff] focus:outline-none">
                        <option value="">Todos los géneros</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->name }}" {{ request('genre') == $genre->name ? 'selected' : '' }}>
                                {{ $genre->spanish_name ?? $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Año -->
                <div>
                    <label class="block text-sm font-medium mb-2">Año</label>
                    <select name="year" class="w-full bg-[#2a2a2a] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#00d4ff] focus:outline-none">
                        <option value="">Todos los años</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div>
                    <label class="block text-sm font-medium mb-2">Estado</label>
                    <select name="status" class="w-full bg-[#2a2a2a] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#00d4ff] focus:outline-none">
                        <option value="">Todos</option>
                        <option value="Ended" {{ request('status') == 'Ended' ? 'selected' : '' }}>Finalizada</option>
                        <option value="Returning Series" {{ request('status') == 'Returning Series' ? 'selected' : '' }}>En emisión</option>
                        <option value="In Production" {{ request('status') == 'In Production' ? 'selected' : '' }}>En producción</option>
                    </select>
                </div>

                <!-- Ordenar por -->
                <div>
                    <label class="block text-sm font-medium mb-2">Ordenar por</label>
                    <select name="sort" class="w-full bg-[#2a2a2a] border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-[#00d4ff] focus:outline-none">
                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularidad</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Calificación</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alfabético</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="col-span-full flex gap-4 mt-4">
                    <button type="submit" class="bg-gradient-to-r from-[#00d4ff] to-[#7b68ee] text-white px-6 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity">
                        Aplicar Filtros
                    </button>
                    <a href="{{ route('browse') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Resultados -->
        <div class="mb-6">
            <p class="text-gray-400">
                Mostrando {{ $series->firstItem() ?? 0 }} - {{ $series->lastItem() ?? 0 }} de {{ $series->total() }} series
            </p>
        </div>

        <!-- Grid de Series -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6 mb-8">
            @foreach($series as $serie)
                <div class="group cursor-pointer" onclick="window.location.href='{{ route('series.show', $serie->id) }}'">
                    <div class="relative overflow-hidden rounded-lg transition-transform duration-300 group-hover:scale-105">
                        <img 
                            src="{{ $serie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $serie->poster_path : '/images/no-poster-series.svg' }}" 
                            alt="{{ $serie->display_title ?? $serie->title }}"
                            class="w-full h-[300px] object-cover"
                            loading="lazy"
                            onerror="this.src='/images/no-poster-series.svg'"
                        >
                        
                        <!-- Overlay con información -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-75 transition-all duration-300 flex items-end p-4">
                            <div class="transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="text-white font-bold text-sm mb-2 line-clamp-2">
                                    {{ $serie->display_title ?? $serie->title }}
                                </h3>
                                
                                <div class="flex items-center gap-2 text-xs text-gray-300 mb-2">
                                    @if($serie->first_air_date)
                                        <span>{{ date('Y', strtotime($serie->first_air_date)) }}</span>
                                    @endif
                                    @if($serie->vote_average)
                                        <span>⭐ {{ number_format($serie->vote_average, 1) }}</span>
                                    @endif
                                </div>

                                <!-- Estadísticas de interacción -->
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="flex items-center gap-1">
                                        <span class="text-green-400">👍</span>
                                        {{ $serie->likes_count ?? 0 }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="text-red-400">❤️</span>
                                        {{ $serie->loves_count ?? 0 }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="text-blue-400">💬</span>
                                        {{ $serie->comments_count ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($series->hasPages())
            <div class="flex justify-center">
                <div class="bg-[#1a1a1a] rounded-lg p-4">
                    {{ $series->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @endif

        <!-- Mensaje si no hay resultados -->
        @if($series->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">😔</div>
                <h3 class="text-xl font-bold mb-2">No se encontraron series</h3>
                <p class="text-gray-400 mb-4">Intenta ajustar los filtros de búsqueda</p>
                <a href="{{ route('browse') }}" class="bg-gradient-to-r from-[#00d4ff] to-[#7b68ee] text-white px-6 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity">
                    Ver todas las series
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection