@props(['title'])

<div class="netflix-card-wrapper group relative h-0 pb-[150%] bg-gray-900 rounded-lg overflow-hidden shadow-lg transition-all duration-300 hover:scale-105 hover:z-10">
    <!-- Imagen principal -->
    <div class="absolute inset-0">
        @if($title->poster_path)
            <img 
                src="{{ $title->poster_url }}" 
                alt="{{ $title->title }}"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'"
            >
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Gradiente inferior para mejor legibilidad -->
        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90"></div>
    </div>
    
    <!-- Contenido de la tarjeta -->
    <div class="absolute inset-0 p-3 flex flex-col justify-between">
        <!-- Badges superiores -->
        <div class="flex justify-between items-start">
            <!-- Año de lanzamiento -->
            <span class="inline-block bg-black/80 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
                {{ $title->release_year }}
            </span>
            
            <!-- Rating -->
            @if($title->vote_average)
                <span class="inline-flex items-center bg-yellow-500/90 backdrop-blur-sm text-black text-xs font-bold px-2 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    {{ number_format($title->vote_average, 1) }}
                </span>
            @endif
        </div>
        
        <!-- Información en hover -->
        <div class="transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <!-- Título -->
            <h3 class="text-white font-bold text-sm mb-1 line-clamp-2">
                {{ $title->title }}
            </h3>
            
            <!-- Géneros -->
            <div class="flex flex-wrap gap-1 mb-2">
                @foreach($title->genres->take(2) as $genre)
                    <span class="text-xs bg-red-600/80 backdrop-blur-sm text-white px-2 py-0.5 rounded-full">
                        {{ $genre->name }}
                    </span>
                @endforeach
            </div>
            
            <!-- Sinopsis breve -->
            <p class="text-xs text-gray-200 line-clamp-3 mb-3">
                {{ $title->synopsis }}
            </p>
            
            <!-- Botones de acción -->
            <div class="flex items-center gap-2">
                <a href="{{ route('titles.show', $title->slug) }}" 
                   class="flex-1 bg-white hover:bg-gray-200 text-black text-xs font-semibold py-2 px-3 rounded-md transition-colors text-center">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Más info
                </a>
                
                @auth
                    <button
                        type="button"
                        onclick="toggleWatchlist({{ $title->id }}, this)"
                        class="bg-gray-800/80 backdrop-blur-sm hover:bg-gray-700 text-white p-2 rounded-md transition-colors">
                        @php
                            $inWatchlist = auth()->user()->getActiveProfile() && 
                                         auth()->user()->getActiveProfile()->watchlist()
                                         ->where('title_id', $title->id)->exists();
                        @endphp
                        
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($inWatchlist)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            @endif
                        </svg>
                    </button>
                @endauth
                
                @if($title->trailer_url)
                    <button
                        onclick="showTrailer('{{ $title->trailer_url }}')"
                        class="bg-red-600/80 backdrop-blur-sm hover:bg-red-700 text-white p-2 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Información básica siempre visible -->
    <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black to-transparent">
        <h3 class="text-white font-semibold text-sm line-clamp-1 group-hover:opacity-0 transition-opacity">
            {{ $title->title }}
        </h3>
        <div class="flex items-center gap-2 text-xs text-gray-300 group-hover:opacity-0 transition-opacity">
            <span>{{ $title->category?->name }}</span>
            @if($title->type === 'series' && $title->seasons_count)
                <span>{{ $title->seasons_count }} temporada{{ $title->seasons_count > 1 ? 's' : '' }}</span>
            @endif
        </div>
    </div>
    
    <!-- Badge de tipo de contenido -->
    <div class="absolute top-3 right-3">
        <span class="bg-black/80 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-sm">
            {{ $title->type === 'movie' ? 'Película' : 'Serie' }}
        </span>
    </div>
</div>