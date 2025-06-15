@extends('layouts.app')

@section('title', 'Próximamente - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="upcoming-hero">
    <div class="upcoming-hero-content">
        <h1 class="upcoming-hero-title">📅 Próximamente</h1>
        <p class="upcoming-hero-subtitle">Descubre los próximos estrenos de K-Dramas y nuevas temporadas</p>
        
        <!-- Stats -->
        <div class="upcoming-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $stats['total'] }}</span>
                <span class="stat-label">Total próximos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['newSeries'] }}</span>
                <span class="stat-label">Series nuevas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['newSeasons'] }}</span>
                <span class="stat-label">Nuevas temporadas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['thisMonth'] }}</span>
                <span class="stat-label">Este mes</span>
            </div>
        </div>
    </div>
</section>

<!-- Filters and Controls -->
<div class="upcoming-controls">
    <div class="controls-container">
        <!-- Filters -->
        <div class="filter-section">
            <h3>Filtrar por:</h3>
            <div class="filter-buttons">
                <a href="{{ route('upcoming.index', ['filter' => 'all', 'sort' => $currentSort]) }}" 
                   class="filter-btn {{ $currentFilter === 'all' ? 'active' : '' }}">
                    📺 Todos
                </a>
                <a href="{{ route('upcoming.index', ['filter' => 'new_series', 'sort' => $currentSort]) }}" 
                   class="filter-btn {{ $currentFilter === 'new_series' ? 'active' : '' }}">
                    🆕 Series Nuevas
                </a>
                <a href="{{ route('upcoming.index', ['filter' => 'new_seasons', 'sort' => $currentSort]) }}" 
                   class="filter-btn {{ $currentFilter === 'new_seasons' ? 'active' : '' }}">
                    🔄 Nuevas Temporadas
                </a>
                <a href="{{ route('upcoming.index', ['filter' => 'this_month', 'sort' => $currentSort]) }}" 
                   class="filter-btn {{ $currentFilter === 'this_month' ? 'active' : '' }}">
                    📅 Este Mes
                </a>
                <a href="{{ route('upcoming.index', ['filter' => 'next_month', 'sort' => $currentSort]) }}" 
                   class="filter-btn {{ $currentFilter === 'next_month' ? 'active' : '' }}">
                    ⏭️ Próximo Mes
                </a>
            </div>
        </div>

        <!-- Sort -->
        <div class="sort-section">
            <h3>Ordenar por:</h3>
            <div class="sort-buttons">
                <a href="{{ route('upcoming.index', ['filter' => $currentFilter, 'sort' => 'date']) }}" 
                   class="sort-btn {{ $currentSort === 'date' ? 'active' : '' }}">
                    📅 Fecha
                </a>
                <a href="{{ route('upcoming.index', ['filter' => $currentFilter, 'sort' => 'popularity']) }}" 
                   class="sort-btn {{ $currentSort === 'popularity' ? 'active' : '' }}">
                    🔥 Popularidad
                </a>
                <a href="{{ route('upcoming.index', ['filter' => $currentFilter, 'sort' => 'title']) }}" 
                   class="sort-btn {{ $currentSort === 'title' ? 'active' : '' }}">
                    🔤 Título
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Results -->
<section class="upcoming-results">
    @if($upcomingSeries->count() > 0)
    <div class="upcoming-grid">
        @foreach($upcomingSeries as $upcoming)
        <div class="upcoming-card" data-type="{{ $upcoming->type }}">
            <!-- Card Header -->
            <div class="upcoming-card-header">
                <div class="type-badge type-{{ $upcoming->type }}">
                    {{ $upcoming->type_icon }} {{ $upcoming->formatted_type }}
                </div>
                @if($upcoming->days_until_release !== null)
                <div class="countdown-badge">
                    @if($upcoming->days_until_release === 0)
                        ¡Hoy!
                    @elseif($upcoming->days_until_release === 1)
                        Mañana
                    @else
                        {{ $upcoming->days_until_release }} días
                    @endif
                </div>
                @endif
            </div>

            <!-- Poster -->
            <div class="upcoming-poster">
                <img src="{{ $upcoming->poster_url }}" 
                     alt="{{ $upcoming->display_title }}"
                     class="poster-image"
                     loading="lazy">
                
                <!-- Overlay Info -->
                <div class="poster-overlay">
                    <div class="overlay-content">
                        @if($upcoming->vote_average > 0)
                        <div class="rating">
                            ⭐ {{ number_format($upcoming->vote_average, 1) }}
                        </div>
                        @endif
                        @if($upcoming->episode_count)
                        <div class="episode-count">
                            {{ $upcoming->episode_count }} episodios
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="upcoming-card-content">
                <h3 class="upcoming-title">
                    <a href="{{ route('upcoming.show', $upcoming) }}">
                        {{ $upcoming->display_title }}
                    </a>
                </h3>
                
                @if($upcoming->isNewSeason() && $upcoming->existingSeries)
                <p class="existing-series">
                    Continuación de: <a href="{{ route('series.show', $upcoming->existingSeries) }}">{{ $upcoming->existingSeries->display_title }}</a>
                </p>
                @endif

                <p class="release-info">
                    <span class="release-date">📅 {{ $upcoming->formatted_release_date }}</span>
                </p>

                @if($upcoming->display_overview)
                <p class="upcoming-overview">
                    {{ Str::limit($upcoming->display_overview, 120) }}
                </p>
                @endif

                <!-- Actions -->
                <div class="upcoming-actions">
                    <a href="{{ route('upcoming.show', $upcoming) }}" class="btn-upcoming-detail">
                        Ver Detalles
                    </a>
                    @auth
                    <button class="btn-upcoming-interest" onclick="toggleInterest({{ $upcoming->id }})">
                        🔔 Me Interesa
                    </button>
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="upcoming-pagination">
        {{ $upcomingSeries->appends(request()->query())->links() }}
    </div>
    @else
    <div class="no-upcoming">
        <div class="no-upcoming-content">
            <h3>📭 No hay próximos estrenos</h3>
            <p>No se encontraron próximos estrenos con los filtros seleccionados.</p>
            <a href="{{ route('upcoming.index') }}" class="btn-reset-filters">Ver Todos</a>
        </div>
    </div>
    @endif
