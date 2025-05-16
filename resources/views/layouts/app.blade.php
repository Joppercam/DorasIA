<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<<<<<<< HEAD
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Título dinámico de la página -->
        <title>{{ isset($title) ? $title . ' | ' : '' }}{{ config('app.name', 'Dorasia') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Dorasia - La mejor plataforma de streaming de contenido asiático: K-Dramas, C-Dramas, J-Dramas y películas asiáticas' }}">
        
        <!-- Favicons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon/favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('favicon/favicon-192x192.png') }}">
        <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
        
        <!-- Metadatos para redes sociales (Open Graph) -->
        <meta property="og:title" content="{{ isset($title) ? $title . ' | ' : '' }}{{ config('app.name', 'Dorasia') }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Dorasia - La mejor plataforma de streaming de contenido asiático: K-Dramas, C-Dramas, J-Dramas y películas asiáticas' }}">
        <meta property="og:image" content="{{ $metaImage ?? asset('images/heroes/hero-bg.jpg') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Dorasia">
        
        <!-- Metadatos para Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ isset($title) ? $title . ' | ' : '' }}{{ config('app.name', 'Dorasia') }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? 'Dorasia - La mejor plataforma de streaming de contenido asiático: K-Dramas, C-Dramas, J-Dramas y películas asiáticas' }}">
        <meta name="twitter:image" content="{{ $metaImage ?? asset('images/heroes/hero-bg.jpg') }}">
        
        <!-- Color del tema para navegadores móviles -->
        <meta name="theme-color" content="#E51013">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        
        <!-- CSS de respaldo -->
        <link href="{{ asset('css/fallback.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/logo-styles.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        <script src="{{ asset('js/head-metadata.js') }}" defer></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Fallback si Vite no carga -->
        <script>
            if (!document.querySelector('link[href*=".css"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '/build/assets/app-DhYqHLjW.css';
                document.head.appendChild(link);
                console.log('CSS fallback cargado');
            }
            
            if (!document.querySelector('script[src*="/assets/app"]')) {
                const script = document.createElement('script');
                script.src = '/build/assets/app-Bf4POITK.js';
                script.setAttribute('type', 'module');
                document.body.appendChild(script);
                console.log('JS fallback cargado');
            }
        </script>

        <!-- Styles -->
        <style>
            body {
                background-color: #141414;
                color: #ffffff;
            }
            
            .dorasia-bg-dark {
                background-color: #141414;
            }
            
            .dorasia-bg-darker {
                background-color: #000000;
            }
            
            .dorasia-bg-highlight {
                background-color: #E51013;
            }
            
            .dorasia-text-highlight {
                color: #E51013;
            }
            
            .dorasia-card {
                transition: transform .3s;
            }
            
            .dorasia-card:hover {
                transform: scale(1.05);
                z-index: 10;
            }
        </style>
        
        @stack('styles')
    </head>
    <body 
        class="font-sans antialiased dorasia-bg-dark text-white {{ isset($pageClass) ? $pageClass : '' }}"
        data-transition-type="{{ $transitionType ?? 'fade' }}"
        x-data="pageTransition"
        x-init="document.addEventListener('DOMContentLoaded', () => window.scrollTo(0, 0))"
    >
        <div class="min-h-screen">
            @include('layouts.navigation')
            
            <!-- Añadir margen para el menú fijo -->
            <div class="pt-0">

            <!-- Page Heading (solo para páginas que no son home) -->
            @isset($header)
                <header class="bg-black/70 shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-500 text-white py-2 px-4 max-w-7xl mx-auto my-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500 text-white py-2 px-4 max-w-7xl mx-auto my-2 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-500 text-white py-2 px-4 max-w-7xl mx-auto my-2 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-black py-8 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4">Dorasia</h3>
                            <p class="text-gray-400">La mejor plataforma para disfrutar de doramas coreanos, películas y series asiáticas.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Enlaces</h3>
                            <ul class="space-y-2">
                                <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white">Inicio</a></li>
                                <li><a href="{{ route('catalog.index') }}" class="text-gray-400 hover:text-white">Catálogo</a></li>
                                @auth
                                <li><a href="{{ route('watchlist.index') }}" class="text-gray-400 hover:text-white">Mi Lista</a></li>
                                @endauth
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Legal</h3>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-gray-400 hover:text-white">Términos y condiciones</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white">Política de privacidad</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-gray-800 pt-8 text-center text-gray-400">
                        <p>&copy; {{ date('Y') }} Dorasia. Todos los derechos reservados.</p>
                    </div>
                </div>
            </footer>

            <!-- Panel de configuración de transiciones -->
            @auth
            <div x-data="{ open: false }" class="fixed bottom-4 right-4 z-50">
                <button @click="open = !open" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute bottom-full right-0 mb-2 w-60 transitions-settings"
                >
                    <h3 class="font-bold">Configuración de transiciones</h3>
                    
                    <div class="transitions-toggle">
                        <input 
                            type="checkbox" 
                            id="transitions-enabled" 
                            x-bind:checked="isTransitionsEnabled()"
                            @click="toggleTransitions()"
                        >
                        <label for="transitions-enabled">Activar transiciones</label>
                    </div>
                    
                    <div class="transitions-options" x-show="isTransitionsEnabled()">
                        <button 
                            class="transitions-option" 
                            :class="{ 'active': transitionType === 'fade' }"
                            @click="setTransitionType('fade')"
                        >
                            Fundido
                        </button>
                        <button 
                            class="transitions-option" 
                            :class="{ 'active': transitionType === 'slide-up' }"
                            @click="setTransitionType('slide-up')"
                        >
                            Deslizamiento
                        </button>
                        <button 
                            class="transitions-option" 
                            :class="{ 'active': transitionType === 'zoom' }"
                            @click="setTransitionType('zoom')"
                        >
                            Zoom
                        </button>
                    </div>
                </div>
            </div>
            @endauth
        </div>
        
        @stack('scripts')
    </body>
</html>
=======
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DorasIA') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/logo.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body class="bg-dark text-white">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-black shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <svg width="150" height="40" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg">
                        <!-- Texto principal "DorasIA" como una palabra unificada con "IA" destacado -->
                        <text x="10" y="35" font-family="'Arial', sans-serif" font-weight="bold" font-size="32" fill="#556270">
                            Doras<tspan fill="#4ECDC4">IA</tspan>
                        </text>
                        
                        <!-- Elemento decorativo (símbolo asiático estilizado) -->
                        <path d="M155,10 C160,15 165,20 160,25 C165,30 160,35 155,40 C150,35 145,30 150,25 C145,20 150,15 155,10" fill="none" stroke="#556270" stroke-width="2"/>
                        
                        <!-- Elemento tecnológico (representando IA) -->
                        <g transform="translate(185, 25) scale(0.6)">
                            <circle cx="0" cy="0" r="10" fill="#4ECDC4" opacity="0.8"/>
                            <circle cx="0" cy="0" r="15" fill="none" stroke="#4ECDC4" stroke-width="1" stroke-dasharray="3,2"/>
                            <circle cx="0" cy="0" r="20" fill="none" stroke="#4ECDC4" stroke-width="1" stroke-dasharray="1,2"/>
                        </g>
                    </svg>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('catalog.movies') }}">{{ __('Películas') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('catalog.series') }}">{{ __('Series') }}</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Países') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="{{ route('catalog.movies') }}?country=china">China</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalog.movies') }}?country=japan">Japón</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalog.movies') }}?country=korea">Corea</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('discover') }}">{{ __('Descubrir') }}</a>
                        </li>
                    </ul>

                    <!-- Search Form -->
                    <form class="d-flex mx-auto" role="search" action="{{ route('search') }}" method="GET">
                        <input class="form-control me-2 bg-dark text-white" type="search" name="q" placeholder="{{ __('Buscar...') }}" aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">{{ __('Buscar') }}</button>
                    </form>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        {{ __('Mi Perfil') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('watchlists.index') }}">
                                        {{ __('Mis Listas') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-black text-white py-5 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5>DorasIA</h5>
                        <p class="text-muted">
                            Tu portal con inteligencia artificial para descubrir el mejor contenido asiático.
                        </p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h6>Explorar</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('catalog.movies') }}" class="text-decoration-none text-muted">Películas</a></li>
                            <li><a href="{{ route('catalog.series') }}" class="text-decoration-none text-muted">Series</a></li>
                            <li><a href="{{ route('discover') }}" class="text-decoration-none text-muted">Descubrir</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h6>Países</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-muted">China</a></li>
                            <li><a href="#" class="text-decoration-none text-muted">Japón</a></li>
                            <li><a href="#" class="text-decoration-none text-muted">Corea</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h6>Legal</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none text-muted">Términos de uso</a></li>
                            <li><a href="#" class="text-decoration-none text-muted">Política de privacidad</a></li>
                            <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Contacto</a></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-muted small">
                            &copy; {{ date('Y') }} DorasIA. Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    @yield('scripts')
</body>
</html>
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
