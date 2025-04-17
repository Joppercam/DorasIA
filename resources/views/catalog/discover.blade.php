@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero Section con filtros -->
    <div class="hero-section mb-5">
        <div class="position-relative">
            <img src="https://via.placeholder.com/1600x500" class="img-fluid w-100" alt="Descubre contenido asiático">
            <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <h1 class="display-4 text-white mb-4">Descubre el mejor contenido asiático</h1>
                            <p class="lead text-white mb-4">Explora nuestra colección de doramas, películas y anime de China, Japón y Corea</p>
                            
                            <!-- Barra de búsqueda -->
                            <form action="{{ route('catalog.search') }}" method="GET" class="mb-4">
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control" name="q" placeholder="Buscar títulos, actores o directores">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros rápidos / Pills -->
    <div class="container mb-5">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('catalog.movies') }}" class="btn btn-outline-primary rounded-pill px-4">Películas</a>
            <a href="{{ route('catalog.tv-shows') }}" class="btn btn-outline-primary rounded-pill px-4">Series/Doramas</a>
            <a href="{{ route('catalog.tv-shows') }}?type=anime" class="btn btn-outline-primary rounded-pill px-4">Anime</a>
            <a href="{{ route('catalog.country', 'kr') }}" class="btn btn-outline-primary rounded-pill px-4">Corea</a>
            <a href="{{ route('catalog.country', 'jp') }}" class="btn btn-outline-primary rounded-pill px-4">Japón</a>
            <a href="{{ route('catalog.country', 'cn') }}" class="btn btn-outline-primary rounded-pill px-4">China</a>
        </div>
    </div>

    <!-- Películas más recientes -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Últimos estrenos</h2>
            <a href="{{ route('catalog.movies') }}?sort=release_date" class="btn btn-sm btn-outline-primary">Ver todos</a>
        </div>
        
        <div class="row">
            @forelse($latestMovies as $movie)
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="content-card h-100">
                    <div class="position-relative">
                        <img src="{{ $movie->poster_path ? asset('storage/' . $movie->poster_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $movie->title }}">
                        <div class="content-type-badge">Película</div>
                        @if($movie->vote_average > 0)
                        <div class="rating-badge">
                            <i class="bi bi-star-fill"></i> {{ number_format($movie->vote_average, 1) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate">{{ $movie->title }}</h6>
                        <p class="card-text small text-muted">
                            {{ $movie->country_of_origin }} • {{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('catalog.movie-detail', $movie->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No hay películas recientes disponibles</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Series más populares -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Doramas populares</h2>
            <a href="{{ route('catalog.tv-shows') }}?type=drama" class="btn btn-sm btn-outline-primary">Ver todos</a>
        </div>
        
        <div class="row">
            @forelse($popularTvShows as $tvShow)
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="content-card h-100">
                    <div class="position-relative">
                        <img src="{{ $tvShow->poster_path ? asset('storage/' . $tvShow->poster_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $tvShow->title }}">
                        <div class="content-type-badge">Serie</div>
                        @if($tvShow->vote_average > 0)
                        <div class="rating-badge">
                            <i class="bi bi-star-fill"></i> {{ number_format($tvShow->vote_average, 1) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate">{{ $tvShow->title }}</h6>
                        <p class="card-text small text-muted">
                            {{ $tvShow->country_of_origin }} • {{ $tvShow->first_air_date ? $tvShow->first_air_date->format('Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('catalog.tv-show-detail', $tvShow->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No hay series populares disponibles</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Películas mejor valoradas -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mejor valoradas</h2>
            <a href="{{ route('catalog.movies') }}?sort=rating" class="btn btn-sm btn-outline-primary">Ver todas</a>
        </div>
        
        <div class="row">
            @forelse($topRatedMovies as $movie)
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="content-card h-100">
                    <div class="position-relative">
                        <img src="{{ $movie->poster_path ? asset('storage/' . $movie->poster_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $movie->title }}">
                        <div class="content-type-badge">Película</div>
                        <div class="rating-badge">
                            <i class="bi bi-star-fill"></i> {{ number_format($movie->vote_average, 1) }}
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate">{{ $movie->title }}</h6>
                        <p class="card-text small text-muted">
                            {{ $movie->country_of_origin }} • {{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('catalog.movie-detail', $movie->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No hay películas valoradas disponibles</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Anime destacado -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Anime destacado</h2>
            <a href="{{ route('catalog.tv-shows') }}?type=anime" class="btn btn-sm btn-outline-primary">Ver todo</a>
        </div>
        
        <div class="row">
            @forelse($animeShows as $anime)
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="content-card h-100">
                    <div class="position-relative">
                        <img src="{{ $anime->poster_path ? asset('storage/' . $anime->poster_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $anime->title }}">
                        <div class="content-type-badge">Anime</div>
                        @if($anime->vote_average > 0)
                        <div class="rating-badge">
                            <i class="bi bi-star-fill"></i> {{ number_format($anime->vote_average, 1) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate">{{ $anime->title }}</h6>
                        <p class="card-text small text-muted">
                            Japón • {{ $anime->first_air_date ? $anime->first_air_date->format('Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('catalog.tv-show-detail', $anime->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No hay anime destacado disponible</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Explorar por género -->
    <div class="container mb-5">
        <h2 class="mb-4">Explorar por género</h2>
        <div class="row">
            @foreach($genres as $genre)
            <div class="col-md-3 col-sm-4 col-6 mb-3">
                <a href="{{ route('catalog.genre', $genre->slug) }}" class="genre-card d-block">
                    <div class="card bg-dark text-white h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="card-title m-0">{{ $genre->name }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Explorar por país -->
    <div class="container mb-5">
        <h2 class="mb-4">Explorar por país</h2>
        <div class="row">
            @foreach($countries->take(8) as $country)
            <div class="col-md-3 col-sm-4 col-6 mb-3">
                <a href="{{ route('catalog.country', $country->code) }}" class="country-card d-block">
                    <div class="card bg-dark text-white h-100">
                        <div class="card-body text-center py-4">
                            <h5 class="card-title m-0">{{ $country->name }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hero-section {
        margin-bottom: 3rem;
    }
    
    .hero-overlay {
        background: linear-gradient(to right, rgba(0,0,0,0.7) 50%, rgba(0,0,0,0.3));
    }
    
    .content-card {
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: none;
    }
    
    .content-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .content-card .card-img-top {
        height: 300px;
        object-fit: cover;
    }
    
    .content-type-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    
    .rating-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: rgba(255, 193, 7, 0.9);
        color: black;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .genre-card, .country-card {
        transition: transform 0.3s ease;
    }
    
    .genre-card:hover, .country-card:hover {
        transform: translateY(-5px);
    }
    
    @media (max-width: 768px) {
        .content-card .card-img-top {
            height: 200px;
        }
    }
</style>
@endsection