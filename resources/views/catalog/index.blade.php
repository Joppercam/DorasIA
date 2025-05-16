<x-app-layout>
    <x-slot name="title">Catálogo</x-slot>
    
    <!-- Header del catálogo -->
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold">Catálogo completo</h1>
            
            @if(request('search'))
                <p class="mt-2 text-gray-400">Resultados para: "{{ request('search') }}"</p>
            @elseif(request('category'))
                <p class="mt-2 text-gray-400">Filtrando por categoría: {{ $categories->where('id', request('category'))->first()?->name ?? 'Desconocida' }}</p>
            @elseif(request('genre'))
                <p class="mt-2 text-gray-400">Filtrando por género: {{ $genres->where('id', request('genre'))->first()?->name ?? 'Desconocido' }}</p>
            @elseif(request('country'))
                <p class="mt-2 text-gray-400">Filtrando por país: {{ request('country') }}</p>
            @endif
        </div>
    </div>
    
    <!-- Filtros rápidos de país -->
    <div class="bg-gray-950 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-3 overflow-x-auto pb-2">
                <span class="text-gray-400 text-sm whitespace-nowrap">Filtrar por país:</span>
                
                @php
                    $countries = [
                        'Corea del Sur' => '🇰🇷',
                        'Japón' => '🇯🇵',
                        'China' => '🇨🇳',
                        'Tailandia' => '🇹🇭',
                        'Taiwán' => '🇹🇼',
                        'Indonesia' => '🇮🇩',
                        'Filipinas' => '🇵🇭',
                        'Vietnam' => '🇻🇳'
                    ];
                    
                    $currentParams = request()->query();
                @endphp
                
                <!-- Botón "Todos" -->
                @php
                    $allParams = $currentParams;
                    unset($allParams['country']);
                @endphp
                <a href="{{ route('catalog.index', $allParams) }}"
                   class="flex items-center space-x-2 px-3 py-1.5 rounded-full text-sm transition whitespace-nowrap
                          {{ !request('country') ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    <span>🌏</span>
                    <span>Todos</span>
                </a>
                
                @foreach($countries as $country => $flag)
                    @php
                        $countryParams = array_merge($currentParams, ['country' => $country]);
                    @endphp
                    <a href="{{ route('catalog.index', $countryParams) }}"
                       class="flex items-center space-x-1 px-3 py-1.5 rounded-full text-sm transition whitespace-nowrap
                              {{ request('country') == $country ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <span>{{ $flag }}</span>
                        <span>{{ $country }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Filtros y resultados -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Sidebar con filtros (versión desktop) -->
            <div class="hidden md:block md:col-span-3 bg-gray-900 rounded-lg p-4 h-min sticky top-20">
                <h2 class="text-xl font-bold mb-4">Filtros</h2>
                
                <form action="{{ route('catalog.index') }}" method="GET">
                    <!-- Preservar la búsqueda actual si existe -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <!-- Preservar el país actual si existe -->
                    @if(request('country'))
                        <input type="hidden" name="country" value="{{ request('country') }}">
                    @endif
                    
                    <!-- Tipo -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Tipo</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input id="type-all" type="radio" name="type" value="" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ !request('type') ? 'checked' : '' }}>
                                <label for="type-all" class="ml-2 text-sm text-gray-300">Todos</label>
                            </div>
                            <div class="flex items-center">
                                <input id="type-movie" type="radio" name="type" value="movie" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'movie' ? 'checked' : '' }}>
                                <label for="type-movie" class="ml-2 text-sm text-gray-300">Películas</label>
                            </div>
                            <div class="flex items-center">
                                <input id="type-series" type="radio" name="type" value="series" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'series' ? 'checked' : '' }}>
                                <label for="type-series" class="ml-2 text-sm text-gray-300">Series</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categorías -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Categorías</h3>
                        <select name="category" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Géneros -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Géneros</h3>
                        <select name="genre" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Todos los géneros</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Ordenar por -->
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Ordenar por</h3>
                        <select name="sort" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Alfabético</option>
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full">
                            Aplicar filtros
                        </button>
                        <a href="{{ route('catalog.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Filtros móviles (toggle) -->
            <div class="md:hidden w-full mb-4">
                <div x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center justify-between w-full bg-gray-900 rounded-lg p-4">
                        <span class="font-semibold">Filtros</span>
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" class="mt-2 bg-gray-900 rounded-lg p-4">
                        <form action="{{ route('catalog.index') }}" method="GET">
                            <!-- Preservar la búsqueda actual si existe -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <!-- Preservar el país actual si existe -->
                            @if(request('country'))
                                <input type="hidden" name="country" value="{{ request('country') }}">
                            @endif
                            
                            <!-- Tipo -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Tipo</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input id="mobile-type-all" type="radio" name="type" value="" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ !request('type') ? 'checked' : '' }}>
                                        <label for="mobile-type-all" class="ml-2 text-sm text-gray-300">Todos</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="mobile-type-movie" type="radio" name="type" value="movie" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'movie' ? 'checked' : '' }}>
                                        <label for="mobile-type-movie" class="ml-2 text-sm text-gray-300">Películas</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="mobile-type-series" type="radio" name="type" value="series" class="h-4 w-4 text-red-600 bg-gray-800 border-gray-600 focus:ring-red-500" {{ request('type') == 'series' ? 'checked' : '' }}>
                                        <label for="mobile-type-series" class="ml-2 text-sm text-gray-300">Series</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Categorías -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Categorías</h3>
                                <select name="category" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Géneros -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Géneros</h3>
                                <select name="genre" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="">Todos los géneros</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                            {{ $genre->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Ordenar por -->
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Ordenar por</h3>
                                <select name="sort" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500">
                                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Alfabético</option>
                                </select>
                            </div>
                            
                            <!-- Botones -->
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm w-full">
                                    Aplicar filtros
                                </button>
                                <a href="{{ route('catalog.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Grid de títulos -->
            <div class="md:col-span-9">
                @if($titles->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($titles as $title)
                            <x-netflix-card 
                                :title="$title" 
                                :watchHistory="isset($titleWatchHistory[$title->id]) ? $titleWatchHistory[$title->id] : null" 
                            />
                        @endforeach
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-8">
                        {{ $titles->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="bg-gray-900 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-xl font-semibold">No se encontraron resultados</h3>
                        <p class="mt-1 text-gray-400">Prueba a ajustar los filtros o realizar otra búsqueda.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                            Ver todo el catálogo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>