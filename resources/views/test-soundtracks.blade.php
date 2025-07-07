<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎵 Test Soundtracks - Dorasia</title>
    <style>
        body {
            background: #1a1a1a;
            color: white;
            font-family: Arial, sans-serif;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .test-section {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .test-title {
            color: #00d4ff;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .play-btn {
            background: linear-gradient(135deg, #00d4ff, #7b68ee);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            cursor: pointer;
            margin-right: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .play-btn:hover {
            transform: scale(1.1);
        }
        .track-info {
            display: inline-block;
            vertical-align: middle;
        }
        .track-title {
            font-weight: bold;
            color: white;
        }
        .track-artist {
            color: #00d4ff;
            font-size: 0.9rem;
        }
        .youtube-id {
            font-size: 0.8rem;
            color: #888;
            margin-top: 0.5rem;
        }
        .status {
            margin-left: 1rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        .reproducible {
            background: rgba(70, 211, 105, 0.2);
            color: #46d369;
        }
        .not-reproducible {
            background: rgba(255, 99, 99, 0.2);
            color: #ff6363;
        }
        .now-playing {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            padding: 1rem;
            border-top: 2px solid #00d4ff;
            display: none;
            align-items: center;
            gap: 1rem;
            z-index: 1000;
        }
        .now-playing-info {
            flex: 1;
        }
        .close-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            cursor: pointer;
        }
        .instructions {
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎵 Test de Soundtracks Reproducibles</h1>
        
        <div class="instructions">
            <h3>📖 Instrucciones:</h3>
            <ol>
                <li>Haz clic en los botones ▶️ <strong>azules</strong> para reproducir canciones</li>
                <li>Las canciones con 🎵 <strong>SÍ son reproducibles</strong></li>
                <li>Las canciones con 🎶 <strong>NO son reproducibles</strong></li>
                <li>Aparecerá una barra "Now Playing" abajo cuando reproduzcas</li>
                <li>Abre la <strong>consola del navegador (F12)</strong> para ver logs</li>
            </ol>
        </div>

        <!-- Tu Nombre Soundtracks -->
        <div class="test-section">
            <div class="test-title">🎬 Tu Nombre - Soundtracks Reproducibles</div>
            
            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('PDSkFeMVNFs', 'Zenzenzense', 'RADWIMPS')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Zenzenzense</div>
                    <div class="track-artist">RADWIMPS</div>
                    <div class="youtube-id">YouTube ID: PDSkFeMVNFs</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>

            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('a2GujJZfXpg', 'Sparkle', 'RADWIMPS')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Sparkle</div>
                    <div class="track-artist">RADWIMPS</div>
                    <div class="youtube-id">YouTube ID: a2GujJZfXpg</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>

            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('9yGKGW43Ppk', 'Nandemonaiya', 'RADWIMPS')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Nandemonaiya</div>
                    <div class="track-artist">RADWIMPS</div>
                    <div class="youtube-id">YouTube ID: 9yGKGW43Ppk</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>
        </div>

        <!-- El juego del calamar Soundtracks -->
        <div class="test-section">
            <div class="test-title">📺 El juego del calamar - Soundtracks Reproducibles</div>
            
            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('IrHKiKCF7YU', 'Way Back Then', 'Jung Jae Il')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Way Back Then</div>
                    <div class="track-artist">Jung Jae Il</div>
                    <div class="youtube-id">YouTube ID: IrHKiKCF7YU</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>

            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('qza1RNS8wTI', 'Pink Soldiers', 'Jung Jae Il')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Pink Soldiers</div>
                    <div class="track-artist">Jung Jae Il</div>
                    <div class="youtube-id">YouTube ID: qza1RNS8wTI</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>
        </div>

        <!-- K-Pop Soundtracks -->
        <div class="test-section">
            <div class="test-title">🎤 K-Pop Soundtracks Adicionales</div>
            
            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('2S24-y0Ij3Y', 'Kill This Love', 'BLACKPINK')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Kill This Love</div>
                    <div class="track-artist">BLACKPINK</div>
                    <div class="youtube-id">YouTube ID: 2S24-y0Ij3Y</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>

            <div style="margin-bottom: 1rem;">
                <button class="play-btn" onclick="playTrack('gdZLi9oWNZg', 'Dynamite', 'BTS')">
                    ▶️
                </button>
                <div class="track-info">
                    <div class="track-title">Dynamite</div>
                    <div class="track-artist">BTS</div>
                    <div class="youtube-id">YouTube ID: gdZLi9oWNZg</div>
                </div>
                <span class="status reproducible">🎵 REPRODUCIBLE</span>
            </div>
        </div>

        <!-- Soundtrack sin YouTube ID -->
        <div class="test-section">
            <div class="test-title">🚫 Ejemplo de Soundtrack NO Reproducible</div>
            
            <div style="margin-bottom: 1rem;">
                <div class="track-info">
                    <div class="track-title">Love Theme</div>
                    <div class="track-artist">Various Artists</div>
                    <div class="youtube-id">YouTube ID: No disponible</div>
                </div>
                <span class="status not-reproducible">🎶 NO REPRODUCIBLE</span>
            </div>
        </div>
    </div>

    <!-- Now Playing Bar -->
    <div class="now-playing" id="nowPlaying">
        <div class="now-playing-info">
            <div id="nowPlayingTitle" style="font-weight: bold;"></div>
            <div id="nowPlayingArtist" style="color: #00d4ff; font-size: 0.9rem;"></div>
        </div>
        <button class="close-btn" onclick="closePlayer()">❌</button>
        <div id="youtube-player" style="display: none;"></div>
    </div>

    <script>
        let currentPlayer = null;

        function playTrack(youtubeId, title, artist) {
            console.log('🎵 Playing track:', title, 'by', artist, 'YouTube ID:', youtubeId);
            
            // Mostrar now playing
            showNowPlaying(title, artist);
            
            // Cargar YouTube API si no existe
            if (!window.YT) {
                console.log('📥 Loading YouTube API...');
                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                
                window.onYouTubeIframeAPIReady = () => {
                    console.log('✅ YouTube API loaded!');
                    initPlayer(youtubeId);
                };
            } else {
                initPlayer(youtubeId);
            }
        }

        function initPlayer(videoId) {
            console.log('🎬 Initializing YouTube player with video:', videoId);
            
            try {
                if (currentPlayer) {
                    currentPlayer.destroy();
                }
                
                currentPlayer = new YT.Player('youtube-player', {
                    height: '0',
                    width: '0',
                    videoId: videoId,
                    playerVars: {
                        'autoplay': 1,
                        'controls': 0,
                        'disablekb': 1,
                        'fs': 0,
                        'modestbranding': 1,
                        'rel': 0,
                        'playsinline': 1,
                        'origin': window.location.origin
                    },
                    events: {
                        'onReady': function(event) {
                            console.log('🎯 Player ready, starting playback...');
                            event.target.playVideo();
                        },
                        'onStateChange': function(event) {
                            console.log('🔄 Player state changed:', event.data);
                            if (event.data == YT.PlayerState.PLAYING) {
                                console.log('🎵 NOW PLAYING!');
                            } else if (event.data == YT.PlayerState.ENDED) {
                                console.log('⏹️ Playback ended');
                                closePlayer();
                            }
                        },
                        'onError': function(event) {
                            console.error('❌ YouTube player error:', event.data);
                            const errorMessages = {
                                2: 'ID de video inválido',
                                5: 'Error de reproductor HTML5',
                                100: 'Video no encontrado',
                                101: 'Video no disponible',
                                150: 'Video no disponible'
                            };
                            const message = errorMessages[event.data] || 'Error desconocido';
                            alert('Error: ' + message);
                            closePlayer();
                        }
                    }
                });
            } catch (error) {
                console.error('💥 Error creating player:', error);
                // Fallback: abrir en YouTube
                window.open(`https://www.youtube.com/watch?v=${videoId}`, '_blank');
                closePlayer();
            }
        }

        function showNowPlaying(title, artist) {
            const nowPlaying = document.getElementById('nowPlaying');
            const titleEl = document.getElementById('nowPlayingTitle');
            const artistEl = document.getElementById('nowPlayingArtist');
            
            titleEl.textContent = title;
            artistEl.textContent = artist;
            nowPlaying.style.display = 'flex';
        }

        function closePlayer() {
            console.log('🛑 Closing player...');
            
            if (currentPlayer) {
                try {
                    currentPlayer.stopVideo();
                } catch (e) {
                    console.log('Player already stopped');
                }
            }
            
            const nowPlaying = document.getElementById('nowPlaying');
            nowPlaying.style.display = 'none';
        }

        // Cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePlayer();
            }
        });

        console.log('🚀 Test page loaded! Click any ▶️ button to test playback.');
    </script>
</body>
</html>