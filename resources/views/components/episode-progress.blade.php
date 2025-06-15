@props(['series', 'showEpisodes' => true])

<div class="episode-progress-container bg-[#1a1a1a] rounded-lg p-6 mb-6" x-data="episodeProgress({ seriesId: {{ $series->id }} })">
    <!-- Progress Summary -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Progreso de la Serie</h3>
            <button 
                @click="toggleEpisodesView()" 
                class="text-[#00d4ff] hover:text-white transition-colors"
                x-show="episodes.length > 0"
            >
                <span x-text="showEpisodes ? 'Ocultar episodios' : 'Ver episodios'"></span>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-400 mb-2">
                <span>Progreso general</span>
                <span x-text="`${progress.completed_episodes || 0}/${progress.total_episodes || 0} episodios`"></span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-3">
                <div 
                    class="bg-gradient-to-r from-[#00d4ff] to-[#7b68ee] h-3 rounded-full transition-all duration-500"
                    :style="`width: ${progress.progress_percentage || 0}%`"
                ></div>
            </div>
            <div class="text-right text-sm text-gray-400 mt-1">
                <span x-text="`${progress.progress_percentage || 0}% completado`"></span>
            </div>
        </div>

        <!-- Next Episode -->
        <div x-show="progress.next_episode" class="bg-[#2a2a2a] rounded-lg p-4">
            <h4 class="text-white font-semibold mb-2">Próximo episodio:</h4>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <p class="text-white" x-text="`S${progress.next_episode?.season_number}E${progress.next_episode?.episode_number} - ${progress.next_episode?.name}`"></p>
                    <p class="text-gray-400 text-sm" x-text="progress.next_episode?.overview?.substring(0, 100) + '...'" x-show="progress.next_episode?.overview"></p>
                </div>
                <button 
                    @click="markEpisodeAsWatched(progress.next_episode?.id)"
                    class="bg-[#00d4ff] text-white px-4 py-2 rounded-lg hover:bg-[#00b8e6] transition-colors"
                    :disabled="loading"
                >
                    Marcar como visto
                </button>
            </div>
        </div>
    </div>

    <!-- Episodes List -->
    <div x-show="showEpisodes && episodes.length > 0" x-transition class="space-y-4">
        <h4 class="text-lg font-semibold text-white mb-4">Lista de Episodios</h4>
        
        <!-- Season Groups -->
        <template x-for="season in groupedEpisodes" :key="season.number">
            <div class="season-group">
                <h5 class="text-white font-semibold mb-3 text-lg" x-text="`Temporada ${season.number}`"></h5>
                
                <div class="space-y-2">
                    <template x-for="episode in season.episodes" :key="episode.id">
                        <div class="episode-item bg-[#2a2a2a] rounded-lg p-4 flex items-center gap-4 hover:bg-[#333] transition-colors">
                            <!-- Episode Image -->
                            <div class="w-16 h-12 bg-gray-600 rounded flex-shrink-0 overflow-hidden">
                                <img 
                                    :src="episode.still_path ? `https://image.tmdb.org/t/p/w300${episode.still_path}` : '/images/no-episode.jpg'"
                                    :alt="episode.name"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            </div>

                            <!-- Episode Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-gray-400 text-sm" x-text="`E${episode.episode_number}`"></span>
                                    <h6 class="text-white font-medium truncate" x-text="episode.name"></h6>
                                    <span class="text-gray-400 text-xs" x-text="episode.formatted_runtime"></span>
                                </div>
                                <p class="text-gray-400 text-sm line-clamp-2" x-text="episode.overview"></p>
                                
                                <!-- Progress Bar for Individual Episode -->
                                <div x-show="episode.user_progress && episode.user_progress.status === 'watching'" class="mt-2">
                                    <div class="w-full bg-gray-600 rounded-full h-1">
                                        <div 
                                            class="bg-[#00d4ff] h-1 rounded-full"
                                            :style="`width: ${episode.user_progress && episode.user_progress.progress_percentage ? episode.user_progress.progress_percentage : 0}%`"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Episode Actions -->
                            <div class="flex items-center gap-2">
                                <!-- Watch Status -->
                                <div class="flex items-center gap-2">
                                    <template x-if="episode.user_progress?.status === 'completed'">
                                        <span class="text-green-400 text-sm">✓ Visto</span>
                                    </template>
                                    <template x-if="episode.user_progress?.status === 'watching'">
                                        <span class="text-yellow-400 text-sm">⏸ En progreso</span>
                                    </template>
                                    <template x-if="!episode.user_progress || episode.user_progress?.status === 'not_started'">
                                        <span class="text-gray-400 text-sm">⚪ No visto</span>
                                    </template>
                                </div>

                                <!-- Action Button -->
                                <template x-if="episode.user_progress?.status === 'completed'">
                                    <button 
                                        @click="markEpisodeAsUnwatched(episode.id)"
                                        class="text-red-400 hover:text-red-300 transition-colors"
                                        :disabled="loading"
                                        title="Marcar como no visto"
                                    >
                                        ✗
                                    </button>
                                </template>
                                <template x-if="!episode.user_progress || episode.user_progress?.status !== 'completed'">
                                    <button 
                                        @click="markEpisodeAsWatched(episode.id)"
                                        class="text-green-400 hover:text-green-300 transition-colors"
                                        :disabled="loading"
                                        title="Marcar como visto"
                                    >
                                        ✓
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading Indicator -->
    <div x-show="loading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#00d4ff]"></div>
        <p class="text-gray-400 mt-2">Cargando...</p>
    </div>
