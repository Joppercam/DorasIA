<nav x-data="{ open: false, profilesOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
    :class="{ 'bg-gradient-to-b from-black/90 to-transparent border-b-0': !scrolled, 'bg-black/90 border-b border-gray-800': scrolled }"
    class="fixed w-full transition-all duration-300 top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center logo-dorasia-cinema">
                        <span class="sr-only">Dorasia</span>
                        <div class="cinema-emblem">
                            <span class="cinema-letter">D</span>
                        </div>
                        <div class="cinema-text-container">
                            <span class="cinema-text">DORASIA</span>
                            <span class="cinema-tagline">Vive el drama asiático</span>
                        </div>
                    </a>
                </div>

                <!-- Barra de búsqueda global -->
                <div class="flex-1 max-w-xl mx-4 hidden sm:block">
                    <div x-data="searchBar()" class="relative">
                        <input type="text" 
                               x-model="query"
                               @input.debounce.300ms="search"
                               @focus="showResults = true"
                               @click.away="showResults = false"
                               placeholder="Buscar títulos, actores, géneros..."
                               class="w-full bg-gray-800 text-white rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500">
                        
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        
                        <!-- Resultados -->
                        <div x-show="showResults && (results.length > 0 || query.length > 0)" 
                             x-transition
                             class="absolute top-full mt-2 w-full bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
                            
                            <!-- Loading state -->
                            <div x-show="loading" class="p-4 text-center text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Buscando...
                            </div>
                            
                            <!-- Resultados -->
                            <div x-show="!loading && results.length > 0">
                                <template x-for="result in results" :key="result.id">
                                    <a :href="`/titles/${result.slug}`" 
                                       class="flex items-center p-3 hover:bg-gray-700 transition">
                                        <img :src="result.poster_url" 
                                             :alt="result.title"
                                             class="w-10 h-14 object-cover rounded mr-3"
                                             onerror="this.src='/posters/placeholder.jpg'">
                                        <div class="flex-1">
                                            <div class="font-medium" x-text="result.title"></div>
                                            <div class="text-sm text-gray-400">
                                                <span x-text="result.release_year"></span>
                                                <span class="mx-1">•</span>
                                                <span x-text="result.type === 'movie' ? 'Película' : 'Serie'"></span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <span x-text="(result.vote_average / 2).toFixed(1)"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            
                            <!-- Sin resultados -->
                            <div x-show="!loading && results.length === 0 && query.length > 0" 
                                 class="p-4 text-center text-gray-400">
                                No se encontraron resultados para "<span x-text="query"></span>"
                            </div>
                            
                            <!-- Enlace a búsqueda avanzada -->
                            <div class="p-3 border-t border-gray-700">
                                <a href="{{ route('search.advanced') }}" 
                                   class="flex items-center justify-center text-sm text-red-500 hover:text-red-400 transition">
                                    <i class="fas fa-sliders-h mr-2"></i>
                                    Búsqueda avanzada
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function searchBar() {
                    return {
                        query: '',
                        results: [],
                        showResults: false,
                        loading: false,
                        
                        async search() {
                            if (this.query.length < 2) {
                                this.results = [];
                                return;
                            }
                            
                            this.loading = true;
                            
                            try {
                                const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
                                const data = await response.json();
                                this.results = data.data || [];
                            } catch (error) {
                                console.error('Error searching:', error);
                                this.results = [];
                            } finally {
                                this.loading = false;
                            }
                        }
                    }
                }
                </script>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.index')">
                        {{ __('Catálogo') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('romantic-dramas.index')" :active="request()->routeIs('romantic-dramas.*')">
                        {{ __('Doramas Románticos') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('people.index')" :active="request()->routeIs('people.*')">
                        {{ __('Actores') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                        {{ __('Noticias') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('comments.index')" :active="request()->routeIs('comments.*')">
                        {{ __('Comunidad') }}
                    </x-nav-link>
                    
                    
                    <!-- Enlaces para usuarios autenticados -->
                    @auth
                        <x-nav-link :href="route('watchlist.index')" :active="request()->routeIs('watchlist.index')">
                            {{ __('Mi Lista') }}
                        </x-nav-link>
                        {{-- Portal Informativo - No hay reproducción
                        <x-nav-link :href="route('watch-history.index')" :active="request()->routeIs('watch-history.index')">
                            {{ __('Continuar Viendo') }}
                        </x-nav-link>
                        --}}
                    @endauth
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Keyboard shortcuts help -->
                <button onclick="window.KeyboardShortcuts?.prototype?.showHelp?.call(new KeyboardShortcuts())" 
                        class="hidden lg:inline-flex items-center text-gray-400 hover:text-white text-sm transition-colors px-3 py-1.5 rounded-md hover:bg-gray-800" 
                        title="Ver atajos de teclado (presiona ?)">
                    <kbd class="px-1.5 py-0.5 bg-gray-800 rounded text-xs mr-1.5 font-mono">?</kbd>
                    <span>Atajos</span>
                </button>
                
                @guest
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-md text-sm">Registrarse</a>
                    </div>
                @else
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button @click="profilesOpen = !profilesOpen" class="flex items-center text-sm font-medium text-white focus:outline-none">
                            <div class="h-8 w-8 rounded overflow-hidden mr-2">
                                @if(Auth::user()->getActiveProfile())
                                    <img src="{{ asset('images/profiles/' . (Auth::user()->getActiveProfile()->avatar_url ?? 'default.jpg')) }}" alt="Perfil" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='{{ asset('images/profiles/default.jpg') }}'">
                                @else
                                    <img src="{{ asset('images/profiles/default.jpg') }}" alt="Perfil" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM0QjU1NjMiLz48cGF0aCBkPSJNNTAgNDVDNTYuNjI3IDQ1IDYyIDM5LjYyNyA2MiAzM0M2MiAyNi4zNzMgNTYuNjI3IDIxIDUwIDIxQzQzLjM3MyAyMSAzOCAyNi4zNzMgMzggMzNDMzggMzkuNjI3IDQzLjM3MyA0NSA1MCA0NVoiIGZpbGw9IiM5Q0E0QjAiLz48cGF0aCBkPSJNMzAgNzkuNUMzMCA2NS45NjkgNDAuOTY5IDU1IDU0LjUgNTVINDUuNUM1OS4wMzEgNTUgNzAgNjUuOTY5IDcwIDc5LjVWODBIMzBWNzkuNVoiIGZpbGw9IiM5Q0E0QjAiLz48L3N2Zz4='">
                                @endif
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        
                        <div x-show="profilesOpen" 
                             @click.away="profilesOpen = false"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-black border border-gray-700">
                            
                            <!-- Perfiles del usuario -->
                            @if(Auth::user()->profiles->count() > 0)
                                <div class="py-1 border-b border-gray-700">
                                    <div class="px-4 py-1 text-xs text-gray-500">Perfiles</div>
                                    @foreach(Auth::user()->profiles as $profile)
                                        <form method="POST" action="{{ route('user-profiles.set-active', $profile) }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white flex items-center">
                                                <div class="h-6 w-6 rounded overflow-hidden mr-2">
                                                    <img src="{{ $profile->avatar_url ?? asset('images/profiles/default.jpg') }}" alt="{{ $profile->name }}" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM0QjU1NjMiLz48cGF0aCBkPSJNNTAgNDVDNTYuNjI3IDQ1IDYyIDM5LjYyNyA2MiAzM0M2MiAyNi4zNzMgNTYuNjI3IDIxIDUwIDIxQzQzLjM3MyAyMSAzOCAyNi4zNzMgMzggMzNDMzggMzkuNjI3IDQzLjM3MyA0NSA1MCA0NVoiIGZpbGw9IiM5Q0E0QjAiLz48cGF0aCBkPSJNMzAgNzkuNUMzMCA2NS45NjkgNDAuOTY5IDU1IDU0LjUgNTVINDUuNUM1OS4wMzEgNTUgNzAgNjUuOTY5IDcwIDc5LjVWODBIMzBWNzkuNVoiIGZpbGw9IiM5Q0E0QjAiLz48L3N2Zz4='">
                                                </div>
                                                {{ $profile->name }}
                                                
                                                @if(Auth::user()->getActiveProfile() && Auth::user()->getActiveProfile()->id === $profile->id)
                                                    <svg class="h-4 w-4 ml-auto text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Opciones de cuenta -->
                            <div class="py-1">
                                <a href="{{ route('user-profiles.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                    {{ __('Gestionar Perfiles') }}
                                </a>
                                <a href="{{ route('profile.statistics') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                    {{ __('Mis Estadísticas') }}
                                </a>
                                @if(Auth::user()->getActiveProfile())
                                    <a href="{{ route('profiles.edit', Auth::user()->getActiveProfile()) }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                        {{ __('Editar Perfil') }}
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                    {{ __('Configuración de Cuenta') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                        {{ __('Cerrar Sesión') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-900 focus:outline-none focus:bg-gray-900 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.index')">
                {{ __('Catálogo') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('romantic-dramas.index')" :active="request()->routeIs('romantic-dramas.*')">
                {{ __('Doramas Románticos') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('people.index')" :active="request()->routeIs('people.*')">
                {{ __('Actores') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                {{ __('Noticias') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('comments.index')" :active="request()->routeIs('comments.*')">
                {{ __('Comunidad') }}
            </x-responsive-nav-link>
            
            
            @auth
                <x-responsive-nav-link :href="route('watchlist.index')" :active="request()->routeIs('watchlist.index')">
                    {{ __('Mi Lista') }}
                </x-responsive-nav-link>
                {{-- Portal Informativo - No hay reproducción
                <x-responsive-nav-link :href="route('watch-history.index')" :active="request()->routeIs('watch-history.index')">
                    {{ __('Continuar Viendo') }}
                </x-responsive-nav-link>
                --}}
            @endauth
        </div>

        @auth
            <!-- Mobile user profiles -->
            <div class="pt-2 pb-3 border-t border-gray-800">
                <div class="px-4 text-xs text-gray-500 mb-2">Perfiles</div>
                
                <div class="space-y-1">
                    @foreach(Auth::user()->profiles as $profile)
                        <form method="POST" action="{{ route('user-profiles.set-active', $profile) }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 flex items-center text-sm text-gray-300 hover:bg-gray-900 hover:text-white">
                                <div class="h-6 w-6 rounded overflow-hidden mr-2">
                                    <img src="{{ $profile->avatar_url ?? asset('images/profiles/default.jpg') }}" alt="{{ $profile->name }}" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM0QjU1NjMiLz48cGF0aCBkPSJNNTAgNDVDNTYuNjI3IDQ1IDYyIDM5LjYyNyA2MiAzM0M2MiAyNi4zNzMgNTYuNjI3IDIxIDUwIDIxQzQzLjM3MyAyMSAzOCAyNi4zNzMgMzggMzNDMzggMzkuNjI3IDQzLjM3MyA0NSA1MCA0NVoiIGZpbGw9IiM5Q0E0QjAiLz48cGF0aCBkPSJNMzAgNzkuNUMzMCA2NS45NjkgNDAuOTY5IDU1IDU0LjUgNTVINDUuNUM1OS4wMzEgNTUgNzAgNjUuOTY5IDcwIDc5LjVWODBIMzBWNzkuNVoiIGZpbGw9IiM5Q0E0QjAiLz48L3N2Zz4='"">
                                </div>
                                {{ $profile->name }}
                                
                                @if(Auth::user()->getActiveProfile() && Auth::user()->getActiveProfile()->id === $profile->id)
                                    <svg class="h-4 w-4 ml-auto text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <!-- Mobile user options -->
            <div class="pt-4 pb-1 border-t border-gray-800">
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('user-profiles.index')">
                        {{ __('Gestionar Perfiles') }}
                    </x-responsive-nav-link>
                    
                    @if(Auth::user()->getActiveProfile())
                        <x-responsive-nav-link :href="route('profiles.edit', Auth::user()->getActiveProfile())">
                            {{ __('Editar Perfil') }}
                        </x-responsive-nav-link>
                    @endif
                    
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Configuración de Cuenta') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <!-- Mobile login/register -->
            <div class="pt-4 pb-4 border-t border-gray-800">
                <div class="space-y-2 px-4">
                    <a href="{{ route('login') }}" class="block text-center bg-transparent border border-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-900">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="block text-center bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">Registrarse</a>
                </div>
            </div>
        @endauth
    </div>
</nav>
