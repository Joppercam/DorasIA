<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
                            <a class="nav-link" href="{{ route('movies.index') }}">{{ __('Películas') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tv-shows.index') }}">{{ __('Series') }}</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Países') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="{{ route('movies.index') }}?country=china">China</a></li>
                                <li><a class="dropdown-item" href="{{ route('movies.index') }}?country=japan">Japón</a></li>
                                <li><a class="dropdown-item" href="{{ route('movies.index') }}?country=korea">Corea</a></li>
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
                            <li><a href="{{ route('movies.index') }}" class="text-decoration-none text-muted">Películas</a></li>
                            <li><a href="{{ route('tv-shows.index') }}" class="text-decoration-none text-muted">Series</a></li>
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