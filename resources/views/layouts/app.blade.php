<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
