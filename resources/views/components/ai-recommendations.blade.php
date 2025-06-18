{{-- 
    Componente de Recomendaciones IA - Dorasia
    Sistema inteligente que muestra recomendaciones personalizadas con explicaciones
--}}

<div class="ai-recommendations-container" x-data="aiRecommendations()" x-init="loadRecommendations()">
    <!-- Header con IA branding -->
    <div class="recommendations-header">
        <div class="ai-branding">
            <div class="ai-icon">🤖</div>
            <div class="ai-text">
                <h2>Recomendado para ti</h2>
                <p x-show="userProfile" class="ai-subtitle">
                    Basado en tu perfil: <span x-text="getPersonalityText()"></span>
                </p>
                <p x-show="!userProfile" class="ai-subtitle">
                    Descubre K-Dramas perfectos para ti
                </p>
            </div>
        </div>
        
        <div class="refresh-controls">
            <button @click="refreshRecommendations()" 
                    :disabled="loading"
                    class="refresh-btn">
                <span x-show="!loading">🔄 Actualizar</span>
                <span x-show="loading" class="loading-spinner">⚡ Generando...</span>
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="recommendations-loading">
        <div class="ai-thinking">
            <div class="thinking-dots">
                <span></span><span></span><span></span>
            </div>
            <p>La IA está analizando tus gustos...</p>
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="recommendations-error">
        <div class="error-content">
            <span class="error-icon">⚠️</span>
            <p>No pudimos generar recomendaciones en este momento</p>
            <button @click="loadRecommendations()" class="retry-btn">Reintentar</button>
        </div>
    </div>

    <!-- Recommendations Grid -->
    <div x-show="!loading && !error && recommendations.length > 0" class="recommendations-grid">
        <template x-for="(item, index) in recommendations" :key="item.id">
            <div class="recommendation-card" 
                 :class="{'featured': index === 0}"
                 @click="openSeries(item.id)">
                
                <!-- Poster -->
                <div class="card-poster">
                    <img :src="item.poster_path || '/images/no-poster.jpg'" 
                         :alt="item.title"
                         loading="lazy">
                    
                    <!-- AI Score Badge -->
                    <div class="ai-score-badge" :class="getScoreClass(item.score)">
                        <span class="score-icon">🎯</span>
                        <span class="score-text" x-text="Math.round(item.score * 100) + '%'"></span>
                    </div>
                    
                    <!-- Reason Badge -->
                    <div class="reason-badge" :class="getReasonClass(item.reasons)">
                        <span x-text="getReasonText(item.reasons)"></span>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="card-content">
                    <h3 class="card-title" x-text="item.title"></h3>
                    
                    <div class="card-meta">
                        <span class="rating">⭐ <span x-text="item.rating"></span></span>
                        <span class="year" x-text="item.year"></span>
                        <span class="episodes" x-text="item.episodes + ' eps'"></span>
                    </div>
                    
                    <p class="card-overview" x-text="item.overview"></p>
                    
                    <!-- Genres Tags -->
                    <div class="genres-tags" x-show="item.genres">
                        <template x-for="genre in getGenresArray(item.genres)">
                            <span class="genre-tag" x-text="genre"></span>
                        </template>
                    </div>
                    
                    <!-- AI Explanation -->
                    <div class="ai-explanation">
                        <div class="explanation-header">
                            <span class="ai-icon-small">🧠</span>
                            <span>¿Por qué te recomendamos esto?</span>
                        </div>
                        <p class="explanation-text" x-text="getExplanationText(item)"></p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-actions">
                    <button @click.stop="addToWatchlist(item.id)" 
                            class="action-btn watchlist-btn"
                            :class="{'added': item.in_watchlist}">
                        <span x-show="!item.in_watchlist">📋 Lista</span>
                        <span x-show="item.in_watchlist">✅ En lista</span>
                    </button>
                    
                    <button @click.stop="toggleDetails(item.id)" 
                            class="action-btn details-btn">
                        👁️ Ver más
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && !error && recommendations.length === 0" class="recommendations-empty">
        <div class="empty-content">
            <div class="empty-icon">🎬</div>
            <h3>¡Empecemos!</h3>
            <p>Califica algunas series para recibir recomendaciones personalizadas</p>
            <a href="/explorar" class="explore-btn">Explorar Series</a>
        </div>
    </div>

    <!-- User Profile Insights (if available) -->
    <div x-show="userProfile && !loading" class="profile-insights">
        <div class="insights-header">
            <h3>🎯 Tu perfil de K-Drama</h3>
            <button @click="showInsights = !showInsights" class="toggle-insights">
                <span x-show="!showInsights">Ver detalles</span>
                <span x-show="showInsights">Ocultar</span>
            </button>
        </div>
        
        <div x-show="showInsights" class="insights-content" x-transition>
            <div class="insight-cards">
                <div class="insight-card">
                    <div class="insight-icon">🎭</div>
                    <div class="insight-text">
                        <h4>Géneros favoritos</h4>
                        <p x-text="userProfile.favorite_genres ? userProfile.favorite_genres.join(', ') : 'Aún no determinados'"></p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">⭐</div>
                    <div class="insight-text">
                        <h4>Tu rating promedio</h4>
                        <p x-text="userProfile.avg_rating ? userProfile.avg_rating + '/5' : 'N/A'"></p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">📺</div>
                    <div class="insight-text">
                        <h4>Prefieres series</h4>
                        <p x-text="userProfile.prefers_short_series ? 'Cortas (≤16 eps)' : 'Largas (>16 eps)'"></p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">🧬</div>
                    <div class="insight-text">
                        <h4>Tu personalidad</h4>
                        <p x-text="userProfile.personality ? userProfile.personality.join(', ') : 'Analizando...'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ai-recommendations-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.recommendations-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%);
    border-radius: 16px;
    border: 1px solid rgba(0, 212, 255, 0.2);
}

