<!-- Simple Soundtrack List -->
@if((isset($series) && $series->soundtracks && $series->soundtracks->count() > 0) || 
    (isset($movie) && $movie->soundtracks && $movie->soundtracks->count() > 0))

@php
    $content = $series ?? $movie;
    $contentType = isset($series) ? 'serie' : 'película';
    $soundtracks = $content->soundtracks;
@endphp

<div class="simple-soundtrack-list">
    <div class="soundtrack-header" onclick="toggleSoundtrackAccordion()">
        <h3 class="soundtrack-title">
            <span class="soundtrack-icon">🎵</span>
            Banda Sonora
            <span class="track-count">{{ $soundtracks->count() }} {{ $soundtracks->count() === 1 ? 'canción' : 'canciones' }}</span>
        </h3>
        <div class="accordion-toggle">
            <span class="click-hint">Tocar para abrir</span>
            <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>

    <div class="soundtrack-tracks" id="soundtrack-accordion-content" style="display: none;">
        @foreach($soundtracks as $index => $soundtrack)
        <div class="track-item">
            <div class="track-info">
                <div class="track-number">{{ $index + 1 }}</div>
                <div class="track-details">
                    <h4 class="track-title">{{ $soundtrack->title }}</h4>
                    <p class="track-artist">{{ $soundtrack->artist }}</p>
                    
                    @if($soundtrack->is_main_theme || $soundtrack->is_ending_theme)
                    <div class="track-badges">
                        @if($soundtrack->is_main_theme)
                        <span class="track-badge main-theme">🎭 Tema Principal</span>
                        @endif
                        @if($soundtrack->is_ending_theme)
                        <span class="track-badge ending-theme">🎬 Ending</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="track-links">
                @if($soundtrack->spotify_url)
                <a href="{{ $soundtrack->spotify_url }}" target="_blank" class="platform-link spotify" title="Escuchar en Spotify">
                    <svg class="platform-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.32 11.28-1.08 15.721 1.621.539.3.719 1.02.42 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                    <span class="platform-name">Spotify</span>
                </a>
                @endif
                
                @if($soundtrack->apple_music_url)
                <a href="{{ $soundtrack->apple_music_url }}" target="_blank" class="platform-link apple" title="Escuchar en Apple Music">
                    <svg class="platform-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/>
                    </svg>
                    <span class="platform-name">Apple Music</span>
                </a>
                @endif
                
                @if($soundtrack->youtube_url || $soundtrack->youtube_id)
                <a href="{{ $soundtrack->youtube_url ?: 'https://www.youtube.com/watch?v=' . $soundtrack->youtube_id }}" target="_blank" class="platform-link youtube" title="Ver en YouTube">
                    <svg class="platform-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    <span class="platform-name">YouTube</span>
                </a>
                @endif
                
                @if(!$soundtrack->spotify_url && !$soundtrack->apple_music_url && !$soundtrack->youtube_url && !$soundtrack->youtube_id)
                    <span class="no-links">🎶 Sin enlaces disponibles</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.simple-soundtrack-list {
    background: rgba(20, 20, 20, 0.95);
    border-radius: 12px;
    margin: 0.75rem 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.soundtrack-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.3);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

.soundtrack-header:hover {
    background: rgba(255, 255, 255, 0.05);
}

.soundtrack-title {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
}

.soundtrack-icon {
    font-size: 1.3rem;
}

.track-count {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
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

.soundtrack-header:hover .click-hint {
    color: #00d4ff;
}

.chevron-icon {
    color: #00d4ff;
    transition: transform 0.3s ease;
}

.accordion-toggle.active .chevron-icon {
    transform: rotate(180deg);
}

.accordion-toggle.active .click-hint {
    display: none;
}

.soundtrack-tracks {
    transition: all 0.3s ease;
}

.soundtrack-tracks.open {
    display: block !important;
}

.soundtrack-tracks {
    padding: 0;
}

.track-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    gap: 1rem;
    transition: background 0.3s ease;
}

