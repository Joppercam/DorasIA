@extends('layouts.app')

@section('title', 'Dorasia - Los Mejores K-Dramas y Películas Coreanas Online')

@section('description', 'Descubre los mejores K-Dramas y películas coreanas con subtítulos en español. Ver series como Squid Game, Crash Landing on You, Vincenzo, Kingdom y más. La mejor plataforma de entretenimiento coreano.')

@section('keywords', 'k-dramas, dramas coreanos, películas coreanas, subtítulos español, entretenimiento coreano, series coreanas, kdrama, doramas, dorasia, squid game, crash landing on you, vincenzo, kingdom, descendientes del sol, goblin, hotel del luna')

@section('og_title', 'Dorasia - La Mejor Plataforma de K-Dramas y Películas Coreanas')
@section('og_description', 'Descubre miles de K-Dramas y películas coreanas con subtítulos en español. La plataforma definitiva para fans del entretenimiento coreano.')

@section('twitter_title', 'Dorasia - K-Dramas y Películas Coreanas')
@section('twitter_description', 'La mejor plataforma para descubrir K-Dramas y películas coreanas con subtítulos en español.')

@section('content')
<!-- Hero Slider Section - Nuevo Diseño -->
@if(isset($heroSeriesList) && $heroSeriesList->count() > 0)
<section class="hero-slider-container">
    <div class="hero-slider" id="heroSlider">
        @foreach($heroSeriesList as $index => $series)
        <div class="hero-slide {{ $loop->first ? 'active' : '' }}" 
             data-slide="{{ $loop->index }}"
             style="background-image: url('{{ $series->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $series->backdrop_path : '/images/no-backdrop.svg' }}')">
            
            <!-- Gradient Overlays -->
            <div class="hero-gradient-overlay"></div>
            <div class="hero-content-overlay"></div>
            
            <!-- Content Container -->
            <div class="hero-content-wrapper">
                <div class="container-fluid">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6 col-md-8">
                            <div class="hero-content animate-in">
                                <!-- Top Badge -->
                                <div class="hero-top-badge">
                                    <span class="badge-trending">🔥 Trending #{{ $loop->index + 1 }}</span>
                                    @if($series->genres->count() > 0)
                                        <span class="badge-genre">{{ $series->genres->first()->display_name ?: $series->genres->first()->name }}</span>
                                    @endif
                                </div>
                                
                                <!-- Title -->
                                <h1 class="hero-title-new">
                                    {{ $series->display_title }}
                                </h1>
                                
                                <!-- Meta Information -->
                                <div class="hero-meta-new">
                                    @if($series->vote_average > 0)
                                    <div class="meta-item-new">
                                        <span class="meta-icon">⭐</span>
                                        <span>{{ number_format($series->vote_average, 1) }}/10</span>
                                    </div>
                                    @endif
                                    
                                    @if($series->first_air_date)
                                    <div class="meta-item-new">
                                        <span class="meta-icon">📅</span>
                                        <span>{{ $series->first_air_date->format('Y') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($series->number_of_episodes)
                                    <div class="meta-item-new">
                                        <span class="meta-icon">📺</span>
                                        <span>{{ $series->number_of_episodes }} episodios</span>
                                    </div>
                                    @endif
                                    
                                    <div class="meta-item-new engagement">
                                        <span>👍 {{ number_format($series->like_count ?? 0) }}</span>
                                        <span>❤️ {{ number_format($series->love_count ?? 0) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <p class="hero-description-new">
                                    {{ Str::limit($series->display_overview ?: 'Descubre este increíble K-Drama lleno de emociones, romance y drama que te mantendrá pegado a la pantalla.', 200) }}
                                </p>
                                
                                <!-- Action Buttons -->
                                <div class="hero-actions">
                                    @if($series->trailer_youtube_id)
                                    <button class="btn-hero-trailer" onclick="playTrailer('{{ $series->trailer_youtube_id }}', '{{ addslashes($series->display_title) }}')">
                                        <div class="trailer-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <span class="trailer-text">Ver Trailer</span>
                                    </button>
                                    @endif
                                    
                                    <a href="{{ route('series.show', $series->id) }}" class="btn-hero-info">
                                        <div class="info-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M12 16v-4M12 8h.01"/>
                                            </svg>
                                        </div>
                                        <span class="info-text">Más Información</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Poster Side -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="hero-poster-side">
                                <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w500' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                                     alt="{{ $series->display_title }}" 
                                     class="hero-poster-img">
                                <div class="poster-reflection"></div>
                            </div>
                        </div>
                        
                        <!-- Mobile Poster -->
                        <div class="col-12 d-block d-lg-none">
                            <div class="hero-poster-mobile">
                                <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                                     alt="{{ $series->display_title }}" 
                                     class="hero-poster-mobile-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Navigation Controls -->
    <div class="hero-navigation">
        <button class="hero-nav-btn prev" onclick="heroSlider.prev()" aria-label="Anterior">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button class="hero-nav-btn next" onclick="heroSlider.next()" aria-label="Siguiente">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
    </div>
    
    <!-- Progress Bar -->
    <div class="hero-progress">
        <div class="progress-bar" id="heroProgress"></div>
    </div>
    
    <!-- Slide Indicators -->
    <div class="hero-dots">
        @foreach($heroSeriesList as $index => $series)
        <button class="dot {{ $loop->first ? 'active' : '' }}" 
                onclick="heroSlider.goTo({{ $loop->index }})" 
                aria-label="Ir a {{ $series->display_title }}">
            <span class="dot-progress"></span>
        </button>
        @endforeach
    </div>
</section>
@endif

<style>
/* Hero Slider Modern Design */
.hero-slider-container {
    position: relative;
    height: 100vh;
    overflow: hidden;
    padding-top: 80px; /* Add space for navigation menu */
}

.hero-slider {
    position: relative;
    height: 100%;
    width: 100%;
}

.hero-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 1s ease-in-out;
    z-index: 1;
}

.hero-slide.active {
    opacity: 1;
    z-index: 2;
}

.hero-gradient-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        45deg,
        rgba(0, 0, 0, 0.9) 0%,
        rgba(0, 0, 0, 0.7) 30%,
        rgba(0, 0, 0, 0.5) 50%,
        rgba(0, 0, 0, 0.3) 70%,
        rgba(0, 0, 0, 0.1) 100%
    );
}

.hero-content-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(0, 0, 0, 0.8) 0%,
        rgba(0, 0, 0, 0.2) 100%
    );
}

