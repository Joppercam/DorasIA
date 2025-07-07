<!-- Soundtrack Section -->
@if(isset($series) && $series->soundtracks && $series->soundtracks->count() > 0)
<div class="soundtrack-section">
    <div class="section-header">
        <h3 class="section-title">
            <span class="section-icon">🎵</span>
            Banda Sonora
        </h3>
        <span class="track-count">{{ $series->soundtracks->count() }} {{ $series->soundtracks->count() == 1 ? 'canción' : 'canciones' }}</span>
    </div>

    <div class="soundtrack-grid">
        @foreach($series->soundtracks as $soundtrack)
        <div class="soundtrack-card" data-track-id="{{ $soundtrack->id }}">
            <div class="track-info">
                <div class="track-header">
                    <h4 class="track-title">{{ $soundtrack->title }}</h4>
                    @if($soundtrack->is_main_theme)
                    <span class="track-badge main-theme">Tema Principal</span>
                    @elseif($soundtrack->is_ending_theme)
                    <span class="track-badge ending-theme">Ending</span>
                    @endif
                </div>
                
                <p class="track-artist">{{ $soundtrack->artist }}</p>
                
                @if($soundtrack->album)
                <p class="track-album">{{ $soundtrack->album }}</p>
                @endif
                
                @if($soundtrack->duration)
                <p class="track-duration">{{ $soundtrack->formatted_duration }}</p>
                @endif
            </div>

            <div class="track-actions">
                <!-- Play/Pause Button -->
                @if($soundtrack->youtube_url)
                <button class="play-btn" onclick="playTrack('{{ $soundtrack->id }}', '{{ $soundtrack->youtube_url }}', '{{ addslashes($soundtrack->title) }}', '{{ addslashes($soundtrack->artist) }}')">
                    <svg class="play-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <svg class="pause-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                </button>
                @endif

                <!-- Streaming Links -->
                <div class="streaming-links">
                    @if($soundtrack->spotify_url)
                    <a href="{{ $soundtrack->spotify_url }}" target="_blank" class="streaming-link spotify" title="Escuchar en Spotify">
                        <span class="spotify-icon">🎵</span>
                    </a>
                    @endif
                    
                    @if($soundtrack->apple_music_url)
                    <a href="{{ $soundtrack->apple_music_url }}" target="_blank" class="streaming-link apple" title="Escuchar en Apple Music">
                        <span class="apple-icon">🍎</span>
                    </a>
                    @endif
                    
                    @if($soundtrack->youtube_url)
                    <a href="{{ $soundtrack->youtube_url }}" target="_blank" class="streaming-link youtube" title="Ver en YouTube">
                        <span class="youtube-icon">📺</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.soundtrack-section {
    background: rgba(20, 20, 20, 0.9);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.section-icon {
    font-size: 1.2rem;
}

.track-count {
    color: #aaa;
    font-size: 0.9rem;
}

.soundtrack-grid {
    display: grid;
    gap: 1rem;
}

.soundtrack-card {
    background: rgba(40, 40, 40, 0.8);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.soundtrack-card:hover {
    background: rgba(50, 50, 50, 0.9);
    border-color: rgba(0, 212, 255, 0.3);
}

.track-info {
    flex: 1;
}

.track-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.track-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    margin: 0;
}

.track-badge {
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.track-badge.main-theme {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    color: white;
}

.track-badge.ending-theme {
    background: linear-gradient(135deg, #ff6b6b, #feca57);
    color: white;
}

.track-artist {
    color: #00d4ff;
    font-weight: 500;
    margin: 0.25rem 0;
}

.track-album, .track-duration {
    color: #aaa;
    font-size: 0.9rem;
    margin: 0.125rem 0;
}

.track-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.play-btn {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.play-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
}

.streaming-links {
    display: flex;
    gap: 0.5rem;
}

.streaming-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
}

.streaming-link:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.2);
}

.streaming-link.spotify:hover {
    background: #1db954;
}

.streaming-link.apple:hover {
    background: #fa243c;
}

.streaming-link.youtube:hover {
    background: #ff0000;
}

