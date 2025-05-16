@props(['title'])

<div 
    x-data="{ 
        showInfo: false,
        trailerModal: false,
        showShareMenu: false,
        showRatingModal: false,
        userRating: 0
    }" 
    class="relative h-[600px] lg:h-[80vh] bg-gradient-to-b from-transparent to-dorasia-bg-dark overflow-hidden">
    
    <!-- Imagen/video de fondo -->
    <div class="absolute inset-0 z-0">
        <!-- Video preview (solo para pantallas grandes) -->
        <div class="absolute inset-0 hidden md:block">
            @if(!empty($title->trailer_url))
                <div class="w-full h-full bg-black opacity-0 hover:opacity-100 transition-opacity duration-300" 
                     x-show="!showInfo"
                     x-cloak>
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                        <button 
                            @click="trailerModal = true; showInfo = false"
                            class="bg-white/20 hover:bg-white/30 text-white rounded-full w-20 h-20 flex items-center justify-center group transition">
                            <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Imagen de fondo -->
        <img src="{{ asset($title->backdrop) }}" 
             alt="{{ $title->title }}" 
             class="w-full h-full object-cover"
             onerror="this.onerror=null; this.src='{{ asset('backdrops/placeholder.jpg') }}'">
        
        <!-- Gradientes adicionales encima del fondo -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-dorasia-bg-dark via-dorasia-bg-dark/60 to-transparent"></div>
    </div>
    
    <!-- Contenido superpuesto -->
    <div class="relative z-10 flex flex-col justify-center h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg">
            <!-- Logo del título si existe (estilo Netflix) -->
            @if(!empty($title->logo))
                <img src="{{ asset($title->logo) }}" alt="{{ $title->title }}" class="w-2/3 mb-4">
            @else
                <h1 class="text-4xl md:text-6xl font-bold mb-2 text-white">{{ $title->title }}</h1>
            @endif
            
            @if($title->original_title && $title->original_title !== $title->title)
                <p class="text-gray-300 text-xl mb-2">{{ $title->original_title }}</p>
            @endif
            
            <div class="flex items-center space-x-4 mb-4">
                <span class="text-sm bg-red-600 px-2 py-0.5 rounded-sm">{{ $title->type === 'movie' ? 'Película' : 'Serie' }}</span>
                <span class="text-sm">{{ $title->release_year }}</span>
                @if($title->type === 'movie' && $title->duration)
                    <span class="text-sm">{{ $title->duration }} min</span>
                @endif
                @if(!empty($title->content_rating))
                    <span class="text-sm border border-gray-600 px-1">{{ $title->content_rating }}</span>
                @endif
                @if(!empty($title->vote_average))
                    <span class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        {{ number_format($title->vote_average, 1) }}
                    </span>
                @endif
            </div>
            
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($title->genres as $genre)
                    <a href="{{ route('catalog.genre', $genre->slug) }}" class="text-xs bg-gray-800 hover:bg-gray-700 px-2 py-1 rounded-full text-gray-300">{{ $genre->name }}</a>
                @endforeach
            </div>
            
            <p class="text-gray-300 mb-6 line-clamp-3 md:line-clamp-none">{{ $title->synopsis }}</p>
            
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('titles.show', $title->slug) }}" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Más información
                </a>
                
                @auth
                <button
                    type="button"
                    class="watchlist-toggle inline-flex items-center px-4 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md transition"
                    data-title-id="{{ $title->id }}"
                    onclick="toggleWatchlist({{ $title->id }}, this)">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Mi Lista
                </button>
                @endauth
                
                @if(!empty($title->trailer_url))
                    <button
                        type="button"
                        @click="trailerModal = true"
                        class="inline-flex items-center px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"></path>
                        </svg>
                        Trailer
                    </button>
                @endif
                
                <!-- Botón de valoración -->
                @auth
                <button
                    type="button"
                    @click="showRatingModal = true"
                    class="inline-flex items-center px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Valorar
                </button>
                @endauth
                
                <!-- Botón de compartir -->
                <div class="relative">
                    <button
                        type="button"
                        @click="showShareMenu = !showShareMenu"
                        class="inline-flex items-center px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"></path>
                        </svg>
                        Compartir
                    </button>
                    
                    <!-- Menú de compartir -->
                    <div 
                        x-show="showShareMenu"
                        @click.away="showShareMenu = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-20 mt-2 w-48 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5"
                        x-cloak>
                        <div class="py-1">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('titles.show', $title->slug)) }}" target="_blank" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('titles.show', $title->slug)) }}&text=Mira {{ $title->title }} en Dorasia" target="_blank" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                                Twitter
                            </a>
                            <a href="https://api.whatsapp.com/send?text=Mira {{ $title->title }} en Dorasia: {{ urlencode(route('titles.show', $title->slug)) }}" target="_blank" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                                WhatsApp
                            </a>
                            <button 
                                onclick="copyToClipboard('{{ route('titles.show', $title->slug) }}')"
                                class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700">
                                Copiar enlace
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de trailer -->
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
        <div class="fixed inset-0 bg-black/90" @click="trailerModal = false"></div>
        
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
                <div
                    x-data="{ loaded: false }"
                    x-init="$watch('trailerModal', value => { loaded = value; })"
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
    
    <!-- Modal de valoración -->
    @auth
    <div
        x-show="showRatingModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80" @click="showRatingModal = false"></div>
        
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
            <div class="p-4 border-b border-gray-800">
                <h3 class="text-lg font-bold">Valorar: {{ $title->title }}</h3>
            </div>
            
            <!-- Formulario de valoración -->
            <form id="rating-hero-form" action="{{ route('ratings.store') }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="title_id" value="{{ $title->id }}">
                
                <!-- Estrellas para valoración -->
                <div class="flex justify-center items-center mb-6" x-data="{ rating: 0 }">
                    @for($i = 1; $i <= 10; $i++)
                        <button 
                            type="button"
                            @click="rating = {{ $i }}; document.getElementById('rating-hero-value').value = {{ $i }}; userRating = {{ $i }}"
                            :class="rating >= {{ $i }} ? 'text-yellow-500' : 'text-gray-500'"
                            class="text-3xl hover:text-yellow-400 transition-colors focus:outline-none">
                            ★
                        </button>
                    @endfor
                    <input type="hidden" id="rating-hero-value" name="rating" :value="rating">
                    <span class="ml-3 text-xl font-bold" x-text="rating ? rating + '/10' : ''"></span>
                </div>
                
                <!-- Campo de comentario -->
                <div class="mb-4">
                    <label for="review" class="block text-sm font-medium text-gray-300 mb-1">¿Qué te pareció?</label>
                    <textarea 
                        name="review" 
                        id="review"
                        rows="3" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                        placeholder="Escribe una reseña breve (opcional)"></textarea>
                </div>
                
                <!-- Botones de acciones -->
                <div class="flex justify-end gap-2">
                    <button 
                        type="button" 
                        @click="showRatingModal = false"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Enviar valoración
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth
</div>

<!-- Script para copiar al portapapeles -->
<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
            .then(() => {
                // Feedback de éxito
                const shareMenu = document.querySelector('[x-show="showShareMenu"]');
                const feedback = document.createElement('div');
                feedback.className = 'px-4 py-2 text-sm text-green-400';
                feedback.textContent = '¡Enlace copiado!';
                shareMenu.appendChild(feedback);
                
                setTimeout(() => {
                    feedback.remove();
                }, 2000);
            })
            .catch(err => {
                console.error('Error al copiar: ', err);
            });
    } else {
        // Fallback para navegadores que no soportan clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            // Feedback de éxito
            alert('Enlace copiado al portapapeles');
        } catch (err) {
            console.error('Error al copiar: ', err);
        }
        
        document.body.removeChild(textArea);
    }
}
</script>

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
</style>