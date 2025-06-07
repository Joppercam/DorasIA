@extends('layouts.app')

@section('title', 'Dorasia - Los Mejores K-Dramas')

@section('content')
<!-- Hero Section -->
@if($featuredSeries)
<section class="hero-section" style="background-image: url('{{ $featuredSeries->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $featuredSeries->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=K-Drama' }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-info-box">
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
                    Más Información
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Content Sections -->
<div style="margin-top: -100px; position: relative; z-index: 20;">


    <!-- Series Populares -->
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
                    @if($series->genres->count() > 0)
                    <div class="card-categories">
                        @foreach($series->genres->take(2) as $genre)
                            <span class="card-category {{ strtolower(str_replace([' ', '&'], ['', ''], $genre->display_name ?: $genre->name)) }}">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
                    
                    <!-- Category badges at the top -->
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
                    
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                    @include('components.series-stats', ['series' => $series])
                    
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
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
    let gap = 16; // gap between cards
    const isMobile = window.innerWidth <= 768;
    
    if (carousel.querySelector('.news-card')) {
        cardWidth = isMobile ? 250 : 300; // news card width
    } else {
        cardWidth = isMobile ? 140 : 220; // regular card width
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
});
</script>
@endsection