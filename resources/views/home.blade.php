@extends('layouts.app')

@section('title', 'Dorasia - Los Mejores K-Dramas')

@section('content')
<!-- Hero Section -->
@if($featuredSeries)
<section class="hero-section" style="background-image: url('{{ $featuredSeries->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $featuredSeries->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=K-Drama' }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ $featuredSeries->display_title }}</h1>
        <p class="hero-description">
            {{ Str::limit($featuredSeries->display_overview ?: 'Descubre este increíble K-Drama y sumérgete en una historia única llena de emociones.', 300) }}
        </p>
        <div class="hero-buttons">
            <a href="{{ route('series.show', $featuredSeries->id) }}" class="btn btn-primary">
                Ver Ahora
            </a>
            <a href="{{ route('series.show', $featuredSeries->id) }}" class="btn btn-secondary">
                Más Información
            </a>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
                    <a href="{{ route('series.show', $series->id) }}" class="card-action-btn" title="Ver detalles">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </a>
                    <div class="card-info">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        
                        @if($series->people->where('pivot.job', 'Acting')->count() > 0)
                        <div class="card-cast">
                            <div class="card-cast-title">Reparto Principal</div>
                            <div class="actor-images">
                                @foreach($series->people->where('pivot.job', 'Acting')->take(4) as $actor)
                                <div class="actor-image" 
                                     style="background-image: url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/w185' . $actor->profile_path : 'https://via.placeholder.com/20x20/444/fff?text=' . substr($actor->name, 0, 1) }}')"
                                     title="{{ $actor->name }}"></div>
                                @endforeach
                            </div>
                            {{ $series->people->where('pivot.job', 'Acting')->take(3)->pluck('name')->join(', ') }}
                        </div>
                        @endif
                        
                        <div class="card-streaming">
                            <div class="card-streaming-title">Disponible en</div>
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
    const cards = carousel.querySelectorAll('.card');
    const cardWidth = 160 + 16; // card width + gap
    const visibleCards = Math.floor(carousel.parentElement.offsetWidth / cardWidth);
    
    let currentSlide = parseInt(carousel.getAttribute('data-current')) || 0;
    currentSlide += direction;
    
    // Implementar carrusel infinito
    if (currentSlide < 0) {
        currentSlide = cards.length - visibleCards;
    } else if (currentSlide > cards.length - visibleCards) {
        currentSlide = 0;
    }
    
    // Update the data attribute
    carousel.setAttribute('data-current', currentSlide);
    
    // Apply the transform with smooth transition
    const translateX = -(currentSlide * cardWidth);
    carousel.style.transform = `translateX(${translateX}px)`;
    
    // Los botones siempre están activos en carrusel infinito
    const prevBtn = container.querySelector('.prev');
    const nextBtn = container.querySelector('.next');
    
    prevBtn.style.opacity = '1';
    nextBtn.style.opacity = '1';
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
        
        prevBtn.style.opacity = '1';
        nextBtn.style.opacity = '1';
        
        // Agregar soporte para teclado
        container.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                slideCarousel(prevBtn, -1);
            } else if (e.key === 'ArrowRight') {
                slideCarousel(nextBtn, 1);
            }
        });
    });
    
    // Inicializar auto-slide (descomenta si quieres carrusel automático)
    // initAutoSlide();
});
</script>
@endsection