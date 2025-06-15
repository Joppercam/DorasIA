@if($upcoming->count() > 0)
<section class="content-section">
    <h2 class="section-title proximamente-title">Próximamente</h2>
    <div class="carousel-container">
        <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
        <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
        <div class="carousel" data-current="0">
            @foreach($upcoming as $item)
            <div class="card upcoming-card" 
                 style="background-image: url('{{ $item->poster_url }}')">
                <div class="card-overlay"></div>
                
                <!-- Ver button -->
                <a href="{{ route('upcoming.show', $item->id) }}" class="card-view-btn upcoming-view-btn" title="Ver detalles">
                    Ver
                </a>
                
                <!-- Countdown Badge (solo el más importante) -->
                @if($item->days_until_release !== null)
                <div class="upcoming-countdown-badge">
                    @if($item->days_until_release === 0)
                        ¡Hoy!
                    @elseif($item->days_until_release === 1)
                        Mañana
                    @else
                        {{ $item->days_until_release }} días
                    @endif
                </div>
                @endif
                
                <div class="card-info">
                    <div class="card-title">{{ $item->display_title }}</div>
                    <div class="card-meta">
                        <span class="card-date">{{ $item->formatted_release_date }}</span>
                        @if($item->isNewSeason())
                        <span class="card-season">Temporada {{ $item->season_number }}</span>
                        @else
                        <span class="card-new">Nueva Serie</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
/* Próximamente Title Destacado */
.proximamente-title {
    background: linear-gradient(135deg, #ff6b9d 0%, #ffc107 50%, #00d4ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.2rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
}

.proximamente-title::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #ff6b9d 0%, #ffc107 50%, #00d4ff 100%);
    border-radius: 2px;
}

@media (max-width: 768px) {
    .proximamente-title {
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }
}

/* Upcoming Cards Styles */
.upcoming-card {
    position: relative;
}

/* Desactivar hover effects para cards de próximamente */
.upcoming-card:hover {
    transform: none !important;
    box-shadow: none !important;
    z-index: auto !important;
}

.upcoming-card .card-overlay {
    opacity: 0.3 !important;
}

.upcoming-card:hover .card-overlay {
    opacity: 0.3 !important;
}

.upcoming-card .card-info {
    opacity: 1 !important;
    transform: none !important;
}

/* Botón Ver específico para cards de próximamente */
.upcoming-view-btn {
    position: absolute;
    top: 0.8rem;
    right: 0.8rem;
    z-index: 20 !important;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    text-decoration: none;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    transition: all 0.3s ease;
    opacity: 1 !important;
    transform: scale(1) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.upcoming-view-btn:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
    color: white;
    text-decoration: none;
}

.upcoming-card:hover .upcoming-view-btn {
    opacity: 1 !important;
    transform: scale(1.05) !important;
}

/* Countdown Badge */
.upcoming-countdown-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    z-index: 15;
    background: rgba(255, 87, 34, 0.9);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.6rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 87, 34, 0.3);
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* Card info styles */
.card-date {
    color: #ffc107;
    font-size: 0.7rem;
    font-weight: 500;
}

.card-season {
    color: #00d4ff;
    font-size: 0.7rem;
    font-weight: 600;
}

.card-new {
    color: #4caf50;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .upcoming-view-btn {
        top: 0.4rem;
        right: 0.4rem;
        padding: 0.3rem 0.6rem;
        font-size: 0.65rem;
    }
    
    .upcoming-countdown-badge {
        top: 0.4rem;
        left: 0.4rem;
        font-size: 0.55rem;
        padding: 0.25rem 0.45rem;
    }
    
    .card-date,
    .card-season,
    .card-new {
        font-size: 0.65rem;
    }
}
</style>

@endif