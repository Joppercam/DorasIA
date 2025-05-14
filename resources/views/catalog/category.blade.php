<x-app-layout>
    <x-slot name="title">{{ $category->name }}</x-slot>
    
    <!-- Hero Banner mejorado para la categoría -->
    <div class="hero-banner relative overflow-hidden bg-gradient-to-b from-transparent to-dorasia-bg-dark" 
         x-data="{ expanded: false }"
         :class="{ 'hero-banner--expanded': expanded, 'hero-banner--collapsed': !expanded }">
        
        <!-- Imagen de fondo -->
        <div class="absolute inset-0 z-0">
            @if(!empty($category->hero_image))
                <img src="{{ asset('storage/' . $category->hero_image) }}" alt="{{ $category->name }}" class="hero-banner__image">
            @else
                <img src="{{ asset('images/heroes/' . Str::slug($category->name) . '.jpg') }}" alt="{{ $category->name }}" class="hero-banner__image">
            @endif
            
            <!-- Gradientes mejorados -->
            <div class="hero-banner__overlay"></div>
        </div>
        
        <!-- Contenido -->
        <div class="relative z-10 flex flex-col justify-end sm:justify-center h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="hero-banner__content max-w-2xl">
                <div class="category-badge">{{ $category->titles_count ?? $titles->count() }} {{ $titles->count() == 1 ? 'Título' : 'Títulos' }}</div>
                
                <h1 class="text-4xl md:text-6xl font-bold mb-3 sm:mb-4 tracking-tight">{{ $category->name }}</h1>
                
                @if($category->description)
                    <div class="hero-banner__description">
                        <p class="text-base sm:text-lg text-gray-300 mb-3 sm:mb-4 leading-relaxed">
                            {{ $category->description }}
                        </p>
                    </div>
                @else
                    <p class="text-base sm:text-lg text-gray-300 mb-3 sm:mb-4 leading-relaxed">
                        Explora nuestra colección de títulos de {{ $category->name }} seleccionados especialmente para ti.
                    </p>
                @endif
                
                <!-- Botón para expandir/colapsar -->
                <button 
                    @click="expanded = !expanded"
                    class="inline-flex items-center text-sm text-gray-300 hover:text-white transition-colors duration-200">
                    <span x-text="expanded ? 'Mostrar menos' : 'Mostrar más'"></span>
                    <svg 
                        class="w-5 h-5 ml-1 transition-transform duration-200" 
                        :class="{ 'rotate-180': expanded }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24" 
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal del catálogo -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="catalogPage()">
        @if($titles->count() > 0)
            <!-- Opciones de filtrado y vista -->
            <div class="catalog-options mb-6">
                <div class="catalog-options__group">
                    <label for="sort-select" class="text-sm text-gray-400">Ordenar por:</label>
                    <select id="sort-select" class="catalog-options__select text-sm" x-model="sortOrder" @change="changeSort($event.target.value)">
                        <option value="latest">Más recientes</option>
                        <option value="oldest">Más antiguas</option>
                        <option value="rating">Mejor valoradas</option>
                        <option value="title">Título A-Z</option>
                    </select>
                </div>
                
                <div class="catalog-options__group">
                    <div class="catalog-options__view-toggle">
                        <button 
                            @click="toggleView('grid')" 
                            class="catalog-options__view-button" 
                            :class="{ 'catalog-options__view-button--active': viewType === 'grid' }"
                            aria-label="Ver en cuadrícula">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4h4v4H4V4zm6 0h4v4h-4V4zm6 0h4v4h-4V4zM4 10h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4zM4 16h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z"></path>
                            </svg>
                        </button>
                        <button 
                            @click="toggleView('list')" 
                            class="catalog-options__view-button" 
                            :class="{ 'catalog-options__view-button--active': viewType === 'list' }"
                            aria-label="Ver en lista">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <button
                        @click="filterVisible = !filterVisible"
                        class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm transition-colors ml-2"
                        aria-label="Filtros">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtros</span>
                    </button>
                </div>
            </div>
            
            <!-- Panel de filtros avanzados (oculto por defecto) -->
            <div 
                x-show="filterVisible" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4"
                class="bg-gray-900/60 backdrop-blur-sm rounded-lg p-4 mb-6 border border-gray-800">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Filtro por año -->
                    <div>
                        <h3 class="text-sm font-medium mb-2">Año</h3>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('year', 'latest')">Últimos</button>
                            <button class="px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('year', '2020s')">2020s</button>
                            <button class="px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('year', '2010s')">2010s</button>
                            <button class="px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('year', '2000s')">2000s</button>
                            <button class="px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('year', 'older')">Anteriores</button>
                        </div>
                    </div>
                    
                    <!-- Filtro por tipo -->
                    <div>
                        <h3 class="text-sm font-medium mb-2">Tipo</h3>
                        <div class="flex gap-3">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox text-red-600" @change="applyFilter('type', 'movie')">
                                <span class="ml-1.5 text-sm">Películas</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox text-red-600" @change="applyFilter('type', 'series')">
                                <span class="ml-1.5 text-sm">Series</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Filtro por valoración -->
                    <div>
                        <h3 class="text-sm font-medium mb-2">Valoración</h3>
                        <div class="flex flex-wrap gap-2">
                            <button class="inline-flex items-center px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('rating', '8')">
                                <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                8+
                            </button>
                            <button class="inline-flex items-center px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('rating', '7')">7+</button>
                            <button class="inline-flex items-center px-2 py-1 text-xs bg-gray-800 hover:bg-gray-700 rounded transition-colors" @click="applyFilter('rating', '6')">6+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros activos y botón para limpiar -->
                <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-800">
                    <div class="flex flex-wrap gap-2">
                        <!-- Aquí irían los tags de filtros activos -->
                    </div>
                    <button 
                        @click="applyFilter('clear', '')"
                        class="text-xs text-red-500 hover:text-red-400 transition-colors">
                        Limpiar filtros
                    </button>
                </div>
            </div>
            
            <!-- Navegación por géneros (rápido acceso a géneros) -->
            <div class="filter-navigation mb-6 pb-2">
                @foreach($category->genres ?? \App\Models\Genre::take(10)->get() as $genre)
                    <a href="{{ route('catalog.genre', $genre->slug) }}" class="filter-navigation__item">
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>
            
            <!-- Sección destacada - Mejor valorados de esta categoría -->
            <div class="featured-section mb-8">
                <h2 class="featured-section__title">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Mejor valorados
                </h2>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($titles->sortByDesc('vote_average')->take(5) as $title)
                        <x-netflix-card :title="$title" />
                    @endforeach
                </div>
            </div>
            
            <!-- Contenedor principal de títulos que cambia según la vista seleccionada -->
            <div class="catalog-container" :class="viewType">
                <!-- Vista en cuadrícula -->
                <div x-show="viewType === 'grid'" class="catalog-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($titles as $title)
                        <x-netflix-card :title="$title" />
                    @endforeach
                </div>
                
                <!-- Vista en lista -->
                <div x-show="viewType === 'list'" class="catalog-list space-y-4">
                    @foreach($titles as $title)
                        <div class="dorasia-card flex bg-gray-900/40 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 hover:transform hover:scale-[1.01]">
                            <div class="dorasia-card__poster w-24 xs:w-32 sm:w-40 md:w-48 flex-shrink-0">
                                <div class="relative pb-[150%]">
                                    @if(!empty($title->poster))
                                        <img src="{{ asset('storage/' . $title->poster) }}" alt="{{ $title->title }}" class="absolute inset-0 h-full w-full object-cover">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-gray-800 text-gray-600">
                                            <span>Sin imagen</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge de tipo -->
                                    <div class="absolute top-2 right-2">
                                        <span class="text-xs bg-red-600 px-1.5 py-0.5 rounded-sm">
                                            {{ $title->type === 'movie' ? 'Película' : 'Serie' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dorasia-card__info p-4 flex flex-col flex-grow">
                                <h3 class="dorasia-card__title text-base sm:text-lg font-semibold mb-1">{{ $title->title }}</h3>
                                
                                <div class="dorasia-card__meta flex flex-wrap items-center gap-x-3 gap-y-1 mb-2">
                                    <span class="text-sm text-gray-400">{{ $title->release_year }}</span>
                                    
                                    @if($title->vote_average)
                                        <span class="flex items-center text-yellow-400 text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ number_format($title->vote_average, 1) }}
                                        </span>
                                    @endif
                                    
                                    @if($title->duration)
                                        <span class="text-sm text-gray-400">{{ $title->duration }} min</span>
                                    @endif
                                </div>
                                
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @foreach($title->genres->take(3) as $genre)
                                        <a href="{{ route('catalog.genre', $genre->slug) }}" class="text-xs bg-gray-800 hover:bg-gray-700 px-2 py-0.5 rounded-full text-gray-300">{{ $genre->name }}</a>
                                    @endforeach
                                </div>
                                
                                <p class="dorasia-card__description text-sm text-gray-400 line-clamp-2 mb-4">
                                    {{ $title->synopsis }}
                                </p>
                                
                                <div class="dorasia-card__actions mt-auto flex flex-wrap gap-2">
                                    <a href="{{ route('titles.show', $title->slug) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        Ver detalles
                                    </a>
                                    
                                    <button 
                                        type="button"
                                        class="watchlist-toggle inline-flex items-center px-3 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded-md text-sm transition-colors"
                                        data-title-id="{{ $title->id }}"
                                        onclick="toggleWatchlist({{ $title->id }}, this)">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Mi Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Paginación -->
            <div class="mt-8">
                {{ $titles->links() }}
            </div>
            
            <!-- Trigger para carga infinita -->
            <div class="infinite-scroll__trigger h-4 invisible"></div>
            
            <!-- Loader para carga infinita -->
            <div class="infinite-scroll__loader" x-show="loading">
                <div class="infinite-scroll__spinner"></div>
                <span>Cargando más títulos...</span>
            </div>
            
            <!-- Recomendaciones personalizadas -->
            @auth
                @php
                    // Esta parte debe ser implementada en el controlador para ser más efectiva
                    $personalRecommendations = \App\Models\Title::where('category_id', $category->id)
                        ->whereNotIn('id', $titles->pluck('id')->toArray())
                        ->inRandomOrder()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($personalRecommendations->count() > 0)
                    <div class="featured-section mt-12 animate-on-scroll">
                        <h2 class="featured-section__title">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"></path>
                            </svg>
                            Recomendado para ti
                        </h2>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($personalRecommendations as $title)
                                <x-netflix-card :title="$title" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth
        @else
            <div class="bg-gray-900 rounded-lg p-8 text-center my-8">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-xl font-semibold">No hay títulos disponibles</h3>
                <p class="mt-1 text-gray-400">Aún no hay títulos en esta categoría.</p>
                <a href="{{ route('catalog.index') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Ver todo el catálogo
                </a>
            </div>
        @endif
    </div>
    
    @push('scripts')
    <script>
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
    </script>
    @endpush
</x-app-layout>