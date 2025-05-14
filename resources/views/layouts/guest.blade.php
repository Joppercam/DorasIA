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
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
