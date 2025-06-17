@extends('layouts.app')

@section('title', 'Dorasia - Las Mejores Películas')

@section('content')
<div class="netflix-mobile-home">
    <!-- Hero Section -->
    @if($featuredMovie)
    <div class="mobile-hero">
        <div class="mobile-hero-image" style="background-image: url('{{ $featuredMovie->backdrop_path ? 'https://image.tmdb.org/t/p/w1280' . $featuredMovie->backdrop_path : 'https://via.placeholder.com/375x250/333/666?text=K-Movie' }}')">
            <div class="mobile-hero-overlay"></div>
            <div class="mobile-hero-content">
                <div class="mobile-hero-logo">
                    <span class="hero-badge">PELÍCULA</span>
                    <h1 class="mobile-hero-title">{{ $featuredMovie->display_title }}</h1>
                </div>
                <div class="mobile-hero-info">
                    @if($featuredMovie->vote_average > 0)
                    <div class="hero-rating">
                        <span class="rating-stars">⭐</span>
                        <span class="rating-number">{{ number_format($featuredMovie->vote_average, 1) }}</span>
                    </div>
                    @endif
                    @if(auth()->check() && $featuredMovie->vote_average > 0)
                    <span class="hero-match">{{ number_format($featuredMovie->vote_average * 10) }}% de coincidencia</span>
                    @endif
                    @if($featuredMovie->release_date)
                    <span class="hero-year">{{ $featuredMovie->release_date->format('Y') }}</span>
                    @endif
                    <span class="hero-maturity">16+</span>
                    @if($featuredMovie->runtime)
                    <span class="hero-seasons">{{ $featuredMovie->runtime }} min</span>
                    @endif
                </div>
                
                @if($featuredMovie->display_overview)
                <p class="mobile-hero-description">
                    {{ Str::limit($featuredMovie->display_overview, 200) }}
                </p>
                @endif
                
                <div class="mobile-hero-actions">
                    <a href="{{ route('movies.show', $featuredMovie->id) }}" class="mobile-info-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                        Ver información
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Categories -->
    <div class="mobile-categories">
        <div class="mobile-category-pills">
            <a href="{{ route('home') }}" class="category-pill">Series</a>
            <a href="{{ route('movies.index') }}" class="category-pill active">Películas</a>
            <a href="{{ route('actors.index') }}" class="category-pill">Actores</a>
            @auth
            <a href="{{ route('profile.watchlist') }}" class="category-pill {{ request()->routeIs('profile.watchlist') ? 'active' : '' }}">Mi lista</a>
            @endauth
        </div>
    </div>

    <!-- Content Rows -->
    <div class="mobile-content-rows">

        <!-- Populares -->
        @if(isset($popularMovies) && $popularMovies && $popularMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Populares en Dorasia</h2>
            <div class="mobile-row-content">
                @foreach($popularMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mobile-card-ranking">{{ $loop->iteration }}</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($movie->display_overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Mejor Calificadas -->
        @if(isset($topRatedMovies) && $topRatedMovies && $topRatedMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Mejor Calificadas</h2>
            <div class="mobile-row-content">
                @foreach($topRatedMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Recientes -->
        @if(isset($recentMovies) && $recentMovies && $recentMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Recientes</h2>
            <div class="mobile-row-content">
                @foreach($recentMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Acción -->
        @if(isset($actionMovies) && $actionMovies && $actionMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Acción</h2>
            <div class="mobile-row-content">
                @foreach($actionMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Comedia -->
        @if(isset($comedyMovies) && $comedyMovies && $comedyMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Comedia</h2>
            <div class="mobile-row-content">
                @foreach($comedyMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Drama -->
        @if(isset($dramaMovies) && $dramaMovies && $dramaMovies->count() > 0)
        <section class="mobile-row">
            <h2 class="mobile-row-title">Drama</h2>
            <div class="mobile-row-content">
                @foreach($dramaMovies->take(10) as $movie)
                <div class="mobile-card" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w342' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}" 
                             alt="{{ $movie->display_title }}" loading="lazy">
                        <div class="mobile-card-overlay">
                            <div class="card-play-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $movie->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($movie->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($movie->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $movie->vote_average > 0)
                            <span class="card-match">{{ number_format($movie->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($movie->release_date)
                            <span class="card-year">{{ $movie->release_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($movie->genres)
                                @foreach($movie->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
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

/* Cards */
.mobile-card {
    flex-shrink: 0;
    width: 160px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.mobile-card:active {
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

.card-match {
    color: #46d369;
    font-size: 0.75rem;
    font-weight: 600;
}

.card-year {
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

.mobile-card-description {
    color: #ccc;
    font-size: 0.75rem;
    line-height: 1.3;
    margin: 0;
}

@media (max-width: 480px) {
    .mobile-hero-title {
        font-size: 1.5rem;
    }
    
    .mobile-hero-actions {
        flex-direction: column;
    }
    
    .mobile-info-btn {
        justify-content: center;
    }
    
    .mobile-card {
        width: 140px;
    }
    
    .mobile-card-image {
        width: 140px;
        height: 210px;
    }
}
</style>
@endsection