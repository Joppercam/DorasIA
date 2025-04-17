@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero Slider / Banner Principal -->
    <div class="hero-slider mb-5">
        <div class="hero-slide position-relative">
            <img src="https://via.placeholder.com/1600x600" class="img-fluid w-100" alt="Banner destacado">
            <div class="hero-content position-absolute bottom-0 start-0 p-5 text-white">
                <h1 class="display-4 fw-bold">Últimos estrenos asiáticos</h1>
                <p class="lead">Descubre las mejores series y películas de China, Japón y Corea</p>
                <a href="{{ route('discover') }}" class="btn btn-primary btn-lg">Explorar ahora</a>
            </div>
        </div>
    </div>

    <!-- Sección de Tendencias -->
    <div class="container mb-5">
        <h2 class="mb-4">Tendencias esta semana</h2>
        <div class="row">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col-md-3 mb-4">
                <div class="card content-card h-100">
                    <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Contenido destacado">
                    <div class="card-body">
                        <h5 class="card-title">Título de contenido #{{ $i }}</h5>
                        <p class="card-text small text-muted">Drama, Romance • 2024</p>
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating me-2">★★★★☆</div>
                            <div class="small">4.5</div>
                        </div>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Carrusel: Doramas Populares -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Doramas Populares</h2>
            <a href="{{ route('tv-shows.index') }}" class="btn btn-outline-primary">Ver todos</a>
        </div>
        <div class="row">
            @for ($i = 1; $i <= 6; $i++)
            <div class="col-md-2 mb-4">
                <div class="card content-card h-100">
                    <img src="https://via.placeholder.com/200x300" class="card-img-top" alt="Dorama popular">
                    <div class="card-body">
                        <h6 class="card-title">Dorama #{{ $i }}</h6>
                        <p class="card-text small text-muted">Corea • 2024</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Carrusel: Películas Chinas -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Películas Chinas</h2>
            <a href="{{ route('movies.index') }}?country=china" class="btn btn-outline-primary">Ver todas</a>
        </div>
        <div class="row">
            @for ($i = 1; $i <= 6; $i++)
            <div class="col-md-2 mb-4">
                <div class="card content-card h-100">
                    <img src="https://via.placeholder.com/200x300" class="card-img-top" alt="Película china">
                    <div class="card-body">
                        <h6 class="card-title">Película china #{{ $i }}</h6>
                        <p class="card-text small text-muted">China • 2023</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Carrusel: Anime Destacado -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Anime Destacado</h2>
            <a href="{{ route('tv-shows.index') }}?genre=anime" class="btn btn-outline-primary">Ver todos</a>
        </div>
        <div class="row">
            @for ($i = 1; $i <= 6; $i++)
            <div class="col-md-2 mb-4">
                <div class="card content-card h-100">
                    <img src="https://via.placeholder.com/200x300" class="card-img-top" alt="Anime destacado">
                    <div class="card-body">
                        <h6 class="card-title">Anime #{{ $i }}</h6>
                        <p class="card-text small text-muted">Japón • 2024</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Plataformas disponibles -->
    <div class="container mb-5">
        <h2 class="mb-4">Disponible en estas plataformas</h2>
        <div class="row align-items-center justify-content-between">
            <div class="col-md-2 mb-3 text-center">
                <img src="https://via.placeholder.com/150x50" alt="Plataforma 1" class="img-fluid">
            </div>
            <div class="col-md-2 mb-3 text-center">
                <img src="https://via.placeholder.com/150x50" alt="Plataforma 2" class="img-fluid">
            </div>
            <div class="col-md-2 mb-3 text-center">
                <img src="https://via.placeholder.com/150x50" alt="Plataforma 3" class="img-fluid">
            </div>
            <div class="col-md-2 mb-3 text-center">
                <img src="https://via.placeholder.com/150x50" alt="Plataforma 4" class="img-fluid">
            </div>
            <div class="col-md-2 mb-3 text-center">
                <img src="https://via.placeholder.com/150x50" alt="Plataforma 5" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hero-slider {
        height: 600px;
        overflow: hidden;
    }
    
    .hero-slide {
        height: 100%;
    }
    
    .hero-content {
        background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));
        width: 100%;
        padding-top: 150px !important;
    }
    
    .content-card {
        transition: transform 0.3s ease;
        overflow: hidden;
    }
    
    .content-card:hover {
        transform: scale(1.05);
    }
    
    .rating {
        color: #ffc107;
    }
</style>
@endsection