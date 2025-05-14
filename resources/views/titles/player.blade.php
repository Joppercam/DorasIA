<x-app-layout>
    <x-slot name="title">
        @if($episode)
            {{ $title->title }} - S{{ $season->number }}E{{ $episode->number }} - {{ $episode->title }}
        @else
            {{ $title->title }}
        @endif
    </x-slot>
    <x-slot name="pageClass">video-player-page</x-slot>
    <x-slot name="transitionType">zoom</x-slot>
    
    <!-- Video Player Container -->
    <div class="bg-black">
        <div class="max-w-7xl mx-auto">
            <div x-data="{ 
                controls: true, 
                progress: 0,
                volume: 100,
                isPaused: false,
                isFullscreen: false,
                showControls: true,
                videoTime: 0,
                videoDuration: 0,
                formatTime(seconds) {
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = Math.floor(seconds % 60);
                    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                },
                saveProgress() {
                    const video = document.getElementById('video-player');
                    if (!video) return;
                    
                    const watched = Math.floor(video.currentTime);
                    const total = Math.floor(video.duration);
                    const watchedPercent = (watched / total) * 100;
                    
                    fetch('{{ route('watch-history.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            @if($episode)
                            episode_id: {{ $episode->id }},
                            @else
                            title_id: {{ $title->id }},
                            @endif
                            watched_seconds: watched,
                            completed: watchedPercent > 90
                        })
                    })
                    .catch(error => console.error('Error saving progress:', error));
                },
                togglePlay() {
                    const video = document.getElementById('video-player');
                    if (video.paused) {
                        video.play();
                        this.isPaused = false;
                    } else {
                        video.pause();
                        this.isPaused = true;
                    }
                },
                toggleFullscreen() {
                    const player = document.getElementById('player-container');
                    if (!document.fullscreenElement) {
                        player.requestFullscreen().catch(err => {
                            console.error('Error attempting to enable fullscreen:', err);
                        });
                        this.isFullscreen = true;
                    } else {
                        document.exitFullscreen();
                        this.isFullscreen = false;
                    }
                },
                updateProgress() {
                    const video = document.getElementById('video-player');
                    this.progress = (video.currentTime / video.duration) * 100;
                    this.videoTime = video.currentTime;
                    this.videoDuration = video.duration;
                    
                    // Guardar progreso cada 10 segundos
                    if (Math.floor(video.currentTime) % 10 === 0) {
                        this.saveProgress();
                    }
                },
                setupVideoListeners() {
                    const video = document.getElementById('video-player');
                    
                    video.addEventListener('timeupdate', () => this.updateProgress());
                    
                    video.addEventListener('ended', () => {
                        this.isPaused = true;
                        this.saveProgress(); // Save final progress
                        
                        // Redirect to next episode if available
                        @if($episode && isset($nextEpisode))
                        setTimeout(() => {
                            window.location.href = '{{ route('titles.watch', [$title->slug, $season->number, $nextEpisode->number]) }}';
                        }, 3000);
                        @endif
                    });
                    
                    video.addEventListener('play', () => {
                        this.isPaused = false;
                    });
                    
                    video.addEventListener('pause', () => {
                        this.isPaused = true;
                    });
                    
                    // Hide controls after 3 seconds of inactivity
                    let timeout;
                    const resetTimeout = () => {
                        this.showControls = true;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            if (!this.isPaused) {
                                this.showControls = false;
                            }
                        }, 3000);
                    };
                    
                    document.getElementById('player-container').addEventListener('mousemove', resetTimeout);
                    document.getElementById('player-container').addEventListener('mousedown', resetTimeout);
                    resetTimeout();
                }
            }" 
            x-init="setupVideoListeners()"
            class="relative bg-black">
                <!-- Video Player Container -->
                <div 
                    id="player-container" 
                    class="relative cursor-pointer w-full aspect-video bg-black" 
                    @click="togglePlay()">
                    
                    <!-- Video Element -->
                    <video 
                        id="video-player"
                        class="w-full h-full"
                        @click.stop="togglePlay()"
                        @dblclick.stop="toggleFullscreen()"
                        preload="metadata"
                        autoplay>
                        
                        <!-- Fuentes de video (normalmente se agregarían en un entorno de producción) -->
                        <source src="{{ asset('videos/sample.mp4') }}" type="video/mp4">
                        Tu navegador no soporta el elemento de video.
                    </video>
                    
                    @if(isset($startTime) && $startTime > 0)
                    <script>
                        // Set video to start at the saved position
                        document.addEventListener('DOMContentLoaded', function() {
                            const video = document.getElementById('video-player');
                            if (video) {
                                video.addEventListener('loadedmetadata', function() {
                                    video.currentTime = {{ $startTime }};
                                });
                            }
                        });
                    </script>
                    @endif
                    
                    <!-- Controles personalizados (visible cuando showControls es true) -->
                    <div 
                        x-show="showControls"
                        @click.stop
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30 flex flex-col justify-between">
                        
                        <!-- Header -->
                        <div class="p-4 flex justify-between items-center">
                            <div>
                                <button class="text-white hover:text-red-500" @click="window.location.href='{{ route('titles.show', $title->slug) }}'">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-white text-lg font-medium">
                                @if($episode)
                                    {{ $title->title }} - S{{ $season->number }}E{{ $episode->number }}
                                @else
                                    {{ $title->title }}
                                @endif
                            </div>
                            <div></div> <!-- Placeholder para mantener el header centrado -->
                        </div>
                        
                        <!-- Controles principales -->
                        <div class="p-4">
                            <!-- Barra de progreso -->
                            <div class="relative h-1 bg-gray-600 rounded-full mb-4 cursor-pointer"
                                @click.stop="
                                    const video = document.getElementById('video-player');
                                    const rect = $el.getBoundingClientRect();
                                    const pos = (event.clientX - rect.left) / rect.width;
                                    video.currentTime = pos * video.duration;
                                ">
                                <div class="absolute left-0 top-0 h-full bg-red-600 rounded-full" :style="`width: ${progress}%`"></div>
                                <div class="absolute h-3 w-3 bg-red-600 rounded-full -top-1 -ml-1.5" :style="`left: ${progress}%`"></div>
                            </div>
                            
                            <!-- Controles inferiores -->
                            <div class="flex justify-between items-center">
                                <!-- Control de reproducción -->
                                <div class="flex items-center gap-4">
                                    <!-- Play/Pause -->
                                    <button @click.stop="togglePlay()" class="text-white hover:text-red-500">
                                        <svg x-show="isPaused" class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg x-show="!isPaused" class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Tiempos de video -->
                                    <div class="text-white text-sm">
                                        <span x-text="formatTime(videoTime)"></span>
                                        <span> / </span>
                                        <span x-text="formatTime(videoDuration)"></span>
                                    </div>
                                    
                                    <!-- Control de volumen -->
                                    <div class="hidden sm:flex items-center gap-2 group">
                                        <button @click.stop="
                                            const video = document.getElementById('video-player');
                                            video.muted = !video.muted;
                                            volume = video.muted ? 0 : 100;
                                            video.volume = volume / 100;
                                        " class="text-white hover:text-red-500">
                                            <svg x-show="volume > 0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                            </svg>
                                            <svg x-show="volume === 0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                                            </svg>
                                        </button>
                                        
                                        <div class="relative w-16 h-1 bg-gray-600 rounded-full cursor-pointer"
                                            @click.stop="
                                                const video = document.getElementById('video-player');
                                                const rect = $el.getBoundingClientRect();
                                                const pos = (event.clientX - rect.left) / rect.width;
                                                volume = Math.round(pos * 100);
                                                video.volume = volume / 100;
                                                video.muted = volume === 0;
                                            ">
                                            <div class="absolute left-0 top-0 h-full bg-white rounded-full" :style="`width: ${volume}%`"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Controles secundarios -->
                                <div class="flex items-center gap-4">
                                    <!-- Episodios (solo para series) -->
                                    @if($episode)
                                        <button @click.stop="window.location.href='{{ route('titles.show', $title->slug) }}'" class="text-white hover:text-red-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <!-- Pantalla completa -->
                                    <button @click.stop="toggleFullscreen()" class="text-white hover:text-red-500">
                                        <svg x-show="!isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                                        </svg>
                                        <svg x-show="isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Información del episodio (para series) -->
    @if($episode)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-gray-900 rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-1">{{ $episode->title }}</h1>
                <div class="flex items-center text-gray-400 mb-4">
                    <span>T{{ $season->number }}:E{{ $episode->number }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $episode->duration }} min</span>
                </div>
                <p class="text-gray-300">{{ $episode->synopsis }}</p>
                
                <!-- Navegación de episodios -->
                <div class="mt-6 flex justify-between items-center">
                    @if($prevEpisode)
                        <a href="{{ route('titles.watch', [$title->slug, $season->number, $prevEpisode->number]) }}" class="inline-flex items-center text-sm text-gray-400 hover:text-white">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Episodio anterior
                        </a>
                    @else
                        <div></div> <!-- Placeholder para mantener el layout -->
                    @endif
                    
                    <a href="{{ route('titles.show', $title->slug) }}" class="text-sm text-gray-400 hover:text-white">
                        Ver todos los episodios
                    </a>
                    
                    @if($nextEpisode)
                        <a href="{{ route('titles.watch', [$title->slug, $season->number, $nextEpisode->number]) }}" class="inline-flex items-center text-sm text-gray-400 hover:text-white">
                            Episodio siguiente
                            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <div></div> <!-- Placeholder para mantener el layout -->
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Información de la película -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-gray-900 rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-1">{{ $title->title }}</h1>
                <div class="flex items-center text-gray-400 mb-4">
                    <span>{{ $title->release_year }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $title->duration }} min</span>
                </div>
                <p class="text-gray-300">{{ $title->synopsis }}</p>
                
                <div class="mt-6">
                    <a href="{{ route('titles.show', $title->slug) }}" class="text-sm text-gray-400 hover:text-white">
                        Volver a los detalles
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    @push('scripts')
    <script>
        // Lógica para manejar teclas de atajo
        document.addEventListener('keydown', function(e) {
            const video = document.getElementById('video-player');
            if (!video) return;
            
            // Espacio para play/pause
            if (e.code === 'Space') {
                e.preventDefault();
                if (video.paused) video.play();
                else video.pause();
            }
            
            // Flecha izquierda para retroceder 10 segundos
            if (e.code === 'ArrowLeft') {
                video.currentTime = Math.max(0, video.currentTime - 10);
            }
            
            // Flecha derecha para avanzar 10 segundos
            if (e.code === 'ArrowRight') {
                video.currentTime = Math.min(video.duration, video.currentTime + 10);
            }
            
            // F para pantalla completa
            if (e.code === 'KeyF') {
                const player = document.getElementById('player-container');
                if (!document.fullscreenElement) {
                    player.requestFullscreen().catch(err => {
                        console.error('Error attempting to enable fullscreen:', err);
                    });
                } else {
                    document.exitFullscreen();
                }
            }
            
            // M para silenciar
            if (e.code === 'KeyM') {
                video.muted = !video.muted;
            }
        });
    </script>
    @endpush
</x-app-layout>