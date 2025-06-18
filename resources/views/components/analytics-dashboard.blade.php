{{-- 
    Componente de Dashboard de Analytics - Dorasia
    Panel administrativo con métricas completas y visualizaciones
--}}

<div class="analytics-dashboard" x-data="analyticsDashboard()" x-init="loadDashboard()">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="dashboard-title">
                <h1>📊 Analytics Dashboard</h1>
                <p class="dashboard-subtitle">Métricas completas y insights de Dorasia</p>
            </div>
            
            <div class="dashboard-controls">
                <select x-model="timeRange" @change="loadDashboard()" class="time-range-select">
                    <option value="7days">Últimos 7 días</option>
                    <option value="30days">Últimos 30 días</option>
                    <option value="90days">Últimos 90 días</option>
                    <option value="1year">Último año</option>
                </select>
                
                <button @click="loadDashboard()" 
                        :disabled="loading"
                        class="refresh-btn">
                    <span x-show="!loading">🔄 Actualizar</span>
                    <span x-show="loading" class="loading-spin">⚡ Cargando...</span>
                </button>
            </div>
        </div>
        
        <div class="last-updated" x-show="lastUpdated">
            <span x-text="'Última actualización: ' + formatDateTime(lastUpdated)"></span>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading && !dashboardData" class="dashboard-loading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Generando análisis con IA...</p>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div x-show="!loading || dashboardData" class="dashboard-content">
        
        <!-- Overview Cards -->
        <div class="overview-section">
            <h2 class="section-title">📈 Vista General</h2>
            <div class="overview-cards" x-show="dashboardData?.overview">
                <div class="metric-card highlight">
                    <div class="metric-icon">👥</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="dashboardData?.overview?.total_users || 0"></div>
                        <div class="metric-label">Usuarios Totales</div>
                        <div class="metric-change positive" x-show="dashboardData?.overview?.new_users">
                            +<span x-text="dashboardData.overview.new_users"></span> nuevos
                        </div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">📺</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="dashboardData?.overview?.total_series || 0"></div>
                        <div class="metric-label">Series en Catálogo</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">⭐</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="dashboardData?.overview?.avg_rating || 0"></div>
                        <div class="metric-label">Rating Promedio</div>
                        <div class="metric-change" x-text="(dashboardData?.overview?.total_ratings || 0) + ' calificaciones'"></div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">📋</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="dashboardData?.overview?.total_watchlists || 0"></div>
                        <div class="metric-label">En Listas</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">📈</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="(dashboardData?.overview?.completion_rate || 0) + '%'"></div>
                        <div class="metric-label">Tasa de Finalización</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">🔄</div>
                    <div class="metric-content">
                        <div class="metric-value" x-text="(dashboardData?.overview?.user_retention || 0) + '%'"></div>
                        <div class="metric-label">Retención de Usuarios</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="charts-grid">
                
                <!-- User Activity Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>👥 Actividad de Usuarios</h3>
                        <div class="chart-legend">
                            <span class="legend-item active">
                                <span class="legend-color" style="background: #00d4ff;"></span>
                                Usuarios Activos Diarios
                            </span>
                        </div>
                    </div>
                    <div class="chart-content">
                        <canvas x-ref="userActivityChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Ratings Activity Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>⭐ Actividad de Calificaciones</h3>
                        <div class="chart-legend">
                            <span class="legend-item">
                                <span class="legend-color" style="background: #7b68ee;"></span>
                                Calificaciones por Día
                            </span>
                        </div>
                    </div>
                    <div class="chart-content">
                        <canvas x-ref="ratingsChart" width="400" height="200"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <!-- Content Analysis -->
        <div class="content-section">
            <h2 class="section-title">🎬 Análisis de Contenido</h2>
            
            <div class="content-grid">
                <!-- Most Rated Series -->
                <div class="content-widget">
                    <div class="widget-header">
                        <h3>🔥 Series Más Calificadas</h3>
                        <span class="widget-period" x-text="getTimeRangeText()"></span>
                    </div>
                    <div class="widget-content">
                        <div class="series-list" x-show="dashboardData?.content?.most_rated_series">
                            <template x-for="(series, index) in (dashboardData?.content?.most_rated_series || [])" :key="series.id">
                                <div class="series-item">
                                    <div class="series-rank" x-text="index + 1"></div>
                                    <div class="series-poster">
                                        <img :src="series.poster_path || '/images/no-poster.jpg'" 
                                             :alt="series.title"
                                             loading="lazy">
                                    </div>
                                    <div class="series-info">
                                        <div class="series-title" x-text="series.title"></div>
                                        <div class="series-stats">
                                            <span class="rating">⭐ <span x-text="series.avg_rating"></span></span>
                                            <span class="count"><span x-text="series.rating_count"></span> calificaciones</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Genre Popularity -->
                <div class="content-widget">
                    <div class="widget-header">
                        <h3>🎭 Popularidad por Género</h3>
                    </div>
                    <div class="widget-content">
                        <div class="genre-chart" x-show="dashboardData?.content?.genre_popularity">
                            <template x-for="(count, genre) in (dashboardData?.content?.genre_popularity || {})" :key="genre">
                                <div class="genre-item">
                                    <div class="genre-name" x-text="genre"></div>
                                    <div class="genre-bar">
                                        <div class="genre-fill" 
                                             :style="'width: ' + getGenrePercentage(count, dashboardData.content.genre_popularity) + '%'">
                                        </div>
                                    </div>
                                    <div class="genre-count" x-text="count"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="content-widget">
                    <div class="widget-header">
                        <h3>📊 Distribución de Ratings</h3>
                    </div>
                    <div class="widget-content">
                        <div class="rating-distribution" x-show="dashboardData?.content?.rating_distribution">
                            <template x-for="(count, rating) in (dashboardData?.content?.rating_distribution || {})" :key="rating">
                                <div class="rating-bar">
                                    <div class="rating-label" x-text="rating + ' ⭐'"></div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar-fill" 
                                             :style="'width: ' + getRatingPercentage(count, dashboardData.content.rating_distribution) + '%'">
                                        </div>
                                    </div>
                                    <div class="rating-count" x-text="count"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Insights -->
        <div class="users-section">
            <h2 class="section-title">👤 Insights de Usuarios</h2>
            
            <div class="users-grid">
                <!-- User Engagement Levels -->
                <div class="users-widget">
                    <div class="widget-header">
                        <h3>💪 Niveles de Engagement</h3>
                    </div>
                    <div class="widget-content">
                        <div class="engagement-chart" x-show="dashboardData?.users?.user_engagement_levels">
                            <template x-for="(data, level) in (dashboardData?.users?.user_engagement_levels || {})" :key="level">
                                <div class="engagement-item">
                                    <div class="engagement-icon" x-text="getEngagementIcon(level)"></div>
                                    <div class="engagement-info">
                                        <div class="engagement-label" x-text="getEngagementLabel(level)"></div>
                                        <div class="engagement-stats">
                                            <span class="engagement-count" x-text="data.count + ' usuarios'"></span>
                                            <span class="engagement-percentage" x-text="'(' + data.percentage + '%)'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Top Reviewers -->
                <div class="users-widget">
                    <div class="widget-header">
                        <h3>🏆 Top Reviewers</h3>
                        <span class="widget-period" x-text="getTimeRangeText()"></span>
                    </div>
                    <div class="widget-content">
                        <div class="reviewers-list" x-show="dashboardData?.users?.top_reviewers">
                            <template x-for="(reviewer, index) in (dashboardData?.users?.top_reviewers || [])" :key="reviewer.id">
                                <div class="reviewer-item">
                                    <div class="reviewer-rank" x-text="index + 1"></div>
                                    <div class="reviewer-avatar">
                                        <div class="avatar-placeholder" x-text="reviewer.name.charAt(0).toUpperCase()"></div>
                                    </div>
                                    <div class="reviewer-info">
                                        <div class="reviewer-name" x-text="reviewer.name"></div>
                                        <div class="reviewer-stats" x-text="reviewer.rating_count + ' calificaciones'"></div>
                                    </div>
                                    <div class="reviewer-badge">🌟</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI and Recommendations -->
        <div class="ai-section">
            <h2 class="section-title">🤖 IA y Recomendaciones</h2>
            
            <div class="ai-grid">
                <!-- Recommendation Performance -->
                <div class="ai-widget">
                    <div class="widget-header">
                        <h3>🎯 Performance de Recomendaciones</h3>
                    </div>
                    <div class="widget-content">
                        <div class="recommendation-metrics" x-show="dashboardData?.recommendations">
                            <div class="metric-row">
                                <div class="metric-name">Precisión del Algoritmo</div>
                                <div class="metric-value-bar">
                                    <div class="progress-bar">
                                        <div class="progress-fill" 
                                             :style="'width: ' + (dashboardData?.recommendations?.recommendation_accuracy || 0) + '%'">
                                        </div>
                                    </div>
                                    <span class="metric-percentage" x-text="(dashboardData?.recommendations?.recommendation_accuracy || 0) + '%'"></span>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-name">Tasa de Clicks</div>
                                <div class="metric-value-bar">
                                    <div class="progress-bar">
                                        <div class="progress-fill" 
                                             :style="'width: ' + (dashboardData?.recommendations?.click_through_rates?.rate_percent || 0) * 5 + '%'">
                                        </div>
                                    </div>
                                    <span class="metric-percentage" x-text="(dashboardData?.recommendations?.click_through_rates?.rate_percent || 0) + '%'"></span>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-name">Conversión</div>
                                <div class="metric-value-bar">
                                    <div class="progress-bar">
                                        <div class="progress-fill" 
                                             :style="'width: ' + (dashboardData?.recommendations?.conversion_rates?.rate_percent || 0) * 8 + '%'">
                                        </div>
                                    </div>
                                    <span class="metric-percentage" x-text="(dashboardData?.recommendations?.conversion_rates?.rate_percent || 0) + '%'"></span>
                                </div>
                            </div>

                            <div class="metric-row">
                                <div class="metric-name">Satisfacción del Usuario</div>
                                <div class="metric-value-bar">
                                    <div class="progress-bar">
                                        <div class="progress-fill" 
                                             :style="'width: ' + (dashboardData?.recommendations?.user_satisfaction?.satisfaction_score || 0) * 20 + '%'">
                                        </div>
                                    </div>
                                    <span class="metric-percentage" x-text="(dashboardData?.recommendations?.user_satisfaction?.satisfaction_score || 0) + '/5'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Insights -->
                <div class="ai-widget">
                    <div class="widget-header">
                        <h3>🧠 Insights de IA</h3>
                    </div>
                    <div class="widget-content">
                        <div class="ai-insights-list" x-show="dashboardData?.recommendations?.ai_insights">
                            <template x-for="insight in (dashboardData?.recommendations?.ai_insights?.insights || [])" :key="insight">
                                <div class="insight-item">
                                    <div class="insight-icon">💡</div>
                                    <div class="insight-text" x-text="insight"></div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="algorithm-performance" x-show="dashboardData?.recommendations?.algorithm_performance">
                            <h4>Performance por Algoritmo</h4>
                            <template x-for="(score, algorithm) in (dashboardData?.recommendations?.algorithm_performance || {})" :key="algorithm">
                                <div class="algorithm-item">
                                    <div class="algorithm-name" x-text="getAlgorithmName(algorithm)"></div>
                                    <div class="algorithm-score">
                                        <div class="score-bar">
                                            <div class="score-fill" :style="'width: ' + score + '%'"></div>
                                        </div>
                                        <span x-text="score + '%'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trending and Performance -->
        <div class="trending-section">
            <h2 class="section-title">🔥 Tendencias y Performance</h2>
            
            <div class="trending-grid">
                <!-- Trending Series -->
                <div class="trending-widget">
                    <div class="widget-header">
                        <h3>📈 Series en Tendencia</h3>
                        <span class="widget-period" x-text="getTimeRangeText()"></span>
                    </div>
                    <div class="widget-content">
                        <div class="trending-list" x-show="dashboardData?.trending?.trending_series">
                            <template x-for="(series, index) in (dashboardData?.trending?.trending_series || [])" :key="series.id">
                                <div class="trending-item">
                                    <div class="trending-rank">
                                        <span x-text="index + 1"></span>
                                        <div class="trending-indicator">🔥</div>
                                    </div>
                                    <div class="trending-poster">
                                        <img :src="series.poster_path || '/images/no-poster.jpg'" 
                                             :alt="series.title"
                                             loading="lazy">
                                    </div>
                                    <div class="trending-info">
                                        <div class="trending-title" x-text="series.title"></div>
                                        <div class="trending-activity" x-text="series.recent_activity + ' interacciones'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- System Performance -->
                <div class="performance-widget">
                    <div class="widget-header">
                        <h3>⚡ Performance del Sistema</h3>
                    </div>
                    <div class="widget-content">
                        <div class="performance-metrics" x-show="dashboardData?.performance">
                            <div class="perf-metric">
                                <div class="perf-icon">🌐</div>
                                <div class="perf-info">
                                    <div class="perf-label">Tiempo de Carga</div>
                                    <div class="perf-value" x-text="(dashboardData?.performance?.page_load_times?.avg_seconds || 0) + 's'"></div>
                                </div>
                                <div class="perf-status good" x-show="(dashboardData?.performance?.page_load_times?.avg_seconds || 0) < 3">✅</div>
                                <div class="perf-status warning" x-show="(dashboardData?.performance?.page_load_times?.avg_seconds || 0) >= 3">⚠️</div>
                            </div>

                            <div class="perf-metric">
                                <div class="perf-icon">🔧</div>
                                <div class="perf-info">
                                    <div class="perf-label">Respuesta API</div>
                                    <div class="perf-value" x-text="(dashboardData?.performance?.api_response_times?.avg_ms || 0) + 'ms'"></div>
                                </div>
                                <div class="perf-status good" x-show="(dashboardData?.performance?.api_response_times?.avg_ms || 0) < 500">✅</div>
                                <div class="perf-status warning" x-show="(dashboardData?.performance?.api_response_times?.avg_ms || 0) >= 500">⚠️</div>
                            </div>

                            <div class="perf-metric">
                                <div class="perf-icon">🚨</div>
                                <div class="perf-info">
                                    <div class="perf-label">Tasa de Errores</div>
                                    <div class="perf-value" x-text="(dashboardData?.performance?.error_rates?.rate_percent || 0) + '%'"></div>
                                </div>
                                <div class="perf-status good" x-show="(dashboardData?.performance?.error_rates?.rate_percent || 0) < 1">✅</div>
                                <div class="perf-status warning" x-show="(dashboardData?.performance?.error_rates?.rate_percent || 0) >= 1">⚠️</div>
                            </div>

                            <div class="perf-metric">
                                <div class="perf-icon">⏱️</div>
                                <div class="perf-info">
                                    <div class="perf-label">Uptime</div>
                                    <div class="perf-value" x-text="(dashboardData?.performance?.server_performance?.uptime_percent || 0) + '%'"></div>
                                </div>
                                <div class="perf-status good" x-show="(dashboardData?.performance?.server_performance?.uptime_percent || 0) > 99">✅</div>
                                <div class="perf-status warning" x-show="(dashboardData?.performance?.server_performance?.uptime_percent || 0) <= 99">⚠️</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="dashboard-error">
        <div class="error-content">
            <span class="error-icon">⚠️</span>
            <h3>Error al cargar el dashboard</h3>
            <p>No se pudieron obtener las métricas en este momento</p>
            <button @click="loadDashboard()" class="retry-btn">Reintentar</button>
        </div>
    </div>
