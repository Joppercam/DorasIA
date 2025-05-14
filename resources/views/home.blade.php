<x-app-layout>
    <x-slot name="title">Inicio</x-slot>
    <x-slot name="pageClass">home-page</x-slot>
    <x-slot name="transitionType">fade</x-slot>
    
    <!-- Estilos específicos para la página de inicio -->
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/netflix-style.css') }}">
    <style>
        /* Eliminar padding para el hero en la página de inicio */
        body {
            padding-top: 0 !important;
            background-color: #141414;
        }
        
        /* Eliminar margen negativo que puede causar scroll horizontal */
        .netflix-hero {
            margin: 0;
        }
        
        /* Esconder contenido original hasta que termine la carga */
        #original-layout {
            display: none;
        }
    </style>
    @endpush
    
    <!-- Hero Banner estilo Netflix -->
    <section class="netflix-hero">
        @if($featuredTitles->count() > 0)
        @php
            $featured = $featuredTitles->first();
        @endphp
        <!-- Video o imagen de fondo -->
        <div class="netflix-hero-background">
            <!-- Imagen de fondo (siempre visible) -->
            <img src="{{ asset($featured->backdrop) }}" 
                 alt="{{ $featured->title }}" 
                 class="netflix-hero-image"
                 onerror="this.onerror=null; this.src='{{ asset('backdrops/placeholder.jpg') }}'">
                 
            <!-- Efecto de hover para reproducción automática (versión demo) -->
            <div class="netflix-hero-video-container hidden md:block">
                @if(!empty($featured->trailer_url))
                <div class="netflix-hero-video-overlay" id="heroVideoTrigger">
                    <div class="flex items-center justify-center h-full">
                        <div class="bg-black/40 p-4 rounded-full">
                            <svg class="w-12 h-12 text-white opacity-80" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="netflix-hero-overlay"></div>
        <div class="netflix-hero-vignette"></div>
        
        <div class="netflix-hero-content">
            <h1 class="netflix-hero-title">{{ $featured->title }}</h1>
            @if($featured->original_title && $featured->original_title !== $featured->title)
                <h2 class="netflix-hero-subtitle">{{ $featured->original_title }}</h2>
            @endif
            
            <div class="flex items-center space-x-4 mb-4">
                <span class="bg-red-600 px-2 py-0.5 text-sm rounded">{{ $featured->type === 'movie' ? 'Película' : 'Serie' }}</span>
                <span class="text-sm">{{ $featured->release_year }}</span>
                @if($featured->type === 'movie' && $featured->duration)
                    <span class="text-sm">{{ $featured->duration }} min</span>
                @endif
                @if(!empty($featured->vote_average))
                    <span class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        {{ number_format($featured->vote_average, 1) }}
                    </span>
                @endif
            </div>
            
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($featured->genres as $genre)
                    <a href="{{ route('catalog.genre', $genre->slug) }}" class="text-xs bg-gray-800 hover:bg-gray-700 px-2 py-1 rounded-full text-gray-300">{{ $genre->name }}</a>
                @endforeach
            </div>
            
            <p class="netflix-hero-description">{{ $featured->synopsis }}</p>
            
            @if($featured->type === 'series' && Auth::check())
                @php
                    $watchHistory = App\Models\WatchHistory::where('title_id', $featured->id)
                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                        ->orderBy('updated_at', 'desc')
                        ->first();
                @endphp
                
                @if($watchHistory)
                <div class="mt-3 mb-5">
                    <div class="netflix-progress-info">
                        @if($watchHistory->episode)
                            <span class="font-semibold text-red-500">Continúa viendo:</span> Temporada {{ $watchHistory->season_number }}, Episodio {{ $watchHistory->episode_number }}
                            <div class="mt-1 h-1 w-full bg-gray-700 rounded overflow-hidden">
                                <div class="h-full bg-red-600" style="width: {{ $watchHistory->progress }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            @elseif($featured->type === 'movie' && Auth::check())
                @php
                    $movieProgress = App\Models\WatchHistory::where('title_id', $featured->id)
                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                        ->first();
                @endphp
                
                @if($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95)
                <div class="mt-3 mb-5">
                    <div class="netflix-progress-info">
                        <span class="font-semibold text-red-500">Continúa viendo:</span> {{ floor($movieProgress->progress) }}% completado
                        <div class="mt-1 h-1 w-full bg-gray-700 rounded overflow-hidden">
                            <div class="h-full bg-red-600" style="width: {{ $movieProgress->progress }}%"></div>
                        </div>
                    </div>
                </div>
                @endif
            @endif
            
            <div class="netflix-hero-buttons">
                @if($featured->type === 'movie')
                    @php
                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $featured->id)
                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                            ->first() : null;
                        $movieWatchUrl = $featured->slug;
                        
                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                            $movieWatchUrl = $featured->slug . '?t=' . floor($movieProgress->current_time);
                        }
                    @endphp
                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-button-play">
                        <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"></path>
                        </svg>
                        {{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}
                    </a>
                @elseif($featured->seasons->count() > 0 && $featured->seasons->first()->episodes->count() > 0)
                    @php
                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $featured->id)
                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                            ->orderBy('updated_at', 'desc')
                            ->first() : null;
                            
                        $seasonNum = $featured->seasons->first()->number;
                        $episodeNum = $featured->seasons->first()->episodes->first()->number;
                        $timeParam = '';
                        
                        if ($watchHistory && $watchHistory->episode) {
                            $seasonNum = $watchHistory->season_number;
                            $episodeNum = $watchHistory->episode_number;
                            if ($watchHistory->progress < 95) {
                                $timeParam = '?t=' . floor($watchHistory->current_time);
                            }
                        }
                    @endphp
                    <a href="{{ route('titles.watch', [$featured->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-button-play">
                        <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"></path>
                        </svg>
                        {{ $watchHistory ? 'Continuar' : 'Reproducir' }}
                    </a>
                @endif
                
                <a href="{{ route('titles.show', $featured->slug) }}" class="netflix-button-more">
                    <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                    </svg>
                    Más información
                </a>
                
                @auth
                <button
                    type="button"
                    class="netflix-button-more watchlist-toggle"
                    data-title-id="{{ $featured->id }}"
                    onclick="toggleWatchlist({{ $featured->id }}, this)">
                    <svg class="netflix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Mi Lista
                </button>
                @endauth
                
                @if(!empty($featured->trailer_url))
                <button 
                    type="button" 
                    class="netflix-button-more" 
                    onclick="openTrailerModal('{{ $featured->title }}', '{{ $featured->trailer_url }}')">
                    <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6.47L5.76 10H20v8H4V6.47M22 4h-4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4z"></path>
                    </svg>
                    Trailer
                </button>
                @endif
                
                <button 
                    type="button" 
                    class="netflix-button-more"
                    onclick="shareContent('{{ $featured->title }}', '{{ route('titles.show', $featured->slug) }}')">
                    <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"></path>
                    </svg>
                    Compartir
                </button>
            </div>
        </div>
        @else
        <!-- Hero Banner por defecto si no hay títulos destacados -->
        <img src="{{ asset('backdrops/placeholder.jpg') }}" alt="Dorasia" class="netflix-hero-image">
        <div class="netflix-hero-overlay"></div>
        <div class="netflix-hero-vignette"></div>
        
        <div class="netflix-hero-content">
            <h1 class="netflix-hero-title">Bienvenido a Dorasia</h1>
            <p class="netflix-hero-description">Tu plataforma para disfrutar de doramas coreanos, películas y series asiáticas.</p>
            
            <div class="netflix-hero-buttons">
                <a href="{{ route('catalog.index') }}" class="netflix-button-play">
                    <svg class="netflix-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"></path>
                    </svg>
                    Explorar catálogo
                </a>
            </div>
        </div>
        @endif
    </section>
    
    <!-- Carruseles de títulos -->
    <div class="pb-12">
        <!-- Títulos mejor valorados -->
        @if($topRatedTitles->count() > 3)
        <section class="netflix-row">
            <h2 class="netflix-row-title">Los mejor valorados</h2>
            
            <div class="netflix-slider">
                <div class="netflix-slider-content">
                    @foreach($topRatedTitles as $title)
                    <div class="netflix-card">
                        <div class="relative">
                            <img src="{{ asset($title->poster) }}" 
                                 alt="{{ $title->title }}" 
                                 class="netflix-card-img"
                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                            @auth
                                @php
                                    $titleProgress = App\Models\WatchHistory::where('title_id', $title->id)
                                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                        ->first();
                                @endphp
                                
                                @if($titleProgress && $titleProgress->progress > 0 && $titleProgress->progress < 95)
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $titleProgress->progress }}%"></div>
                                    </div>
                                    <div class="continue-badge">Continuar</div>
                                @endif
                            @endauth
                        </div>
                        <div class="netflix-card-content">
                            <h3 class="netflix-card-title">{{ $title->title }}</h3>
                            <div class="netflix-card-info">
                                <span>{{ $title->release_year }}</span>
                                @if($title->vote_average)
                                <span class="flex items-center text-yellow-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($title->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="netflix-card-buttons">
                                <!-- Botón de reproducir -->
                                @if($title->type === 'movie')
                                    @php
                                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->first() : null;
                                        $movieWatchUrl = $title->slug;
                                        
                                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                                            $movieWatchUrl = $title->slug . '?t=' . floor($movieProgress->current_time);
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-card-button" title="{{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                                    @php
                                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first() : null;
                                            
                                        $seasonNum = $title->seasons->first()->number;
                                        $episodeNum = $title->seasons->first()->episodes->first()->number;
                                        $timeParam = '';
                                        
                                        if ($watchHistory && $watchHistory->episode) {
                                            $seasonNum = $watchHistory->season_number;
                                            $episodeNum = $watchHistory->episode_number;
                                            if ($watchHistory->progress < 95) {
                                                $timeParam = '?t=' . floor($watchHistory->current_time);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', [$title->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-card-button" title="{{ $watchHistory ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <!-- Botón de Mi Lista -->
                                @auth
                                <button
                                    type="button"
                                    class="netflix-card-button watchlist-toggle"
                                    data-title-id="{{ $title->id }}"
                                    onclick="toggleWatchlist({{ $title->id }}, this)">
                                    <svg class="netflix-card-icons" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                @endauth
                                
                                <!-- Botón de Información -->
                                <a href="{{ route('titles.show', $title->slug) }}" class="netflix-card-button">
                                    <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="netflix-slider-prev">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </div>
                
                <div class="netflix-slider-next">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </div>
            </div>
        </section>
        @endif
        
        <!-- Carruseles por categoría -->
        @foreach($categories as $category)
        @if($category->titles->count() > 0)
        <section class="netflix-row">
            <h2 class="netflix-row-title">{{ $category->name }}</h2>
            
            <div class="netflix-slider">
                <div class="netflix-slider-content">
                    @foreach($category->titles as $title)
                    <div class="netflix-card">
                        <div class="relative">
                            <img src="{{ asset($title->poster) }}" 
                                 alt="{{ $title->title }}" 
                                 class="netflix-card-img"
                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                            @auth
                                @php
                                    $titleProgress = App\Models\WatchHistory::where('title_id', $title->id)
                                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                        ->first();
                                @endphp
                                
                                @if($titleProgress && $titleProgress->progress > 0 && $titleProgress->progress < 95)
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $titleProgress->progress }}%"></div>
                                    </div>
                                    <div class="continue-badge">Continuar</div>
                                @endif
                            @endauth
                        </div>
                        <div class="netflix-card-content">
                            <h3 class="netflix-card-title">{{ $title->title }}</h3>
                            <div class="netflix-card-info">
                                <span>{{ $title->release_year }}</span>
                                @if($title->vote_average)
                                <span class="flex items-center text-yellow-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($title->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="netflix-card-buttons">
                                <!-- Botón de reproducir -->
                                @if($title->type === 'movie')
                                    @php
                                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->first() : null;
                                        $movieWatchUrl = $title->slug;
                                        
                                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                                            $movieWatchUrl = $title->slug . '?t=' . floor($movieProgress->current_time);
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-card-button" title="{{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                                    @php
                                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first() : null;
                                            
                                        $seasonNum = $title->seasons->first()->number;
                                        $episodeNum = $title->seasons->first()->episodes->first()->number;
                                        $timeParam = '';
                                        
                                        if ($watchHistory && $watchHistory->episode) {
                                            $seasonNum = $watchHistory->season_number;
                                            $episodeNum = $watchHistory->episode_number;
                                            if ($watchHistory->progress < 95) {
                                                $timeParam = '?t=' . floor($watchHistory->current_time);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', [$title->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-card-button" title="{{ $watchHistory ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <!-- Botón de Mi Lista -->
                                @auth
                                <button
                                    type="button"
                                    class="netflix-card-button watchlist-toggle"
                                    data-title-id="{{ $title->id }}"
                                    onclick="toggleWatchlist({{ $title->id }}, this)">
                                    <svg class="netflix-card-icons" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                @endauth
                                
                                <!-- Botón de Información -->
                                <a href="{{ route('titles.show', $title->slug) }}" class="netflix-card-button">
                                    <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="netflix-slider-prev">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </div>
                
                <div class="netflix-slider-next">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </div>
            </div>
        </section>
        @endif
        @endforeach
        
        <!-- Títulos más vistos -->
        @if($mostWatchedTitles->count() > 3)
        <section class="netflix-row">
            <h2 class="netflix-row-title">Los más vistos</h2>
            
            <div class="netflix-slider">
                <div class="netflix-slider-content">
                    @foreach($mostWatchedTitles as $title)
                    <div class="netflix-card">
                        <div class="relative">
                            <img src="{{ asset($title->poster) }}" 
                                 alt="{{ $title->title }}" 
                                 class="netflix-card-img"
                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                            @auth
                                @php
                                    $titleProgress = App\Models\WatchHistory::where('title_id', $title->id)
                                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                        ->first();
                                @endphp
                                
                                @if($titleProgress && $titleProgress->progress > 0 && $titleProgress->progress < 95)
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $titleProgress->progress }}%"></div>
                                    </div>
                                    <div class="continue-badge">Continuar</div>
                                @endif
                            @endauth
                        </div>
                        <div class="netflix-card-content">
                            <h3 class="netflix-card-title">{{ $title->title }}</h3>
                            <div class="netflix-card-info">
                                <span>{{ $title->release_year }}</span>
                                @if($title->vote_average)
                                <span class="flex items-center text-yellow-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($title->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="netflix-card-buttons">
                                <!-- Botón de reproducir -->
                                @if($title->type === 'movie')
                                    @php
                                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->first() : null;
                                        $movieWatchUrl = $title->slug;
                                        
                                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                                            $movieWatchUrl = $title->slug . '?t=' . floor($movieProgress->current_time);
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-card-button" title="{{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                                    @php
                                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first() : null;
                                            
                                        $seasonNum = $title->seasons->first()->number;
                                        $episodeNum = $title->seasons->first()->episodes->first()->number;
                                        $timeParam = '';
                                        
                                        if ($watchHistory && $watchHistory->episode) {
                                            $seasonNum = $watchHistory->season_number;
                                            $episodeNum = $watchHistory->episode_number;
                                            if ($watchHistory->progress < 95) {
                                                $timeParam = '?t=' . floor($watchHistory->current_time);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', [$title->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-card-button" title="{{ $watchHistory ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <!-- Botón de Mi Lista -->
                                @auth
                                <button
                                    type="button"
                                    class="netflix-card-button watchlist-toggle"
                                    data-title-id="{{ $title->id }}"
                                    onclick="toggleWatchlist({{ $title->id }}, this)">
                                    <svg class="netflix-card-icons" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                @endauth
                                
                                <!-- Botón de Información -->
                                <a href="{{ route('titles.show', $title->slug) }}" class="netflix-card-button">
                                    <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="netflix-slider-prev">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </div>
                
                <div class="netflix-slider-next">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </div>
            </div>
        </section>
        @endif
        
        <!-- Títulos más comentados -->
        @if($mostCommentedTitles->count() > 3)
        <section class="netflix-row">
            <h2 class="netflix-row-title">Los más comentados</h2>
            
            <div class="netflix-slider">
                <div class="netflix-slider-content">
                    @foreach($mostCommentedTitles as $title)
                    <div class="netflix-card">
                        <div class="relative">
                            <img src="{{ asset($title->poster) }}" 
                                 alt="{{ $title->title }}" 
                                 class="netflix-card-img"
                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                            @auth
                                @php
                                    $titleProgress = App\Models\WatchHistory::where('title_id', $title->id)
                                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                        ->first();
                                @endphp
                                
                                @if($titleProgress && $titleProgress->progress > 0 && $titleProgress->progress < 95)
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $titleProgress->progress }}%"></div>
                                    </div>
                                    <div class="continue-badge">Continuar</div>
                                @endif
                            @endauth
                        </div>
                        <div class="netflix-card-content">
                            <h3 class="netflix-card-title">{{ $title->title }}</h3>
                            <div class="netflix-card-info">
                                <span>{{ $title->release_year }}</span>
                                @if($title->vote_average)
                                <span class="flex items-center text-yellow-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($title->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="netflix-card-buttons">
                                <!-- Botón de reproducir -->
                                @if($title->type === 'movie')
                                    @php
                                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->first() : null;
                                        $movieWatchUrl = $title->slug;
                                        
                                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                                            $movieWatchUrl = $title->slug . '?t=' . floor($movieProgress->current_time);
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-card-button" title="{{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                                    @php
                                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first() : null;
                                            
                                        $seasonNum = $title->seasons->first()->number;
                                        $episodeNum = $title->seasons->first()->episodes->first()->number;
                                        $timeParam = '';
                                        
                                        if ($watchHistory && $watchHistory->episode) {
                                            $seasonNum = $watchHistory->season_number;
                                            $episodeNum = $watchHistory->episode_number;
                                            if ($watchHistory->progress < 95) {
                                                $timeParam = '?t=' . floor($watchHistory->current_time);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', [$title->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-card-button" title="{{ $watchHistory ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <!-- Botón de Mi Lista -->
                                @auth
                                <button
                                    type="button"
                                    class="netflix-card-button watchlist-toggle"
                                    data-title-id="{{ $title->id }}"
                                    onclick="toggleWatchlist({{ $title->id }}, this)">
                                    <svg class="netflix-card-icons" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                @endauth
                                
                                <!-- Botón de Información -->
                                <a href="{{ route('titles.show', $title->slug) }}" class="netflix-card-button">
                                    <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="netflix-slider-prev">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </div>
                
                <div class="netflix-slider-next">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </div>
            </div>
        </section>
        @endif
        
        <!-- Añadidos recientemente -->
        @if($recentTitles->count() > 3)
        <section class="netflix-row">
            <h2 class="netflix-row-title">Añadidos recientemente</h2>
            
            <div class="netflix-slider">
                <div class="netflix-slider-content">
                    @foreach($recentTitles as $title)
                    <div class="netflix-card">
                        <div class="relative">
                            <img src="{{ asset($title->poster) }}" 
                                 alt="{{ $title->title }}" 
                                 class="netflix-card-img"
                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                            @auth
                                @php
                                    $titleProgress = App\Models\WatchHistory::where('title_id', $title->id)
                                        ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                        ->first();
                                @endphp
                                
                                @if($titleProgress && $titleProgress->progress > 0 && $titleProgress->progress < 95)
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $titleProgress->progress }}%"></div>
                                    </div>
                                    <div class="continue-badge">Continuar</div>
                                @endif
                            @endauth
                        </div>
                        <div class="netflix-card-content">
                            <h3 class="netflix-card-title">{{ $title->title }}</h3>
                            <div class="netflix-card-info">
                                <span>{{ $title->release_year }}</span>
                                @if($title->vote_average)
                                <span class="flex items-center text-yellow-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($title->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="netflix-card-buttons">
                                <!-- Botón de reproducir -->
                                @if($title->type === 'movie')
                                    @php
                                        $movieProgress = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->first() : null;
                                        $movieWatchUrl = $title->slug;
                                        
                                        if ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) {
                                            $movieWatchUrl = $title->slug . '?t=' . floor($movieProgress->current_time);
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', $movieWatchUrl) }}" class="netflix-card-button" title="{{ ($movieProgress && $movieProgress->progress > 0 && $movieProgress->progress < 95) ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                                    @php
                                        $watchHistory = Auth::check() ? App\Models\WatchHistory::where('title_id', $title->id)
                                            ->where('profile_id', Auth::user()->getActiveProfile()?->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first() : null;
                                            
                                        $seasonNum = $title->seasons->first()->number;
                                        $episodeNum = $title->seasons->first()->episodes->first()->number;
                                        $timeParam = '';
                                        
                                        if ($watchHistory && $watchHistory->episode) {
                                            $seasonNum = $watchHistory->season_number;
                                            $episodeNum = $watchHistory->episode_number;
                                            if ($watchHistory->progress < 95) {
                                                $timeParam = '?t=' . floor($watchHistory->current_time);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ route('titles.watch', [$title->slug, $seasonNum, $episodeNum]) }}{{ $timeParam }}" class="netflix-card-button" title="{{ $watchHistory ? 'Continuar' : 'Reproducir' }}">
                                        <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <!-- Botón de Mi Lista -->
                                @auth
                                <button
                                    type="button"
                                    class="netflix-card-button watchlist-toggle"
                                    data-title-id="{{ $title->id }}"
                                    onclick="toggleWatchlist({{ $title->id }}, this)">
                                    <svg class="netflix-card-icons" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                @endauth
                                
                                <!-- Botón de Información -->
                                <a href="{{ route('titles.show', $title->slug) }}" class="netflix-card-button">
                                    <svg class="netflix-card-icons" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="netflix-slider-prev">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </div>
                
                <div class="netflix-slider-next">
                    <svg class="netflix-arrow-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </div>
            </div>
        </section>
        @endif
    </div>
    
    <!-- Modales -->
    <div id="trailerModal" class="netflix-modal" style="display: none;">
        <div class="netflix-modal-content">
            <button class="netflix-modal-close" onclick="closeTrailerModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h3 id="trailerTitle" class="text-xl font-bold p-4 border-b border-gray-700"></h3>
            <div class="netflix-trailer-container">
                <iframe id="trailerFrame" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    
    <div id="shareModal" class="netflix-modal" style="display: none;">
        <div class="netflix-modal-content max-w-md">
            <button class="netflix-modal-close" onclick="closeShareModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Compartir</h3>
                <p id="shareText" class="mb-4"></p>
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" id="shareFacebook" target="_blank" class="bg-blue-600 text-white p-3 rounded text-center hover:bg-blue-700">
                        Facebook
                    </a>
                    <a href="#" id="shareTwitter" target="_blank" class="bg-blue-400 text-white p-3 rounded text-center hover:bg-blue-500">
                        Twitter
                    </a>
                    <a href="#" id="shareWhatsapp" target="_blank" class="bg-green-600 text-white p-3 rounded text-center hover:bg-green-700">
                        WhatsApp
                    </a>
                    <button id="shareCopy" class="bg-gray-700 text-white p-3 rounded text-center hover:bg-gray-600" onclick="copyShareLink()">
                        Copiar enlace
                    </button>
                </div>
                <p id="copyMessage" class="mt-4 text-center text-green-500 hidden">¡Enlace copiado!</p>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // JavaScript para la interactividad
        document.addEventListener('DOMContentLoaded', function() {
            // Detectar scroll para cambiar el color del navbar
            const nav = document.querySelector('nav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });
            
            // Inicializar manualmente el evento de scroll para aplicar el estado correcto
            window.dispatchEvent(new Event('scroll'));
            
            // Funcionalidad para el hero video
            const heroVideoTrigger = document.getElementById('heroVideoTrigger');
            if (heroVideoTrigger) {
                heroVideoTrigger.addEventListener('click', function() {
                    // Al hacer clic en el overlay del hero, abrir el modal del trailer
                    @if(!empty($featuredTitles->first()->trailer_url))
                    openTrailerModal('{{ $featuredTitles->first()->title }}', '{{ $featuredTitles->first()->trailer_url }}');
                    @endif
                });
            }
            
            // Funcionalidad para los controles de carrusel
            const sliders = document.querySelectorAll('.netflix-slider');
            
            sliders.forEach(slider => {
                const content = slider.querySelector('.netflix-slider-content');
                const prevBtn = slider.querySelector('.netflix-slider-prev');
                const nextBtn = slider.querySelector('.netflix-slider-next');
                
                if (prevBtn && nextBtn && content) {
                    prevBtn.addEventListener('click', () => {
                        content.scrollBy({ left: -800, behavior: 'smooth' });
                    });
                    
                    nextBtn.addEventListener('click', () => {
                        content.scrollBy({ left: 800, behavior: 'smooth' });
                    });
                }
            });
        });
        
        // Funciones para la watchlist
        function toggleWatchlist(titleId, button) {
            fetch('{{ route('watchlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title_id: titleId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar todos los botones para este título
                const buttons = document.querySelectorAll(`.watchlist-toggle[data-title-id="${titleId}"]`);
                
                buttons.forEach(btn => {
                    if (data.status === 'added') {
                        btn.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                    } else {
                        btn.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>';
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Verificar estado inicial de los botones de watchlist
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.watchlist-toggle');
            const uniqueTitleIds = new Set();
            
            buttons.forEach(button => {
                const titleId = button.dataset.titleId;
                uniqueTitleIds.add(titleId);
            });
            
            uniqueTitleIds.forEach(titleId => {
                fetch('{{ url('/api/watchlist/status') }}/' + titleId, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.in_watchlist) {
                        const buttons = document.querySelectorAll(`.watchlist-toggle[data-title-id="${titleId}"]`);
                        buttons.forEach(btn => {
                            btn.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
        
        // Funciones para modales
        function openTrailerModal(title, trailerUrl) {
            const modal = document.getElementById('trailerModal');
            const titleEl = document.getElementById('trailerTitle');
            const frame = document.getElementById('trailerFrame');
            
            // Convertir URL de YouTube a embedded
            let embedUrl = trailerUrl;
            if (trailerUrl.includes('youtube.com/watch?v=')) {
                const videoId = trailerUrl.split('v=')[1].split('&')[0];
                embedUrl = `https://www.youtube.com/embed/${videoId}`;
            } else if (trailerUrl.includes('youtu.be/')) {
                const videoId = trailerUrl.split('.be/')[1].split('?')[0];
                embedUrl = `https://www.youtube.com/embed/${videoId}`;
            }
            
            titleEl.textContent = 'Trailer: ' + title;
            frame.src = embedUrl;
            modal.style.display = 'flex';
            
            // Prevenir scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        function closeTrailerModal() {
            const modal = document.getElementById('trailerModal');
            const frame = document.getElementById('trailerFrame');
            
            modal.style.display = 'none';
            frame.src = ''; // Detener la reproducción del video
            
            // Restaurar scroll del body
            document.body.style.overflow = '';
        }
        
        function shareContent(title, url) {
            const modal = document.getElementById('shareModal');
            const text = document.getElementById('shareText');
            const fbLink = document.getElementById('shareFacebook');
            const twLink = document.getElementById('shareTwitter');
            const waLink = document.getElementById('shareWhatsapp');
            const copyMsg = document.getElementById('copyMessage');
            
            text.textContent = `Comparte "${title}" con tus amigos`;
            
            fbLink.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            twLink.href = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=¡Mira ${encodeURIComponent(title)} en Dorasia!`;
            waLink.href = `https://api.whatsapp.com/send?text=¡Mira ${encodeURIComponent(title)} en Dorasia! ${encodeURIComponent(url)}`;
            
            // Guardar URL para la función de copia
            window.shareUrl = url;
            
            copyMsg.classList.add('hidden');
            modal.style.display = 'flex';
            
            // Prevenir scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            modal.style.display = 'none';
            
            // Restaurar scroll del body
            document.body.style.overflow = '';
        }
        
        function copyShareLink() {
            const copyMsg = document.getElementById('copyMessage');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.shareUrl)
                    .then(() => {
                        copyMsg.classList.remove('hidden');
                        setTimeout(() => {
                            copyMsg.classList.add('hidden');
                        }, 3000);
                    })
                    .catch(err => {
                        console.error('Error al copiar: ', err);
                    });
            } else {
                // Fallback para navegadores que no soportan clipboard API
                const textArea = document.createElement('textarea');
                textArea.value = window.shareUrl;
                document.body.appendChild(textArea);
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    copyMsg.classList.remove('hidden');
                    setTimeout(() => {
                        copyMsg.classList.add('hidden');
                    }, 3000);
                } catch (err) {
                    console.error('Error al copiar: ', err);
                }
                
                document.body.removeChild(textArea);
            }
        }
    </script>
    @endpush
</x-app-layout>