.hero-content-wrapper {
    position: relative;
    z-index: 10;
    height: 100%;
}

.hero-content {
    padding: 1rem 0;
    animation: slideInLeft 1s ease-out;
}

.hero-content.animate-in {
    animation: slideInLeft 1s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.hero-top-badge {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.badge-trending {
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.badge-genre {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.hero-title-new {
    font-size: 2.8rem;
    font-weight: 900;
    color: white;
    margin: 0 0 0.5rem 0;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.hero-meta-new {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.meta-item-new {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
    font-weight: 600;
}

.meta-item-new.engagement {
    gap: 0.8rem;
}

.meta-item-new.engagement span {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.meta-icon {
    font-size: 1rem;
}

.hero-description-new {
    max-width: 500px;
    margin-bottom: 1.5rem;
}

.hero-description-new p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    line-height: 1.5;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
}

.hero-actions {
    display: flex;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

/* New Hero Button Styles */
.btn-hero-trailer {
    background: linear-gradient(135deg, #e50914, #b20710);
    color: white;
    border: none;
    padding: 0.9rem 1.8rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(229, 9, 20, 0.4);
    position: relative;
    overflow: hidden;
}

.btn-hero-trailer:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-hero-trailer:hover:before {
    left: 100%;
}

.btn-hero-trailer:hover {
    background: linear-gradient(135deg, #f40612, #d00510);
    transform: translateY(-2px);
    box-shadow: 0 6px 30px rgba(229, 9, 20, 0.6);
    color: white;
    text-decoration: none;
}

.trailer-icon {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-hero-trailer:hover .trailer-icon {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.trailer-text {
    font-weight: 700;
    letter-spacing: 0.5px;
}

.btn-hero-info {
    background: rgba(42, 42, 42, 0.8);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 0.9rem 1.8rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    backdrop-filter: blur(15px);
    position: relative;
    overflow: hidden;
}

.btn-hero-info:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s;
}

.btn-hero-info:hover:before {
    left: 100%;
}

.btn-hero-info:hover {
    background: rgba(60, 60, 60, 0.9);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.info-icon {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-hero-info:hover .info-icon {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.info-text {
    font-weight: 600;
    letter-spacing: 0.3px;
}

.hero-poster-side {
    position: relative;
    max-width: 350px;
    margin: 0 auto;
}

.hero-poster-img {
    width: 100%;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
    transition: transform 0.3s ease;
}

.hero-poster-img:hover {
    transform: scale(1.05);
}

.poster-reflection {
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
    height: 50px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.3), transparent);
    border-radius: 0 0 15px 15px;
    filter: blur(5px);
}

/* Mobile Poster Styles */
.hero-poster-mobile {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.hero-poster-mobile-img {
    width: 120px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    transition: transform 0.3s ease;
}

.hero-poster-mobile-img:hover {
    transform: scale(1.05);
}

/* Navigation Controls */
.hero-navigation {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 2rem;
    z-index: 15;
    pointer-events: none;
}

.hero-nav-btn {
    background: rgba(0, 0, 0, 0.5);
    border: none;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto;
    backdrop-filter: blur(10px);
}

.hero-nav-btn:hover {
    background: rgba(0, 0, 0, 0.8);
    transform: scale(1.1);
}

.hero-nav-btn.prev {
    left: 2rem;
}

.hero-nav-btn.next {
    right: 2rem;
}

/* Progress Bar */
.hero-progress {
    position: absolute;
    bottom: 120px;
    left: 2rem;
    right: 2rem;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    z-index: 15;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #00d4ff, #0099cc);
    border-radius: 2px;
    width: 0%;
    transition: width 0.3s ease;
}

/* Slide Indicators */
.hero-dots {
    position: absolute;
    bottom: 60px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 1rem;
    z-index: 15;
}

.dot {
    width: 60px;
    height: 6px;
    background: rgba(255, 255, 255, 0.3);
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dot.active {
    background: rgba(255, 255, 255, 0.6);
}

.dot-progress {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, #00d4ff, #0099cc);
    border-radius: 3px;
    width: 0%;
    transition: width 7s linear;
}

.dot.active .dot-progress {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title-new {
        font-size: 3rem;
    }
    
    .hero-poster-side {
        max-width: 280px;
    }
}

@media (max-width: 768px) {
    .hero-slider-container {
        height: 100vh;
        min-height: 600px;
        padding-top: 80px; /* Space for navigation */
    }
    
    .hero-content-wrapper {
        display: flex;
        align-items: center; /* Center content vertically */
        padding-bottom: 120px; /* Space from bottom for navigation */
    }
    
    .hero-content {
        text-align: center;
        padding: 1rem;
        width: 100%;
    }
    
    .hero-poster-mobile {
        margin-bottom: 1.5rem;
    }
    
    .hero-poster-mobile-img {
        width: 100px;
    }
    
    .hero-title-new {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .hero-description-new {
        max-width: 100%;
        margin-bottom: 1rem;
    }
    
    .hero-description-new p {
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    .hero-meta-new {
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }
    
    .hero-actions {
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 1rem;
    }
    
    .btn-hero-trailer,
    .btn-hero-info {
        padding: 0.7rem 1.2rem;
        font-size: 0.9rem;
    }
    
    .trailer-icon,
    .info-icon {
        width: 28px;
        height: 28px;
    }
    
    .hero-navigation {
        padding: 0 1rem;
    }
    
    .hero-nav-btn {
        width: 45px;
        height: 45px;
    }
    
    .hero-progress {
        bottom: 80px;
        left: 1rem;
        right: 1rem;
    }
    
    .hero-dots {
        bottom: 40px;
        gap: 0.5rem;
    }
    
    .dot {
        width: 35px;
        height: 3px;
    }
}

@media (max-width: 480px) {
    .hero-slider-container {
        padding-top: 100px; /* More space for small screens */
    }
    
    .hero-content-wrapper {
        padding-bottom: 140px; /* More space for small screens */
    }
    
    .hero-content {
        padding: 1rem 0.8rem;
    }
    
    .hero-poster-mobile-img {
        width: 90px;
    }
    
    .hero-title-new {
        font-size: 1.6rem;
    }
    
    .hero-top-badge {
        justify-content: center;
        margin-bottom: 0.8rem;
    }
    
    .badge-trending,
    .badge-genre {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
    }
    
    .hero-description-new {
        margin-bottom: 0.8rem;
    }
    
    .hero-description-new p {
        font-size: 0.85rem;
    }
    
    .hero-meta-new {
        gap: 0.6rem;
        margin-bottom: 1rem;
        font-size: 0.75rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
    }
    
    .btn-hero-trailer,
    .btn-hero-info {
        width: 100%;
        max-width: 260px;
        justify-content: center;
        padding: 0.7rem 1rem;
        font-size: 0.85rem;
    }
    
    .trailer-icon,
    .info-icon {
        width: 26px;
        height: 26px;
    }
}
</style>

<script>
// Modern Hero Slider Implementation
class HeroSlider {
    constructor() {
        this.container = document.getElementById('heroSlider');
        this.slides = document.querySelectorAll('.hero-slide');
        this.dots = document.querySelectorAll('.dot');
        this.progressBar = document.getElementById('heroProgress');
        this.currentIndex = 0;
        this.isTransitioning = false;
        this.autoPlayInterval = null;
        this.progressInterval = null;
        this.autoPlayDelay = 8000; // 8 seconds
        
        this.init();
    }
    
    init() {
        if (this.slides.length <= 1) return;
        
        this.setupEventListeners();
        this.startAutoPlay();
        this.startProgress();
    }
    
    setupEventListeners() {
        // Pause auto-play on hover
        this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });
    }
    
    goTo(index) {
        if (index === this.currentIndex || this.isTransitioning) return;
        
        this.isTransitioning = true;
        
        // Update slides
        this.slides[this.currentIndex].classList.remove('active');
        this.slides[index].classList.add('active');
        
        // Update dots
        this.dots[this.currentIndex].classList.remove('active');
        this.dots[index].classList.add('active');
        
        // Reset progress for new slide
        this.resetProgress();
        
        this.currentIndex = index;
        
        // Reset transition flag after animation
        setTimeout(() => {
            this.isTransitioning = false;
        }, 1000);
        
        // Restart auto-play and progress
        this.startAutoPlay();
        this.startProgress();
    }
    
    next() {
        const nextIndex = (this.currentIndex + 1) % this.slides.length;
        this.goTo(nextIndex);
    }
    
    prev() {
        const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
        this.goTo(prevIndex);
    }
    
    startAutoPlay() {
        this.stopAutoPlay();
        this.autoPlayInterval = setInterval(() => {
            this.next();
        }, this.autoPlayDelay);
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }
    
    pauseAutoPlay() {
        this.stopAutoPlay();
        this.stopProgress();
    }
    
    startProgress() {
        this.stopProgress();
        
        // Reset progress bar
        this.progressBar.style.width = '0%';
        
        // Start progress animation
        setTimeout(() => {
            this.progressBar.style.width = '100%';
            this.progressBar.style.transition = `width ${this.autoPlayDelay}ms linear`;
        }, 100);
        
        // Start dot progress
        const currentDot = this.dots[this.currentIndex];
        if (currentDot) {
            const dotProgress = currentDot.querySelector('.dot-progress');
            if (dotProgress) {
                dotProgress.style.width = '0%';
                setTimeout(() => {
                    dotProgress.style.width = '100%';
                }, 100);
            }
        }
    }
    
    stopProgress() {
        if (this.progressBar) {
            this.progressBar.style.transition = 'width 0.3s ease';
            this.progressBar.style.width = '0%';
        }
        
        // Reset all dot progress
        this.dots.forEach(dot => {
            const dotProgress = dot.querySelector('.dot-progress');
            if (dotProgress) {
                dotProgress.style.width = '0%';
            }
        });
    }
    
    resetProgress() {
        this.stopProgress();
        setTimeout(() => {
            this.startProgress();
        }, 100);
    }
}

// Initialize hero slider when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.heroSlider = new HeroSlider();
});
</script>

<!-- Content Sections -->
<div style="margin-top: -100px; position: relative; z-index: 20;">
<div class="netflix-mobile-home"

    <!-- Categories -->
    <div class="mobile-categories">
        <div class="mobile-category-pills">
            <a href="{{ route('home') }}" class="category-pill {{ request()->routeIs('home') ? 'active' : '' }}">Series</a>
            <a href="{{ route('movies.index') }}" class="category-pill {{ request()->routeIs('movies.*') ? 'active' : '' }}">Películas</a>
            <a href="{{ route('actors.index') }}" class="category-pill {{ request()->routeIs('actors.*') ? 'active' : '' }}">Actores</a>
            @auth
            <a href="{{ route('profile.watchlist') }}" class="category-pill {{ request()->routeIs('profile.watchlist') ? 'active' : '' }}">Mi lista</a>
            @endauth
        </div>
    </div>

    <!-- Content Rows -->
    <div class="mobile-content-rows">

        <!-- Populares -->
        @if(isset($popularSeries) && $popularSeries && $popularSeries->count() > 0)
        <section class="mobile-row" data-type="popular">
            <h2 class="mobile-row-title">🔥 K-Dramas Populares</h2>
            <div class="mobile-row-content">
                @foreach($popularSeries->take(20) as $series)
                <div class="mobile-card" onclick="location.href='{{ route('series.show', $series->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                             alt="{{ $series->display_title }}" loading="lazy" onerror="this.src='/images/no-poster-series.svg'">
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mobile-card-ranking">{{ $loop->iteration }}</div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $series->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($series->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $series->vote_average > 0)
                            <span class="card-match">{{ number_format($series->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($series->genres)
                                @foreach($series->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($series->overview_es ?: $series->overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Mejor Calificadas -->
        @if(isset($topRatedSeries) && $topRatedSeries && $topRatedSeries->count() > 0)
        <section class="mobile-row" data-type="top_rated">
            <h2 class="mobile-row-title">⭐ Mejor Calificados</h2>
            <div class="mobile-row-content">
                @foreach($topRatedSeries->take(20) as $series)
                <div class="mobile-card" onclick="location.href='{{ route('series.show', $series->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                             alt="{{ $series->display_title }}" loading="lazy" onerror="this.src='/images/no-poster-series.svg'">
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $series->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($series->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $series->vote_average > 0)
                            <span class="card-match">{{ number_format($series->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($series->genres)
                                @foreach($series->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($series->overview_es ?: $series->overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Romance -->
        @if(isset($romanceSeries) && $romanceSeries && $romanceSeries->count() > 0)
        <section class="mobile-row" data-type="romance">
            <h2 class="mobile-row-title">💕 Romance Coreano</h2>
            <div class="mobile-row-content">
                @foreach($romanceSeries->take(20) as $series)
                <div class="mobile-card" onclick="location.href='{{ route('series.show', $series->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                             alt="{{ $series->display_title }}" loading="lazy" onerror="this.src='/images/no-poster-series.svg'">
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $series->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($series->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $series->vote_average > 0)
                            <span class="card-match">{{ number_format($series->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($series->genres)
                                @foreach($series->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($series->overview_es ?: $series->overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Drama -->
        @if(isset($dramasSeries) && $dramasSeries && $dramasSeries->count() > 0)
        <section class="mobile-row" data-type="drama">
            <h2 class="mobile-row-title">🎭 Drama Asiático</h2>
            <div class="mobile-row-content">
                @foreach($dramasSeries->take(20) as $series)
                <div class="mobile-card" onclick="location.href='{{ route('series.show', $series->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                             alt="{{ $series->display_title }}" loading="lazy" onerror="this.src='/images/no-poster-series.svg'">
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $series->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($series->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $series->vote_average > 0)
                            <span class="card-match">{{ number_format($series->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($series->genres)
                                @foreach($series->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($series->overview_es ?: $series->overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Próximamente -->
        @if(isset($upcomingSeries) && $upcomingSeries && $upcomingSeries->count() > 0)
        <section class="mobile-row" data-type="upcoming">
            <h2 class="mobile-row-title">🆕 Próximos K-Dramas</h2>
            <div class="mobile-row-content">
                @foreach($upcomingSeries->take(20) as $series)
                <div class="mobile-card" onclick="location.href='{{ route('series.show', $series->id) }}'">
                    <div class="mobile-card-image">
                        <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w342' . $series->poster_path : '/images/no-poster-series.svg' }}" 
                             alt="{{ $series->display_title }}" loading="lazy" onerror="this.src='/images/no-poster-series.svg'">
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-info">
                        <h3 class="mobile-card-title">{{ $series->display_title }}</h3>
                        <div class="mobile-card-meta">
                            @if($series->vote_average > 0)
                            <div class="card-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                            </div>
                            @endif
                            @if(auth()->check() && $series->vote_average > 0)
                            <span class="card-match">{{ number_format($series->vote_average * 10) }}% de coincidencia</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format('Y') }}</span>
                            @endif
                            <span class="card-maturity">16+</span>
                        </div>
                        <div class="mobile-card-genres">
                            @if($series->genres)
                                @foreach($series->genres->take(3) as $genre)
                                    <span class="card-genre">{{ $genre->display_name ?: $genre->name }}</span>@if(!$loop->last) • @endif
                                @endforeach
                            @endif
                        </div>
                        <p class="mobile-card-description">{{ Str::limit($series->overview_es ?: $series->overview, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Actores Destacados -->
        <section class="mobile-row" data-type="featured_actors">
            <h2 class="mobile-row-title">🎭 Estrellas K-Drama</h2>
            <div class="mobile-row-content">
                @php
                $featuredActors = \App\Models\Person::where('known_for_department', 'Acting')
                    ->whereNotNull('profile_path')
                    ->orderBy('popularity', 'desc')
                    ->limit(12)
                    ->get();
                @endphp
                
                @foreach($featuredActors as $actor)
                <div class="mobile-card" onclick="location.href='{{ route('actors.show', $actor->id) }}'">
                    <div class="mobile-card-image">
                        @if($actor->profile_path)
                        <img src="https://image.tmdb.org/t/p/w500{{ $actor->profile_path }}" alt="{{ $actor->name }}" loading="lazy" onerror="this.src='/images/no-actor-photo.svg'">
                        @else
                        <img src="/images/no-actor-photo.svg" alt="{{ $actor->name }}" loading="lazy">
                        @endif
                        <div class="mobile-card-overlay">
                            <div class="card-info-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-card-title">{{ $actor->display_name }}</div>
                    @if($actor->known_for_department)
                    <div class="mobile-card-year">{{ $actor->known_for_department === 'Acting' ? 'Actor' : $actor->known_for_department }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </section>



        <!-- Carrusel de Streaming Gratuito eliminado en esta versión -->

    </div>
</div>

<style>
/* Hero Section Styles */
.hero-section {
    position: relative;
    height: 90vh;
    min-height: 700px;
    background-size: cover;
    background-position: center center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    color: white;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        rgba(0,0,0,0.8) 0%,
        rgba(0,0,0,0.6) 40%,
        rgba(0,0,0,0.4) 70%,
        transparent 100%
    );
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    width: 100%;
}

.hero-info-box {
    max-width: 550px;
}

.mobile-hero-poster {
    width: 200px;
    height: auto;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.hero-categories {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.hero-category {
    background: rgba(255,255,255,0.2);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.1);
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.1;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.hero-meta {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.hero-rating {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.rating-stars {
    font-size: 1.2rem;
}

.rating-number {
    font-weight: 600;
    font-size: 1.1rem;
}

.hero-year, .hero-episodes {
    color: rgba(255,255,255,0.8);
    font-weight: 500;
}

.hero-engagement {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.hero-likes, .hero-loves {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-likes {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.3);
}

.hero-loves {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
}

.hero-description {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    color: rgba(255,255,255,0.9);
    text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-hero {
    padding: 0.8rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    backdrop-filter: blur(5px);
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    margin-right: 1rem;
}

.btn-play {
    background: rgba(255,255,255,0.95);
    color: #000;
}

.btn-play:hover {
    background: rgba(255,255,255,1);
    transform: scale(1.05);
    color: #000;
    text-decoration: none;
}

.btn-secondary {
    background: rgba(109, 109, 110, 0.7);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(109, 109, 110, 0.9);
    transform: scale(1.05);
    color: white;
    text-decoration: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .hero-section {
        height: 80vh;
        min-height: 600px;
        background-attachment: scroll;
    }
    
    .hero-content {
        padding: 0 1rem;
        display: flex;
        align-items: center;
        height: 100%;
    }
    
    .hero-info-box {
        text-align: center;
        max-width: 100%;
    }
    
    .hero-title {
        font-size: 2.2rem;
        margin-bottom: 0.8rem;
    }
    
    .hero-meta {
        justify-content: center;
        gap: 1rem;
    }
    
    .hero-description {
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
    
    .mobile-hero-poster {
        width: 150px;
        margin: 0 auto 1rem auto;
    }
    
    .hero-categories {
        justify-content: center;
    }
}

/* Hero Slider Controls */
.hero-controls {
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
    padding: 0 3rem;
    pointer-events: none;
    z-index: 15;
}

.hero-nav {
    background: rgba(0, 0, 0, 0.8);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    pointer-events: all;
    opacity: 0.9;
    backdrop-filter: blur(10px);
}

.hero-nav:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.6);
    opacity: 1;
    transform: scale(1.1);
}

.hero-nav:active {
    transform: scale(0.95);
}

/* Hero Indicators */
.hero-indicators {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 15;
}

.hero-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.7);
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.hero-indicator:hover {
    background: rgba(255, 255, 255, 0.5);
    transform: scale(1.2);
}

.hero-indicator.active {
    background: white;
    width: 30px;
    border-radius: 5px;
}

@media (max-width: 768px) {
    .hero-controls {
        padding: 0 0.5rem;
        top: 60%;
    }
    
    .hero-nav {
        width: 50px;
        height: 50px;
        opacity: 0.8;
        background: rgba(0, 0, 0, 0.7);
    }
    
    .hero-nav svg {
        width: 24px;
        height: 24px;
    }
    
    .btn-hero {
        padding: 0.7rem 1.5rem;
        font-size: 0.9rem;
        margin-right: 0.5rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        gap: 0.8rem;
        align-items: flex-start;
    }
    
    .hero-buttons .btn-hero {
        margin-right: 0;
    }
    
    .hero-indicators {
        bottom: 15px;
    }
    
    .hero-indicator {
        width: 6px;
        height: 6px;
        border-width: 1px;
    }
    
    .hero-indicator.active {
        width: 20px;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.8rem;
    }
    
    .hero-description {
        font-size: 0.9rem;
    }
    
    .btn-hero {
        padding: 0.7rem 1.5rem;
        font-size: 0.9rem;
    }
}

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

.mobile-play-btn {
    background: white;
    color: black;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
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
    width: 200px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.mobile-card:active {
    transform: scale(0.98);
}

.mobile-card-image {
    position: relative;
    width: 200px;
    height: 300px;
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

.card-info-btn {
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.8);
    border: 2px solid rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.card-info-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
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
    
    .mobile-play-btn,
    .mobile-info-btn {
        justify-content: center;
    }
    
    .mobile-card {
        width: 160px;
    }
    
    .mobile-card-image {
        width: 160px;
        height: 240px;
    }
}

/* === ACTOR CONTENT SECTIONS === */

/* Actor Content Sections */
.actor-content-section {
    margin-bottom: 2.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.exclusive-badge {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 212, 255, 0.3);
}

.register-prompt {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.register-prompt a {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 600;
}

.view-all-link {
    color: #00d4ff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.view-all-link:hover {
    text-decoration: underline;
}

/* Actor Content Row */
.actor-content-row {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    scroll-behavior: smooth;
}

.actor-content-row::-webkit-scrollbar {
    display: none;
}

.actor-content-card {
    flex-shrink: 0;
    width: 220px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.actor-content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 212, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
}

.actor-content-card.featured-content {
    border: 2px solid rgba(255, 215, 0, 0.3);
}

.actor-content-card.featured-content:hover {
    box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

/* Content type indicator */
.content-type-indicator {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    z-index: 2;
}

/* Content badges */
.content-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
    z-index: 2;
}

.content-badge.featured {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4a 100%);
    color: #000;
    border: 1px solid rgba(255, 215, 0, 0.5);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.content-badge.exclusive {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border: 1px solid rgba(0, 212, 255, 0.5);
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

/* Content Thumbnail */
.content-thumbnail {
    position: relative;
    width: 100%;
    height: 150px;
    overflow: hidden;
    background: #1a1a1a;
}

.content-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Cambiar a contain para mostrar la imagen completa */
    object-position: center;
}

.content-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #333 0%, #555 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.6);
}

.content-duration {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.content-lock {
    position: absolute;
    top: 6px;
    right: 6px;
    background: rgba(0, 0, 0, 0.8);
    color: #ffd700;
    padding: 0.3rem;
    border-radius: 50%;
    font-size: 0.8rem;
}

.content-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.actor-content-card:hover .content-overlay {
    opacity: 1;
}

.content-play-btn {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #333;
}

.featured-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #333;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 600;
}

.new-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    background: linear-gradient(135deg, #46d369 0%, #2ea54b 100%);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 600;
}

/* Content Info */
.content-info {
    padding: 1rem;
}

.content-type {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 0.5rem;
}

.content-title {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.content-actor {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.actor-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    object-fit: cover;
}

.actor-name {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.75rem;
    font-weight: 500;
}

.content-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
}

.content-stats {
    display: flex;
    gap: 0.5rem;
}

/* Actors with Content Section */
.actors-with-content-section .actors-content-row {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.actor-with-content-card {
    flex-shrink: 0;
    width: 180px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.actor-with-content-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 212, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
}

.actor-profile {
    position: relative;
    width: 100%;
    height: 140px;
    overflow: hidden;
}

.actor-profile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.actor-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.content-count-badge {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 600;
}

.actor-info {
    padding: 1rem;
}

.actor-info .actor-name {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.content-preview {
    margin-bottom: 0.5rem;
}

.content-preview-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-bottom: 0.3rem;
    font-size: 0.7rem;
}

.content-type-mini {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.1rem 0.4rem;
    border-radius: 8px;
    font-weight: 600;
    min-width: fit-content;
}

.content-title-mini {
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.2;
}

.exclusive-prompt {
    background: rgba(255, 215, 0, 0.1);
    color: #ffd700;
    padding: 0.3rem 0.6rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    border: 1px solid rgba(255, 215, 0, 0.3);
}

/* Mobile Responsive for Actor Content */
@media (max-width: 768px) {
    .actor-content-card {
        width: 180px;
    }
    
    .content-thumbnail {
        height: 110px;
    }
    
    .content-info {
        padding: 0.8rem;
    }
    
    .content-title {
        font-size: 0.8rem;
    }
    
    .actor-with-content-card {
        width: 150px;
    }
    
    .actor-profile {
        height: 120px;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .actor-content-card {
        width: 160px;
    }
    
    .content-thumbnail {
        height: 100px;
    }
    
    .actor-with-content-card {
        width: 130px;
    }
    
    .actor-profile {
        height: 100px;
    }
}

</style>

<!-- Trailer Modal -->
@include('components.trailer-modal')

<script>
// Trailer functionality
function playTrailer(youtubeId, title) {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    const trailerTitle = document.getElementById('trailerTitle');
    
    if (modal && iframe && trailerTitle) {
        trailerTitle.textContent = title;
        iframe.src = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&rel=0`;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

// Share functionality
function shareContent(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(console.error);
    } else {
        // Fallback to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('¡Enlace copiado al portapapeles!');
        }).catch(() => {
            alert('No se pudo copiar el enlace');
        });
    }
}
</script>
@endsection