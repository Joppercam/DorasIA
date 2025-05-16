@props(['title'])

<a href="{{ route('titles.show', $title->slug) }}" 
   class="netflix-modern-card group relative block h-0 pb-[150%] bg-gray-900 rounded-md overflow-hidden transition-all duration-300 hover:scale-105 hover:z-20">
    
    <!-- Imagen de fondo -->
    <div class="absolute inset-0">
        @if($title->poster_path)
            <img 
                src="{{ $title->poster_url }}" 
                alt="{{ $title->title }}"
                loading="lazy"
                class="w-full h-full object-cover"
                onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'"
            >
        @else
            <div class="w-full h-full bg-gradient-to-br from-red-900 to-black flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ substr($title->title, 0, 1) }}</span>
            </div>
        @endif
        
        <!-- Overlay oscuro al hover -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-300"></div>
    </div>
    
    <!-- Indicador de progreso (si existe) -->
    @auth
        @php
            $profile = auth()->user()->getActiveProfile();
            $watchHistory = null;
            if($profile) {
                $watchHistory = $profile->watchHistory()
                    ->where('title_id', $title->id)
                    ->first();
            }
        @endphp
        
        @if($watchHistory && $watchHistory->progress > 0)
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                <div class="h-full bg-red-600" style="width: {{ $watchHistory->progress }}%"></div>
            </div>
        @endif
    @endauth
    
    <!-- Contenido al hover -->
    <div class="absolute inset-0 p-3 flex flex-col justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <!-- Título -->
        <h3 class="text-white font-bold text-base mb-1">{{ $title->title }}</h3>
        
        <!-- Rating con estrellas -->
        <div class="mb-2">
            <x-rating-stars :title-id="$title->id" :show-count="false" size="sm" />
        </div>
        
        <!-- Metadatos -->
        <div class="flex items-center gap-3 text-xs text-gray-300 mb-2">
            <span class="text-green-500 font-semibold">
                @if($title->vote_average >= 7)
                    {{ rand(92, 98) }}% Match
                @else
                    {{ rand(75, 91) }}% Match
                @endif
            </span>
            <span>{{ $title->release_year }}</span>
            @if($title->type === 'series' && $title->seasons_count)
                <span>{{ $title->seasons_count }} Season{{ $title->seasons_count > 1 ? 's' : '' }}</span>
            @endif
            @if($title->content_rating)
                <span class="border border-gray-400 px-1">{{ $title->content_rating }}</span>
            @endif
        </div>
        
        <!-- Géneros -->
        <div class="flex flex-wrap gap-1">
            @foreach($title->genres->take(3) as $genre)
                <span class="text-xs text-gray-300">
                    {{ $genre->name }}{{ !$loop->last ? ' •' : '' }}
                </span>
            @endforeach
        </div>
    </div>
    
    <!-- Badges flotantes -->
    <div class="absolute top-2 left-2 right-2 flex justify-between items-start pointer-events-none">
        <!-- Badge de nuevo -->
        @if($title->created_at >= now()->subDays(7))
            <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                NEW
            </span>
        @elseif($title->release_year == date('Y'))
            <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">
                {{ date('Y') }}
            </span>
        @else
            <span></span>
        @endif
        
        <!-- Rating -->
        @if($title->vote_average >= 8)
            <span class="bg-yellow-500 text-black text-xs font-bold px-2 py-1 rounded flex items-center">
                <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                Top
            </span>
        @endif
    </div>
    
    <!-- Iconos de acción al hover -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <!-- Play button -->
        <div class="bg-white hover:bg-gray-200 text-black rounded-full p-3 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        @auth
            @if(auth()->user()->getActiveProfile())
                <button
                    type="button"
                    onclick="event.preventDefault(); toggleWatchlist({{ $title->id }}, this)"
                    class="bg-black/70 hover:bg-black text-white rounded-full p-2 transition-colors border-2 border-gray-400">
                    @php
                        $inWatchlist = auth()->user()->getActiveProfile()->watchlist()
                                     ->where('title_id', $title->id)->exists();
                    @endphp
                    
                    <svg class="w-4 h-4" fill="{{ $inWatchlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        @if($inWatchlist)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        @endif
                    </svg>
                </button>
            @endif
        @endauth
    </div>
    
    <!-- Plataforma de streaming (esquina inferior derecha) -->
    @if($title->streaming_platforms)
        @php
            $platforms = is_string($title->streaming_platforms) 
                ? json_decode($title->streaming_platforms, true) 
                : $title->streaming_platforms;
            $firstPlatform = $platforms[0] ?? null;
        @endphp
        
        @if($firstPlatform)
            <div class="absolute bottom-2 right-2">
                <div class="w-8 h-8 rounded bg-black/70 flex items-center justify-center">
                    @switch($firstPlatform)
                        @case('netflix')
                            <span class="text-red-600 font-bold text-xs">N</span>
                            @break
                        @case('viki')
                            <span class="text-blue-400 font-bold text-xs">V</span>
                            @break
                        @case('disney')
                            <span class="text-blue-500 font-bold text-xs">D+</span>
                            @break
                        @case('hbo')
                            <span class="text-purple-600 font-bold text-xs">HBO</span>
                            @break
                        @default
                            <span class="text-white font-bold text-xs">{{ strtoupper(substr($firstPlatform, 0, 1)) }}</span>
                    @endswitch
                </div>
            </div>
        @endif
    @endif
</a>