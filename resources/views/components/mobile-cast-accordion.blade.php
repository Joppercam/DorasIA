<!-- Mobile-First Cast Accordion -->
@if((isset($series) && $series->people && $series->people->count() > 0) || 
    (isset($movie) && $movie->people && $movie->people->count() > 0))

@php
    $content = $series ?? $movie;
    $contentType = isset($series) ? 'serie' : 'película';
    $cast = $content->people;
@endphp

<div class="mobile-cast-accordion">
    <div class="accordion-header" onclick="toggleCastAccordion()">
        <div class="accordion-title">
            <span class="cast-icon">🎭</span>
            <h3>Reparto</h3>
            <span class="cast-count-badge">{{ $cast->count() }}</span>
        </div>
        <div class="accordion-toggle">
            <span class="click-hint">Tocar para abrir</span>
            <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>

    <div class="accordion-content" id="cast-accordion-content">
        <div class="cast-info">
            <div class="info-text">
                <span class="info-icon">ℹ️</span>
                <span class="info-message">
                    Toca en cualquier actor para ver más información sobre su carrera y filmografía.
                </span>
            </div>
        </div>

        <div class="mobile-cast-list">
            @foreach($cast as $index => $person)
            <div class="mobile-cast-card" data-cast-id="{{ $person->id }}" data-cast-index="{{ $index }}">
                <a href="{{ route('actors.show', $person->id) }}" class="cast-link">
                    <div class="cast-member-container">
                        <div class="cast-photo-container">
                            @if($person->profile_path)
                            <img src="https://image.tmdb.org/t/p/w200{{ $person->profile_path }}" 
                                 alt="{{ $person->name }}"
                                 class="cast-photo">
                            @else
                            <div class="cast-photo-placeholder">👤</div>
                            @endif
                        </div>
                        
                        <div class="cast-info-container">
                            <div class="cast-member-info">
                                <h4 class="cast-name">{{ $person->display_name ?: $person->name }}</h4>
                                @if($person->pivot && $person->pivot->character)
                                <p class="cast-character">{{ $person->pivot->character }}</p>
                                @endif
                                @if($person->known_for_department)
                                <p class="cast-department">{{ $person->known_for_department === 'Acting' ? 'Actor/Actriz' : $person->known_for_department }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="cast-actions">
                            <svg class="view-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </div>
                    </div>
                </a>
                
                <!-- Expandable details (future enhancement) -->
                <div class="cast-details" id="cast-details-{{ $person->id }}" style="display: none;">
                    @if($person->biography)
                    <div class="detail-row">
                        <span class="detail-label">Biografía:</span>
                        <span class="detail-value">{{ Str::limit($person->biography, 120) }}</span>
                    </div>
                    @endif
                    
                    @if($person->birthday)
                    <div class="detail-row">
                        <span class="detail-label">Nacimiento:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($person->birthday)->format('d M Y') }}</span>
                    </div>
                    @endif
                    
                    @if($person->place_of_birth)
                    <div class="detail-row">
                        <span class="detail-label">Lugar:</span>
                        <span class="detail-value">{{ $person->place_of_birth }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        @if($cast->count() > 8)
        <div class="cast-footer">
            <p class="cast-total-info">
                Mostrando todos los {{ $cast->count() }} miembros del reparto
            </p>
        </div>
        @endif
    </div>
</div>

<style>
.mobile-cast-accordion {
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

.cast-icon {
    font-size: 1.5rem;
}

.accordion-title h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: white;
}

.cast-count-badge {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
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
    color: #ff6b6b;
}

.accordion-toggle .chevron-icon {
    color: #ff6b6b;
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
    max-height: 3000px;
    opacity: 1;
    visibility: visible;
}

.cast-info {
    padding: 0.75rem;
    background: rgba(255, 107, 107, 0.05);
    border-bottom: 1px solid rgba(255, 107, 107, 0.1);
    border-left: 3px solid #ff6b6b;
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

.mobile-cast-list {
    padding: 0;
}

.mobile-cast-card {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.mobile-cast-card:last-child {
    border-bottom: none;
}

.mobile-cast-card:hover {
    background: rgba(255, 255, 255, 0.03);
}

.cast-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.cast-link:hover {
    text-decoration: none;
    color: inherit;
}

.cast-member-container {
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

.cast-link:hover .cast-member-container {
    transform: translateX(2px);
}

.cast-photo-container {
    flex-shrink: 0;
}

.cast-photo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 107, 107, 0.3);
    transition: all 0.3s ease;
}

.cast-link:hover .cast-photo {
    border-color: rgba(255, 107, 107, 0.8);
    transform: scale(1.05);
}

.cast-photo-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: rgba(255, 255, 255, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.cast-link:hover .cast-photo-placeholder {
    border-color: rgba(255, 107, 107, 0.8);
    background: rgba(255, 107, 107, 0.1);
    transform: scale(1.05);
}

.cast-info-container {
    flex: 1;
    min-width: 0;
}

.cast-member-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.cast-name {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    line-height: 1.2;
    transition: color 0.3s ease;
    word-wrap: break-word;
}

.cast-link:hover .cast-name {
    color: #ff6b6b;
}

.cast-character {
    margin: 0;
    font-size: 0.85rem;
    color: #ff8e53;
    font-weight: 500;
    line-height: 1.2;
    word-wrap: break-word;
}

.cast-department {
    margin: 0;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

.cast-actions {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    padding: 0.5rem;
}

.view-icon {
    color: rgba(255, 255, 255, 0.4);
    transition: all 0.3s ease;
}

.cast-link:hover .view-icon {
    color: #ff6b6b;
    transform: scale(1.1);
}

.cast-details {
    padding: 0 0.75rem 0.75rem 4.5rem;
    animation: slideDown 0.3s ease;
}

.cast-details.open {
    display: block;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.4rem;
    font-size: 0.85rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.detail-label {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    min-width: 80px;
}

.detail-value {
    color: white;
    flex: 1;
    text-align: right;
}

.cast-footer {
    padding: 0.75rem;
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.02);
}

.cast-total-info {
    margin: 0;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
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
    .mobile-cast-accordion {
        border-radius: 12px;
    }
    
    .cast-member-container {
        padding: 1rem;
    }
    
    .cast-photo, .cast-photo-placeholder {
        width: 90px;
        height: 90px;
    }
    
    .cast-name {
        font-size: 1.1rem;
    }
    
    .cast-character {
        font-size: 0.9rem;
    }
}

@media (max-width: 375px) {
    .accordion-header {
        padding: 0.6rem;
    }
    
    .cast-member-container {
        padding: 0.6rem;
    }
    
    .cast-photo, .cast-photo-placeholder {
        width: 65px;
        height: 65px;
    }
    
    .cast-name {
        font-size: 0.9rem;
    }
    
    .cast-character {
        font-size: 0.8rem;
    }
    
    .cast-info {
        padding: 0.6rem;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .cast-member-container {
        padding: 1rem;
    }
    
    .cast-link:active .cast-member-container {
        background: rgba(255, 107, 107, 0.1);
        transform: scale(0.98);
    }
    
    .accordion-header:active {
        background: rgba(255, 255, 255, 0.1);
    }
}
</style>

<script>
let isCastAccordionOpen = false;

// Toggle del acordeón principal de reparto
function toggleCastAccordion() {
    const content = document.getElementById('cast-accordion-content');
    const toggle = document.querySelector('.mobile-cast-accordion .accordion-toggle');
    const hint = document.querySelector('.mobile-cast-accordion .click-hint');
    
    isCastAccordionOpen = !isCastAccordionOpen;
    
    if (isCastAccordionOpen) {
        content.classList.add('open');
        toggle.classList.add('active');
        if (hint) hint.textContent = 'Tocar para cerrar';
    } else {
        content.classList.remove('open');
        toggle.classList.remove('active');
        if (hint) hint.textContent = 'Tocar para abrir';
    }
}

// Toggle de detalles de actor individual (para futuras mejoras)
function toggleCastDetails(castId) {
    const details = document.getElementById(`cast-details-${castId}`);
    const isOpen = details.style.display !== 'none';
    
    // Cerrar todos los detalles abiertos
    document.querySelectorAll('.cast-details').forEach(el => {
        el.style.display = 'none';
    });
    
    // Abrir el clickeado si no estaba abierto
    if (!isOpen) {
        details.style.display = 'block';
        details.classList.add('open');
    }
}

// Accordión cerrado por defecto - usuario debe hacer clic para abrir
// No auto-abrir en ningún caso
</script>

@endif