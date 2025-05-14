@props(['title'])

<div class="relative h-[400px] xs:h-[450px] sm:h-[500px] md:h-[80vh] bg-gradient-to-b from-transparent to-dorasia-bg-dark overflow-hidden">
    <!-- Imagen de fondo -->
    <div class="absolute inset-0 z-0">
        <!-- La imagen de fondo optimizada para dispositivos -->
        <div class="absolute inset-0">
            <img src="{{ asset($title->backdrop) }}" 
                 alt="{{ $title->title }}" 
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null; this.src='{{ asset('backdrops/placeholder.jpg') }}'">
        </div>
        
        <!-- Gradientes adicionales encima del fondo - mejorados para móvil -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-transparent sm:from-black/80 sm:via-black/50"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-dorasia-bg-dark via-dorasia-bg-dark/70 to-transparent sm:via-transparent"></div>
        <!-- Overlay adicional para mejorar la legibilidad en móvil -->
        <div class="absolute inset-0 bg-black/30 sm:bg-black/10"></div>
    </div>
    
    <!-- Contenido -->
    <div class="relative z-10 flex flex-col justify-end sm:justify-center h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 sm:pb-0">
        <div class="w-full max-w-full sm:max-w-lg">
            <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-bold mb-2 sm:mb-4 text-shadow-md">{{ $title->title }}</h1>
            
            @if($title->original_title && $title->original_title !== $title->title)
                <p class="text-gray-300 text-base xs:text-lg sm:text-xl mb-2 text-shadow-sm">{{ $title->original_title }}</p>
            @endif
            
            <div class="flex flex-wrap items-center gap-2 xs:gap-3 sm:space-x-4 mb-2 sm:mb-4">
                <span class="text-xs sm:text-sm bg-dorasia-red px-2 py-0.5 rounded-sm">{{ $title->type === 'movie' ? 'Película' : 'Serie' }}</span>
                <span class="text-xs sm:text-sm">{{ $title->release_year }}</span>
                @if($title->type === 'movie' && $title->duration)
                    <span class="text-xs sm:text-sm">{{ $title->duration }} min</span>
                @endif
            </div>
            
            <div class="flex flex-wrap gap-1.5 xs:gap-2 mb-3 sm:mb-4">
                @foreach($title->genres->take(3) as $genre)
                    <a href="{{ route('catalog.genre', $genre->slug) }}" class="text-[10px] xs:text-xs bg-gray-800 hover:bg-gray-700 px-1.5 xs:px-2 py-0.5 xs:py-1 rounded-full text-gray-300">{{ $genre->name }}</a>
                @endforeach
                @if($title->genres->count() > 3)
                    <span class="text-[10px] xs:text-xs bg-gray-800 px-1.5 xs:px-2 py-0.5 xs:py-1 rounded-full text-gray-300">+{{ $title->genres->count() - 3 }}</span>
                @endif
            </div>
            
            <p class="text-xs xs:text-sm sm:text-base text-gray-300 mb-4 sm:mb-6 line-clamp-2 sm:line-clamp-3 text-shadow-sm">{{ $title->synopsis }}</p>
            
            <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:space-x-4">
                <a href="{{ route('titles.show', $title->slug) }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 xs:px-4 sm:px-5 py-2 sm:py-2.5 bg-dorasia-red hover:bg-dorasia-red-dark text-white font-medium rounded-md transition-colors">
                    <svg class="w-4 h-4 xs:w-5 xs:h-5 mr-1.5 xs:mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $title->type === 'movie' ? 'Ver ahora' : 'Ver detalles' }}
                </a>
                
                @auth
                <button
                    type="button"
                    class="watchlist-toggle inline-flex items-center justify-center w-full sm:w-auto px-3 xs:px-4 sm:px-4 py-2 sm:py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md transition-colors"
                    data-title-id="{{ $title->id }}"
                    onclick="toggleWatchlist({{ $title->id }}, this)">
                    <svg class="w-4 h-4 xs:w-5 xs:h-5 mr-1.5 xs:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="whitespace-nowrap">Mi Lista</span>
                </button>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    .text-shadow-sm {
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    .text-shadow-md {
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
</style>

@auth
@push('scripts')
<script>
    // Esta función se define aquí, pero normalmente estaría en un archivo JS separado
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
            // Actualizar la UI según el resultado
            if (data.status === 'added') {
                button.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
            } else {
                button.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Verificar estado inicial
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.watchlist-toggle');
        
        buttons.forEach(button => {
            const titleId = button.dataset.titleId;
            
            fetch(`/api/watchlist/status/${titleId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.in_watchlist) {
                    button.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>
@endpush
@endauth