</div>

<style>
.analytics-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: #0a0a0a;
    min-height: 100vh;
}

.dashboard-header {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.dashboard-title h1 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.dashboard-subtitle {
    color: #ccc;
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
}

.dashboard-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.time-range-select {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    padding: 0.7rem 1rem;
    font-size: 0.9rem;
}

.refresh-btn {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border: none;
    padding: 0.7rem 1.5rem;
    border-radius: 8px;
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

.loading-spin {
    animation: spin 1s linear infinite;
}

.last-updated {
    color: #aaa;
    font-size: 0.8rem;
}

.dashboard-loading {
    text-align: center;
    padding: 4rem 2rem;
}

.loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(0, 212, 255, 0.3);
    border-top: 4px solid #00d4ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.section-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 3rem 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.overview-section {
    margin-bottom: 3rem;
}

.overview-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.metric-card {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 212, 255, 0.2);
    border-color: rgba(0, 212, 255, 0.3);
}

.metric-card.highlight {
    border-color: rgba(0, 212, 255, 0.5);
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%);
}

.metric-icon {
    font-size: 3rem;
    line-height: 1;
}

.metric-content {
    flex: 1;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.metric-label {
    color: #ccc;
    font-size: 1rem;
    font-weight: 600;
}

.metric-change {
    color: #aaa;
    font-size: 0.8rem;
    margin-top: 0.3rem;
}

.metric-change.positive {
    color: #00ff88;
}

.charts-section {
    margin-bottom: 3rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.chart-container {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-header h3 {
    color: white;
    margin: 0;
    font-size: 1.2rem;
}

.chart-legend {
    display: flex;
    gap: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #ccc;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.chart-content {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    border: 2px dashed rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

.content-section,
.users-section,
.ai-section,
.trending-section {
    margin-bottom: 3rem;
}

.content-grid,
.users-grid,
.ai-grid,
.trending-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.content-widget,
.users-widget,
.ai-widget,
.trending-widget,
.performance-widget {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    overflow: hidden;
}

.widget-header {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-header h3 {
    color: white;
    margin: 0;
    font-size: 1.1rem;
}

.widget-period {
    color: #00d4ff;
    font-size: 0.8rem;
}

.widget-content {
    padding: 2rem;
}

.series-list,
.reviewers-list,
.trending-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.series-item,
.reviewer-item,
.trending-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.series-rank,
.reviewer-rank,
.trending-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.9rem;
}

.series-poster,
.trending-poster {
    width: 3rem;
    height: 4rem;
    border-radius: 6px;
    overflow: hidden;
}

.series-poster img,
.trending-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.series-info,
.reviewer-info,
.trending-info {
    flex: 1;
}

.series-title,
.reviewer-name,
.trending-title {
    color: white;
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.series-stats,
.reviewer-stats,
.trending-activity {
    color: #ccc;
    font-size: 0.8rem;
}

.reviewer-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    overflow: hidden;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.reviewer-badge {
    font-size: 1.5rem;
}

.genre-chart,
.rating-distribution {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.genre-item,
.rating-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.genre-name,
.rating-label {
    min-width: 6rem;
    color: white;
    font-size: 0.9rem;
}

.genre-bar,
.rating-bar-container {
    flex: 1;
    height: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.25rem;
    overflow: hidden;
}

.genre-fill,
.rating-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #00d4ff 0%, #7b68ee 100%);
    border-radius: 0.25rem;
    transition: width 0.5s ease;
}

.genre-count,
.rating-count {
    min-width: 3rem;
    text-align: right;
    color: #ccc;
    font-size: 0.8rem;
}

.engagement-chart {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.engagement-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.engagement-icon {
    font-size: 2rem;
}

.engagement-info {
    flex: 1;
}

.engagement-label {
    color: white;
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.engagement-stats {
    color: #ccc;
    font-size: 0.9rem;
}

.engagement-percentage {
    color: #00d4ff;
    margin-left: 0.5rem;
}

.recommendation-metrics,
.performance-metrics {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.metric-row,
.perf-metric {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.metric-name,
.perf-label {
    color: white;
    font-weight: 600;
    flex: 1;
}

.metric-value-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 2;
}

.progress-bar,
.score-bar {
    flex: 1;
    height: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.25rem;
    overflow: hidden;
}

.progress-fill,
.score-fill {
    height: 100%;
    background: linear-gradient(90deg, #00d4ff 0%, #7b68ee 100%);
    border-radius: 0.25rem;
    transition: width 0.5s ease;
}

.metric-percentage {
    min-width: 3rem;
    text-align: right;
    color: #00d4ff;
    font-weight: 600;
}

.ai-insights-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 212, 255, 0.05);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 8px;
}

.insight-icon {
    font-size: 1.2rem;
    margin-top: 0.1rem;
}

.insight-text {
    color: #ccc;
    line-height: 1.4;
}

.algorithm-performance h4 {
    color: white;
    margin: 0 0 1rem 0;
    font-size: 1rem;
}

.algorithm-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.algorithm-name {
    color: white;
    font-weight: 600;
    min-width: 8rem;
}

.algorithm-score {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.perf-icon {
    font-size: 1.5rem;
}

.perf-info {
    flex: 1;
}

.perf-value {
    color: white;
    font-weight: 600;
    margin-top: 0.2rem;
}

.perf-status {
    font-size: 1.2rem;
}

.dashboard-error {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 100, 100, 0.3);
    border-radius: 16px;
    margin: 2rem 0;
}

.error-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.error-icon {
    font-size: 3rem;
}

.error-content h3 {
    color: white;
    margin: 0;
}

.error-content p {
    color: #ccc;
    margin: 0;
}

.retry-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.retry-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
}

@media (max-width: 1200px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .content-grid,
    .users-grid,
    .ai-grid,
    .trending-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .analytics-dashboard {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .dashboard-controls {
        flex-direction: column;
        width: 100%;
    }
    
    .time-range-select,
    .refresh-btn {
        width: 100%;
    }
    
    .overview-cards {
        grid-template-columns: 1fr;
    }
    
    .metric-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
function analyticsDashboard() {
    return {
        // Dashboard state
        dashboardData: null,
        loading: false,
        error: false,
        timeRange: '30days',
        lastUpdated: null,
        
        async loadDashboard() {
            this.loading = true;
            this.error = false;
            
            try {
                const response = await fetch(`/api/analytics/dashboard?range=${this.timeRange}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Network error');
                
                const data = await response.json();
                
                if (data.success) {
                    this.dashboardData = data.dashboard;
                    this.lastUpdated = data.generated_at;
                    
                    // Initialize charts after data is loaded
                    this.$nextTick(() => {
                        this.initializeCharts();
                    });
                } else {
                    this.error = true;
                }
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },
        
        initializeCharts() {
            // Initialize user activity chart
            if (this.$refs.userActivityChart && this.dashboardData?.overview?.daily_active_users) {
                this.drawLineChart(
                    this.$refs.userActivityChart, 
                    this.dashboardData.overview.daily_active_users,
                    '#00d4ff'
                );
            }
            
            // Initialize ratings chart
            if (this.$refs.ratingsChart && this.dashboardData?.engagement?.rating_activity) {
                this.drawLineChart(
                    this.$refs.ratingsChart, 
                    this.dashboardData.engagement.rating_activity,
                    '#7b68ee'
                );
            }
        },
        
        drawLineChart(canvas, data, color) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            
            // Clear canvas
            ctx.clearRect(0, 0, width, height);
            
            if (!data || data.length === 0) {
                // Draw placeholder
                ctx.fillStyle = '#444';
                ctx.font = '14px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Sin datos disponibles', width / 2, height / 2);
                return;
            }
            
            // Find min/max values
            const values = data.map(d => d.count || d.ratings || 0);
            const maxValue = Math.max(...values);
            const minValue = Math.min(...values);
            const range = maxValue - minValue || 1;
            
            // Set up drawing parameters
            const padding = 40;
            const chartWidth = width - (padding * 2);
            const chartHeight = height - (padding * 2);
            const stepX = chartWidth / (data.length - 1);
            
            // Draw grid
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
            ctx.lineWidth = 1;
            
            for (let i = 0; i <= 5; i++) {
                const y = padding + (chartHeight * i / 5);
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(width - padding, y);
                ctx.stroke();
            }
            
            // Draw line
            ctx.strokeStyle = color;
            ctx.lineWidth = 3;
            ctx.beginPath();
            
            data.forEach((point, index) => {
                const x = padding + (index * stepX);
                const value = point.count || point.ratings || 0;
                const y = padding + chartHeight - ((value - minValue) / range * chartHeight);
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
            
            // Draw points
            ctx.fillStyle = color;
            data.forEach((point, index) => {
                const x = padding + (index * stepX);
                const value = point.count || point.ratings || 0;
                const y = padding + chartHeight - ((value - minValue) / range * chartHeight);
                
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fill();
            });
            
            // Draw labels
            ctx.fillStyle = '#ccc';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            
            // Y-axis labels
            for (let i = 0; i <= 5; i++) {
                const value = minValue + (range * i / 5);
                const y = padding + chartHeight - (chartHeight * i / 5);
                ctx.textAlign = 'right';
                ctx.fillText(Math.round(value), padding - 10, y + 4);
            }
            
            // X-axis labels (show every few points)
            const labelStep = Math.ceil(data.length / 6);
            data.forEach((point, index) => {
                if (index % labelStep === 0) {
                    const x = padding + (index * stepX);
                    const date = new Date(point.date).toLocaleDateString('es-ES', { 
                        month: 'short', 
                        day: 'numeric' 
                    });
                    ctx.textAlign = 'center';
                    ctx.fillText(date, x, height - 10);
                }
            });
        },
        
        formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        getTimeRangeText() {
            const ranges = {
                '7days': 'Últimos 7 días',
                '30days': 'Últimos 30 días',
                '90days': 'Últimos 90 días',
                '1year': 'Último año'
            };
            return ranges[this.timeRange] || 'Período personalizado';
        },
        
        getGenrePercentage(count, allGenres) {
            const maxCount = Math.max(...Object.values(allGenres));
            return (count / maxCount) * 100;
        },
        
        getRatingPercentage(count, allRatings) {
            const maxCount = Math.max(...Object.values(allRatings));
            return (count / maxCount) * 100;
        },
        
        getEngagementIcon(level) {
            const icons = {
                power_users: '🚀',
                active_users: '💪',
                casual_users: '👤'
            };
            return icons[level] || '👤';
        },
        
        getEngagementLabel(level) {
            const labels = {
                power_users: 'Power Users',
                active_users: 'Usuarios Activos',
                casual_users: 'Usuarios Casuales'
            };
            return labels[level] || level;
        },
        
        getAlgorithmName(algorithm) {
            const names = {
                content_based: 'Basado en Contenido',
                collaborative: 'Colaborativo',
                hybrid: 'Híbrido'
            };
            return names[algorithm] || algorithm;
        }
    }
}
</script>