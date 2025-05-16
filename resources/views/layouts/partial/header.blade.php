<header class="bg-gray-800 shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-400">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-10">
                </a>
            </div>
            
            <!-- Navigation - Desktop -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('catalog.movies') }}" class="text-gray-300 hover:text-white transition">Películas</a>
                <a href="{{ route('catalog.series') }}" class="text-gray-300 hover:text-white transition">Series</a>
                <a href="{{ route('catalog.index', ['country' => 'korea']) }}" class="text-gray-300 hover:text-white transition">Corea</a>
                <a href="{{ route('catalog.index', ['country' => 'japan']) }}" class="text-gray-300 hover:text-white transition">Japón</a>
                <a href="{{ route('catalog.index', ['country' => 'china']) }}" class="text-gray-300 hover:text-white transition">China</a>
            </nav>
            
            <!-- Right Side Menu -->
            <div class="flex items-center space-x-4">
                <!-- Search Button -->
                <button @click="$store.search.toggle()" class="text-gray-300 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                
                <!-- User Menu -->
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover">
                            <span class="hidden md:inline text-sm">{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 py-2 bg-gray-800 rounded-md shadow-lg z-10">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Mi Perfil</a>
                            <a href="{{ route('watchlist.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Mi Lista</a>
                            <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Favoritos</a>
                            
                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-gray-700 my-1"></div>
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Panel Admin</a>
                            @endif
                            
                            <div class="border-t border-gray-700 my-1"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm transition">Iniciar Sesión</a>
                        <span class="text-gray-500">|</span>
                        <a href="{{ route('register') }}" class="text-gray-300 hover:text-white text-sm transition">Registrarse</a>
                    </div>
                @endauth
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button @click="$store.mobileMenu.toggle()" class="text-gray-300 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div x-show="$store.mobileMenu.open" class="md:hidden bg-gray-900 pb-4" x-transition>
        <nav class="container mx-auto px-4 py-2 flex flex-col space-y-3">
            <a href="{{ route('catalog.movies') }}" class="text-gray-300 hover:text-white py-2 transition">Películas</a>
            <a href="{{ route('catalog.series') }}" class="text-gray-300 hover:text-white py-2 transition">Series</a>
            <a href="{{ route('catalog.index', ['country' => 'korea']) }}" class="text-gray-300 hover:text-white py-2 transition">Corea</a>
            <a href="{{ route('catalog.index', ['country' => 'japan']) }}" class="text-gray-300 hover:text-white py-2 transition">Japón</a>
            <a href="{{ route('catalog.index', ['country' => 'china']) }}" class="text-gray-300 hover:text-white py-2 transition">China</a>
        </nav>
    </div>
    
    <!-- Search Overlay -->
    <div x-show="$store.search.open" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center" x-transition>
        <div class="container mx-auto px-4 py-20">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Buscar películas, series, actores..." 
                    class="w-full py-4 px-6 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:ring-indigo-400"
                    @keydown.escape="$store.search.close()"
                    x-ref="searchInput"
                    x-init="$nextTick(() => $refs.searchInput.focus())"
                >
                <button @click="$store.search.close()" class="absolute right-4 top-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Search Results will be loaded here via AJAX -->
            <div id="search-results" class="mt-6"></div>
        </div>
    </div>
</header>