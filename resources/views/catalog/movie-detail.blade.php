@extends('layouts.app')

@section('content')
<!-- Hero Banner con backdrop -->
<div class="movie-backdrop position-relative">
    <div class="backdrop-image" style="background-image: url('{{ $movie->backdrop_path ? asset('storage/' . $movie->backdrop_path) : 'https://via.placeholder.com/1200x600' }}');">
        <div class="backdrop-overlay"></div>
    </div>
    
    <div class="container position-relative py-5">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="poster-container">
                    <img src="{{ $movie->poster_path ? asset('storage/' . $movie->poster_path) : 'https://via.placeholder.com/300x450' }}" 
                         class="img-fluid rounded shadow" alt="{{ $movie->title }}">
                </div>
            </div>
            <div class="col-md-9">
                <div class="text-white">
                    <h1 class="display-5 fw-bold mb-1">{{ $movie->title }}</h1>
                    
                    @if($movie->original_title != $movie->title)
                    <h2 class="h5 mb-3 text-light">{{ $movie->original_title }}</h2>
                    @endif
                    
                    <div class="d-flex flex-wrap align-items-center mb-3">
                        <span class="badge bg-primary me-2">Película</span>
                        <span class="me-3">{{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}</span>
                        @if($movie->runtime)
                        <span class="me-3">{{ floor($movie->runtime / 60) }}h {{ $movie->runtime % 60 }}m</span>
                        @endif
                        
                        @if($movie->vote_average)
                        <div class="d-flex align-items-center me-3">
                            <div class="rating-star me-1">★</div>
                            <div>{{ number_format($movie->vote_average, 1) }}/10</div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="genres-list mb-4">
                        @foreach($movie->genres as $genre)
                        <a href="{{ route('catalog.genre', $genre->slug) }}" class="badge bg-secondary me-1">{{ $genre->name }}</a>
                        @endforeach
                    </div>
                    
                    @if($movie->overview)
                    <div class="movie-overview mb-4">
                        <h3 class="h5 mb-2">Sinopsis</h3>
                        <p>{{ $movie->overview }}</p>
                    </div>
                    @endif
                    
                    <!-- Acciones rápidas -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @if(auth()->check())
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-circle"></i> Añadir a lista
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Ver más tarde</a></li>
                                <li><a class="dropdown-item" href="#">Favoritos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createListModal">
                                    <i class="bi bi-plus-circle"></i> Crear nueva lista
                                </a></li>
                            </ul>
                        </div>
                        
                        <button class="btn btn-outline-light add-rating" data-bs-toggle="modal" data-bs-target="#rateMovieModal">
                            <i class="bi bi-star"></i> Valorar
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light">
                            <i class="bi bi-box-arrow-in-right"></i> Inicia sesión para añadir a tu lista
                        </a>
                        @endif
                        
                        <button class="btn btn-outline-light share-button" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="bi bi-share"></i> Compartir
                        </button>
                    </div>
                    
                    <!-- Dónde ver -->
                    @if($movie->availability->count() > 0)
                    <div class="where-to-watch mb-4">
                        <h3 class="h5 mb-3">Dónde ver</h3>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($movie->availability->unique('platform_id') as $availability)
                            <a href="{{ $availability->url }}" target="_blank" class="platform-badge" title="Ver en {{ $availability->platform->name }}">
                                <img src="{{ $availability->platform->logo_path ? asset('storage/' . $availability->platform->logo_path) : 'https://via.placeholder.com/80x40' }}" 
                                     alt="{{ $availability->platform->name }}" class="img-fluid platform-logo">
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <!-- Pestañas -->
    <ul class="nav nav-tabs" id="movieTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                Detalles
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cast-tab" data-bs-toggle="tab" data-bs-target="#cast" type="button" role="tab">
                Reparto
            </button>
        </li>
        @if($movie->availability->count() > 0)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="availability-tab" data-bs-toggle="tab" data-bs-target="#availability" type="button" role="tab">
                Disponibilidad
            </button>
        </li>
        @endif
    </ul>
    
    <div class="tab-content py-4" id="movieTabsContent">
        <!-- Pestaña de Detalles -->
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="h4 mb-4">Información de la película</h3>
                    
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row" width="200">Título original</th>
                                <td>{{ $movie->original_title }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Año de estreno</th>
                                <td>{{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">País de origen</th>
                                <td>{{ $movie->country_of_origin }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Idioma original</th>
                                <td>{{ $movie->original_language }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Duración</th>
                                <td>{{ $movie->runtime ? $movie->runtime . ' minutos' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Estado</th>
                                <td>{{ $movie->status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-md-4">
                    @if($movie->crew->where('job', 'Director')->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="h5 m-0">Director</h4>
                        </div>
                        <div class="card-body">
                            @foreach($movie->crew->where('job', 'Director') as $director)
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <img src="{{ $director->person->profile_path ? asset('storage/' . $director->person->profile_path) : 'https://via.placeholder.com/50x50' }}" 
                                         class="rounded-circle" width="50" height="50" alt="{{ $director->person->name }}">
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $director->person->name }}</h5>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Estadísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h5 m-0">Estadísticas</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Valoración</span>
                                <span class="fw-bold">{{ number_format($movie->vote_average, 1) }}/10</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Votos</span>
                                <span class="fw-bold">{{ $movie->vote_count }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Popularidad</span>
                                <span class="fw-bold">{{ number_format($movie->popularity, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pestaña de Reparto -->
        <div class="tab-pane fade" id="cast" role="tabpanel" aria-labelledby="cast-tab">
            <h3 class="h4 mb-4">Reparto principal</h3>
            
            <div class="row">
                @forelse($movie->cast->sortBy('order')->take(12) as $actor)
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <div class="card cast-card h-100">
                        <img src="{{ $actor->person->profile_path ? asset('storage/' . $actor->person->profile_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $actor->person->name }}">
                        <div class="card-body p-2 text-center">
                            <h5 class="card-title h6">{{ $actor->person->name }}</h5>
                            <p class="card-text small text-muted">{{ $actor->character }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">No hay información de reparto disponible.</p>
                </div>
                @endforelse
            </div>
            
            @if($movie->crew->count() > 0)
            <h3 class="h4 mt-5 mb-4">Equipo técnico</h3>
            
            <div class="row">
                @foreach($movie->crew->sortBy('department')->take(6) as $crew)
                <div class="col-md-2 col-sm-4 col-6 mb-4">
                    <div class="card cast-card h-100">
                        <img src="{{ $crew->person->profile_path ? asset('storage/' . $crew->person->profile_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $crew->person->name }}">
                        <div class="card-body p-2 text-center">
                            <h5 class="card-title h6">{{ $crew->person->name }}</h5>
                            <p class="card-text small text-muted">{{ $crew->job }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Pestaña de Disponibilidad -->
        @if($movie->availability->count() > 0)
        <div class="tab-pane fade" id="availability" role="tabpanel" aria-labelledby="availability-tab">
            <h3 class="h4 mb-4">Disponibilidad por plataforma</h3>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Plataforma</th>
                            <th>País</th>
                            <th>Calidad</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movie->availability as $availability)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $availability->platform->logo_path ? asset('storage/' . $availability->platform->logo_path) : 'https://via.placeholder.com/40x20' }}" 
                                         alt="{{ $availability->platform->name }}" height="20" class="me-2">
                                    {{ $availability->platform->name }}
                                </div>
                            </td>
                            <td>{{ $availability->country->name }}</td>
                            <td>{{ $availability->quality ?? 'Estándar' }}</td>
                            <td>
                                @if($availability->type == 'subscription')
                                <span class="badge bg-primary">Suscripción</span>
                                @elseif($availability->type == 'rent')
                                <span class="badge bg-warning text-dark">Alquiler</span>
                                @elseif($availability->type == 'purchase')
                                <span class="badge bg-success">Compra</span>
                                @else
                                <span class="badge bg-secondary">{{ $availability->type }}</span>
                                @endif
                            </td>
                            <td>
                                @if($availability->price)
                                {{ $availability->price }} €
                                @elseif($availability->type == 'subscription')
                                Incluido
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                <a href="{{ $availability->url }}" target="_blank" class="btn btn-sm btn-primary">
                                    Ver ahora
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Películas Relacionadas -->
    @if($relatedMovies->count() > 0)
    <div class="related-content mt-5 mb-5">
        <h3 class="h4 mb-4">Películas relacionadas</h3>
        
        <div class="row">
            @foreach($relatedMovies as $relatedMovie)
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="content-card h-100">
                    <div class="position-relative">
                        <img src="{{ $relatedMovie->poster_path ? asset('storage/' . $relatedMovie->poster_path) : 'https://via.placeholder.com/200x300' }}" 
                             class="card-img-top" alt="{{ $relatedMovie->title }}">
                        <div class="content-type-badge">Película</div>
                        @if($relatedMovie->vote_average > 0)
                        <div class="rating-badge">
                            <i class="bi bi-star-fill"></i> {{ number_format($relatedMovie->vote_average, 1) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate">{{ $relatedMovie->title }}</h6>
                        <p class="card-text small text-muted">
                            {{ $relatedMovie->country_of_origin }} • {{ $relatedMovie->release_date ? $relatedMovie->release_date->format('Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('catalog.movie-detail', $relatedMovie->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de Valoración -->
@if(auth()->check())
<div class="modal fade" id="rateMovieModal" tabindex="-1" aria-labelledby="rateMovieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rateMovieModalLabel">Valorar "{{ $movie->title }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ratings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="content_type" value="App\Models\Movie">
                <input type="hidden" name="content_id" value="{{ $movie->id }}">
                
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <div class="rating-stars">
                            <input type="radio" id="star10" name="rating" value="10" />
                            <label for="star10" title="5 estrellas">★</label>
                            <input type="radio" id="star9" name="rating" value="9" />
                            <label for="star9" title="4.5 estrellas">★</label>
                            <input type="radio" id="star8" name="rating" value="8" />
                            <label for="star8" title="4 estrellas">★</label>
                            <input type="radio" id="star7" name="rating" value="7" />
                            <label for="star7" title="3.5 estrellas">★</label>
                            <input type="radio" id="star6" name="rating" value="6" />
                            <label for="star6" title="3 estrellas">★</label>
                            <input type="radio" id="star5" name="rating" value="5" />
                            <label for="star5" title="2.5 estrellas">★</label>
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4" title="2 estrellas">★</label>
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3" title="1.5 estrellas">★</label>
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2" title="1 estrella">★</label>
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1" title="0.5 estrellas">★</label>
                        </div>
                        <div class="selected-rating mt-2">Valoración: <span id="rating-value">0</span>/10</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="review" class="form-label">Reseña (opcional)</label>
                        <textarea class="form-control" id="review" name="review" rows="4" placeholder="Escribe tu opinión sobre la película..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="contains_spoilers" name="contains_spoilers" value="1">
                        <label class="form-check-label" for="contains_spoilers">
                            Esta reseña contiene spoilers
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar valoración</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Compartir -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Compartir "{{ $movie->title }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center gap-3 mb-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('catalog.movie-detail', $movie->slug)) }}" 
                       target="_blank" class="btn btn-outline-primary btn-lg rounded-circle">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode('Mira ' . $movie->title) }}&url={{ urlencode(route('catalog.movie-detail', $movie->slug)) }}" 
                       target="_blank" class="btn btn-outline-info btn-lg rounded-circle">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Mira ' . $movie->title . ': ' . route('catalog.movie-detail', $movie->slug)) }}" 
                       target="_blank" class="btn btn-outline-success btn-lg rounded-circle">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="mailto:?subject={{ urlencode('Recomendación: ' . $movie->title) }}&body={{ urlencode('Mira esta película: ' . route('catalog.movie-detail', $movie->slug)) }}" 
                       class="btn btn-outline-secondary btn-lg rounded-circle">
                        <i class="bi bi-envelope"></i>
                    </a>
                </div>
                
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareUrl" value="{{ route('catalog.movie-detail', $movie->slug) }}" readonly>
                    <button class="btn btn-outline-primary" type="button" id="copyButton">Copiar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
    .movie-backdrop {
        background-color: #000;
        margin-bottom: 2rem;
    }
    
    .backdrop-image {
        height: 600px;
        background-size: cover;
        background-position: center top;
        position: relative;
    }
    
    .backdrop-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.9) 100%);
    }
    
    .poster-container {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .rating-star {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .platform-badge {
        display: inline-block;
        background-color: white;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .platform-badge:hover {
        transform: translateY(-5px);
    }
    
    .platform-logo {
        height: 30px;
        width: auto;
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
    
    .cast-card .card-img-top {
        height: 250px;
        object-fit: cover;
    }
    
    /* Sistema de valoración por estrellas */
    .rating-stars {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    
    .rating-stars input {
        display: none;
    }
    
    .rating-stars label {
        cursor: pointer;
        font-size: 2.5rem;
        color: #ddd;
        padding: 0 0.1rem;
    }
    
    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #ffc107;
    }
    
    @media (max-width: 768px) {
        .backdrop-image {
            height: 400px;
        }
        
        .content-card .card-img-top {
            height: 200px;
        }
        
        .cast-card .card-img-top {
            height: 180px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sistema de valoración
        const ratingInputs = document.querySelectorAll('.rating-stars input');
        const ratingValue = document.getElementById('rating-value');
        
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                ratingValue.textContent = this.value;
            });
        });
        
        // Botón de copiar enlace
        const copyButton = document.getElementById('copyButton');
        const shareUrl = document.getElementById('shareUrl');
        
        if (copyButton && shareUrl) {
            copyButton.addEventListener('click', function() {
                shareUrl.select();
                document.execCommand('copy');
                
                copyButton.textContent = 'Copiado!';
                setTimeout(() => {
                    copyButton.textContent = 'Copiar';
                }, 2000);
            });
        }
    });
</script>
@endsection