@extends('layouts.app')

@section('title', 'Actores - Dorasia')

@section('content')
<div class="netflix-mobile-home">
    <!-- Hero Section -->
    @if($featuredActor)
    <div class="mobile-hero">
        <div class="mobile-hero-image" style="background-image: url('{{ $featuredActor->profile_path ? 'https://image.tmdb.org/t/p/w1280' . $featuredActor->profile_path : 'https://via.placeholder.com/375x250/333/666?text=Actor' }}')">
            <div class="mobile-hero-overlay"></div>
            <div class="mobile-hero-content">
                <div class="mobile-hero-logo">
                    <span class="hero-badge">ACTOR</span>
                    <h1 class="mobile-hero-title">{{ $featuredActor->name }}</h1>
                </div>
                <div class="mobile-hero-info">
                    @if($featuredActor->popularity > 0)
                    <div class="hero-rating">
                        <span class="rating-stars">⭐</span>
                        <span class="rating-number">{{ number_format($featuredActor->popularity, 1) }}</span>
                    </div>
                    @endif
                    @if($featuredActor->birthday)
                    <span class="hero-year">{{ $featuredActor->birthday->age ?? 'N/A' }} años</span>
                    @endif
                    @if($featuredActor->place_of_birth)
                    <span class="hero-seasons">{{ Str::limit($featuredActor->place_of_birth, 20) }}</span>
                    @endif
                    <span class="hero-maturity">{{ $featuredActor->known_for_department ?: 'Acting' }}</span>
                </div>
                
                @if($featuredActor->biography)
                <p class="mobile-hero-description">
                    {{ Str::limit($featuredActor->biography, 200) }}
                </p>
                @endif
                
                <div class="mobile-hero-actions">
                    <a href="{{ route('actors.show', $featuredActor->id) }}" class="mobile-info-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                        Ver perfil
                    </a>
                    @auth
                    <button class="mobile-follow-btn" data-actor-id="{{ $featuredActor->id }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                        Seguir
                    </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Categories -->
    <div class="mobile-categories">
        <div class="mobile-category-pills">
            <a href="{{ route('home') }}" class="category-pill">Series</a>
            <a href="{{ route('movies.index') }}" class="category-pill">Películas</a>
            <a href="{{ route('actors.index') }}" class="category-pill active">Actores</a>
            @auth
            <a href="{{ route('profile.watchlist') }}" class="category-pill">Mi lista</a>
            @endauth
        </div>
    </div>

    <!-- Content Rows -->
    <div class="mobile-content-rows">

        <!-- Más Populares -->
        @if(isset($popularActors) && $popularActors && $popularActors->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Más Populares</h2>
            <div class="mobile-row-content">
                @foreach($popularActors->take(10) as $actor)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $actor->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w342' . $actor->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actor' }}" 
                             alt="{{ $actor->display_name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mobile-card-ranking">{{ $loop->iteration }}</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $actor->display_name }}</h3>
                        <div class="mobile-card-meta">
                            @if($actor->popularity > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($actor->popularity, 1) }}</span>
                            </div>
                            @endif
                            @if($actor->birthday)
                            <span class="card-year">{{ $actor->birthday->age ?? 'N/A' }} años</span>
                            @endif
                            <span class="card-maturity">{{ $actor->known_for_department ?: 'Acting' }}</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($actor->place_of_birth)
                                <span class="card-genre">{{ Str::limit($actor->place_of_birth, 30) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Trending -->
        @if(isset($trendingActors) && $trendingActors && $trendingActors->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Trending 🔥</h2>
            <div class="mobile-row-content">
                @foreach($trendingActors->take(10) as $actor)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $actor->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w342' . $actor->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actor' }}" 
                             alt="{{ $actor->display_name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="trending-badge">🔥</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $actor->display_name }}</h3>
                        <div class="mobile-card-meta">
                            @if($actor->followers_count > 0)
                            <span class="card-followers">{{ $actor->followers_count }} seguidores</span>
                            @endif
                            @if($actor->birthday)
                            <span class="card-year">{{ $actor->birthday->age ?? 'N/A' }} años</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Actrices -->
        @if(isset($actresses) && $actresses && $actresses->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Actrices ✨</h2>
            <div class="mobile-row-content">
                @foreach($actresses->take(10) as $actress)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $actress->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $actress->profile_path ? 'https://image.tmdb.org/t/p/w342' . $actress->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actriz' }}" 
                             alt="{{ $actress->name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $actress->name }}</h3>
                        <div class="mobile-card-meta">
                            @if($actress->popularity > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($actress->popularity, 1) }}</span>
                            </div>
                            @endif
                            @if($actress->birthday)
                            <span class="card-year">{{ $actress->birthday->age ?? 'N/A' }} años</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Actores -->
        @if(isset($maleActors) && $maleActors && $maleActors->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Actores 🎭</h2>
            <div class="mobile-row-content">
                @foreach($maleActors->take(10) as $maleActor)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $maleActor->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $maleActor->profile_path ? 'https://image.tmdb.org/t/p/w342' . $maleActor->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actor' }}" 
                             alt="{{ $maleActor->name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $maleActor->name }}</h3>
                        <div class="mobile-card-meta">
                            @if($maleActor->popularity > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($maleActor->popularity, 1) }}</span>
                            </div>
                            @endif
                            @if($maleActor->birthday)
                            <span class="card-year">{{ $maleActor->birthday->age ?? 'N/A' }} años</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Nuevas Generaciones -->
        @if(isset($youngActors) && $youngActors && $youngActors->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Nuevas Generaciones 🌟</h2>
            <div class="mobile-row-content">
                @foreach($youngActors->take(10) as $youngActor)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $youngActor->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $youngActor->profile_path ? 'https://image.tmdb.org/t/p/w342' . $youngActor->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actor' }}" 
                             alt="{{ $youngActor->name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="young-badge">🌟</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $youngActor->name }}</h3>
                        <div class="mobile-card-meta">
                            @if($youngActor->birthday)
                            <span class="card-year">{{ $youngActor->birthday->age ?? 'N/A' }} años</span>
                            @endif
                            @if($youngActor->popularity > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($youngActor->popularity, 1) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Veteranos -->
        @if(isset($veteranActors) && $veteranActors && $veteranActors->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Veteranos 👑</h2>
            <div class="mobile-row-content">
                @foreach($veteranActors->take(10) as $veteranActor)
                <div class="mobile-card actor-card" onclick="location.href='{{ route('actors.show', $veteranActor->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $veteranActor->profile_path ? 'https://image.tmdb.org/t/p/w342' . $veteranActor->profile_path : 'https://via.placeholder.com/160x240/333/666?text=Actor' }}" 
                             alt="{{ $veteranActor->name }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M5 16L3 5l5.5-3L12 4.5 15.5 2 21 5l-2 11H5zm2.7-2h8.6l.9-5.4-3.1-1.8L12 8l-2.1-1.2L6.8 8.6 7.7 14z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="veteran-badge">👑</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $veteranActor->name }}</h3>
                        <div class="mobile-card-meta">
                            @if($veteranActor->birthday)
                            <span class="card-year">{{ $veteranActor->birthday->age ?? 'N/A' }} años</span>
                            @endif
                            @if($veteranActor->popularity > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($veteranActor->popularity, 1) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

    </div>
</div>

<style>
.netflix-mobile-home {
    background: #141414;
    color: white;
    min-height: 100vh;
}

/* Hero Section */
.mobile-hero {
    position: relative;
    height: 60vh;
    min-height: 400px;
}

.mobile-hero-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
}

.mobile-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
}

.mobile-hero-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 2rem 1rem;
    z-index: 10;
}

.mobile-hero-logo {
    margin-bottom: 1rem;
}

.hero-badge {
    background: #0099ff;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    display: inline-block;
    box-shadow: 0 2px 8px rgba(0, 153, 255, 0.4);
}

.mobile-hero-title {
    font-size: 2rem;
    font-weight: 900;
    margin: 0.5rem 0;
    line-height: 1.1;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.mobile-hero-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.hero-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.hero-rating .rating-stars {
    font-size: 0.8rem;
}

.hero-rating .rating-number {
    color: #ffd700;
    font-weight: 600;
    font-size: 0.9rem;
}

.hero-match {
    color: #46d369;
    font-weight: 600;
    font-size: 0.9rem;
}

.hero-year, .hero-seasons {
    color: #ccc;
    font-size: 0.9rem;
}

.hero-maturity {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.3rem;
    border-radius: 3px;
    font-size: 0.75rem;
    font-weight: 600;
}

.mobile-hero-description {
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0 0 1.5rem 0;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
}

.mobile-hero-actions {
    display: flex;
    gap: 0.75rem;
}

.mobile-info-btn {
    background: rgba(109, 109, 110, 0.7);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    border: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    text-decoration: none;
}

.mobile-follow-btn {
    background: #0099ff;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    border: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
}

/* Categories */
.mobile-categories {
    padding: 1rem;
    border-bottom: 1px solid #333;
}

.mobile-category-pills {
    display: flex;
    gap: 1rem;
}

.category-pill {
    padding: 0.5rem 0;
    color: #999;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.category-pill:hover {
    color: #ccc;
    text-decoration: none;
}

.category-pill.active {
    color: white;
    border-bottom-color: #e50914;
}

/* Content Rows */
.mobile-content-rows {
    padding: 0 1rem;
}

.mobile-row {
    margin-bottom: 2rem;
}

.mobile-row-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: white;
}

.mobile-row-content {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    scroll-behavior: smooth;
}

.mobile-row-content::-webkit-scrollbar {
    display: none;
}

/* Actor Cards */
.mobile-card.actor-card {
    flex-shrink: 0;
    width: 160px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.mobile-card.actor-card:active {
    transform: scale(0.98);
}

.mobile-card-image {
    position: relative;
    width: 160px;
    height: 240px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.mobile-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mobile-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.mobile-card:hover .mobile-card-overlay {
    opacity: 1;
}

.card-play-btn {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-card-ranking {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    background: rgba(0,0,0,0.8);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.trending-badge, .young-badge, .veteran-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

.trending-badge {
    background: linear-gradient(45deg, #ff6b35, #f7931e);
}

.young-badge {
    background: linear-gradient(45deg, #667eea, #764ba2);
}

.veteran-badge {
    background: linear-gradient(45deg, #f093fb, #f5576c);
}

.mobile-card-info {
    padding: 0 0.25rem;
}

.mobile-card-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
    color: white;
}

.mobile-card-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    flex-wrap: wrap;
}

.card-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.card-rating .rating-stars {
    font-size: 0.7rem;
}

.card-rating .rating-number {
    color: #ffd700;
    font-weight: 600;
    font-size: 0.75rem;
}

.card-followers, .card-year {
    color: #ccc;
    font-size: 0.75rem;
}

.card-maturity {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.2rem;
    border-radius: 2px;
    font-size: 0.65rem;
    font-weight: 600;
}

.mobile-card-genres {
    color: #999;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.card-genre {
    font-size: 0.75rem;
}

@media (max-width: 480px) {
    .mobile-hero-title {
        font-size: 1.5rem;
    }
    
    .mobile-hero-actions {
        flex-direction: column;
    }
    
    .mobile-info-btn,
    .mobile-follow-btn {
        justify-content: center;
    }
    
    .mobile-card.actor-card {
        width: 140px;
    }
    
    .mobile-card-image {
        width: 140px;
        height: 210px;
    }
}
</style>

<script>
// Follow/Unfollow functionality
document.addEventListener('DOMContentLoaded', function() {
    const followBtns = document.querySelectorAll('.mobile-follow-btn');
    
    followBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const actorId = this.dataset.actorId;
            toggleFollow(actorId, this);
        });
    });
    
    // Enhanced Lazy Loading with Intersection Observer
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            // Load images when they're 100px away from viewport
            rootMargin: '100px'
        });

        // Observe all lazy images
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }
});

function toggleFollow(actorId, btn) {
    const isFollowing = btn.textContent.trim() === 'Siguiendo';
    const url = isFollowing ? `/actors/${actorId}/unfollow` : `/actors/${actorId}/follow`;
    const method = isFollowing ? 'DELETE' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.textContent = data.is_following ? 'Siguiendo' : 'Seguir';
            btn.style.background = data.is_following ? '#46d369' : '#0099ff';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection