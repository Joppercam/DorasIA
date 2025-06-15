@extends('layouts.app')

@section('title', $upcomingSeries->display_title . ' - Próximamente - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background-image: url('{{ $upcomingSeries->backdrop_url }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            <!-- Poster para móvil -->
            <img src="{{ $upcomingSeries->poster_url }}" 
                 alt="{{ $upcomingSeries->display_title }}" 
                 class="mobile-hero-poster d-block d-md-none">
            
            <!-- Type Badge -->
            <div class="hero-categories">
                <span class="hero-category">{{ $upcomingSeries->formatted_type }}</span>
                @if($upcomingSeries->days_until_release !== null)
                <span class="hero-category">
                    @if($upcomingSeries->days_until_release === 0)
                        ¡Estreno Hoy!
                    @elseif($upcomingSeries->days_until_release === 1)
                        Estreno Mañana
                    @else
                        {{ $upcomingSeries->days_until_release }} días restantes
                    @endif
                </span>
                @endif
            </div>
            
            <h1 class="hero-title">{{ $upcomingSeries->display_title }}</h1>
            
            <!-- Release Date and Meta -->
            <div class="hero-meta">
                <span class="hero-year">📅 {{ $upcomingSeries->formatted_release_date }}</span>
                @if($upcomingSeries->vote_average > 0)
                <span class="hero-rating">⭐ {{ number_format($upcomingSeries->vote_average, 1) }}</span>
                @endif
                @if($upcomingSeries->episode_count)
                <span class="hero-episodes">{{ $upcomingSeries->episode_count }} episodios</span>
                @endif
            </div>
            
            @if($upcomingSeries->display_overview)
            <p class="hero-description">{{ $upcomingSeries->display_overview }}</p>
            @endif
            
            <div class="hero-buttons">
                @if($upcomingSeries->isNewSeason() && $existingSeries)
                <a href="{{ route('series.show', $existingSeries->id) }}" class="btn btn-hero">
                    Ver Serie Original
                </a>
                @endif
                <a href="{{ route('upcoming.index') }}" class="btn btn-secondary">
                    Ver Más Próximos Estrenos
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Series Information -->
<div style="margin-top: -100px; position: relative; z-index: 20;" id="info">
    
    <!-- Main Info -->
    <section class="content-section">
        <div class="series-detail-container">
            <div class="series-poster">
                <img src="{{ $upcomingSeries->poster_url }}" 
                     alt="{{ $upcomingSeries->display_title }}"
                     class="detail-poster-img">
            </div>
            
            <div class="series-info">
                <!-- Release Info -->
                <div class="detail-section">
                    <h3 class="detail-section-title">Información de Estreno</h3>
                    <div class="detail-genres">
                        <span class="detail-genre-tag">{{ $upcomingSeries->formatted_type }}</span>
                        @if($upcomingSeries->season_number && $upcomingSeries->isNewSeason())
                        <span class="detail-genre-tag">Temporada {{ $upcomingSeries->season_number }}</span>
                        @endif
                    </div>
                </div>
                
                <!-- Enhanced Series Details -->
                <div class="series-details-modern">
                    <div class="details-cards-grid">
                        <div class="detail-card premiere-card">
                            <div class="detail-icon">📅</div>
                            <div class="detail-content">
                                <span class="detail-label">Fecha de Estreno</span>
                                <span class="detail-value">{{ $upcomingSeries->formatted_release_date }}</span>
                            </div>
                        </div>
                        
                        @if($upcomingSeries->days_until_release !== null)
                        <div class="detail-card status-card">
                            <div class="detail-icon">⏰</div>
                            <div class="detail-content">
                                <span class="detail-label">Tiempo Restante</span>
                                <span class="detail-value">
                                    @if($upcomingSeries->days_until_release === 0)
                                        ¡Estreno Hoy!
                                    @elseif($upcomingSeries->days_until_release === 1)
                                        Mañana
                                    @else
                                        {{ $upcomingSeries->days_until_release }} días
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        @if($upcomingSeries->episode_count)
                        <div class="detail-card episodes-card">
                            <div class="detail-icon">📺</div>
                            <div class="detail-content">
                                <span class="detail-label">Episodios</span>
                                <span class="detail-value">{{ $upcomingSeries->episode_count }} episodios</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($upcomingSeries->vote_average > 0)
                        <div class="detail-card rating-card">
                            <div class="detail-icon">⭐</div>
                            <div class="detail-content">
                                <span class="detail-label">Calificación Esperada</span>
                                <span class="detail-value">{{ number_format($upcomingSeries->vote_average, 1) }}/10</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Serie Original (si es nueva temporada) -->
    @if($upcomingSeries->isNewSeason() && $existingSeries)
    <section class="content-section">
        <h2 class="section-title">🔄 Serie Original</h2>
        <div class="series-detail-container">
            <div class="series-poster">
                @if($existingSeries->poster_path)
                <img src="https://image.tmdb.org/t/p/w500{{ $existingSeries->poster_path }}" 
                     alt="{{ $existingSeries->display_title }}"
                     class="detail-poster-img">
                @endif
            </div>
            
            <div class="series-info">
                <h3>{{ $existingSeries->display_title }}</h3>
                @if($existingSeries->display_overview)
                <p style="color: rgba(255,255,255,0.9); line-height: 1.6; margin: 1rem 0;">
                    {{ Str::limit($existingSeries->display_overview, 300) }}
                </p>
                @endif
                
                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('series.show', $existingSeries->id) }}" class="btn btn-hero">
                        Ver Serie Completa
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Más Próximos Estrenos -->
    @if(isset($relatedUpcoming) && $relatedUpcoming->count() > 0)
    <section class="content-section">
        <h2 class="section-title">📅 Más Próximos Estrenos</h2>
        <div class="carousel-container">
            <div class="carousel" data-current="0">
                @foreach($relatedUpcoming as $item)
                <div class="card" 
                     style="background-image: url('{{ $item->poster_url }}')">
                    <div class="card-overlay"></div>
                    
                    <a href="{{ route('upcoming.show', $item->id) }}" class="card-view-btn">
                        Ver
                    </a>
                    
                    <div class="card-info">
                        <div class="card-title">{{ $item->display_title }}</div>
                        <div class="card-meta">
                            <span class="card-date">{{ $item->formatted_release_date }}</span>
                            <span class="card-new">{{ $item->formatted_type }}</span>
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
.detail-genre-tag {
    display: inline-block;
    background: rgba(0, 212, 255, 0.9);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.card-date {
    color: #ffc107;
    font-size: 0.7rem;
}

.card-new {
    color: #4caf50;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    /* Hero Section Mobile */
    .hero-section {
        min-height: 60vh !important;
    }
    
    .hero-info-box {
        max-width: 100% !important;
        padding: 1.5rem !important;
        margin: 0 1rem !important;
    }
    
    .hero-title {
        font-size: 1.8rem !important;
        line-height: 1.2 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .hero-meta {
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
        justify-content: center !important;
        margin-bottom: 1rem !important;
    }
    
    .hero-description {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
        text-align: center !important;
    }
    
    /* Series Detail Container Mobile */
    .series-detail-container {
        display: block !important;
        gap: 1.5rem !important;
    }
    
    .series-poster {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .detail-poster-img {
        max-width: 200px !important;
        height: auto !important;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    }
    
    /* Details Cards Grid Mobile */
    .details-cards-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .detail-card {
        padding: 1rem !important;
    }
    
    .detail-icon {
        font-size: 1.2rem !important;
    }
    
    .detail-label {
        font-size: 0.8rem !important;
    }
    
    .detail-value {
        font-size: 0.9rem !important;
    }
    
    /* Hero buttons mobile */
    .hero-buttons {
        flex-direction: column !important;
        gap: 0.8rem !important;
        width: 100% !important;
    }
    
    .btn {
        width: 100% !important;
        text-align: center !important;
    }
    
    /* Section titles mobile */
    .section-title {
        font-size: 1.3rem !important;
        margin-bottom: 1rem !important;
    }
    
    /* Content sections mobile */
    .content-section {
        padding: 1rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    /* Carousel mobile optimization */
    .carousel-container {
        margin: 0 -1rem !important;
        padding: 0 1rem !important;
    }
    
    .card {
        min-width: 150px !important;
        aspect-ratio: 2/3 !important;
    }
    
    .card-title {
        font-size: 0.8rem !important;
        line-height: 1.3 !important;
    }
    
    .card-meta {
        gap: 0.3rem !important;
    }
    
    .card-date,
    .card-new {
        font-size: 0.65rem !important;
    }
    
    /* Mobile hero categories */
    .hero-categories {
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .hero-category {
        font-size: 0.7rem !important;
        padding: 0.3rem 0.6rem !important;
    }
    
    /* Mobile negative margin adjustment */
    #info {
        margin-top: -50px !important;
    }
}
</style>
@endsection