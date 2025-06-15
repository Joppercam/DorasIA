@extends('layouts.app')

@section('title', ($movie->display_title ?: $movie->title) . ' - Dorasia')

@section('content')
<div style="margin-top: -1rem;">

    <!-- Movie Hero Section -->
    <section class="movie-hero" 
             style="background-image: url('{{ $movie->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $movie->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=K-Movie' }}')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="movie-hero-container">
                <!-- Poster -->
                <div class="movie-hero-poster">
                    @if($movie->poster_path)
                        <img src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}" 
                             alt="{{ $movie->display_title ?: $movie->title }}"
                             class="poster-image">
                    @else
                        <div class="poster-placeholder">🎬</div>
                    @endif
                </div>
                
                <!-- Info -->
                <div class="movie-hero-info">
                    <!-- Géneros -->
                    @if($movie->genres->count() > 0)
                    <div class="hero-genres">
                        @foreach($movie->genres->take(3) as $genre)
                            <span class="hero-genre">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <h1 class="movie-hero-title">{{ $movie->display_title ?: $movie->title }}</h1>
                    
                    <!-- Tagline -->
                    @if($movie->tagline)
                        <p class="movie-tagline">{{ $movie->tagline }}</p>
                    @endif
                    
                    <!-- Meta info -->
                    <div class="movie-meta">
                        @if($movie->vote_average > 0)
                        <div class="meta-item">
                            <span class="meta-icon">⭐</span>
                            <span class="meta-value">{{ number_format($movie->vote_average, 1) }}</span>
                        </div>
                        @endif
                        
                        @if($movie->year)
                        <div class="meta-item">
                            <span class="meta-icon">📅</span>
                            <span class="meta-value">{{ $movie->year }}</span>
                        </div>
                        @endif
                        
                        @if($movie->formatted_runtime)
                        <div class="meta-item">
                            <span class="meta-icon">⏱️</span>
                            <span class="meta-value">{{ $movie->formatted_runtime }}</span>
                        </div>
                        @endif
                        
                        @if($movie->status)
                        <div class="meta-item">
                            <span class="status-badge status-{{ strtolower($movie->status) }}">
                                {{ ucfirst($movie->status) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Overview -->
                    @if($movie->display_overview || $movie->overview)
                    <p class="movie-overview">
                        {{ $movie->display_overview ?: $movie->overview }}
                    </p>
                    @endif
                    
                    <!-- User Actions for Movies -->
                    @auth
                    <!-- Mobile Movie Actions -->
                    <div class="movie-actions d-block d-md-none">
                        @include('components.movie-rating-buttons', ['movie' => $movie])
                    </div>
                    
                    <!-- Desktop Movie Actions -->
                    <div class="movie-actions d-none d-md-flex">
                        @include('components.movie-rating-buttons', ['movie' => $movie])
                    </div>
                    
                    <!-- Rating Info for Mobile -->
                    @if(auth()->check())
                        @php
                            $userRating = $movie->userRating(auth()->id());
                            $currentRating = $userRating ? $userRating->rating_type : null;
                        @endphp
                        @if($currentRating)
                        <div class="current-rating-info d-block d-md-none">
                            <span class="rating-label">Tu calificación:</span>
                            <span class="rating-display">
                                @if($currentRating === 'dislike')
                                    👎 No me gusta
                                @elseif($currentRating === 'like')
                                    👍 Me gusta
                                @else
                                    ❤️ Me encanta
                                @endif
                            </span>
                        </div>
                        @endif
                    @endif
                    @endauth
                    
                </div>
            </div>
        </div>
    </section>

    <!-- Movie Details Cards -->
    <section class="content-section movie-details-section">
        <div class="details-cards-grid">
            <!-- Fecha de estreno -->
            @if($movie->release_date)
            <div class="detail-card premiere-card">
                <div class="detail-icon">📅</div>
                <div class="detail-content">
                    <span class="detail-label">Fecha de estreno</span>
                    <span class="detail-value">{{ $movie->formatted_release_date }}</span>
                </div>
            </div>
            @endif
            
            <!-- Duración -->
            @if($movie->runtime)
            <div class="detail-card runtime-card">
                <div class="detail-icon">⏱️</div>
                <div class="detail-content">
                    <span class="detail-label">Duración</span>
                    <span class="detail-value">{{ $movie->formatted_runtime }}</span>
                </div>
            </div>
            @endif
            
            <!-- Calificación -->
            @if($movie->vote_average > 0)
            <div class="detail-card rating-card">
                <div class="detail-icon">⭐</div>
                <div class="detail-content">
                    <span class="detail-label">Calificación</span>
                    <span class="detail-value rating-score">
                        {{ number_format($movie->vote_average, 1) }}/10
                        <small>({{ number_format($movie->vote_count) }} votos)</small>
                    </span>
                </div>
            </div>
            @endif
            
            <!-- Idioma -->
            <div class="detail-card language-card">
                <div class="detail-icon">🌏</div>
                <div class="detail-content">
                    <span class="detail-label">Idioma original</span>
                    <span class="detail-value">{{ $movie->original_language === 'ko' ? 'Coreano' : 'Otro' }}</span>
                </div>
            </div>
            
            <!-- Presupuesto -->
            @if($movie->budget > 0)
            <div class="detail-card budget-card">
                <div class="detail-icon">💰</div>
                <div class="detail-content">
                    <span class="detail-label">Presupuesto</span>
                    <span class="detail-value">${{ number_format($movie->budget) }}</span>
                </div>
            </div>
            @endif
            
            <!-- Recaudación -->
            @if($movie->revenue > 0)
            <div class="detail-card revenue-card">
                <div class="detail-icon">💵</div>
                <div class="detail-content">
                    <span class="detail-label">Recaudación</span>
                    <span class="detail-value">${{ number_format($movie->revenue) }}</span>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Películas Relacionadas -->
    @if($relatedMovies->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Películas Similares</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($relatedMovies as $relatedMovie)
                <div class="card movie-related-card" 
                     style="background-image: url('{{ $relatedMovie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $relatedMovie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}')"
                     onclick="window.location.href='{{ route('movies.show', $relatedMovie) }}'">
                    <div class="card-overlay"></div>
                    
                    <!-- Movie info -->
                    <div class="card-info">
                        <div class="card-title">{{ $relatedMovie->display_title ?: $relatedMovie->title }}</div>
                        <div class="card-meta">
                            @if($relatedMovie->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($relatedMovie->vote_average, 1) }}</span>
                            @endif
                            @if($relatedMovie->year)
                            <span class="card-year">{{ $relatedMovie->year }}</span>
                            @endif
                            @if($relatedMovie->formatted_runtime)
                            <span class="card-runtime">{{ $relatedMovie->formatted_runtime }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>

<style>
/* Movie Hero */
.movie-hero {
    position: relative;
    min-height: 70vh;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    background-attachment: fixed;
}

.movie-hero .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(0,0,0,0.8) 0%,
        rgba(0,0,0,0.6) 50%,
        rgba(0,0,0,0.9) 100%
    );
}

.movie-hero-container {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    align-items: start;
}

.movie-hero-poster {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    aspect-ratio: 2/3;
}

.poster-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.poster-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: rgba(255,255,255,0.4);
}