</div>

<script>
function episodeProgress(config) {
    return {
        seriesId: config.seriesId,
        progress: {
            total_episodes: 0,
            completed_episodes: 0,
            watching_episodes: 0,
            progress_percentage: 0,
            next_episode: null
        },
        episodes: [],
        groupedEpisodes: [],
        showEpisodes: {{ $showEpisodes ? 'true' : 'false' }},
        loading: false,

        async init() {
            await this.loadProgress();
            await this.loadEpisodes();
        },

        async loadProgress() {
            try {
                const response = await fetch(`/series/${this.seriesId}/progress`);
                const data = await response.json();
                if (data.success) {
                    this.progress = data.progress;
                }
            } catch (error) {
                console.error('Error cargando progreso:', error);
            }
        },

        async loadEpisodes() {
            try {
                const response = await fetch(`/series/${this.seriesId}/episodes`);
                const data = await response.json();
                if (data.success) {
                    this.episodes = data.episodes;
                    this.groupEpisodesBySeason();
                }
            } catch (error) {
                console.error('Error cargando episodios:', error);
            }
        },

        groupEpisodesBySeason() {
            const seasons = {};
            this.episodes.forEach(episode => {
                if (!seasons[episode.season_number]) {
                    seasons[episode.season_number] = {
                        number: episode.season_number,
                        episodes: []
                    };
                }
                seasons[episode.season_number].episodes.push(episode);
            });
            this.groupedEpisodes = Object.values(seasons);
        },

        async markEpisodeAsWatched(episodeId) {
            if (this.loading) return;
            
            this.loading = true;
            try {
                const response = await fetch(`/episodes/${episodeId}/watched`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    await this.loadProgress();
                    await this.loadEpisodes();
                }
            } catch (error) {
                console.error('Error marcando episodio como visto:', error);
            } finally {
                this.loading = false;
            }
        },

        async markEpisodeAsUnwatched(episodeId) {
            if (this.loading) return;
            
            this.loading = true;
            try {
                const response = await fetch(`/episodes/${episodeId}/watched`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    await this.loadProgress();
                    await this.loadEpisodes();
                }
            } catch (error) {
                console.error('Error marcando episodio como no visto:', error);
            } finally {
                this.loading = false;
            }
        },

        toggleEpisodesView() {
            this.showEpisodes = !this.showEpisodes;
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>