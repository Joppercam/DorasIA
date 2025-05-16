@props(['titles'])

@if($titles && $titles->isNotEmpty())
    @php
        $title = $titles->first();
    @endphp
    
    <div class="relative h-[400px] md:h-[600px] bg-gradient-to-b from-transparent to-gray-900 overflow-hidden">
        <!-- Imagen de fondo -->
        <div class="absolute inset-0">
            @if($title->backdrop_path)
                <img src="{{ $title->backdrop_url }}" 
                     alt="{{ $title->title }}" 
                     class="w-full h-full object-cover"
                     onerror="this.onerror=null; this.src='/backdrops/placeholder.jpg'">
            @else
                <img src="/backdrops/placeholder.jpg" 
                     alt="{{ $title->title }}" 
                     class="w-full h-full object-cover">
            @endif
            
            <!-- Gradientes para mejorar legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        </div>
        
        <!-- Contenido -->
        <div class="relative z-10 flex flex-col justify-end h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                    {{ $title->title }}
                </h1>
                <p class="text-lg text-gray-300 mb-6 line-clamp-3">
                    {{ $title->description }}
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('titles.show', $title->slug) }}" 
                       class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        Ver detalles
                    </a>
                    @auth
                        <button type="button"
                                onclick="toggleWatchlist({{ $title->id }})"
                                class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Mi lista
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Hero de respaldo -->
    <div class="relative h-[400px] md:h-[600px] bg-gradient-to-b from-transparent to-gray-900">
        <div class="absolute inset-0 bg-gradient-to-r from-pink-900 to-purple-900 opacity-60"></div>
        <div class="relative z-10 flex flex-col justify-center items-center h-full text-center px-4">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                Doramas Románticos
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl">
                Explora nuestra colección de los mejores doramas románticos asiáticos
            </p>
        </div>
    </div>
@endif

@push('scripts')
<script>
function toggleWatchlist(titleId) {
    // Implementar lógica de watchlist aquí
    console.log('Toggle watchlist for title:', titleId);
}
</script>
@endpush