.ai-branding {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ai-icon {
    font-size: 3rem;
    animation: aiPulse 2s ease-in-out infinite alternate;
}

@keyframes aiPulse {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.ai-text h2 {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.ai-subtitle {
    color: #ccc;
    margin: 0.5rem 0 0 0;
    font-size: 0.9rem;
}

.refresh-btn {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.refresh-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

.refresh-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.recommendations-loading {
    text-align: center;
    padding: 4rem 2rem;
}

.ai-thinking {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.thinking-dots {
    display: flex;
    gap: 0.5rem;
}

.thinking-dots span {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    animation: thinking 1.4s ease-in-out infinite both;
}

.thinking-dots span:nth-child(1) { animation-delay: -0.32s; }
.thinking-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes thinking {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.recommendation-card {
    background: rgba(20, 20, 20, 0.8);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.recommendation-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 212, 255, 0.2);
    border-color: rgba(0, 212, 255, 0.4);
}

.recommendation-card.featured {
    grid-column: span 2;
    border: 2px solid #00d4ff;
    box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
}

.card-poster {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.card-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.recommendation-card:hover .card-poster img {
    transform: scale(1.05);
}

.ai-score-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.8);
    padding: 0.5rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.ai-score-badge.high { border: 1px solid #00ff88; color: #00ff88; }
.ai-score-badge.medium { border: 1px solid #00d4ff; color: #00d4ff; }
.ai-score-badge.low { border: 1px solid #ffa500; color: #ffa500; }

.reason-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.reason-badge.content { background: rgba(0, 212, 255, 0.8); color: white; }
.reason-badge.collaborative { background: rgba(123, 104, 238, 0.8); color: white; }
.reason-badge.trending { background: rgba(255, 165, 0, 0.8); color: white; }

.card-content {
    padding: 1.5rem;
}

.card-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 0.8rem 0;
    color: white;
    line-height: 1.3;
}

.card-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.8rem;
    color: #ccc;
}

.card-overview {
    color: #aaa;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 1rem;
}

.genres-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.genre-tag {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    color: #ccc;
}

.ai-explanation {
    background: rgba(0, 212, 255, 0.05);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.explanation-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #00d4ff;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
}

.explanation-text {
    font-size: 0.8rem;
    color: #ccc;
    line-height: 1.4;
    margin: 0;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
    padding: 0 1.5rem 1.5rem;
}

.action-btn {
    flex: 1;
    padding: 0.6rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.05);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.watchlist-btn.added {
    background: rgba(0, 255, 136, 0.2);
    border-color: #00ff88;
    color: #00ff88;
}

.profile-insights {
    background: rgba(20, 20, 20, 0.6);
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid rgba(0, 212, 255, 0.2);
}

.insights-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.insights-header h3 {
    margin: 0;
    color: white;
}

.toggle-insights {
    background: none;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.8rem;
}

.insight-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.insight-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 12px;
}

.insight-icon {
    font-size: 1.5rem;
}

.insight-text h4 {
    margin: 0 0 0.3rem 0;
    color: white;
    font-size: 0.9rem;
}

.insight-text p {
    margin: 0;
    color: #ccc;
    font-size: 0.8rem;
}

.recommendations-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-content h3 {
    color: white;
    margin-bottom: 1rem;
}

.empty-content p {
    color: #ccc;
    margin-bottom: 2rem;
}

.explore-btn {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.explore-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

@media (max-width: 768px) {
    .ai-recommendations-container {
        padding: 1rem;
    }
    
    .recommendations-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .recommendations-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .recommendation-card.featured {
        grid-column: span 1;
    }
    
    .insight-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function aiRecommendations() {
    return {
        loading: false,
        error: false,
        recommendations: [],
        userProfile: null,
        showInsights: false,
        
        async loadRecommendations() {
            this.loading = true;
            this.error = false;
            
            try {
                const response = await fetch('/api/recommendations?limit=12', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Network error');
                
                const data = await response.json();
                
                if (data.success) {
                    this.recommendations = data.recommendations || [];
                    this.userProfile = data.user_profile;
                } else {
                    this.error = true;
                }
                
            } catch (error) {
                console.error('Error loading recommendations:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },
        
        async refreshRecommendations() {
            await this.loadRecommendations();
        },
        
        openSeries(seriesId) {
            window.location.href = `/series/${seriesId}`;
        },
        
        async addToWatchlist(seriesId) {
            // Implementation for adding to watchlist
            console.log('Adding to watchlist:', seriesId);
        },
        
        toggleDetails(seriesId) {
            // Implementation for showing more details
            console.log('Show details for:', seriesId);
        },
        
        getPersonalityText() {
            if (!this.userProfile?.personality) return '';
            return this.userProfile.personality.join(', ');
        },
        
        getScoreClass(score) {
            if (score >= 0.8) return 'high';
            if (score >= 0.6) return 'medium';
            return 'low';
        },
        
        getReasonClass(reasons) {
            if (!reasons || !reasons.length) return 'content';
            const primaryReason = reasons[0];
            return primaryReason.replace('-', '');
        },
        
        getReasonText(reasons) {
            if (!reasons || !reasons.length) return 'IA';
            
            const reasonMap = {
                'content-based': 'Por tus gustos',
                'collaborative': 'Usuarios similares',
                'trending': 'Trending',
                'popular': 'Popular'
            };
            
            return reasonMap[reasons[0]] || 'IA';
        },
        
        getGenresArray(genres) {
            if (!genres) return [];
            return genres.split(',').map(g => g.trim()).slice(0, 3);
        },
        
        getExplanationText(item) {
            const reasons = item.reasons || [];
            const score = Math.round(item.score * 100);
            
            let explanation = `Coincidencia del ${score}% con tu perfil. `;
            
            if (reasons.includes('content-based')) {
                explanation += 'Te gusta este tipo de contenido. ';
            }
            
            if (reasons.includes('collaborative')) {
                explanation += 'Usuarios con gustos similares lo califican muy bien. ';
            }
            
            if (reasons.includes('trending')) {
                explanation += 'Está siendo muy popular últimamente. ';
            }
            
            if (item.rating >= 8.5) {
                explanation += 'Además, tiene excelentes calificaciones.';
            }
            
            return explanation;
        }
    }
}
</script>