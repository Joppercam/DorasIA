@extends('layouts.app')

@section('title', 'Dorasia - Los Mejores K-Dramas')

@section('content')
<style>
/* Force hide rating elements in home page only */
body .card .card-rating-buttons,
body .card .watchlist-button-container,
body .card .series-stats,
body .card .rating-btn,
body .card .watchlist-btn {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
}
</style>
<div style="margin-top: -1rem;">
<!-- Hero Section with Rotation -->
@if($featuredSeries)
<section class="hero-section" id="heroSection" style="background-image: url('{{ $featuredSeries->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $featuredSeries->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=K-Drama' }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            <!-- Poster para móvil -->
            <img src="{{ $featuredSeries->poster_path ? 'https://image.tmdb.org/t/p/w500' . $featuredSeries->poster_path : 'https://via.placeholder.com/150x225/333/666?text=K-Drama' }}" 
                 alt="{{ $featuredSeries->display_title }}" 
                 class="mobile-hero-poster d-block d-md-none">
            
            <!-- Categories -->
            @if($featuredSeries->genres->count() > 0)
            <div class="hero-categories">
                @foreach($featuredSeries->genres->take(3) as $genre)
                    <span class="hero-category">{{ $genre->display_name ?: $genre->name }}</span>
                @endforeach
            </div>
            @endif
            
            <h1 class="hero-title">{{ $featuredSeries->display_title }}</h1>
            
            <!-- Rating and Year -->
            <div class="hero-meta">
                @if($featuredSeries->vote_average > 0)
                <div class="hero-rating">
                    <span class="rating-stars">⭐</span>
                    <span class="rating-number">{{ number_format($featuredSeries->vote_average, 1) }}</span>
                </div>
                @endif
                @if($featuredSeries->first_air_date)
                <span class="hero-year">{{ $featuredSeries->first_air_date->format('Y') }}</span>
                @endif
                @if($featuredSeries->number_of_episodes)
                <span class="hero-episodes">{{ $featuredSeries->number_of_episodes }} episodios</span>
                @endif
            </div>
            
            <p class="hero-description">
                {{ Str::limit($featuredSeries->display_overview ?: 'Descubre este increíble K-Drama y sumérgete en una historia única llena de emociones.', 280) }}
            </p>
            
            <div class="hero-buttons">
                <a href="{{ route('series.show', $featuredSeries->id) }}" class="btn btn-hero">
                    Ver
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Content Sections -->
<div style="margin-top: 50px; position: relative; z-index: 20;">

    <!-- Próximamente Section -->
    @include('components.upcoming-widget', ['upcoming' => $upcomingSeries])

    <!-- Mejores Películas -->
    @if($topRatedMovies->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Mejores Películas</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($topRatedMovies as $movie)
                <div class="card movie-card" 
                     style="background-image: url('{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Movie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #ff6b9d, #ff8e8e); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);">
                            PELÍCULA
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('movies.show', $movie) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($movie->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($movie->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <div class="card-info">
                        <div class="card-title">{{ $movie->display_title ?: $movie->title }}</div>
                        <div class="card-meta">
                            @if($movie->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($movie->vote_average, 1) }}</span>
                            @endif
                            @if($movie->year)
                            <span class="card-year">{{ $movie->year }}</span>
                            @endif
                            @if($movie->formatted_runtime)
                            <span class="card-runtime">{{ $movie->formatted_runtime }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Populares - NETFLIX RECTANGULAR CARDS -->
    @if($popularSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Tendencias</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($popularSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Mejor Calificadas -->
    @if($topRatedSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Mejor Calificadas</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($topRatedSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Romance -->
    @if($romanceSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Romance</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($romanceSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Drama -->
    @if($dramasSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Drama</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($dramasSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Comedia -->
    @if($comedySeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Comedia</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($comedySeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Acción -->
    @if($actionSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Acción y Aventura</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($actionSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series de Misterio -->
    @if($mysterySeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Misterio y Suspenso</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($mysterySeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Históricas -->
    @if($historicalSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Dramas Históricos (Sageuk)</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($historicalSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Recientes -->
    @if($recentSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Últimos Estrenos</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($recentSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Series Vistas -->
    @if(auth()->check() && $watchedSeries && $watchedSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">🎬 Series que has Visto</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($watchedSeries as $series)
                <div class="card" 
                     style="background-image: url('{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Drama' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Serie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #4caf50, #66bb6a); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);">
                            SERIE
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('series.show', $series->id) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Watched badge -->
                    <div style="position: absolute; top: 0.8rem; left: 0.8rem; z-index: 10;">
                        <span style="background: rgba(40, 167, 69, 0.9); color: white; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 600; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            ✅ Vista
                        </span>
                    </div>
                    
                    
                    
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        
                        <div class="card-streaming">
                            <div class="streaming-platforms">
                                @php
                                    $platforms = ['Netflix', 'Disney+', 'Prime', 'Viki'];
                                    $selectedPlatforms = array_rand(array_flip($platforms), rand(1, 2));
                                    if (!is_array($selectedPlatforms)) $selectedPlatforms = [$selectedPlatforms];
                                @endphp
                                @foreach($selectedPlatforms as $platform)
                                    <span class="streaming-platform {{ strtolower(str_replace('+', '', $platform)) }}">{{ $platform }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay de información al hacer click -->
                    <div class="card-info-overlay">
                        <div style="color: white; font-weight: bold;">
                            👎 {{ $series->dislike_count ?? 0 }} 
                            👍 {{ $series->like_count ?? 0 }} 
                            ❤️ {{ $series->love_count ?? 0 }}
                        </div>
                        <div style="color: white;">→</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===== PELÍCULAS POPULARES - VERSIÓN ACTUALIZADA 14/06/2025 ===== -->
    @if($popularMovies->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Películas</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($popularMovies as $movie)
                <div class="card movie-card" 
                     style="background-image: url('{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Movie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #ff6b9d, #ff8e8e); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);">
                            PELÍCULA
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('movies.show', $movie) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($movie->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($movie->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <div class="card-info">
                        <div class="card-title">{{ $movie->display_title ?: $movie->title }}</div>
                        <div class="card-meta">
                            @if($movie->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($movie->vote_average, 1) }}</span>
                            @endif
                            @if($movie->year)
                            <span class="card-year">{{ $movie->year }}</span>
                            @endif
                            @if($movie->formatted_runtime)
                            <span class="card-runtime">{{ $movie->formatted_runtime }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Películas Recientes -->
    @if($recentMovies->count() > 0)
    <section class="content-section">
        <h2 class="section-title">🆕 Últimas Películas</h2>
        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
                @foreach($recentMovies as $movie)
                <div class="card movie-card" 
                     style="background-image: url('{{ $movie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $movie->poster_path : 'https://via.placeholder.com/160x240/333/666?text=K-Movie' }}')">
                    <div class="card-overlay"></div>
                    
                    <!-- Movie badge - top left -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10;">
                        <span style="background: linear-gradient(135deg, #ff6b9d, #ff8e8e); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);">
                            PELÍCULA
                        </span>
                    </div>
                    
                    <!-- Ver button - bottom right -->
                    <div style="position: absolute; bottom: 0.6rem; right: 0.6rem; z-index: 10;">
                        <a href="{{ route('movies.show', $movie) }}" class="card-view-btn" title="Ver detalles" onclick="event.stopPropagation()">
                            Ver
                        </a>
                    </div>
                    
                    <!-- Category badges -->
                    @if($movie->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($movie->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <div class="card-info">
                        <div class="card-title">{{ $movie->display_title ?: $movie->title }}</div>
                        <div class="card-meta">
                            @if($movie->vote_average > 0)
                            <span class="card-rating">⭐ {{ number_format($movie->vote_average, 1) }}</span>
                            @endif
                            @if($movie->year)
                            <span class="card-year">{{ $movie->year }}</span>
                            @endif
                            @if($movie->formatted_runtime)
                            <span class="card-runtime">{{ $movie->formatted_runtime }}</span>
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

<script>
function slideCarousel(button, direction) {
    const container = button.parentElement;
    const carousel = container.querySelector('.carousel');
    const cards = carousel.querySelectorAll('.card, .news-card');
    
    if (cards.length === 0) return;
    
    // Determine card width based on card type and screen size
    let cardWidth;
    let gap = 8; // gap between cards
    const isMobile = window.innerWidth <= 768;
    
    if (carousel.querySelector('.news-card')) {
        cardWidth = isMobile ? 250 : 300; // news card width
    } else {
        cardWidth = isMobile ? 140 : 200; // regular card width (updated for vertical cards)
    }
    
    const totalCardWidth = cardWidth + gap;
    const containerWidth = carousel.parentElement.offsetWidth;
    const visibleCards = Math.max(1, Math.floor(containerWidth / totalCardWidth));
    
    let currentSlide = parseInt(carousel.getAttribute('data-current')) || 0;
    currentSlide += direction;
    
    // Prevent going out of bounds
    const maxSlide = Math.max(0, cards.length - visibleCards);
    if (currentSlide < 0) {
        currentSlide = maxSlide;
    } else if (currentSlide > maxSlide) {
        currentSlide = 0;
    }
    
    // Update the data attribute
    carousel.setAttribute('data-current', currentSlide);
    
    // Apply the transform with smooth transition
    const translateX = -(currentSlide * totalCardWidth);
    carousel.style.transform = `translateX(${translateX}px)`;
    
    // Los botones siempre están activos en carrusel infinito
    const prevBtn = container.querySelector('.prev');
    const nextBtn = container.querySelector('.next');
    
    if (prevBtn) prevBtn.style.opacity = '1';
    if (nextBtn) nextBtn.style.opacity = '1';
}

// Auto-slide for infinite carousel (opcional)
function initAutoSlide() {
    document.querySelectorAll('.carousel-container').forEach((container, index) => {
        setInterval(() => {
            const nextBtn = container.querySelector('.next');
            if (nextBtn && !container.matches(':hover')) {
                slideCarousel(nextBtn, 1);
            }
        }, 8000 + (index * 1000)); // Diferentes intervalos para cada carrusel
    });
}

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    // Netflix-style hover delay with mobile support
    let hoverTimer;
    let touchTimer;
    
    document.querySelectorAll('.card').forEach(card => {
        // Desktop hover
        card.addEventListener('mouseenter', function() {
            const thisCard = this;
            hoverTimer = setTimeout(() => {
                thisCard.classList.add('hovering');
            }, 500);
        });
        
        card.addEventListener('mouseleave', function() {
            clearTimeout(hoverTimer);
            this.classList.remove('hovering');
        });
        
        // Mobile touch support
        card.addEventListener('touchstart', function(e) {
            const thisCard = this;
            
            // Remove hovering from all other cards
            document.querySelectorAll('.card.hovering').forEach(otherCard => {
                if (otherCard !== thisCard) {
                    otherCard.classList.remove('hovering');
                }
            });
            
            touchTimer = setTimeout(() => {
                thisCard.classList.add('hovering');
            }, 300); // Faster for mobile
        });
        
        card.addEventListener('touchend', function(e) {
            clearTimeout(touchTimer);
        });
        
        // Click outside to remove hover on mobile
        document.addEventListener('touchstart', function(e) {
            if (!e.target.closest('.card')) {
                document.querySelectorAll('.card.hovering').forEach(card => {
                    card.classList.remove('hovering');
                });
            }
        });
    });
    
    document.querySelectorAll('.carousel-container').forEach(container => {
        const prevBtn = container.querySelector('.prev');
        const nextBtn = container.querySelector('.next');
        const carousel = container.querySelector('.carousel');
        
        if (prevBtn) prevBtn.style.opacity = '1';
        if (nextBtn) nextBtn.style.opacity = '1';
        
        // Reset carousel position
        if (carousel) {
            carousel.setAttribute('data-current', '0');
            carousel.style.transform = 'translateX(0px)';
        }
        
        // Agregar soporte para teclado
        container.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && prevBtn) {
                slideCarousel(prevBtn, -1);
            } else if (e.key === 'ArrowRight' && nextBtn) {
                slideCarousel(nextBtn, 1);
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        document.querySelectorAll('.carousel').forEach(carousel => {
            carousel.setAttribute('data-current', '0');
            carousel.style.transform = 'translateX(0px)';
        });
    });
    
    // Inicializar auto-slide (descomenta si quieres carrusel automático)
    // initAutoSlide();
    
    // Hero Rotation System
    @if(isset($heroSeriesList) && $heroSeriesList->count() > 1)
    @php
    $heroSeriesData = $heroSeriesList->map(function($series) {
        return [
            'id' => $series->id,
            'title' => $series->display_title,
            'overview' => Str::limit($series->display_overview ?: 'Descubre este increíble K-Drama y sumérgete en una historia única llena de emociones.', 280),
            'backdrop' => $series->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $series->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=K-Drama',
            'poster' => $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : 'https://via.placeholder.com/150x225/333/666?text=K-Drama',
            'rating' => $series->vote_average,
            'year' => $series->first_air_date ? $series->first_air_date->format('Y') : null,
            'episodes' => $series->number_of_episodes,
            'genres' => $series->genres->take(3)->map(function($g) { return $g->display_name ?: $g->name; })->toArray(),
            'route' => route('series.show', $series->id)
        ];
    })->toArray();
    @endphp
    const heroSeries = @json($heroSeriesData);
    
    let currentHeroIndex = 0;
    
    function rotateHero() {
        currentHeroIndex = (currentHeroIndex + 1) % heroSeries.length;
        const series = heroSeries[currentHeroIndex];
        
        // Fade out
        const heroSection = document.getElementById('heroSection');
        heroSection.style.transition = 'opacity 0.8s ease-in-out';
        heroSection.style.opacity = '0.7';
        
        setTimeout(() => {
            // Update hero content
            heroSection.style.backgroundImage = `url('${series.backdrop}')`;
            
            // Update poster
            const mobilePoster = heroSection.querySelector('.mobile-hero-poster');
            if (mobilePoster) {
                mobilePoster.src = series.poster;
                mobilePoster.alt = series.title;
            }
            
            // Update title
            const heroTitle = heroSection.querySelector('.hero-title');
            if (heroTitle) heroTitle.textContent = series.title;
            
            // Update description
            const heroDesc = heroSection.querySelector('.hero-description');
            if (heroDesc) heroDesc.textContent = series.overview;
            
            // Update button link
            const heroBtn = heroSection.querySelector('.btn-hero');
            if (heroBtn) heroBtn.href = series.route;
            
            // Update rating
            const ratingNumber = heroSection.querySelector('.rating-number');
            if (ratingNumber && series.rating && series.rating > 0) {
                const rating = parseFloat(series.rating);
                if (!isNaN(rating)) {
                    ratingNumber.textContent = rating.toFixed(1);
                }
            }
            
            // Update year
            const heroYear = heroSection.querySelector('.hero-year');
            if (heroYear && series.year) {
                heroYear.textContent = series.year;
            }
            
            // Update episodes
            const heroEpisodes = heroSection.querySelector('.hero-episodes');
            if (heroEpisodes && series.episodes) {
                heroEpisodes.textContent = series.episodes + ' episodios';
            }
            
            // Update genres
            const genresContainer = heroSection.querySelector('.hero-categories');
            if (genresContainer && series.genres.length > 0) {
                genresContainer.innerHTML = series.genres.map(genre => 
                    `<span class="hero-category">${genre || 'Drama'}</span>`
                ).join('');
            }
            
            // Fade in
            setTimeout(() => {
                heroSection.style.opacity = '1';
            }, 100);
        }, 800);
    }
    
    // Rotate hero every 15 seconds
    setInterval(rotateHero, 15000);
    @endif
});
</script>
</div>
@endsection