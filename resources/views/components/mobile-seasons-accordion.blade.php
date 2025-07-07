<!-- Mobile-First Seasons/Episodes Accordion -->
@if((isset($series) && $series->episodes && $series->episodes->count() > 0))

@php
    $content = $series;
    $episodes = $content->episodes;
    // Group episodes by season
    $episodesBySeason = $episodes->groupBy('season_number');
    $totalSeasons = $episodesBySeason->count();
    $totalEpisodes = $episodes->count();
@endphp

<div class="mobile-seasons-accordion">
    <div class="accordion-header" onclick="toggleSeasonsAccordion()">
        <div class="accordion-title">
            <span class="seasons-icon">📺</span>
            <h3>Temporadas</h3>
            <span class="seasons-count-badge">{{ $totalSeasons }}</span>
        </div>
        <div class="accordion-toggle">
            <span class="click-hint">Tocar para abrir</span>
            <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>

    <div class="accordion-content" id="seasons-accordion-content">
        <div class="seasons-info">
            <div class="info-text">
                <span class="info-icon">ℹ️</span>
                <span class="info-message">
                    Explora las <strong>{{ $totalSeasons }} temporadas</strong> con {{ $totalEpisodes }} episodios. 
                    Toca en cualquier temporada para ver los episodios.
                </span>
            </div>
        </div>

        <div class="seasons-overview">
            <div class="overview-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $totalSeasons }}</span>
                    <span class="stat-label">Temporadas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $totalEpisodes }}</span>
                    <span class="stat-label">Episodios</span>
                </div>
                @if($series->first_air_date)
                <div class="stat-item">
                    <span class="stat-number">{{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}</span>
                    <span class="stat-label">Estreno</span>
                </div>
                @endif
                @if($series->status)
                <div class="stat-item status">
                    <span class="stat-label">{{ $series->status === 'Ended' ? 'Finalizada' : 'En emisión' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="mobile-seasons-list">
            @foreach($episodesBySeason->sortKeys() as $seasonNumber => $seasonEpisodes)
            @php
                $seasonEpisodeCount = $seasonEpisodes->count();
                $firstEpisode = $seasonEpisodes->first();
                $lastEpisode = $seasonEpisodes->last();
                $seasonYear = $firstEpisode && $firstEpisode->air_date ? 
                    \Carbon\Carbon::parse($firstEpisode->air_date)->format('Y') : null;
            @endphp
            <div class="mobile-season-card" data-season-id="{{ $seasonNumber }}">
                <div class="season-header" onclick="toggleSeasonEpisodes({{ $seasonNumber }})">
                    <div class="season-info">
                        <div class="season-title">Temporada {{ $seasonNumber }}</div>
                        <div class="season-meta">
                            <span class="episode-count">({{ $seasonEpisodeCount }} episodios)</span>
                            @if($seasonYear)
                            <span class="season-year">{{ $seasonYear }}</span>
                            @endif
                            @if($series->vote_average > 0)
                            <span class="season-rating">⭐ {{ number_format($series->vote_average, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="season-toggle">
                        <svg class="season-chevron" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                        </svg>
                    </div>
                </div>

                <div class="season-episodes" id="season-episodes-{{ $seasonNumber }}">
                    <div class="episodes-list">
                        @foreach($seasonEpisodes->sortBy('episode_number') as $episode)
                        <div class="episode-card" data-episode-id="{{ $episode->id }}">
                            <div class="episode-main-info" onclick="toggleEpisodeDetails({{ $episode->id }})">
                                <div class="episode-number">{{ $episode->episode_number }}</div>
                                <div class="episode-basic-info">
                                    <h4 class="episode-title">{{ $episode->name ?: "Episodio {$episode->episode_number}" }}</h4>
                                    @if($episode->air_date)
                                    <div class="episode-date">{{ \Carbon\Carbon::parse($episode->air_date)->format('d M Y') }}</div>
                                    @endif
                                    @if($episode->runtime)
                                    <div class="episode-runtime">{{ $episode->runtime }} min</div>
                                    @endif
                                </div>
                                <div class="episode-actions">
                                    <div class="episode-progress">
                                        <!-- Placeholder for watch progress -->
                                        <div class="progress-indicator not-watched">●</div>
                                    </div>
                                    <svg class="episode-chevron" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Episode details (expandable) -->
                            <div class="episode-details" id="episode-details-{{ $episode->id }}">
                                @if($episode->overview)
                                <div class="episode-overview">
                                    <p>{{ $episode->overview }}</p>
                                </div>
                                @endif
                                
                                <div class="episode-metadata">
                                    @if($episode->air_date)
                                    <div class="metadata-item">
                                        <span class="metadata-label">Fecha de emisión:</span>
                                        <span class="metadata-value">{{ \Carbon\Carbon::parse($episode->air_date)->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($episode->runtime)
                                    <div class="metadata-item">
                                        <span class="metadata-label">Duración:</span>
                                        <span class="metadata-value">{{ $episode->runtime }} minutos</span>
                                    </div>
                                    @endif
                                    
                                    @if($episode->vote_average && $episode->vote_average > 0)
                                    <div class="metadata-item">
                                        <span class="metadata-label">Valoración:</span>
                                        <span class="metadata-value">⭐ {{ number_format($episode->vote_average, 1) }}</span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Watch buttons -->
                                <div class="episode-watch-actions">
                                    
                                    @auth
                                    <button class="watch-btn secondary" onclick="addToWatchlist({{ $episode->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                        </svg>
                                        Agregar a lista
                                    </button>
                                    
                                    <button class="watch-btn tertiary" onclick="markAsWatched({{ $episode->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                        Marcar como visto
                                    </button>
                                    @else
                                    <button class="watch-btn secondary" onclick="redirectToLogin()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                        </svg>
                                        Iniciar sesión para agregar
                                    </button>
                                    
                                    <button class="watch-btn tertiary" onclick="redirectToLogin()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                        Iniciar sesión para marcar
                                    </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.mobile-seasons-accordion {
    background: rgba(20, 20, 20, 0.95);
    border-radius: 12px;
    margin: 0.75rem 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.accordion-header {
    padding: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.accordion-header:hover {
    background: rgba(255, 255, 255, 0.05);
}

.accordion-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.seasons-icon {
    font-size: 1.5rem;
}

.accordion-title h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: white;
}

.seasons-count-badge {
    background: linear-gradient(135deg, #9c27b0, #673ab7);
    color: white;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.accordion-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.click-hint {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
    transition: color 0.3s ease;
}

.accordion-header:hover .click-hint {
    color: #9c27b0;
}

.accordion-toggle .chevron-icon {
    color: #9c27b0;
    transition: transform 0.3s ease;
}

.accordion-toggle.active .chevron-icon {
    transform: rotate(180deg);
}

.accordion-toggle.active .click-hint {
    display: none;
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
    opacity: 0;
    visibility: hidden;
}

.accordion-content.open {
    max-height: 4000px;
    opacity: 1;
    visibility: visible;
}

.seasons-info {
    padding: 0.75rem;
    background: rgba(156, 39, 176, 0.05);
    border-bottom: 1px solid rgba(156, 39, 176, 0.1);
    border-left: 3px solid #9c27b0;
    margin-bottom: 0;
}

.info-text {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.info-icon {
    font-size: 1rem;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.info-message {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.3;
}

.info-message strong {
    color: #bb86fc;
    font-weight: 700;
}

.seasons-overview {
    padding: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.overview-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 60px;
}

.stat-item.status {
    flex-direction: row;
    min-width: auto;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: 700;
    color: #bb86fc;
    line-height: 1;
}

.stat-label {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.2rem;
}

.stat-item.status .stat-label {
    margin-top: 0;
    background: rgba(156, 39, 176, 0.2);
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    color: #bb86fc;
    font-weight: 600;
}

.mobile-seasons-list {
    padding: 0;
}

.mobile-season-card {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.mobile-season-card:last-child {
    border-bottom: none;
}

.season-header {
    padding: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.03);
}

.season-header:hover {
    background: rgba(156, 39, 176, 0.05);
}

.season-info {
    flex: 1;
}

.season-title {
    font-size: 1rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.3rem;
}

.season-meta {
    display: flex;
    gap: 0.8rem;
    align-items: center;
    flex-wrap: wrap;
}

.episode-count {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
}

.season-year {
    background: rgba(156, 39, 176, 0.2);
    color: #bb86fc;
    padding: 0.15rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.season-rating {
    color: #ffd700;
    font-weight: 600;
    font-size: 0.8rem;
}

.season-toggle {
    display: flex;
    align-items: center;
    padding: 0.25rem;
}

.season-chevron {
    color: #9c27b0;
    transition: transform 0.3s ease;
}

.season-header.active .season-chevron {
    transform: rotate(180deg);
}

.season-episodes {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: rgba(0, 0, 0, 0.2);
}

.season-episodes.open {
    max-height: 2000px;
}

.episodes-list {
    padding: 0;
}

.episode-card {
    border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    transition: all 0.3s ease;
}

.episode-card:last-child {
    border-bottom: none;
}

.episode-card:hover {
    background: rgba(255, 255, 255, 0.02);
}

.episode-main-info {
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.episode-number {
    width: 32px;
    height: 32px;
    background: rgba(156, 39, 176, 0.2);
    color: #bb86fc;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.episode-basic-info {
    flex: 1;
    min-width: 0;
}

.episode-title {
    margin: 0 0 0.2rem 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: white;
    line-height: 1.2;
    word-wrap: break-word;
}

.episode-date {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.1rem;
}

.episode-runtime {
    font-size: 0.75rem;
    color: #9c27b0;
    font-weight: 500;
}

.episode-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.episode-progress {
    display: flex;
    align-items: center;
}

.progress-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    font-size: 0.6rem;
}

.progress-indicator.not-watched {
    color: rgba(255, 255, 255, 0.3);
}

.progress-indicator.watching {
    color: #ffd700;
}

.progress-indicator.watched {
    color: #4caf50;
}

.episode-chevron {
    color: rgba(255, 255, 255, 0.4);
    transition: all 0.3s ease;
}

.episode-main-info:hover .episode-chevron {
    color: #9c27b0;
    transform: scale(1.1);
}

.episode-details {
    display: none;
    padding: 0 0.75rem 0.75rem 3.5rem;
    animation: slideDown 0.3s ease;
}

.episode-details.open {
    display: block;
}

.episode-overview {
    margin-bottom: 0.75rem;
}

.episode-overview p {
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    line-height: 1.4;
}

.episode-metadata {
    margin-bottom: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.metadata-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
}

.metadata-label {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

.metadata-value {
    color: white;
    font-weight: 600;
}

.episode-watch-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.watch-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.watch-btn.primary {
    background: linear-gradient(135deg, #9c27b0, #673ab7);
    color: white;
}

.watch-btn.primary:hover {
    background: linear-gradient(135deg, #8e24aa, #5e35b1);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(156, 39, 176, 0.3);
}

.watch-btn.secondary {
    background: rgba(156, 39, 176, 0.1);
    color: #bb86fc;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

.watch-btn.secondary:hover {
    background: rgba(156, 39, 176, 0.2);
    border-color: rgba(156, 39, 176, 0.5);
}

.watch-btn.tertiary {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.watch-btn.tertiary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive breakpoints */
@media (min-width: 768px) {
    .episode-watch-actions {
        flex-direction: row;
    }
    
    .watch-btn {
        flex: 1;
    }
}

@media (max-width: 375px) {
    .accordion-header {
        padding: 0.6rem;
    }
    
    .season-header {
        padding: 0.6rem;
    }
    
    .episode-main-info {
        padding: 0.6rem;
    }
    
    .episode-number {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }
    
    .episode-title {
        font-size: 0.9rem;
    }
    
    .seasons-info {
        padding: 0.6rem;
    }
    
    .seasons-overview {
        padding: 0.6rem;
    }
    
    .overview-stats {
        gap: 0.8rem;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .season-header:active {
        background: rgba(156, 39, 176, 0.1);
    }
    
    .episode-main-info:active {
        background: rgba(255, 255, 255, 0.05);
        transform: scale(0.98);
    }
    
    .accordion-header:active {
        background: rgba(255, 255, 255, 0.1);
    }
}
</style>

<script>
let isSeasonsAccordionOpen = false;
let openSeasons = new Set();
let openEpisodes = new Set();

// Toggle del acordeón principal de temporadas
function toggleSeasonsAccordion() {
    const content = document.getElementById('seasons-accordion-content');
    const toggle = document.querySelector('.mobile-seasons-accordion .accordion-toggle');
    const hint = document.querySelector('.mobile-seasons-accordion .click-hint');
    
    isSeasonsAccordionOpen = !isSeasonsAccordionOpen;
    
    if (isSeasonsAccordionOpen) {
        content.classList.add('open');
        toggle.classList.add('active');
        if (hint) hint.textContent = 'Tocar para cerrar';
    } else {
        content.classList.remove('open');
        toggle.classList.remove('active');
        if (hint) hint.textContent = 'Tocar para abrir';
    }
}

// Toggle de episodios de una temporada específica
function toggleSeasonEpisodes(seasonNumber) {
    const seasonEpisodes = document.getElementById(`season-episodes-${seasonNumber}`);
    const seasonHeader = seasonEpisodes.previousElementSibling;
    const isOpen = openSeasons.has(seasonNumber);
    
    if (isOpen) {
        seasonEpisodes.classList.remove('open');
        seasonHeader.classList.remove('active');
        openSeasons.delete(seasonNumber);
    } else {
        seasonEpisodes.classList.add('open');
        seasonHeader.classList.add('active');
        openSeasons.add(seasonNumber);
    }
}

// Toggle de detalles de episodio individual
function toggleEpisodeDetails(episodeId) {
    const details = document.getElementById(`episode-details-${episodeId}`);
    const isOpen = openEpisodes.has(episodeId);
    
    // Cerrar todos los detalles de episodios abiertos
    openEpisodes.forEach(id => {
        if (id !== episodeId) {
            const otherDetails = document.getElementById(`episode-details-${id}`);
            if (otherDetails) {
                otherDetails.classList.remove('open');
            }
        }
    });
    openEpisodes.clear();
    
    // Abrir el clickeado si no estaba abierto
    if (!isOpen) {
        details.classList.add('open');
        openEpisodes.add(episodeId);
    }
}

// Función de reproducción eliminada en esta versión

// Global variable to check authentication status
const isUserAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

function addToWatchlist(episodeId) {
    if (!isUserAuthenticated) {
        redirectToLogin();
        return;
    }
    
    console.log('Add to watchlist:', episodeId);
    // TODO: Implementar agregar a lista de seguimiento
    alert('Agregado a la lista de seguimiento');
}

function markAsWatched(episodeId) {
    if (!isUserAuthenticated) {
        redirectToLogin();
        return;
    }
    
    console.log('Mark as watched:', episodeId);
    // TODO: Implementar marcado como visto
    const episodeCard = document.querySelector(`[data-episode-id="${episodeId}"]`);
    if (episodeCard) {
        const progressIndicator = episodeCard.querySelector('.progress-indicator');
        if (progressIndicator) {
            progressIndicator.className = 'progress-indicator watched';
            progressIndicator.textContent = '●';
        }
    }
    alert('Marcado como visto');
}

function redirectToLogin() {
    alert('Necesitas iniciar sesión para usar esta función');
    window.location.href = '/login';
}

// Acordeón cerrado por defecto - usuario debe hacer clic para abrir
// No auto-abrir en ningún caso
</script>

@endif