.movie-hero-info {
    padding-top: 2rem;
}

.hero-genres {
    display: flex;
    gap: 0.8rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.hero-genre {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.movie-hero-title {
    color: white;
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.1;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
}

.movie-tagline {
    color: rgba(255,255,255,0.9);
    font-size: 1.2rem;
    font-style: italic;
    margin: 0 0 2rem 0;
    line-height: 1.4;
}

.movie-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    align-items: center;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.meta-icon {
    font-size: 1.2rem;
}

.meta-value {
    font-weight: 600;
    font-size: 1rem;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-released {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.status-upcoming {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.movie-overview {
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 0;
    max-width: 600px;
}

/* Movie Actions */
.movie-actions {
    margin-top: 2rem;
}

.action-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.watchlist-btn:hover {
    background: rgba(0, 212, 255, 0.2);
    border-color: rgba(0, 212, 255, 0.5);
    color: #00d4ff;
}

.like-btn:hover {
    background: rgba(76, 175, 80, 0.2);
    border-color: rgba(76, 175, 80, 0.5);
    color: #4caf50;
}

.love-btn:hover {
    background: rgba(244, 67, 54, 0.2);
    border-color: rgba(244, 67, 54, 0.5);
    color: #f44336;
}

/* Movie Details Section */
.movie-details-section {
    margin: 3rem 0;
}

.details-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.detail-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.detail-card:hover {
    transform: translateY(-3px);
    background: linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.06));
    border-color: rgba(0, 212, 255, 0.3);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.detail-icon {
    font-size: 2rem;
    background: rgba(0, 212, 255, 0.15);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid rgba(0, 212, 255, 0.2);
}

.detail-content {
    flex: 1;
}

.detail-label {
    display: block;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    display: block;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.rating-score small {
    color: rgba(255,255,255,0.6);
    font-weight: 400;
    font-size: 0.9rem;
}

/* Related Movies */
.movie-related-card {
    min-width: 200px;
    height: 300px;
    cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
    .movie-hero {
        min-height: 60vh;
        background-attachment: scroll;
    }
    
    .movie-hero-container {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 0 1rem;
        text-align: center;
    }
    
    .movie-hero-poster {
        max-width: 250px;
        margin: 0 auto;
    }
    
    .movie-hero-title {
        font-size: 2rem;
    }
    
    .movie-tagline {
        font-size: 1rem;
    }
    
    .movie-meta {
        justify-content: center;
        gap: 1rem;
    }
    
    .details-cards-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .detail-card {
        padding: 1rem;
    }
    
    .detail-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .movie-hero-title {
        font-size: 1.5rem;
    }
    
    .hero-genres {
        justify-content: center;
    }
    
    .movie-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

@endsection