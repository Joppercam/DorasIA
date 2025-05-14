@props(['title', 'watchHistory' => null])

<div class="group relative w-[140px] xs:w-[160px] sm:w-[200px] md:w-[240px] flex-shrink-0 px-1 transition duration-300 hover:z-10 touch-manipulation" 
     x-data="{ 
         showDetails: false, 
         showCommentForm: false,
         showTrailerModal: false,
         showShareMenu: false,
         hoverActive: false,
         isTouch: false,
         watchHistory: @json($watchHistory)
     }"
     @touchstart="isTouch = true"
     @mouseenter="hoverActive = true; if(!isTouch) showDetails = true"
     @mouseleave="hoverActive = false; if(!isTouch) showDetails = false"
     @click="if(isTouch && !showDetails) { showDetails = true; $event.preventDefault(); } else if (isTouch && showDetails && $event.target.tagName !== 'BUTTON' && $event.target.tagName !== 'A') { showDetails = false; $event.preventDefault(); }">
    <!-- Tarjeta básica que se expande al hacer hover/tap -->
    <div class="relative transform transition-transform duration-300" :class="{'scale-110': showDetails || hoverActive}">
        <!-- Imagen de portada -->
        <div class="relative pb-[150%] rounded overflow-hidden shadow-lg bg-gray-800">
            <img src="{{ asset($title->poster) }}" 
                 alt="{{ $title->title }}" 
                 class="absolute inset-0 h-full w-full object-cover"
                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                 
                 
            <!-- Gradiente superior para mejorar la visibilidad del overlay de texto -->
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/80 to-transparent opacity-0 transition duration-300 pointer-events-none"
                 :class="{'opacity-100': showDetails || hoverActive}"></div>
            
            <!-- Overlay de contenido (visible en hover/tap) -->
            <div class="absolute inset-0 bg-black/80 opacity-0 transition duration-300 flex flex-col justify-between p-2 xs:p-3 text-[10px] xs:text-xs"
                 :class="{'opacity-100': showDetails || hoverActive}">
                <div>
                    <h3 class="font-bold text-xs xs:text-sm">{{ $title->title }}</h3>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-[10px] xs:text-xs">{{ $title->release_year }}</span>
                        @if($title->vote_average)
                            <span class="flex items-center text-yellow-400 text-[10px] xs:text-xs">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mr-0.5 xs:mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                {{ number_format($title->vote_average, 1) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Sinopsis breve -->
                    <p class="text-gray-300 mt-1.5 xs:mt-2 line-clamp-2 xs:line-clamp-3 text-[10px] xs:text-xs">{{ $title->synopsis }}</p>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col gap-1 mt-2">
                    <!-- Reproducir/Ver/Continuar -->
                    @if($title->type === 'movie')
                        @if(isset($watchHistory) && $watchHistory && $watchHistory->title_id == $title->id && $watchHistory->progress > 0 && $watchHistory->progress < 95)
                            <a href="{{ route('titles.watch', [$title->slug, null, null, $watchHistory->watched_seconds]) }}" class="bg-dorasia-red text-white hover:bg-dorasia-red-dark px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs font-medium flex items-center justify-center transition-colors">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                Continuar ({{ $watchHistory->getFormattedResumeTime() }})
                            </a>
                        @else
                            <a href="{{ route('titles.watch', $title->slug) }}" class="bg-white text-black hover:bg-gray-200 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs font-medium flex items-center justify-center transition-colors">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                Reproducir
                            </a>
                        @endif
                    @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                        @if(isset($watchHistory) && $watchHistory && $watchHistory->episode_id && $watchHistory->progress > 0 && $watchHistory->progress < 95)
                            <a href="{{ route('titles.watch', [$title->slug, $watchHistory->season_number, $watchHistory->episode_number, $watchHistory->watched_seconds]) }}" class="bg-dorasia-red text-white hover:bg-dorasia-red-dark px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs font-medium flex items-center justify-center transition-colors">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="truncate">Continuar S{{ $watchHistory->season_number }}:E{{ $watchHistory->episode_number }}</span>
                            </a>
                        @else
                            <a href="{{ route('titles.watch', [$title->slug, $title->seasons->first()->number, $title->seasons->first()->episodes->first()->number]) }}" class="bg-white text-black hover:bg-gray-200 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs font-medium flex items-center justify-center transition-colors">
                                <svg class="w-2.5 h-2.5 xs:w-3 xs:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                Ver episodios
                            </a>
                        @endif
                    @endif
                    
                    <div class="flex gap-1 justify-between">
                        <!-- Botón de Mi Lista -->
                        @auth
                        <button
                            type="button"
                            class="watchlist-toggle flex-1 bg-gray-800 hover:bg-gray-700 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-colors active:bg-gray-600"
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
                            class="flex-1 bg-gray-800 hover:bg-gray-700 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-colors active:bg-gray-600">
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
                            class="flex-1 bg-gray-800 hover:bg-gray-700 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-colors active:bg-gray-600">
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
                                class="w-full bg-gray-800 hover:bg-gray-700 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-colors active:bg-gray-600">
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
                    
                    <!-- Botón para más información -->
                    <a 
                        href="{{ route('titles.show', $title->slug) }}" 
                        class="mt-1 bg-gray-700 hover:bg-gray-600 px-2 py-1.5 rounded-sm text-center text-[10px] xs:text-xs transition-colors active:bg-gray-500">
                        Más información
                    </a>
                </div>
            </div>
            
            <!-- Country tag (top-right) -->
            <div class="absolute top-1 right-1 bg-black/80 px-1.5 py-0.5 text-[0.65rem] uppercase font-semibold z-20
                       @if($title->country == 'Corea del Sur') text-cyan-400
                       @elseif($title->country == 'Japón') text-red-400
                       @elseif(in_array($title->country, ['China', 'Taiwán', 'Hong Kong'])) text-amber-400
                       @else text-white @endif">
                {{ Str::limit($title->country, 5, '') }}
            </div>
            
            <!-- Rating tag (top-left) -->
            @if(!empty($title->vote_average))
            <div class="absolute top-1 left-1 bg-black/80 rounded-br px-1.5 py-0.5 text-[0.65rem] font-semibold z-20
                       @if($title->vote_average >= 7) text-green-400
                       @elseif($title->vote_average >= 5) text-yellow-400
                       @else text-red-400 @endif">
                <div class="flex items-center">
                    <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    {{ number_format($title->vote_average, 1) }}
                </div>
            </div>
            @endif
            
            <!-- Category tag (bottom-left) -->
            @if($title->categories()->count() > 0)
            <div class="absolute bottom-1 left-1 bg-blue-700/80 rounded px-1.5 py-0.5 text-[0.65rem] uppercase font-semibold z-20">
                {{ Str::limit($title->categories()->first()->name, 8, '') }}
            </div>
            @endif
            
            <!-- Streaming platform tag (bottom-right) -->
            @if(!empty($title->streaming_platforms))
            @php
                $platforms = explode(',', $title->streaming_platforms);
                $firstPlatform = trim($platforms[0]);
            @endphp
            <div class="absolute bottom-1 right-1 bg-purple-700/80 rounded px-1.5 py-0.5 text-[0.65rem] uppercase font-semibold z-20">
                {{ Str::limit($firstPlatform, 6, '') }}
            </div>
            @endif
            
            <!-- Category tag (bottom-left) -->
            @if($title->category)
            <div class="absolute bottom-1 left-1 bg-blue-600/90 px-1.5 py-0.5 text-[0.65rem] uppercase font-semibold text-white rounded-tr">
                {{ Str::limit($title->category->name, 8, '') }}
            </div>
            @endif
            
            <!-- Streaming platform tag (bottom-right) -->
            @if(!empty($title->streaming_platforms))
            <div class="absolute bottom-1 right-1 bg-purple-600/90 px-1.5 py-0.5 text-[0.65rem] uppercase font-semibold text-white rounded-tl">
                @php
                    $platforms = explode(',', $title->streaming_platforms);
                    echo Str::limit($platforms[0], 7, '');
                @endphp
            </div>
            @endif
            
            <!-- Botón de reproducir trailer -->
            @if(!empty($title->trailer_url))
            <div class="absolute bottom-8 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <button
                    @click="showTrailerModal = true; $event.stopPropagation();"
                    class="bg-black/60 hover:bg-black/80 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            @endif
        </div>
        
        <h3 class="text-xs truncate mt-1 group-hover:hidden">{{ $title->title }}</h3>
        
        <!-- Indicador de progreso -->
        @if(isset($watchHistory) && $watchHistory && $watchHistory->progress > 0)
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                <div class="absolute left-0 top-0 h-full bg-red-600" style="width: {{ $watchHistory->progress }}%"></div>
            </div>
        @endif
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
</div>

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
</style>