@extends('layouts.app')

@section('title', $series->title . ' - Ver K-Drama Online | Dorasia')

@section('description', 'Ver ' . $series->title . ' online con subtítulos en español. ' . Str::limit($series->display_overview ?? 'K-Drama coreano disponible en Dorasia.', 140) . ' Reparto, temporadas, episodios y más.')

@section('keywords', 'ver ' . strtolower($series->title) . ' online, ' . strtolower($series->title) . ' subtítulos español, k-drama ' . strtolower($series->title) . ', drama coreano, dorasia, ' . ($series->genres ? $series->genres->pluck('name')->map(fn($g) => strtolower($g))->implode(', ') : 'drama, romance'))

@section('og_title', $series->title . ' - K-Drama Coreano | Dorasia')
@section('og_description', 'Descubre ' . $series->title . '. ' . Str::limit($series->display_overview ?? 'K-Drama coreano con subtítulos en español.', 150))
@section('og_image', $series->poster_path ? $series->posterUrl('w500') : '/og-image.png')
@section('og_type', 'video.tv_show')

@section('twitter_title', $series->title . ' - K-Drama | Dorasia')
@section('twitter_description', Str::limit($series->display_overview ?? 'K-Drama coreano con subtítulos en español.', 150))

@section('content')
<!-- Hero Section Rediseñado -->
@if($series->original_backdrop_url)
<section class="hero-section-new" style="background-image: url('{{ $series->original_backdrop_url }}')"
@else
<section class="hero-section-new no-backdrop"
@endif>
    <div class="hero-overlay-new"></div>
    <div class="container">
        <div class="hero-container-new">
            <!-- Contenido Principal -->
            <div class="hero-main-content">
                <!-- Rating Buttons -->
                <div class="rating-actions-top">
                    @include('components.rating-buttons', ['series' => $series])
                </div>
                
                <!-- Título y metadata -->
                <div class="hero-title-section">
                    <h1 class="hero-title-new">{{ $series->title }}</h1>
                    @if($series->original_title && $series->original_title !== $series->title)
                    <p class="hero-original-title-new">{{ $series->original_title }}</p>
                    @endif
                    
                    <!-- Meta información -->
                    <div class="hero-meta-new">
                        @if($series->vote_average > 0)
                        <span class="meta-item rating">
                            ⭐ {{ number_format($series->vote_average, 1) }}
                        </span>
                        @endif
                        @if($series->first_air_date)
                        <span class="meta-item">{{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}</span>
                        @endif
                        @if($series->number_of_seasons)
                        <span class="meta-item">{{ $series->number_of_seasons }} temporadas</span>
                        @endif
                        @if($series->number_of_episodes)
                        <span class="meta-item">{{ $series->number_of_episodes }} episodios</span>
                        @endif
                    </div>
                </div>
                
                <!-- Descripción -->
                @if($series->display_overview)
                <div class="hero-description-new">
                    <p>{{ $series->display_overview }}</p>
                </div>
                @endif
                
                
                <!-- Acciones principales -->
                <div class="hero-actions-new">
                    @if($series->hasTrailer())
                    <button class="btn-primary-new trailer-btn" onclick="playTrailer('{{ $series->trailer_youtube_id }}', '{{ addslashes($series->display_title) }}')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Ver Trailer
                        @if($series->has_spanish_trailer)
                        <span class="spanish-trailer-badge">🇪🇸 ESP</span>
                        @else
                        <span class="subtitle-trailer-badge">🔤 SUB</span>
                        @endif
                    </button>
                    @endif
                    
                </div>
                
            </div>
            
            <!-- Poster lateral -->
            <div class="hero-poster-side">
                @if($series->poster_path)
                <img src="{{ $series->detail_poster_url }}" 
                     alt="{{ $series->display_title }}"
                     class="poster-image-new">
                @else
                <div class="poster-placeholder-new">📺</div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Player de Streaming eliminado en esta versión -->

<!-- Información Detallada -->
<div class="series-info-section" id="info">
    <div class="container">
        <div class="info-grid">
            <!-- Detalles -->
            <div class="info-card">
                <h3 class="info-card-title">ℹ️ Detalles</h3>
                <div class="details-grid-new">
                    @if($series->first_air_date)
                    <div class="detail-item-new">
                        <span class="detail-label-new">Estreno:</span>
                        <span class="detail-value-new">{{ \Carbon\Carbon::parse($series->first_air_date)->format('d M Y') }}</span>
                    </div>
                    @endif
                    
                    @if($series->status)
                    <div class="detail-item-new">
                        <span class="detail-label-new">Estado:</span>
                        <span class="detail-value-new">{{ $series->status === 'Ended' ? 'Finalizada' : 'En emisión' }}</span>
                    </div>
                    @endif
                    
                    @if($series->original_language)
                    <div class="detail-item-new">
                        <span class="detail-label-new">Idioma:</span>
                        <span class="detail-value-new">{{ $series->original_language === 'ko' ? 'Coreano' : strtoupper($series->original_language) }}</span>
                    </div>
                    @endif
                    
                    @if($series->vote_count > 0)
                    <div class="detail-item-new">
                        <span class="detail-label-new">Votos:</span>
                        <span class="detail-value-new">{{ number_format($series->vote_count) }}</span>
                    </div>
                    @endif
                    
                    <!-- Categorías -->
                    @if($series->genres && $series->genres->count() > 0)
                    <div class="detail-item-new">
                        <span class="detail-label-new">Categorías:</span>
                        <span class="detail-value-new">
                            <div class="detail-categories">
                                @foreach($series->genres as $genre)
                                <span class="detail-category-tag">{{ $genre->display_name ?: $genre->name }}</span>
                                @endforeach
                            </div>
                        </span>
                    </div>
                    @endif
                    
                    <!-- Disponible en -->
                    <div class="detail-item-new">
                        <span class="detail-label-new">Disponible en:</span>
                        <span class="detail-value-new">
                            <div class="detail-availability">
                                @if($series->netflix_available)
                                <div class="availability-item">
                                    <span class="platform-name">🔴 Netflix</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->disney_available)
                                <div class="availability-item">
                                    <span class="platform-name">🏰 Disney+</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->amazon_available)
                                <div class="availability-item">
                                    <span class="platform-name">📦 Prime Video</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->apple_available)
                                <div class="availability-item">
                                    <span class="platform-name">🍎 Apple TV+</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->hbo_available)
                                <div class="availability-item">
                                    <span class="platform-name">🎭 HBO Max</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->crunchyroll_available)
                                <div class="availability-item">
                                    <span class="platform-name">🍊 Crunchyroll</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->viki_available)
                                <div class="availability-item">
                                    <span class="platform-name">💜 Viki</span>
                                    <span class="platform-status available">Disponible</span>
                                </div>
                                @endif
                                
                                @if($series->network)
                                <div class="availability-item">
                                    <span class="platform-name">📺 {{ $series->network }}</span>
                                    <span class="platform-status available">Original</span>
                                </div>
                                @endif
                                
                                @if(!$series->netflix_available && !$series->disney_available && !$series->amazon_available && !$series->apple_available && !$series->hbo_available && !$series->crunchyroll_available && !$series->viki_available)
                                <div class="availability-item">
                                    <span class="platform-name">🔍 Información no disponible</span>
                                    <span class="platform-status unavailable">Consultar</span>
                                </div>
                                @endif
                            </div>
                        </span>
                    </div>
                </div>
                
            </div>

            <!-- Reparto -->
            @include('components.mobile-cast-accordion', ['series' => $series])

            <!-- Temporadas y Episodios -->
            @include('components.mobile-seasons-accordion', ['series' => $series])
            
            <!-- Banda Sonora -->
            @include('components.simple-soundtrack-list', ['series' => $series])
            
            <!-- Reviews and Comments Accordion -->
            @include('components.mobile-reviews-accordion', ['series' => $series])
        </div>
    </div>
