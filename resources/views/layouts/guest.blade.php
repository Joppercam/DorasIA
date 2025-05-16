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
        <link rel="apple-touch-icon" href="{{ asset('favicon/favicon-180x180.png') }}">
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            /* Estilo para imágenes de fondo en móviles */
            @media (max-width: 640px) {
                .auth-background {
                    background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9)), url("{{ asset('backdrops/backdrop-1.jpg') }}");
                    background-size: cover;
                    background-position: center;
                    background-attachment: fixed;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-200 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center py-8 px-4 bg-gradient-to-b from-black to-gray-900 auth-background">
            <div class="w-full max-w-md flex flex-col items-center">
                <a href="/" class="mt-4 mb-6">
                    <div class="flex items-center logo-dorasia-cinema">
                        <span class="sr-only">Dorasia</span>
                        <div class="cinema-emblem">
                            <span class="cinema-letter">D</span>
                        </div>
                        <div class="cinema-text-container">
                            <span class="cinema-text text-white text-3xl">DORASIA</span>
                            <span class="cinema-tagline text-red-500">Vive el drama asiático</span>
                        </div>
                    </div>
                </a>
            
                <div class="w-full bg-gray-800 bg-opacity-70 backdrop-blur-sm shadow-xl rounded-lg px-6 sm:px-8 py-6 sm:py-8 overflow-hidden border border-gray-700">
                    {{ $slot }}
                </div>
                
                <div class="mt-6 text-sm text-gray-400 text-center px-2">
                    <p>© {{ date('Y') }} Dorasia. Todos los derechos reservados.</p>
                    <p class="mt-1">La mejor plataforma de K-dramas, C-dramas y J-dramas.</p>
                </div>
            </div>
        </div>
    </body>
</html>