/* Responsive */
@media (max-width: 768px) {
    .soundtrack-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .track-actions {
        justify-content: center;
    }
    
    .section-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
</style>

<script>
let currentPlayer = null;
let currentTrackId = null;

function playTrack(trackId, youtubeUrl, title, artist) {
    const trackCard = document.querySelector(`[data-track-id="${trackId}"]`);
    const playBtn = trackCard.querySelector('.play-btn');
    const playIcon = trackCard.querySelector('.play-icon');
    const pauseIcon = trackCard.querySelector('.pause-icon');
    
    // Si ya está reproduciendo esta canción, pausar
    if (currentTrackId === trackId && currentPlayer) {
        pauseTrack();
        return;
    }
    
    // Pausar canción anterior si existe
    if (currentPlayer) {
        pauseTrack();
    }
    
    // Extraer video ID de YouTube URL
    const videoId = extractYouTubeId(youtubeUrl);
    if (!videoId) {
        console.error('No se pudo extraer ID de YouTube');
        return;
    }
    
    // Crear mini reproductor
    createMiniPlayer(videoId, title, artist, trackId);
    
    // Actualizar UI
    currentTrackId = trackId;
    playIcon.style.display = 'none';
    pauseIcon.style.display = 'block';
    trackCard.classList.add('playing');
}

function pauseTrack() {
    if (currentPlayer) {
        currentPlayer.stopVideo();
    }
    
    // Resetear UI
    if (currentTrackId) {
        const currentCard = document.querySelector(`[data-track-id="${currentTrackId}"]`);
        if (currentCard) {
            const playIcon = currentCard.querySelector('.play-icon');
            const pauseIcon = currentCard.querySelector('.pause-icon');
            playIcon.style.display = 'block';
            pauseIcon.style.display = 'none';
            currentCard.classList.remove('playing');
        }
    }
    
    currentTrackId = null;
    removeMiniPlayer();
}

function extractYouTubeId(url) {
    const regex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/;
    const match = url.match(regex);
    return match ? match[1] : null;
}

function createMiniPlayer(videoId, title, artist, trackId) {
    // Remover reproductor anterior
    removeMiniPlayer();
    
    // Crear contenedor del mini reproductor
    const miniPlayer = document.createElement('div');
    miniPlayer.id = 'mini-soundtrack-player';
    miniPlayer.innerHTML = `
        <div class="mini-player-content">
            <div class="mini-player-info">
                <h4>${title}</h4>
                <p>${artist}</p>
            </div>
            <div class="mini-player-controls">
                <button onclick="pauseTrack()" class="mini-pause-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                </button>
                <button onclick="removeMiniPlayer()" class="mini-close-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            <div id="mini-youtube-player" style="display: none;"></div>
        </div>
    `;
    
    document.body.appendChild(miniPlayer);
    
    // Cargar YouTube Player API si no existe
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
        window.onYouTubeIframeAPIReady = () => initYouTubePlayer(videoId);
    } else {
        initYouTubePlayer(videoId);
    }
}

function initYouTubePlayer(videoId) {
    currentPlayer = new YT.Player('mini-youtube-player', {
        height: '0',
        width: '0',
        videoId: videoId,
        playerVars: {
            'autoplay': 1,
            'controls': 0,
            'disablekb': 1,
            'fs': 0,
            'modestbranding': 1,
            'rel': 0
        },
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.ENDED) {
        pauseTrack();
    }
}

function removeMiniPlayer() {
    const miniPlayer = document.getElementById('mini-soundtrack-player');
    if (miniPlayer) {
        miniPlayer.remove();
    }
    currentPlayer = null;
}

// Estilos para el mini reproductor
const miniPlayerStyles = `
<style>
#mini-soundtrack-player {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(20, 20, 20, 0.95);
    border: 1px solid rgba(0, 212, 255, 0.5);
    border-radius: 8px;
    padding: 1rem;
    max-width: 280px;
    z-index: 1000;
    backdrop-filter: blur(10px);
    animation: slideInUp 0.3s ease;
}

.mini-player-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mini-player-info {
    flex: 1;
    min-width: 0;
}

.mini-player-info h4 {
    color: white;
    font-size: 0.9rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mini-player-info p {
    color: #00d4ff;
    font-size: 0.8rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mini-player-controls {
    display: flex;
    gap: 0.5rem;
}

.mini-pause-btn, .mini-close-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mini-pause-btn:hover, .mini-close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(0, 212, 255, 0.5);
}

@keyframes slideInUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.soundtrack-card.playing {
    background: rgba(0, 212, 255, 0.1) !important;
    border-color: rgba(0, 212, 255, 0.5) !important;
}

@media (max-width: 768px) {
    #mini-soundtrack-player {
        bottom: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
`;

// Agregar estilos al head
if (!document.getElementById('mini-player-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'mini-player-styles';
    styleElement.innerHTML = miniPlayerStyles;
    document.head.appendChild(styleElement);
}
</script>
@endif