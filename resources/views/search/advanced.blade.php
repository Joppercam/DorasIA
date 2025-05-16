<x-app-layout>
    <x-slot name="title">Búsqueda Avanzada</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-8">Búsqueda Avanzada</h1>
        
        <div class="bg-gray-900 rounded-lg p-6" x-data="advancedSearch()">
            <form @submit.prevent="search">
                <!-- Búsqueda por texto -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Buscar por texto</label>
                    <input type="text" 
                           x-model="query"
                           placeholder="Título, actor, sinopsis..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <!-- Tipo de contenido -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Tipo de contenido</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="radio" x-model="type" value="all" class="mr-2">
                            <span>Todo</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" x-model="type" value="titles" class="mr-2">
                            <span>Títulos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" x-model="type" value="people" class="mr-2">
                            <span>Personas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" x-model="type" value="news" class="mr-2">
                            <span>Noticias</span>
                        </label>
                    </div>
                </div>
                
                <!-- Filtros para títulos -->
                <div x-show="type === 'all' || type === 'titles'" class="space-y-6 mb-6">
                    <!-- Géneros -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Géneros</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-48 overflow-y-auto">
                            @foreach($genres as $genre)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           value="{{ $genre->id }}"
                                           x-model="filters.genre"
                                           class="mr-2">
                                    <span class="text-sm">{{ $genre->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Categorías -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Categorías</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($categories as $category)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           value="{{ $category->id }}"
                                           x-model="filters.category"
                                           class="mr-2">
                                    <span class="text-sm">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- País de origen -->
                    <div>
                        <label class="block text-sm font-medium mb-2">País de origen</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach($countries as $country)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           value="{{ $country }}"
                                           x-model="filters.country"
                                           class="mr-2">
                                    <span class="text-sm">{{ $country }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Año de lanzamiento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Año desde</label>
                            <input type="number" 
                                   x-model="filters.year_from"
                                   min="1900"
                                   max="{{ date('Y') }}"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Año hasta</label>
                            <input type="number" 
                                   x-model="filters.year_to"
                                   min="1900"
                                   max="{{ date('Y') }}"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    
                    <!-- Valoración mínima -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Valoración mínima</label>
                        <input type="range" 
                               x-model="filters.rating"
                               min="0"
                               max="10"
                               step="0.5"
                               class="w-full">
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>0</span>
                            <span x-text="`${filters.rating} estrellas`"></span>
                            <span>10</span>
                        </div>
                    </div>
                    
                    <!-- Tipo de medio -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Tipo de medio</label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" x-model="filters.media_type" value="" class="mr-2">
                                <span>Todos</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="filters.media_type" value="movie" class="mr-2">
                                <span>Películas</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="filters.media_type" value="series" class="mr-2">
                                <span>Series</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros para personas -->
                <div x-show="type === 'people'" class="mb-6">
                    <label class="block text-sm font-medium mb-2">Departamento</label>
                    <select x-model="filters.department"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Todos</option>
                        <option value="Acting">Actuación</option>
                        <option value="Directing">Dirección</option>
                        <option value="Writing">Guión</option>
                        <option value="Production">Producción</option>
                    </select>
                </div>
                
                <!-- Filtros para noticias -->
                <div x-show="type === 'news'" class="space-y-4 mb-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="filters.featured" class="mr-2">
                            <span>Solo noticias destacadas</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Fecha desde</label>
                            <input type="date" 
                                   x-model="filters.date_from"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Fecha hasta</label>
                            <input type="date" 
                                   x-model="filters.date_to"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>
                
                <!-- Ordenamiento -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Ordenar por</label>
                    <select x-model="sort"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="relevance">Relevancia</option>
                        <option value="newest">Más reciente</option>
                        <option value="oldest">Más antiguo</option>
                        <option value="rating" x-show="type === 'all' || type === 'titles'">Mejor valorado</option>
                        <option value="popular" x-show="type === 'all' || type === 'titles'">Más popular</option>
                        <option value="name" x-show="type === 'people'">Nombre</option>
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition">
                        Buscar
                    </button>
                    <button type="button" 
                            @click="resetForm"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                        Limpiar
                    </button>
                </div>
            </form>
            
            <!-- Resultados -->
            <div x-show="results.length > 0" class="mt-8">
                <h2 class="text-xl font-bold mb-4">
                    Resultados (<span x-text="meta.total"></span>)
                </h2>
                
                <!-- Facetas -->
                <div x-show="facets" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div x-show="facets.genres && facets.genres.length > 0">
                        <h3 class="font-medium mb-2">Géneros encontrados</h3>
                        <div class="bg-gray-800 rounded-lg p-3 space-y-1 max-h-40 overflow-y-auto">
                            <template x-for="genre in facets.genres" :key="genre.id">
                                <div class="flex justify-between text-sm">
                                    <span x-text="genre.name"></span>
                                    <span class="text-gray-400" x-text="`(${genre.count})`"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div x-show="facets.categories && facets.categories.length > 0">
                        <h3 class="font-medium mb-2">Categorías encontradas</h3>
                        <div class="bg-gray-800 rounded-lg p-3 space-y-1 max-h-40 overflow-y-auto">
                            <template x-for="category in facets.categories" :key="category.id">
                                <div class="flex justify-between text-sm">
                                    <span x-text="category.name"></span>
                                    <span class="text-gray-400" x-text="`(${category.count})`"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div x-show="facets.years && facets.years.length > 0">
                        <h3 class="font-medium mb-2">Décadas</h3>
                        <div class="bg-gray-800 rounded-lg p-3 space-y-1 max-h-40 overflow-y-auto">
                            <template x-for="year in facets.years" :key="year.decade">
                                <div class="flex justify-between text-sm">
                                    <span x-text="year.label"></span>
                                    <span class="text-gray-400" x-text="`(${year.count})`"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de resultados -->
                <div class="space-y-4">
                    <template x-for="result in results" :key="result.id">
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition">
                            <a :href="result.url" class="flex gap-4">
                                <img :src="result.poster_url" 
                                     :alt="result.title"
                                     class="w-24 h-36 object-cover rounded"
                                     onerror="this.src='/images/placeholder.jpg'">
                                
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium mb-1" x-text="result.title"></h3>
                                    
                                    <div class="text-sm text-gray-400 space-y-1">
                                        <template x-if="result.type === 'title'">
                                            <div>
                                                <span x-text="result.release_year"></span>
                                                <span class="mx-2">•</span>
                                                <span x-text="result.media_type === 'movie' ? 'Película' : 'Serie'"></span>
                                                <span class="mx-2">•</span>
                                                <span x-text="result.country"></span>
                                                <template x-if="result.vote_average">
                                                    <span>
                                                        <span class="mx-2">•</span>
                                                        <span class="text-yellow-400">★</span>
                                                        <span x-text="result.vote_average.toFixed(1)"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        
                                        <template x-if="result.type === 'person'">
                                            <div>
                                                <span x-text="result.department"></span>
                                                <span class="mx-2">•</span>
                                                <span x-text="`${result.titles_count} títulos`"></span>
                                            </div>
                                        </template>
                                        
                                        <template x-if="result.type === 'news'">
                                            <div>
                                                <span x-text="new Date(result.published_at).toLocaleDateString()"></span>
                                                <template x-if="result.featured">
                                                    <span class="mx-2 text-red-500">• Destacado</span>
                                                </template>
                                            </div>
                                        </template>
                                        
                                        <p x-text="result.synopsis || result.excerpt" class="line-clamp-2"></p>
                                        
                                        <template x-if="result.genres">
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <template x-for="genre in result.genres" :key="genre">
                                                    <span class="bg-gray-700 px-2 py-1 rounded text-xs" x-text="genre"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
                
                <!-- Paginación -->
                <div x-show="meta.last_page > 1" class="mt-8 flex justify-center">
                    <nav class="flex gap-2">
                        <button @click="changePage(meta.current_page - 1)"
                                :disabled="meta.current_page === 1"
                                class="px-3 py-1 bg-gray-800 rounded disabled:opacity-50">
                            Anterior
                        </button>
                        
                        <template x-for="page in pageNumbers" :key="page">
                            <button @click="changePage(page)"
                                    :class="page === meta.current_page ? 'bg-red-600' : 'bg-gray-800'"
                                    class="px-3 py-1 rounded"
                                    x-text="page"></button>
                        </template>
                        
                        <button @click="changePage(meta.current_page + 1)"
                                :disabled="meta.current_page === meta.last_page"
                                class="px-3 py-1 bg-gray-800 rounded disabled:opacity-50">
                            Siguiente
                        </button>
                    </nav>
                </div>
            </div>
            
            <!-- Loading state -->
            <div x-show="loading" class="mt-8 text-center">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
                <p class="mt-2">Buscando...</p>
            </div>
            
            <!-- Empty state -->
            <div x-show="!loading && searched && results.length === 0" class="mt-8 text-center">
                <i class="fas fa-search text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-400">No se encontraron resultados</p>
                <p class="text-sm text-gray-500 mt-2">Intenta con otros términos o filtros</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function advancedSearch() {
        return {
            query: '',
            type: 'all',
            filters: {
                genre: [],
                category: [],
                country: [],
                year_from: '',
                year_to: '',
                rating: 0,
                media_type: '',
                department: '',
                featured: false,
                date_from: '',
                date_to: ''
            },
            sort: 'relevance',
            results: [],
            meta: {},
            facets: null,
            loading: false,
            searched: false,
            
            init() {
                // Check if we have search params in URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('q')) {
                    this.query = urlParams.get('q');
                    this.search();
                }
            },
            
            search(page = 1) {
                this.loading = true;
                this.searched = true;
                
                const searchParams = {
                    q: this.query,
                    type: this.type,
                    sort: this.sort,
                    page: page,
                    filters: {}
                };
                
                // Add filters based on type
                if (this.type === 'all' || this.type === 'titles') {
                    if (this.filters.genre.length > 0) searchParams.filters.genre = this.filters.genre;
                    if (this.filters.category.length > 0) searchParams.filters.category = this.filters.category;
                    if (this.filters.country.length > 0) searchParams.filters.country = this.filters.country;
                    if (this.filters.year_from) searchParams.filters.year = [this.filters.year_from, this.filters.year_to || new Date().getFullYear()];
                    if (this.filters.rating > 0) searchParams.filters.rating = this.filters.rating;
                    if (this.filters.media_type) searchParams.filters.type = this.filters.media_type;
                }
                
                if (this.type === 'people') {
                    if (this.filters.department) searchParams.filters.department = this.filters.department;
                }
                
                if (this.type === 'news') {
                    if (this.filters.featured) searchParams.filters.featured = true;
                    if (this.filters.date_from) searchParams.filters.date_from = this.filters.date_from;
                    if (this.filters.date_to) searchParams.filters.date_to = this.filters.date_to;
                }
                
                fetch('/api/search?' + new URLSearchParams(searchParams))
                    .then(response => response.json())
                    .then(data => {
                        this.results = data.data || [];
                        this.meta = data.meta || {};
                        this.facets = data.facets || null;
                        this.loading = false;
                        
                        // Update URL
                        const url = new URL(window.location);
                        url.searchParams.set('q', this.query);
                        if (this.type !== 'all') url.searchParams.set('type', this.type);
                        if (this.sort !== 'relevance') url.searchParams.set('sort', this.sort);
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.loading = false;
                    });
            },
            
            changePage(page) {
                if (page >= 1 && page <= this.meta.last_page) {
                    this.search(page);
                }
            },
            
            get pageNumbers() {
                const pages = [];
                const current = this.meta.current_page || 1;
                const last = this.meta.last_page || 1;
                
                // Always show first page
                pages.push(1);
                
                // Show pages around current
                for (let i = Math.max(2, current - 2); i <= Math.min(last - 1, current + 2); i++) {
                    pages.push(i);
                }
                
                // Always show last page
                if (last > 1) {
                    pages.push(last);
                }
                
                return pages;
            },
            
            resetForm() {
                this.query = '';
                this.type = 'all';
                this.filters = {
                    genre: [],
                    category: [],
                    country: [],
                    year_from: '',
                    year_to: '',
                    rating: 0,
                    media_type: '',
                    department: '',
                    featured: false,
                    date_from: '',
                    date_to: ''
                };
                this.sort = 'relevance';
                this.results = [];
                this.meta = {};
                this.facets = null;
                this.searched = false;
                
                // Clear URL params
                window.history.pushState({}, '', window.location.pathname);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>