@props(['title'])

<div class="w-[180px] md:w-[200px] flex-shrink-0 px-1 md:px-2 scroll-snap-align-start" style="scroll-snap-align: start;" x-data="{ trailerModal: false }">
    <div class="block dorasia-card h-full">
        <div class="relative pb-[150%] rounded overflow-hidden shadow-lg mb-2 bg-gray-800">
            <!-- Usar la imagen guardada en el campo poster -->
            <a href="{{ route('titles.show', $title->slug) }}" class="block">
                <img src="{{ asset($title->poster) }}" 
                     alt="{{ $title->title }}" 
                     class="absolute inset-0 h-full w-full object-cover"
                     onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                
                <!-- Gradiente superior permanente para el título -->
                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/90 to-transparent pointer-events-none">
                    <h3 class="text-white text-xs p-2 font-medium line-clamp-2">{{ $title->title }}</h3>
                </div>
            </a>
            
            <!-- Etiqueta de país en la esquina superior -->
            <div class="absolute top-0 right-0 bg-black/80 px-1.5 py-0.5 text-xs uppercase font-semibold
                        @if($title->country == 'Corea del Sur') text-cyan-400
                        @elseif($title->country == 'Japón') text-red-400
                        @elseif(in_array($title->country, ['China', 'Taiwán', 'Hong Kong'])) text-amber-400
                        @else text-white @endif">
                {{ Str::limit($title->country, 5, '') }}
            </div>
            
            <!-- Valoración en la esquina inferior izquierda -->
            <div class="absolute bottom-2 left-2 bg-black/80 rounded px-2 py-1 text-xs">
                <x-rating-stars :title-id="$title->id" :show-count="false" size="sm" />
            </div>
            
            <!-- Botón de reproducir trailer -->
            @if(!empty($title->trailer_url))
            <div class="absolute bottom-0 left-0 right-0 flex justify-center mb-2">
                <button
                    @click="trailerModal = true"
                    class="bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            @endif
            
            <!-- Overlay con información -->
            <a href="{{ route('titles.show', $title->slug) }}" class="block">
                <div class="absolute inset-0 bg-black/80 opacity-0 hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-3 text-xs">
                    <div>
                        <p class="font-bold">{{ $title->title }}</p>
                        <p class="text-gray-400">{{ $title->release_year }}</p>
                        
                        <!-- Géneros -->
                        <div class="flex flex-wrap mt-1 gap-1">
                            @foreach($title->genres->take(3) as $genre)
                                <span class="bg-red-600 px-1.5 py-0.5 rounded-sm text-xs">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Plataformas de streaming (si están disponibles) -->
                    @if(!empty($title->streaming_platforms))
                    <div class="mt-2">
                        <p class="text-gray-300 mb-1 text-xs">Disponible en:</p>
                        <div class="flex flex-wrap gap-1">
                            @php
                                $platforms = explode(',', $title->streaming_platforms);
                            @endphp
                            
                            @foreach($platforms as $platform)
                                <span class="bg-gray-800 px-1.5 py-0.5 rounded-sm text-xs">{{ trim($platform) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Indicador de trailer disponible -->
                    @if(!empty($title->trailer_url))
                    <div class="mt-2">
                        <span class="inline-flex items-center bg-red-600 px-2 py-1 rounded-sm text-xs">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                            </svg>
                            Ver trailer
                        </span>
                    </div>
                    @endif
                </div>
            </a>
        </div>
        <!-- Removido el título duplicado que estaba debajo de la tarjeta -->
        
        <!-- Modal para mostrar el trailer -->
        @if(!empty($title->trailer_url))
        <div
            x-show="trailerModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/80" @click="trailerModal = false"></div>
            
            <!-- Modal -->
            <div
                class="relative bg-dorasia-bg-dark rounded-lg shadow-xl max-w-5xl w-full mx-4 z-10"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform scale-95 opacity-0"
                x-transition:enter-end="transform scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform scale-100 opacity-100"
                x-transition:leave-end="transform scale-95 opacity-0">
                
                <!-- Modal header -->
                <div class="flex justify-between items-center border-b border-gray-700 p-4">
                    <h3 class="text-lg font-medium">Trailer: {{ $title->title }}</h3>
                    <button @click="trailerModal = false" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Embedded video -->
                <div class="aspect-w-16 aspect-h-9">
                    @php
                        // Convertir URL de YouTube a embedded, pero SIN autoplay
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
                    <!-- El iframe se cargará solo cuando se muestre el modal -->
                    <div
                        x-data="{ loaded: false }"
                        x-init="$watch('trailerModal', value => { loaded = value; })"
                        class="w-full h-full">
                        <template x-if="loaded">
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
        @endif
    </div>
</div>

<!-- Estilos para el aspect ratio -->
<style>
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
    [x-cloak] { display: none !important; }
</style>