</div>

<style>
/* Hero Section Nuevo */
.hero-section-new {
    min-height: 80vh;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
    display: flex;
    align-items: center;
    padding: 100px 0 60px;
}

.hero-overlay-new {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.9) 100%);
}

.hero-container-new {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 4rem;
    position: relative;
    z-index: 10;
    align-items: start;
}

.hero-main-content {
    max-width: 100%;
}

.rating-actions-top {
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
    background: rgba(0, 0, 0, 0.8);
    padding: 1rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.rating-actions-top .card-rating-buttons {
    display: flex !important;
    gap: 1rem !important;
}

.rating-actions-top button {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    border: none !important;
    color: white !important;
    font-size: 1.4rem !important;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.rating-actions-top button:hover {
    transform: scale(1.1);
}

.hero-title-new {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin: 0 0 0.5rem 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);
    line-height: 1.1;
}

.hero-original-title-new {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.8);
    font-style: italic;
    margin-bottom: 1rem;
}

.hero-meta-new {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.meta-item {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.meta-item.rating {
    background: rgba(255, 215, 0, 0.2);
    border: 1px solid rgba(255, 215, 0, 0.5);
}

.hero-description-new {
    background: rgba(0, 0, 0, 0.7);
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

.hero-description-new p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 0;
}

/* Read more button styles removed - showing full text now */

.hero-actions-new {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.btn-primary-new {
    background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
    border: none;
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(229, 9, 20, 0.3);
}

.btn-primary-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(229, 9, 20, 0.4);
}

.btn-secondary-new {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-secondary-new:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
}

.hero-poster-side {
    position: sticky;
    top: 120px;
}

.poster-image-new {
    width: 100%;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    transition: transform 0.3s ease;
}

.poster-image-new:hover {
    transform: scale(1.05);
}

.poster-placeholder-new {
    width: 100%;
    height: 450px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.5);
}

/* Info Section */
.series-info-section {
    background: #0a0a0a;
    padding: 4rem 0;
    margin-top: -2rem;
    position: relative;
    z-index: 5;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-card-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cast-grid-new {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.cast-grid-new {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.cast-member-new {
    text-align: center;
    transition: transform 0.3s ease;
    display: block;
    text-decoration: none;
    color: inherit;
}

.cast-member-new:hover {
    transform: translateY(-5px);
    text-decoration: none;
}

.cast-link {
    position: relative;
    cursor: pointer;
}

.cast-link:hover .cast-photo-new {
    border-color: rgba(0, 212, 255, 0.8);
    transform: scale(1.05);
}

.cast-link:hover .cast-photo-placeholder-new {
    border-color: rgba(0, 212, 255, 0.8);
    background: rgba(0, 212, 255, 0.1);
    transform: scale(1.05);
}

.cast-link:hover .cast-name-new {
    color: #00d4ff;
}

.cast-photo-new {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(0, 212, 255, 0.3);
    margin: 0 auto 0.8rem;
    display: block;
    transition: all 0.3s ease;
}

.cast-photo-placeholder-new {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: rgba(255,255,255,0.4);
    margin: 0 auto 0.8rem;
    border: 3px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}

.cast-info-new {
    text-align: center;
}

.cast-name-new {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.3rem;
    line-height: 1.2;
    transition: color 0.3s ease;
}

.cast-character-new {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
    font-style: italic;
    line-height: 1.2;
}


.details-grid-new {
    display: grid;
    gap: 1rem;
}

.detail-item-new {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-label-new {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 600;
}

.detail-value-new {
    color: white;
    font-weight: 500;
}

/* Details Accordion Integration */
.details-accordion-container {
    width: 100%;
    margin-top: 1.5rem;
}

.details-accordion-container .details-accordion {
    background: transparent;
    border: none;
    border-radius: 0;
    margin-top: 0;
}

.details-accordion-container .accordion-header {
    padding: 1rem 0;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.details-accordion-container .accordion-header:hover {
    background: rgba(255,255,255,0.1);
}

.details-accordion-container .accordion-content {
    padding: 1rem 0 0 0;
}

.details-accordion-container .accordion-section {
    margin-bottom: 1rem;
}

.details-accordion-container .accordion-section:last-child {
    margin-bottom: 0;
}

/* Additional Styles for New Sections */
.section-subtitle {
    color: rgba(0, 212, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 600;
    background: rgba(0, 212, 255, 0.1);
    padding: 0.2rem 0.8rem;
    border-radius: 15px;
    margin-left: 1rem;
    border: 1px solid rgba(0, 212, 255, 0.2);
}

.section-description {
    color: rgba(255,255,255,0.7);
    font-size: 0.95rem;
    margin: 1rem 0 1.5rem 0;
    line-height: 1.5;
}

.reviews-section {
    margin-top: 1rem;
}

.review-item {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 1.2rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.review-item:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(0, 212, 255, 0.3);
}

.review-source {
    color: #00d4ff;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.review-excerpt {
    color: rgba(255,255,255,0.9);
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 0.8rem;
    font-style: italic;
}

.review-rating {
    color: #ffd700;
    font-size: 0.9rem;
}

.comments-section {
    margin-top: 1rem;
}

.no-comments {
    text-align: center;
    padding: 3rem 1rem;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    border: 2px dashed rgba(255,255,255,0.1);
}

.no-comments-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.no-comments p {
    color: rgba(255,255,255,0.7);
    font-size: 1rem;
    margin: 0.5rem 0;
}

.no-comments-sub {
    color: rgba(255,255,255,0.5);
    font-size: 0.9rem;
}

.btn-comment-first, .btn-comment-login {
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    margin-top: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-comment-first:hover, .btn-comment-login:hover {
    background: linear-gradient(135deg, #0099cc, #0077aa);
    transform: translateY(-2px);
}

/* Professional Reviews Styles */
.review-source a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.review-source a:hover {
    color: #66e0ff;
    text-decoration: underline;
}

.review-author {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
    font-style: italic;
}

.rating-number {
    margin-left: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
}

.show-more-reviews {
    text-align: center;
    margin-top: 1.5rem;
}

.btn-show-more {
    background: transparent;
    color: #00d4ff;
    border: 1px solid #00d4ff;
    padding: 0.6rem 1.5rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-show-more:hover {
    background: #00d4ff;
    color: #000;
    transform: translateY(-2px);
}


/* Responsive */
@media (max-width: 768px) {
    .hero-container-new {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .hero-poster-side {
        order: -1;
        position: static;
        max-width: 200px;
        margin: 0 auto;
    }
    
    .hero-title-new {
        font-size: 2.5rem;
    }
    
    .rating-actions-top {
        justify-content: center;
    }
    
    .rating-actions-top button {
        width: 50px !important;
        height: 50px !important;
        font-size: 1.2rem !important;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .cast-grid-new {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }
    
    .cast-photo-new, .cast-photo-placeholder-new {
        width: 80px;
        height: 80px;
    }
}

/* Categorías y Disponibilidad en Detalles */
.detail-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.detail-category-tag {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
    font-weight: 500;
}

.detail-availability {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.detail-availability .availability-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.8rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-availability .platform-name {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
}

.detail-availability .platform-status {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

.detail-availability .platform-status.available {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.detail-availability .platform-status.unavailable {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

@media (max-width: 768px) {
    .detail-categories {
        gap: 0.3rem;
    }
    
    .detail-category-tag {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
    }
    
    .detail-availability .availability-item {
        padding: 0.4rem 0.6rem;
    }
    
    .detail-availability .platform-name {
        font-size: 0.8rem;
    }
    
    .detail-availability .platform-status {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
    }
}

/* Spanish trailer badges */
.spanish-trailer-badge {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.subtitle-trailer-badge {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
    border: 1px solid rgba(59, 130, 246, 0.3);
}
</style>

<script>
// JavaScript for series page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Series page JavaScript loaded');
});

// Function to toggle details accordion - moved from component
function toggleDetailsAccordion(button) {
    const accordion = button.closest('.details-accordion');
    const content = accordion.querySelector('.accordion-content');
    const isOpen = content.style.display !== 'none';
    
    if (isOpen) {
        content.style.display = 'none';
        button.classList.remove('active');
    } else {
        content.style.display = 'block';
        button.classList.add('active');
    }
}
</script>

@include('components.trailer-modal')

<!-- JSON-LD Datos Estructurados para la Serie -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "TVSeries",
    "name": "{{ $series->title }}",
    @if($series->original_title && $series->original_title !== $series->title)
    "alternateName": "{{ $series->original_title }}",
    @endif
    "description": "{{ Str::limit($series->display_overview ?? 'K-Drama coreano disponible en Dorasia con subtítulos en español.', 300) }}",
    @if($series->poster_path)
    "image": "{{ $series->posterUrl('w500') }}",
    @endif
    @if($series->first_air_date)
    "datePublished": "{{ $series->first_air_date->format('Y-m-d') }}",
    @endif
    @if($series->number_of_seasons)
    "numberOfSeasons": {{ $series->number_of_seasons }},
    @endif
    @if($series->number_of_episodes)
    "numberOfEpisodes": {{ $series->number_of_episodes }},
    @endif
    @if($series->vote_average > 0)
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ $series->vote_average }}",
        "bestRating": "10",
        "worstRating": "1"
        @if($series->vote_count > 0)
        ,"ratingCount": {{ $series->vote_count }}
        @endif
    },
    @endif
    @if($series->genres && $series->genres->count() > 0)
    "genre": [
        @foreach($series->genres as $genre)
        "{{ $genre->name }}"@if(!$loop->last),@endif
        @endforeach
    ],
    @endif
    "inLanguage": "ko",
    "subtitleLanguage": "es",
    "countryOfOrigin": {
        "@type": "Country",
        "name": "Corea del Sur"
    },
    @if($series->actors && $series->actors->count() > 0)
    "actor": [
        @foreach($series->actors->take(5) as $actor)
        {
            "@type": "Person",
            "name": "{{ $actor->name }}"
        }@if(!$loop->last),@endif
        @endforeach
    ],
    @endif
    "url": "{{ url()->current() }}",
    "publisher": {
        "@type": "Organization",
        "name": "Dorasia",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ url('/icons/icon-192x192.png') }}"
        }
    }
}
</script>

@endsection