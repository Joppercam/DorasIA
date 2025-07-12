{{-- Details Info Accordion Component --}}
<div class="details-accordion">
    <button class="accordion-header" onclick="toggleDetailsAccordion(this)">
        <span class="accordion-title">📊 Información Adicional</span>
        <span class="accordion-chevron">▼</span>
    </button>
    
    <div class="accordion-content" style="display: none;">
        
        {{-- Categories/Genres --}}
        @if($series->genres && $series->genres->count() > 0)
        <div class="accordion-section">
            <h4 class="section-subtitle">🏷️ Categorías</h4>
            <div class="section-description">
                <div class="categories-container">
                    @foreach($series->genres as $genre)
                    <span class="category-tag">{{ $genre->display_name ?: $genre->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        {{-- Availability/Streaming Info --}}
        <div class="accordion-section">
            <h4 class="section-subtitle">📺 Disponible en</h4>
            <div class="section-description">
                <div class="availability-info">
                    <div class="platform-item">
                        <span class="platform-name">🎬 Dorasia</span>
                        <span class="platform-status available">Disponible</span>
                    </div>
                    @if($series->hasTrailer())
                    <div class="platform-item">
                        <span class="platform-name">🎥 Trailer</span>
                        <span class="platform-status available">{{ $series->has_spanish_trailer ? 'Español' : 'Subtitulado' }}</span>
                    </div>
                    @endif
                    <div class="platform-item">
                        <span class="platform-name">🔤 Subtítulos</span>
                        <span class="platform-status available">Español</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Network Information --}}
        @if($series->network)
        <div class="accordion-section">
            <h4 class="section-subtitle">📺 Red de Transmisión</h4>
            <div class="section-description">
                {{ $series->network }}
            </div>
        </div>
        @endif
        
        {{-- Production Information --}}
        @if($series->production_companies && is_array($series->production_companies) && count($series->production_companies) > 0)
        <div class="accordion-section">
            <h4 class="section-subtitle">🏢 Productoras</h4>
            <div class="section-description">
                @foreach($series->production_companies as $company)
                    <span class="production-company">{{ $company['name'] ?? $company }}</span>@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Countries --}}
        @if($series->origin_country && is_array($series->origin_country) && count($series->origin_country) > 0)
        <div class="accordion-section">
            <h4 class="section-subtitle">🌍 País de Origen</h4>
            <div class="section-description">
                @foreach($series->origin_country as $country)
                    {{ $country === 'KR' ? 'Corea del Sur' : $country }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Spoken Languages --}}
        @if($series->spoken_languages && is_array($series->spoken_languages) && count($series->spoken_languages) > 0)
        <div class="accordion-section">
            <h4 class="section-subtitle">🗣️ Idiomas</h4>
            <div class="section-description">
                @foreach($series->spoken_languages as $language)
                    {{ $language['name'] ?? ($language === 'ko' ? 'Coreano' : $language) }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Keywords/Tags --}}
        @if($series->keywords && is_array($series->keywords) && count($series->keywords) > 0)
        <div class="accordion-section">
            <h4 class="section-subtitle">🏷️ Palabras Clave</h4>
            <div class="section-description">
                <div class="keywords-container">
                    @foreach(array_slice($series->keywords, 0, 10) as $keyword)
                    <span class="keyword-tag">{{ $keyword['name'] ?? $keyword }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        {{-- External IDs --}}
        @if($series->imdb_id || $series->tmdb_id)
        <div class="accordion-section">
            <h4 class="section-subtitle">🔗 Enlaces Externos</h4>
            <div class="section-description">
                <div class="external-links">
                    @if($series->imdb_id)
                    <a href="https://www.imdb.com/title/{{ $series->imdb_id }}" target="_blank" rel="noopener" class="external-link imdb">
                        📽️ Ver en IMDb
                    </a>
                    @endif
                    @if($series->tmdb_id)
                    <a href="https://www.themoviedb.org/tv/{{ $series->tmdb_id }}" target="_blank" rel="noopener" class="external-link tmdb">
                        🎬 Ver en TMDB
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        {{-- Additional Stats --}}
        <div class="accordion-section">
            <h4 class="section-subtitle">📈 Estadísticas</h4>
            <div class="section-description">
                <div class="stats-grid">
                    @if($series->vote_count > 0)
                    <div class="stat-item">
                        <span class="stat-label">Votos totales:</span>
                        <span class="stat-value">{{ number_format($series->vote_count) }}</span>
                    </div>
                    @endif
                    @if($series->popularity)
                    <div class="stat-item">
                        <span class="stat-label">Popularidad:</span>
                        <span class="stat-value">{{ number_format($series->popularity, 0) }}</span>
                    </div>
                    @endif
                    @if($series->created_at)
                    <div class="stat-item">
                        <span class="stat-label">Agregado:</span>
                        <span class="stat-value">{{ $series->created_at->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
.details-accordion {
    width: 100%;
    margin-top: 1rem;
}

.accordion-header {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.accordion-header:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.3);
}

.accordion-header.active {
    background: rgba(0, 212, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.5);
}

.accordion-title {
    font-weight: 600;
    font-size: 1rem;
}

.accordion-chevron {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
}

.accordion-header.active .accordion-chevron {
    transform: rotate(180deg);
}

.accordion-content {
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 1rem;
}

.accordion-section {
    margin-bottom: 1.5rem;
}

.accordion-section:last-child {
    margin-bottom: 0;
}

.section-subtitle {
    color: #00d4ff;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    line-height: 1.5;
}

.production-company {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    display: inline-block;
    margin-bottom: 0.3rem;
}

.keywords-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.keyword-tag {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    border: 1px solid rgba(0, 212, 255, 0.3);
}

.categories-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.category-tag {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
    font-weight: 500;
}

.availability-info {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.platform-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.platform-name {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

.platform-status {
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.platform-status.available {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.platform-status.unavailable {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.external-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.external-link {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.external-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.external-link.imdb:hover {
    background: rgba(245, 197, 24, 0.2);
    border-color: rgba(245, 197, 24, 0.5);
}

.external-link.tmdb:hover {
    background: rgba(1, 180, 228, 0.2);
    border-color: rgba(1, 180, 228, 0.5);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.8rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.stat-value {
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .accordion-header {
        padding: 0.8rem 1rem;
    }
    
    .accordion-title {
        font-size: 0.9rem;
    }
    
    .external-links {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>