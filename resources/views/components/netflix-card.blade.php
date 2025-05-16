@props(['title'])

<a href="{{ route('titles.show', $title->slug) }}" class="group relative w-[140px] xs:w-[160px] sm:w-[200px] md:w-[240px] flex-shrink-0 px-1 transition duration-300 hover:z-10 touch-manipulation block cursor-pointer" 
   x-data="{ 
       showDetails: false, 
       showCommentForm: false,
       showTrailerModal: false,
       showShareMenu: false,
       hoverActive: false,
       isTouch: false,
   }"
   @touchstart="isTouch = true"
   @mouseenter="hoverActive = true; if(!isTouch) showDetails = true"
   @mouseleave="hoverActive = false; if(!isTouch) showDetails = false"
   @click.prevent="
       const target = $event.target;
       const isButton = target.tagName === 'BUTTON' || target.closest('button');
       const isInternalLink = (target.tagName === 'A' || target.closest('a')) && target !== $el;
       const isShareMenu = target.closest('[x-show=\"showShareMenu\"]');
       
       if (isButton || isInternalLink || isShareMenu || showCommentForm || showTrailerModal) {
           $event.stopPropagation();
           return;
       }
       window.location.href = '{{ route('titles.show', $title->slug) }}';
   ">
    <!-- Tarjeta básica que se expande al hacer hover/tap -->
    <div class="relative transform transition-all duration-300" 
         :class="{
             'scale-110 shadow-2xl z-20': showDetails || hoverActive,
             'translate-y-[-5px] shadow-xl': hoverActive && !showDetails
         }">
        <!-- Imagen de portada -->
        <div class="relative pb-[150%] rounded overflow-hidden shadow-lg bg-gray-800">
            <img src="{{ asset($title->poster) }}" 
                 alt="{{ $title->title }}" 
                 class="absolute inset-0 h-full w-full object-cover transform transition-transform duration-700"
                 :class="{'scale-105': hoverActive || showDetails}"
                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
            
            <!-- Gradiente superior permanente para el título -->
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/90 to-transparent pointer-events-none">
                <h3 class="text-white text-xs p-2 font-medium line-clamp-2">{{ $title->title }}</h3>
            </div>
            
            <!-- Gradiente superior para mejorar la visibilidad del overlay de texto en hover -->
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/80 to-transparent opacity-0 transition duration-300 pointer-events-none"
                 :class="{'opacity-100': showDetails || hoverActive}"></div>
            
            <!-- Overlay de contenido (visible en hover/tap) -->
            <div class="absolute inset-0 bg-black/85 opacity-0 transition-all duration-300 flex flex-col justify-between p-2 xs:p-3 text-[10px] xs:text-xs backdrop-blur-sm"
                 :class="{'opacity-100': showDetails || hoverActive}"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-4">
                <div>
                    <h3 class="font-bold text-xs xs:text-sm">{{ $title->title }}</h3>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-[10px] xs:text-xs">{{ $title->release_year }}</span>
                        <x-rating-stars :title-id="$title->id" :show-count="false" size="sm" />
                    </div>
                    
                    <!-- Sinopsis breve -->
                    <p class="text-gray-300 mt-1.5 xs:mt-2 line-clamp-2 xs:line-clamp-3 text-[10px] xs:text-xs">{{ $title->synopsis }}</p>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col gap-1 mt-2">
                    
                    <div class="flex gap-1 justify-between">
                        <!-- Botón de Mi Lista -->
                        @auth
                        <button
                            type="button"
                            class="watchlist-toggle flex-1 bg-gray-800/80 hover:bg-red-600 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-all duration-200 active:bg-red-700 hover:scale-105 backdrop-blur-sm"
                            data-title-id="{{ $title->id }}"
                            onclick="toggleWatchlist({{ $title->id }}, this)">
                            <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="sr-only">Mi Lista</span>
                        </button>
                        @endauth
                        
                        <!-- Botón de Trailer -->
                        @if(!empty($title->trailer_url))
                        <button
                            @click="showTrailerModal = true; $event.stopPropagation();"
                            type="button"
                            class="flex-1 bg-gray-800/80 hover:bg-blue-600 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-all duration-200 active:bg-blue-700 hover:scale-105 backdrop-blur-sm">
                            <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Trailer</span>
                        </button>
                        @endif
                        
                        <!-- Botón de Comentarios -->
                        @auth
                        <button
                            @click="showCommentForm = true; $event.stopPropagation();"
                            type="button"
                            class="flex-1 bg-gray-800/80 hover:bg-green-600 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-all duration-200 active:bg-green-700 hover:scale-105 backdrop-blur-sm">
                            <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Comentar</span>
                        </button>
                        @endauth
                        
                        <!-- Botón de Compartir -->
                        <div class="relative flex-1">
                            <button
                                @click="showShareMenu = !showShareMenu; $event.stopPropagation();"
                                type="button"
                                class="w-full bg-gray-800/80 hover:bg-purple-600 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-all duration-200 active:bg-purple-700 hover:scale-105 backdrop-blur-sm">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"></path>
                                </svg>
                                <span class="sr-only">Compartir</span>
                            </button>
                            
                            <!-- Menú de compartir -->
                            <div 
                                x-show="showShareMenu"
                                @click.away="showShareMenu = false"
                                @click.stop="$event.stopPropagation()"
                                class="absolute z-20 left-0 mt-1 w-32 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5"
                                x-cloak>
                                <div class="py-1 text-[10px] xs:text-xs">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('titles.show', $title->slug)) }}" target="_blank" class="block px-2 py-1.5 text-white hover:bg-gray-700 active:bg-gray-600">
                                        Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('titles.show', $title->slug)) }}&text=Mira {{ $title->title }} en Dorasia" target="_blank" class="block px-2 py-1.5 text-white hover:bg-gray-700 active:bg-gray-600">
                                        Twitter
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text=Mira {{ $title->title }} en Dorasia: {{ urlencode(route('titles.show', $title->slug)) }}" target="_blank" class="block px-2 py-1.5 text-white hover:bg-gray-700 active:bg-gray-600">
                                        WhatsApp
                                    </a>
                                    <button 
                                        onclick="copyToClipboard('{{ route('titles.show', $title->slug) }}')"
                                        class="block w-full text-left px-2 py-1.5 text-white hover:bg-gray-700 active:bg-gray-600">
                                        Copiar enlace
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Removido botón de detalles ya que toda la tarjeta es clickeable -->
                </div>
            </div>
            
            <!-- Etiquetas informativas (mantenidas) -->
            @php
                $platforms = !empty($title->streaming_platforms) 
                    ? (is_string($title->streaming_platforms) ? json_decode($title->streaming_platforms) : $title->streaming_platforms)
                    : ['netflix'];
                $firstPlatform = $platforms[0] ?? 'netflix';
                
                // Iconos y colores para cada plataforma
                $platformIcons = [
                    'netflix' => ['icon' => 'N', 'color' => 'bg-red-600'],
                    'viki' => ['icon' => 'V', 'color' => 'bg-green-600'],
                    'disney' => ['icon' => 'D+', 'color' => 'bg-blue-600'],
                    'hbo' => ['icon' => 'HBO', 'color' => 'bg-purple-800'],
                    'prime' => ['icon' => 'P', 'color' => 'bg-blue-500'],
                    'apple' => ['icon' => 'TV+', 'color' => 'bg-gray-700'],
                    'crunchyroll' => ['icon' => 'CR', 'color' => 'bg-orange-500']
                ];
                
                $platformInfo = $platformIcons[$firstPlatform] ?? ['icon' => 'STR', 'color' => 'bg-purple-600'];
                
                // Colores para las valoraciones
                $ratingColor = 'text-gray-400';
                $ratingBgColor = 'bg-gray-700';
                if (!empty($title->vote_average)) {
                    if ($title->vote_average >= 7) {
                        $ratingColor = 'text-white';
                        $ratingBgColor = 'bg-green-600';
                    } elseif ($title->vote_average >= 5) {
                        $ratingColor = 'text-black';
                        $ratingBgColor = 'bg-yellow-400';
                    } else {
                        $ratingColor = 'text-white';
                        $ratingBgColor = 'bg-red-500';
                    }
                }
            @endphp
            
            <!-- Etiqueta superior izquierda (Categoría) -->
            @if($title->category)
            <div class="absolute top-2 left-2 z-20">
                <div class="bg-blue-600 px-1.5 py-0.5 text-[0.6rem] uppercase font-semibold text-white rounded shadow-md">
                    {{ Str::limit($title->category->name, 7, '') }}
                </div>
            </div>
            @endif
            
            <!-- Etiqueta superior derecha (Valoración) -->
            @if(!empty($title->vote_average))
            <div class="absolute top-2 right-2 z-20">
                <div class="flex items-center {{ $ratingBgColor }} {{ $ratingColor }} text-[0.6rem] font-semibold rounded-full px-1.5 py-0.5 shadow-md">
                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    {{ number_format($title->vote_average, 1) }}
                </div>
            </div>
            @endif
            
            <!-- Etiqueta inferior izquierda (Año) -->
            <div class="absolute bottom-2 left-2 z-20">
                <div class="bg-black/70 text-[0.6rem] text-gray-300 font-medium px-1.5 py-0.5 rounded shadow-md">
                    {{ $title->release_year }}
                </div>
            </div>
            
            <!-- Etiqueta inferior derecha (Plataforma) con tooltip -->
            <div class="absolute bottom-2 right-2 z-20">
                <div class="group/tooltip relative">
                    <div class="{{ $platformInfo['color'] }} w-5 h-5 flex items-center justify-center text-[0.65rem] font-bold text-white rounded shadow-md cursor-help">
                        {{ $platformInfo['icon'] }}
                    </div>
                    
                    <!-- Tooltip -->
                    <div class="absolute hidden group-hover/tooltip:block right-0 bottom-6 bg-black/90 text-white text-[0.65rem] px-2 py-1 rounded shadow-lg z-50 min-w-max whitespace-nowrap">
                        <div class="absolute -bottom-2 right-1 w-0 h-0 border-x-[5px] border-x-transparent border-t-[5px] border-t-black/90"></div>
                        <span>Disponible en {{ Str::title($firstPlatform) }}</span>
                        @if(count($platforms) > 1)
                            <div class="mt-1 pt-1 border-t border-gray-700">
                                <span class="text-gray-300">También en:</span>
                                <ul class="mt-0.5">
                                    @foreach(array_slice($platforms, 1, 3) as $platform)
                                        <li class="text-purple-300">{{ Str::title($platform) }}</li>
                                    @endforeach
                                    @if(count($platforms) > 4)
                                        <li class="text-gray-400">+{{ count($platforms) - 4 }} más</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Removido el título duplicado que estaba debajo de la tarjeta -->
        
        <!-- Indicador de progreso - ELIMINADO (Portal informativo) -->
    </div>
    
    <!-- Modal para comentarios -->
    @auth
    <div
        x-show="showCommentForm"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
        @click.away="showCommentForm = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/90" @click.stop="showCommentForm = false"></div>
        
        <!-- Modal -->
        <div
            class="relative bg-gray-900 rounded-lg shadow-xl max-w-md w-full mx-4 z-10"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform scale-95 opacity-0"
            x-transition:enter-end="transform scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-95 opacity-0">
            
            <!-- Modal header -->
            <div class="flex justify-between items-center border-b border-gray-800 p-4">
                <h3 class="text-lg font-medium">Comentar: {{ $title->title }}</h3>
                <button @click.stop="showCommentForm = false" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Formulario de comentario -->
            <form action="{{ route('comments.store') }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="commentable_type" value="App\Models\Title">
                <input type="hidden" name="commentable_id" value="{{ $title->id }}">
                
                <div class="mb-4">
                    <textarea 
                        name="content" 
                        rows="4" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                        placeholder="¿Qué te pareció '{{ $title->title }}'?"></textarea>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button 
                        type="button" 
                        @click.stop="showCommentForm = false"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Publicar
                    </button>
                </div>
            </form>
            
            <!-- Mostrar comentarios recientes -->
            <div class="px-4 pb-4">
                <h4 class="text-sm font-semibold mb-2 text-gray-400">Comentarios recientes</h4>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @forelse($title->comments->take(3) as $comment)
                        <div class="bg-gray-800 p-2 rounded text-xs">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium">{{ $comment->profile->name ?? 'Usuario' }}</span>
                                <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-300">{{ Str::limit($comment->content, 100) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-xs">No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                    @endforelse
                </div>
                <div class="mt-2 text-center">
                    <a href="{{ route('titles.show', $title->slug) }}" class="text-red-500 hover:text-red-400 text-xs">
                        Ver todos los comentarios
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endauth
    
    <!-- Modal para trailer -->
    <div
        x-show="showTrailerModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
        @click.away="showTrailerModal = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/90" @click.stop="showTrailerModal = false"></div>
        
        <!-- Modal -->
        <div
            class="relative bg-dorasia-bg-dark rounded-lg shadow-xl max-w-4xl w-full mx-4 z-10"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform scale-95 opacity-0"
            x-transition:enter-end="transform scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-95 opacity-0">
            
            <!-- Modal header -->
            <div class="flex justify-between items-center border-b border-gray-700 p-3">
                <h3 class="text-lg font-medium">{{ $title->title }}</h3>
                <button @click.stop="showTrailerModal = false" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Embedded video -->
            <div class="aspect-w-16 aspect-h-9">
                <div
                    x-data="{ loaded: false }"
                    x-init="$watch('showTrailerModal', value => { loaded = value; })"
                    class="w-full h-full">
                    <template x-if="loaded">
                        @php
                            // Convertir URL de YouTube a embedded
                            $trailerUrl = $title->trailer_url;
                            if (strpos($trailerUrl, 'youtube.com/watch?v=') !== false) {
                                $videoId = substr($trailerUrl, strpos($trailerUrl, 'v=') + 2);
                                if (strpos($videoId, '&') !== false) {
                                    $videoId = substr($videoId, 0, strpos($videoId, '&'));
                                }
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            } elseif (strpos($trailerUrl, 'youtu.be/') !== false) {
                                $videoId = substr($trailerUrl, strpos($trailerUrl, '.be/') + 4);
                                if (strpos($videoId, '?') !== false) {
                                    $videoId = substr($videoId, 0, strpos($videoId, '?'));
                                }
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            } else {
                                $embedUrl = $trailerUrl;
                            }
                        @endphp
                        <iframe
                            class="w-full h-full"
                            :src="'{{ $embedUrl }}'"
                            title="Trailer: {{ $title->title }}"
                            frameborder="0"
                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </template>
                    <div x-show="!loaded" class="w-full h-full flex items-center justify-center bg-black">
                        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-white">Cargando trailer...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>

<style>
    [x-cloak] { display: none !important; }
    
    .aspect-w-16 {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    }
    .aspect-h-9 {
        position: relative;
    }
    .aspect-w-16 iframe {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
    }
    
    /* Ensure tags remain visible on hover */
    .group:hover .absolute.top-1,
    .group:hover .absolute.bottom-1 {
        z-index: 20;
    }
    
    /* Glow effect on hover */
    .group:hover > div {
        filter: drop-shadow(0 0 20px rgba(239, 68, 68, 0.5));
    }
    
    /* Smooth transitions for card animations */
    .group > div {
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
</style>