.track-item:last-child {
    border-bottom: none;
}

.track-item:hover {
    background: rgba(255, 255, 255, 0.03);
}

.track-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
}

.track-number {
    width: 24px;
    height: 24px;
    background: rgba(0, 212, 255, 0.2);
    border: 1px solid rgba(0, 212, 255, 0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: #00d4ff;
    flex-shrink: 0;
}

.track-details {
    flex: 1;
    min-width: 0;
}

.track-title {
    margin: 0 0 0.2rem 0;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    line-height: 1.2;
    word-wrap: break-word;
}

.track-artist {
    margin: 0 0 0.3rem 0;
    font-size: 0.9rem;
    color: #00d4ff;
    line-height: 1.2;
}

.track-badges {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.track-badge {
    padding: 0.15rem 0.4rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.track-badge.main-theme {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: white;
    border: 1px solid rgba(255, 107, 107, 0.3);
}

.track-badge.ending-theme {
    background: linear-gradient(135deg, #a8edea, #fed6e3);
    color: #2c3e50;
    border: 1px solid rgba(168, 237, 234, 0.3);
}

.track-links {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.platform-link {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0.6rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-decoration: none;
    color: white;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.platform-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.platform-link.spotify:hover {
    background: rgba(29, 185, 84, 0.2);
    border-color: rgba(29, 185, 84, 0.5);
    color: #1db954;
}

.platform-link.spotify .platform-icon {
    color: #1db954;
}

.platform-link.apple:hover {
    background: rgba(250, 36, 60, 0.2);
    border-color: rgba(250, 36, 60, 0.5);
    color: #fa243c;
}

.platform-link.apple .platform-icon {
    color: #fa243c;
}

.platform-link.youtube:hover {
    background: rgba(255, 0, 0, 0.2);
    border-color: rgba(255, 0, 0, 0.5);
    color: #ff0000;
}

.platform-link.youtube .platform-icon {
    color: #ff0000;
}

.platform-icon {
    font-size: 1rem;
    transition: color 0.3s ease;
}

.platform-name {
    font-size: 0.8rem;
}

.no-links {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.85rem;
    font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
    .track-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .track-info {
        width: 100%;
    }
    
    .track-links {
        width: 100%;
        justify-content: flex-start;
    }
    
    .platform-link {
        flex: 1;
        justify-content: center;
        min-width: 80px;
    }
    
    .platform-name {
        display: none;
    }
    
    .platform-link .platform-icon {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .soundtrack-header {
        padding: 0.75rem;
    }
    
    .track-item {
        padding: 0.75rem;
    }
    
    .soundtrack-title {
        font-size: 1rem;
    }
    
    .track-title {
        font-size: 0.9rem;
    }
    
    .track-artist {
        font-size: 0.8rem;
    }
    
    .track-number {
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
    }
    
    .platform-link {
        padding: 0.3rem 0.4rem;
        min-width: 40px;
    }
    
    .platform-link .platform-icon {
        font-size: 1rem;
    }
}
</style>

<script>
let isSoundtrackAccordionOpen = false;

// Toggle del acordeón de banda sonora
function toggleSoundtrackAccordion() {
    const content = document.getElementById('soundtrack-accordion-content');
    const toggle = document.querySelector('.simple-soundtrack-list .accordion-toggle');
    const hint = document.querySelector('.simple-soundtrack-list .click-hint');
    
    isSoundtrackAccordionOpen = !isSoundtrackAccordionOpen;
    
    if (isSoundtrackAccordionOpen) {
        content.style.display = 'block';
        content.classList.add('open');
        toggle.classList.add('active');
        if (hint) hint.textContent = 'Tocar para cerrar';
    } else {
        content.style.display = 'none';
        content.classList.remove('open');
        toggle.classList.remove('active');
        if (hint) hint.textContent = 'Tocar para abrir';
    }
}

// Acordeón cerrado por defecto
</script>

@endif