</section>

<style>
/* Upcoming Hero Styles */
.upcoming-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 4rem 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

.upcoming-hero-content {
    max-width: 1200px;
    margin: 0 auto;
}

.upcoming-hero-title {
    font-size: 3rem;
    color: white;
    margin-bottom: 1rem;
    font-weight: 700;
}

.upcoming-hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
}

.upcoming-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    margin-top: 0.5rem;
}

/* Controls Styles */
.upcoming-controls {
    background: rgba(255,255,255,0.05);
    padding: 2rem;
    margin-bottom: 2rem;
}

.controls-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.filter-section h3,
.sort-section h3 {
    color: white;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.filter-buttons,
.sort-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn,
.sort-btn {
    background: rgba(255,255,255,0.1);
    color: white;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.2);
}

.filter-btn:hover,
.sort-btn:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.filter-btn.active,
.sort-btn.active {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    border-color: transparent;
}

/* Upcoming Grid */
.upcoming-results {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.upcoming-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.upcoming-card {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.upcoming-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    background: rgba(255,255,255,0.08);
}

.upcoming-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: rgba(0,0,0,0.3);
}

.type-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.type-badge.type-new_series {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.type-badge.type-new_season {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.countdown-badge {
    background: rgba(255, 87, 34, 0.2);
    color: #ff5722;
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid rgba(255, 87, 34, 0.3);
}

.upcoming-poster {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.poster-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.upcoming-card:hover .poster-image {
    transform: scale(1.05);
}

.poster-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    padding: 2rem 1rem 1rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.upcoming-card:hover .poster-overlay {
    opacity: 1;
}

.overlay-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rating,
.episode-count {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
}

.upcoming-card-content {
    padding: 1.5rem;
}

.upcoming-title {
    margin-bottom: 0.5rem;
}

.upcoming-title a {
    color: white;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.upcoming-title a:hover {
    color: #00d4ff;
}

.existing-series {
    color: #ccc;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.existing-series a {
    color: #00d4ff;
    text-decoration: none;
}

.release-info {
    margin-bottom: 1rem;
}

.release-date {
    color: #ffc107;
    font-weight: 600;
    font-size: 0.9rem;
}

.upcoming-overview {
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.upcoming-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-upcoming-detail {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    flex: 1;
    text-align: center;
}

.btn-upcoming-detail:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
    color: white;
    text-decoration: none;
}

.btn-upcoming-interest {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-upcoming-interest:hover {
    background: rgba(255,255,255,0.2);
}

/* No Results */
.no-upcoming {
    text-align: center;
    padding: 4rem 2rem;
}

.no-upcoming-content h3 {
    color: white;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.no-upcoming-content p {
    color: #ccc;
    margin-bottom: 2rem;
}

.btn-reset-filters {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    text-decoration: none;
    padding: 0.8rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-reset-filters:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
    color: white;
    text-decoration: none;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .upcoming-hero-title {
        font-size: 2rem;
    }
    
    .upcoming-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .controls-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .upcoming-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .filter-buttons,
    .sort-buttons {
        gap: 0.3rem;
    }
    
    .filter-btn,
    .sort-btn {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }
}
</style>

<script>
function toggleInterest(upcomingId) {
    fetch(`/upcoming/${upcomingId}/interest`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar UI para mostrar interés
            event.target.textContent = '✅ Me Interesa';
            event.target.disabled = true;
        } else {
            alert(data.message || 'Error al marcar interés');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>
@endsection