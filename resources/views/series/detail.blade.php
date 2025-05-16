{{-- resources/views/series/detail.blade.php --}}
@extends('layouts.app')

@section('title', $tvShow->title)
@section('meta_description', Str::limit($tvShow->overview, 160))

@section('content')
<div class="series-detail">
    <!-- Hero Banner -->
    <div class="hero-banner" style="background-image: url('{{ $tvShow->backdrop_path ? asset('storage/' . $tvShow->backdrop_path) : asset('images/default-backdrop.jpg') }}')">
        <div class="hero-overlay"></div>
        
        <div class="container py-5">
            <div class="row">
                <!-- Poster -->
                <div class="col-md-3 mb-4">
                    <div class="poster-wrapper position-relative">
                        <img src="{{ $tvShow->poster_path ? asset('storage/' . $tvShow->poster_path) : asset('images/poster-placeholder.jpg') }}" 
                             alt="{{ $tvShow->title }}" class="img-fluid rounded shadow-lg poster-img">
                        
                        @if($tvShow->vote_average > 0)
                        <div class="rating-badge">
                            <div class="rating-circle">
                                <span class="rating-number">{{ number_format($tvShow->vote_average, 1) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Acciones Principales -->
                    <div class="mt-4 d-flex flex-column gap-2">
                        @auth
                        <div class="dropdown w-100">
                            <button class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center" 
                                    id="watchlistDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg me-2"></i> Añadir a lista
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="watchlistDropdown">
                                <li>
                                    <button class="dropdown-item d-flex align-items-center" 
                                            onclick="toggleWatchlist('{{ $tvShow->id }}', 'tv-show', 'default')">
                                        <i class="bi bi-bookmark-plus me-2"></i> Mi Lista
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach(auth()->user()->watchlists as $watchlist)
                                <li>
                                    <button class="dropdown-item d-flex align-items-center"
                                            onclick="toggleWatchlist('{{ $tvShow->id }}', 'tv-show', '{{ $watchlist->id }}')">
                                        <i class="bi bi-list-ul me-2"></i> {{ $watchlist->name }}
                                    </button>
                                </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('watchlists.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i> Crear nueva lista
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light flex-grow-1 d-flex align-items-center justify-content-center"
                                    onclick="toggleFavorite('{{ $tvShow->id }}', 'tv-show')"
                                    id="favoriteBtn">
                                <i class="bi {{ $isFavorite ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-md-2"></i>
                                <span class="d-none d-md-inline">{{ $isFavorite ? 'Favorito' : 'Favorito' }}</span>
                            </button>
                            
                            <button class="btn btn-outline-light flex-grow-1 d-flex align-items-center justify-content-center"
                                    data-bs-toggle="modal" data-bs-target="#rateModal">
                                <i class="bi bi-star me-md-2"></i>
                                <span class="d-none d-md-inline">Valorar</span>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión
                        </a>
                        <p class="text-center text-muted small mt-2">Para guardar en tu lista y valorar</p>
                        @endauth
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="info-sidebar mt-4">
                        <div class="info-card mb-3">
                            <h3 class="h6 mb-3">Información</h3>
                            <ul class="list-unstyled series-details">
                                @if($tvShow->first_air_date)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Estreno:</span>
                                    <span>{{ $tvShow->first_air_date->format('d M, Y') }}</span>
                                </li>
                                @endif
                                
                                @if($tvShow->last_air_date)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Último episodio:</span>
                                    <span>{{ $tvShow->last_air_date->format('d M, Y') }}</span>
                                </li>
                                @endif
                                
                                @if($tvShow->status)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Estado:</span>
                                    <span>{{ $tvShow->status }}</span>
                                </li>
                                @endif
                                
                                @if($tvShow->number_of_seasons)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Temporadas:</span>
                                    <span>{{ $tvShow->number_of_seasons }}</span>
                                </li>
                                @endif
                                
                                @if($tvShow->number_of_episodes)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Episodios:</span>
                                    <span>{{ $tvShow->number_of_episodes }}</span>
                                </li>
                                @endif
                                
                                @if($tvShow->original_language)
                                <li class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Idioma original:</span>
                                    <span>{{ strtoupper($tvShow->original_language) }}</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($tvShow->availability->count() > 0)
                        <div class="info-card">
                            <h3 class="h6 mb-3">Disponible en</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($tvShow->availability->unique('platform_id') as $availability)
                                <a href="{{ $availability->url }}" target="_blank" 
                                   class="platform-badge" title="{{ $availability->platform->name }}">
                                    <img src="{{ $availability->platform->logo_path ? asset('storage/' . $availability->platform->logo_path) : asset('images/platform-placeholder.png') }}" 
                                         alt="{{ $availability->platform->name }}" class="platform-logo">
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Detalles -->
                <div class="col-md-9">
                    <div class="mb-4">
                        <div class="d-flex flex-wrap align-items-center mb-2 gap-2">
                            @if($tvShow->country_of_origin)
                            <span class="badge bg-primary me-2">{{ $tvShow->country_of_origin }}</span>
                            @endif
                            
                            <span class="badge bg-secondary me-2">{{ $tvShow->show_type }}</span>
                            
                            @if($tvShow->first_air_date)
                            <span class="text-muted">{{ $tvShow->first_air_date->format('Y') }}</span>
                            @endif
                        </div>
                        
                        <h1 class="display-4 mb-2">{{ $tvShow->title }}</h1>
                        
                        @if($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                        <h2 class="h4 text-muted mb-3">{{ $tvShow->original_title }}</h2>
                        @endif
                        
                        <!-- Géneros -->
                        <div class="genres-list mb-4">
                            @foreach($tvShow->genres as $genre)
                            <a href="{{ route('catalog.genre', $genre->slug) }}" class="genre-badge">{{ $genre->name }}</a>
                            @endforeach
                        </div>
                        
                        <!-- Sinopsis -->
                        <div class="overview mb-5">
                            <p class="lead">{{ $tvShow->overview }}</p>
                        </div>
                        
                        <!-- Tabs de Navegación -->
                        <ul class="nav nav-tabs mb-4" id="seriesTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="episodes-tab" data-bs-toggle="tab" 
                                        data-bs-target="#episodes" type="button" role="tab" aria-selected="true">
                                    <i class="bi bi-list-ul me-1"></i> Episodios
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cast-tab" data-bs-toggle="tab" 
                                        data-bs-target="#cast" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-people me-1"></i> Reparto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" 
                                        data-bs-target="#gallery" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-images me-1"></i> Galería
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                                        data-bs-target="#reviews" type="button" role="tab" aria-selected="false">
                                    <i class="bi bi-chat-square-text me-1"></i> Reseñas
                                    <span class="badge bg-secondary ms-1">{{ count($tvShow->ratings ?? []) }}</span>
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Contenido de los Tabs -->
                        <div class="tab-content" id="seriesTabsContent">
                            <!-- Tab de Episodios -->
                            <div class="tab-pane fade show active" id="episodes" role="tabpanel" aria-labelledby="episodes-tab">
                                @if($tvShow->seasons->count() > 0)
                                <div class="seasons-wrapper">
                                    <!-- Selector de Temporada -->
                                    <div class="season-selector mb-4">
                                        <select class="form-select" id="seasonSelector">
                                            @foreach($tvShow->seasons as $season)
                                            <option value="season-{{ $season->id }}">
                                                Temporada {{ $season->season_number }} 
                                                @if($season->name && $season->name !== 'Season ' . $season->season_number)
                                                - {{ $season->name }}
                                                @endif
                                                ({{ $season->episodes->count() }} episodios)
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Lista de Episodios por Temporada -->
                                    @foreach($tvShow->seasons as $season)
                                    <div class="season-episodes" id="season-{{ $season->id }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                        @if($season->overview)
                                        <div class="season-overview mb-4">
                                            <p>{{ $season->overview }}</p>
                                        </div>
                                        @endif
                                        
                                        <div class="episode-list">
                                            @foreach($season->episodes as $episode)
                                            <div class="episode-card">
                                                <div class="row g-0">
                                                    <div class="col-md-3">
                                                        <div class="episode-image">
                                                            <img src="{{ $episode->still_path ? asset('storage/' . $episode->still_path) : asset('images/episode-placeholder.jpg') }}" 
                                                                 class="img-fluid rounded-start" alt="Episodio {{ $episode->episode_number }}">
                                                            <div class="episode-number">{{ $episode->episode_number }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="episode-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h3 class="episode-title">{{ $episode->name }}</h3>
                                                                @if($episode->air_date)
                                                                <span class="episode-date">{{ $episode->air_date->format('d/m/Y') }}</span>
                                                                @endif
                                                            </div>
                                                            @if($episode->runtime)
                                                            <div class="episode-runtime mb-2">
                                                                <i class="bi bi-clock me-1"></i> {{ $episode->runtime }} min
                                                            </div>
                                                            @endif
                                                            <p class="episode-overview">{{ $episode->overview ?: 'No hay descripción disponible para este episodio.' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> No hay información de episodios disponible para esta serie.
                                </div>
                                @endif
                            </div>
                            
                            <!-- Tab de Reparto -->
                            <div class="tab-pane fade" id="cast" role="tabpanel" aria-labelledby="cast-tab">
                                @if($tvShow->cast->count() > 0)
                                <h3 class="h5 mb-4">Actores principales</h3>
                                
                                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3 mb-5">
                                    @foreach($tvShow->cast->sortBy('order')->take(15) as $castMember)
                                    <div class="col">
                                        <div class="cast-card">
                                            <div class="cast-image">
                                                <img src="{{ $castMember->person->profile_path ? asset('storage/' . $castMember->person->profile_path) : asset('images/person-placeholder.jpg') }}" 
                                                     alt="{{ $castMember->person->name }}" class="img-fluid rounded">
                                            </div>
                                            <div class="cast-info">
                                                <h4 class="cast-name">{{ $castMember->person->name }}</h4>
                                                <p class="cast-character">{{ $castMember->character }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                
                                @if($tvShow->crew->count() > 0)
                                <h3 class="h5 mb-4">Equipo de producción</h3>
                                
                                <div class="row">
                                    @php
                                        // Agrupar por departamento
                                        $departments = $tvShow->crew->groupBy('department');
                                    @endphp
                                    
                                    @foreach($departments as $department => $crewMembers)
                                    <div class="col-md-6 mb-4">
                                        <h4 class="h6 border-bottom pb-2 mb-3">{{ $department }}</h4>
                                        <ul class="list-unstyled crew-list">
                                            @foreach($crewMembers as $crewMember)
                                            <li class="crew-item">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $crewMember->person->profile_path ? asset('storage/' . $crewMember->person->profile_path) : asset('images/person-placeholder.jpg') }}" 
                                                         alt="{{ $crewMember->person->name }}" class="crew-img me-3">
                                                    <div>
                                                        <div class="crew-name">{{ $crewMember->person->name }}</div>
                                                        <div class="crew-job text-muted">{{ $crewMember->job }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                
                                @if($tvShow->cast->count() == 0 && $tvShow->crew->count() == 0)
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> No hay información de reparto disponible para esta serie.
                                </div>
                                @endif
                            </div>
                            
                            <!-- Tab de Galería -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                                <!-- Implementación de galería de imágenes con modal lightbox -->
                                <div class="mb-4" id="gallery-wrapper">
                                    @if(isset($tvShow->images) && count($tvShow->images) > 0)
                                    <div class="row g-3 gallery-container" data-masonry='{"percentPosition": true }'>
                                        @foreach($tvShow->images as $image)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <a href="{{ asset('storage/' . $image->file_path) }}" class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="{{ asset('storage/' . $image->file_path) }}">
                                                <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $tvShow->title }}" class="img-fluid rounded">
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                    @elseif(isset($tvShow->backdrop_path) && $tvShow->backdrop_path)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="{{ asset('storage/' . $tvShow->backdrop_path) }}" class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="{{ asset('storage/' . $tvShow->backdrop_path) }}">
                                                <img src="{{ asset('storage/' . $tvShow->backdrop_path) }}" alt="{{ $tvShow->title }}" class="img-fluid rounded">
                                            </a>
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i> No hay imágenes disponibles para esta serie.
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Videos/Trailers si están disponibles -->
                                @if(isset($tvShow->videos) && count($tvShow->videos) > 0)
                                <h3 class="h5 mb-4">Videos</h3>
                                <div class="row g-3">
                                    @foreach($tvShow->videos as $video)
                                    <div class="col-md-6">
                                        <div class="video-card mb-4">
                                            <div class="ratio ratio-16x9">
                                                <iframe src="https://www.youtube.com/embed/{{ $video->key }}" 
                                                        title="{{ $video->name }}" 
                                                        allowfullscreen></iframe>
                                            </div>
                                            <div class="video-info p-2">
                                                <h4 class="h6">{{ $video->name }}</h4>
                                                <span class="badge bg-secondary">{{ $video->type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            
                            <!-- Tab de Reseñas -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <!-- Formulario para dejar reseñas solo para usuarios autenticados -->
                                @auth
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h3 class="h5 mb-0">Deja tu reseña</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('ratings.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="content_type" value="tv-show">
                                            <input type="hidden" name="content_id" value="{{ $tvShow->id }}">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Tu puntuación</label>
                                                <div class="rating-stars-wrapper d-flex justify-content-center mb-3">
                                                    <div class="rating-stars">
                                                        @for($i = 10; $i >= 1; $i--)
                                                        <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                                        <label for="rating-{{ $i }}" title="{{ $i }} {{ $i == 1 ? 'estrella' : 'estrellas' }}">★</label>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="text-center rating-text mb-3">
                                                    <span id="ratingText">Selecciona una puntuación</span>
                                                </div>
                                                @error('rating')
                                                <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="review" class="form-label">Tu opinión (opcional)</label>
                                                <textarea class="form-control" id="review" name="review" rows="4" 
                                                          placeholder="Comparte lo que piensas sobre esta serie...">{{ old('review') }}</textarea>
                                                @error('review')
                                                <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="containsSpoilers" name="contains_spoilers" value="1" {{ old('contains_spoilers') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="containsSpoilers">
                                                    Esta reseña contiene spoilers
                                                </label>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">Publicar reseña</button>
                                        </form>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-info mb-4">
                                    <i class="bi bi-info-circle me-2"></i> 
                                    <a href="{{ route('login') }}" class="alert-link">Inicia sesión</a> para dejar tu reseña sobre esta serie.
                                </div>
                                @endauth
                                
                                <!-- Listado de reseñas -->
                                <h3 class="h5 mb-3">Reseñas de usuarios</h3>
                                
                                @if(isset($tvShow->ratings) && count($tvShow->ratings) > 0)
                                <div class="reviews-list">
                                    @foreach($tvShow->ratings as $rating)
                                    <div class="review-card mb-4">
                                        <div class="review-header d-flex justify-content-between align-items-start">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $rating->user->avatar ? asset('storage/' . $rating->user->avatar) : asset('images/avatar-placeholder.jpg') }}" 
                                                     alt="{{ $rating->user->name }}" class="review-avatar me-3">
                                                <div>
                                                    <div class="review-user">{{ $rating->user->name }}</div>
                                                    <div class="review-date text-muted">
                                                        {{ $rating->created_at->format('d M, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <span class="rating-value">{{ $rating->rating }}</span>
                                                <span class="rating-max">/10</span>
                                            </div>
                                        </div>
                                        
                                        @if($rating->contains_spoilers)
                                        <div class="spoiler-warning mb-2" data-bs-toggle="collapse" data-bs-target="#spoiler-content-{{ $rating->id }}" aria-expanded="false">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Esta reseña contiene spoilers. Haz clic para mostrar.
                                        </div>
                                        <div class="collapse" id="spoiler-content-{{ $rating->id }}">
                                            <div class="review-content">
                                                {{ $rating->review }}
                                            </div>
                                        </div>
                                        @else
                                        <div class="review-content">
                                            {{ $rating->review }}
                                        </div>
                                        @endif
                                        
                                        <!-- Opciones para el autor de la reseña -->
                                        @if(auth()->check() && auth()->id() == $rating->user_id)
                                        <div class="review-actions mt-2 text-end">
                                            <button class="btn btn-sm btn-outline-secondary me-2" 
                                                    data-bs-toggle="modal" data-bs-target="#editReviewModal" 
                                                    data-review-id="{{ $rating->id }}" 
                                                    data-review-rating="{{ $rating->rating }}" 
                                                    data-review-text="{{ $rating->review }}" 
                                                    data-review-spoilers="{{ $rating->contains_spoilers }}">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </button>
                                            <form action="{{ route('ratings.destroy', $rating->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar esta reseña?')">
                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="alert alert-light">
                                    <i class="bi bi-chat-square me-2"></i> No hay reseñas de usuarios todavía. 
                                    ¡Sé el primero en valorar esta serie!
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de series similares/recomendadas -->
    @if(isset($similarSeries) && count($similarSeries) > 0)
    <div class="container py-5">
        <h2 class="h3 mb-4">Series similares</h2>
        
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach($similarSeries as $similarShow)
            <div class="col">
                <div class="similar-card">
                    <a href="{{ route('series.show', $similarShow->id) }}" class="similar-link">
                        <div class="similar-poster">
                            <img src="{{ $similarShow->poster_path ? asset('storage/' . $similarShow->poster_path) : asset('images/poster-placeholder.jpg') }}" 
                                 alt="{{ $similarShow->title }}" class="img-fluid">
                            @if($similarShow->vote_average > 0)
                            <div class="similar-rating">
                                <i class="bi bi-star-fill"></i> {{ number_format($similarShow->vote_average, 1) }}
                            </div>
                            @endif
                        </div>
                        <div class="similar-title">{{ $similarShow->title }}</div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de visualización de imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-body p-0 text-center">
                <img src="" id="modalImage" class="img-fluid">
            </div>
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Modal para editar reseña -->
@auth
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar reseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReviewForm" action="{{ route('ratings.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tu puntuación</label>
                        <div class="rating-stars-wrapper d-flex justify-content-center mb-3">
                            <div class="rating-stars edit-stars">
                                @for($i = 10; $i >= 1; $i--)
                                <input type="radio" id="edit-rating-{{ $i }}" name="rating" value="{{ $i }}">
                                <label for="edit-rating-{{ $i }}" title="{{ $i }} {{ $i == 1 ? 'estrella' : 'estrellas' }}">★</label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editReview" class="form-label">Tu opinión</label>
                        <textarea class="form-control" id="editReview" name="review" rows="4"></textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="editContainsSpoilers" name="contains_spoilers" value="1">
                        <label class="form-check-label" for="editContainsSpoilers">
                            Esta reseña contiene spoilers
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- Modal de Valoración -->
@auth
<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rateModalLabel">Valorar "{{ $tvShow->title }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ratings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="content_type" value="tv-show">
                <input type="hidden" name="content_id" value="{{ $tvShow->id }}">
                <div class="modal-body text-center">
                    <div class="rating-stars-wrapper d-flex justify-content-center mb-3">
                        <div class="rating-stars">
                            @for($i = 10; $i >= 1; $i--)
                            <input type="radio" id="modal-rating-{{ $i }}" name="rating" value="{{ $i }}">
                            <label for="modal-rating-{{ $i }}" title="{{ $i }} {{ $i == 1 ? 'estrella' : 'estrellas' }}">★</label>
                            @endfor
                        </div>
                    </div>
                    <div id="modalRatingText" class="mb-3">Selecciona una puntuación</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar valoración</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth
@endsection

@section('styles')
<style>
    /* Hero Banner */
    .hero-banner {
        height: 70vh;
        min-height: 400px;
        max-height: 600px;
        background-size: cover;
        background-position: center 25%;
        position: relative;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.9) 100%);
    }
    
    /* Poster y detalles */
    .poster-wrapper {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
    }
    
    .poster-img {
        display: block;
        width: 100%;
    }
    
    .rating-badge {
        position: absolute;
        top: -20px;
        right: -20px;
    }
    
    .rating-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    }
    
    .rating-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
    }
    
    /* Info Sidebar */
    .info-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .platform-badge {
        display: inline-block;
        background-color: white;
        border-radius: 8px;
        padding: 8px;
        transition: transform 0.3s ease;
    }
    
    .platform-badge:hover {
        transform: translateY(-3px);
    }
    
    .platform-logo {
        height: 25px;
        width: auto;
    }
    
    /* Géneros */
    .genre-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 50px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        transition: background-color 0.3s ease;
    }
    
    .genre-badge:hover {
        background-color: var(--bs-primary);
        text-decoration: none;
    }
    
    /* Episodios */
    .episode-card {
        margin-bottom: 1.5rem;
        border-radius: 8px;
        overflow: hidden;
        background-color: rgba(255, 255, 255, 0.05);
        transition: transform 0.3s ease;
    }
    
    .episode-card:hover {
        transform: translateY(-5px);
    }
    
    .episode-image {
        position: relative;
        height: 100%;
    }
    
    .episode-number {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background-color: var(--bs-primary);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
    }
    
    .episode-body {
        padding: 1rem;
    }
    
    .episode-title {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    
    .episode-date {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
    }
    
    .episode-runtime {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
    }
    
    /* Reparto */
    .cast-card {
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }
    
    .cast-card:hover {
        transform: translateY(-5px);
    }
    
    .cast-image {
        margin-bottom: 0.5rem;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .cast-info {
        text-align: center;
    }
    
    .cast-name {
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .cast-character {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 0;
    }
    
    .crew-list {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .crew-item {
        margin-bottom: 1rem;
    }
    
    .crew-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .crew-name {
        font-size: 0.9rem;
    }
    
    .crew-job {
        font-size: 0.8rem;
    }
    
    /* Galería */
    .gallery-item {
        display: block;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .gallery-item:hover {
        transform: scale(1.05);
    }
    
    /* Reseñas */
    .rating-stars-wrapper {
        margin-bottom: 1rem;
    }
    
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
        font-size: 1.8rem;
        color: #ccc;
        margin: 0 2px;
    }
    
    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #ffc107;
    }
    
    .review-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .review-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .review-user {
        font-weight: 500;
    }
    
    .review-date {
        font-size: 0.8rem;
    }
    
    .review-rating {
        background-color: var(--bs-primary);
        border-radius: 8px;
        padding: 0.5rem 0.8rem;
        color: white;
        font-weight: bold;
    }
    
    .review-content {
        margin-top: 1rem;
        line-height: 1.6;
    }
    
    .spoiler-warning {
        background-color: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.5);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        margin-top: 1rem;
        cursor: pointer;
    }
    
    /* Series similares */
    .similar-card {
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    
    .similar-card:hover {
        transform: translateY(-5px);
    }
    
    .similar-poster {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .similar-rating {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: rgba(255, 193, 7, 0.9);
        color: black;
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .similar-title {
        font-size: 0.9rem;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hero-banner {
            height: 50vh;
        }
        
        .rating-circle {
            width: 50px;
            height: 50px;
        }
        
        .rating-number {
            font-size: 1.2rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Script for series detail page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Season selector functionality
    const seasonSelector = document.getElementById('seasonSelector');
    if (seasonSelector) {
        seasonSelector.addEventListener('change', function() {
            // Hide all season episodes
            document.querySelectorAll('.season-episodes').forEach(function(element) {
                element.style.display = 'none';
            });
            
            // Show the selected season episodes
            const selectedSeason = document.getElementById(this.value);
            if (selectedSeason) {
                selectedSeason.style.display = 'block';
            }
        });
    }
    
    // Rating stars functionality
    const setupRatingStars = function(containerSelector, textElementSelector) {
        const ratingInputs = document.querySelectorAll(`${containerSelector} input[type="radio"]`);
        const ratingText = document.querySelector(textElementSelector);
        
        if (ratingInputs.length && ratingText) {
            const ratingLabels = {
                1: 'Terrible',
                2: 'Muy mala',
                3: 'Mala',
                4: 'Regular',
                5: 'Aceptable',
                6: 'Bien',
                7: 'Muy bien',
                8: 'Genial',
                9: 'Excelente',
                10: 'Obra maestra'
            };
            
            ratingInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const rating = parseInt(this.value);
                    ratingText.textContent = `${rating}/10 - ${ratingLabels[rating]}`;
                });
            });
        }
    };
    
    // Setup rating stars for the main form
    setupRatingStars('.rating-stars', '#ratingText');
    
    // Setup rating stars for the modal form
    setupRatingStars('.rating-stars', '#modalRatingText');
    
    // Image gallery modal functionality
    const galleryItems = document.querySelectorAll('.gallery-item');
    const modalImage = document.getElementById('modalImage');
    
    if (galleryItems.length && modalImage) {
        galleryItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const imageSrc = this.getAttribute('data-image-src');
                modalImage.src = imageSrc;
            });
        });
    }
    
    // Edit review modal functionality
    const editModal = document.getElementById('editReviewModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const reviewId = button.getAttribute('data-review-id');
            const reviewRating = button.getAttribute('data-review-rating');
            const reviewText = button.getAttribute('data-review-text');
            const reviewSpoilers = button.getAttribute('data-review-spoilers');
            
            // Set form action URL with the correct review ID
            const form = editModal.querySelector('#editReviewForm');
            form.action = form.action.replace(/\/\d+$/, `/${reviewId}`);
            
            // Set the rating
            const ratingInput = editModal.querySelector(`input[name="rating"][value="${reviewRating}"]`);
            if (ratingInput) {
                ratingInput.checked = true;
            }
            
            // Set the review text
            const reviewTextarea = editModal.querySelector('#editReview');
            if (reviewTextarea) {
                reviewTextarea.value = reviewText;
            }
            
            // Set the spoilers checkbox
            const spoilersCheckbox = editModal.querySelector('#editContainsSpoilers');
            if (spoilersCheckbox) {
                spoilersCheckbox.checked = reviewSpoilers === '1';
            }
        });
    }
    
    // Functions for watchlist and favorites (referenced in the template)
    window.toggleWatchlist = function(contentId, contentType, watchlistId) {
        fetch('/api/watchlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                content_id: contentId,
                content_type: contentType,
                watchlist_id: watchlistId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Could add visual feedback here if needed
                console.log('Watchlist updated successfully');
            }
        })
        .catch(error => console.error('Error:', error));
    };
    
    window.toggleFavorite = function(contentId, contentType) {
        fetch('/api/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                content_id: contentId,
                content_type: contentType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const favoriteBtn = document.getElementById('favoriteBtn');
                if (favoriteBtn) {
                    const icon = favoriteBtn.querySelector('i');
                    if (data.isFavorite) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart');
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
    };
    
    // Initialize Masonry for gallery if it exists
    const galleryContainer = document.querySelector('.gallery-container');
    if (galleryContainer && typeof Masonry !== 'undefined') {
        new Masonry(galleryContainer, {
            itemSelector: '.col',
            percentPosition: true
        });
    }
});