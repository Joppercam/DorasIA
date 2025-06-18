{{-- 
    Componente de Estrategia TikTok - Dorasia
    Estrategia completa para @dorasia.cl con herramientas de contenido
--}}

<div class="tiktok-strategy" x-data="tiktokStrategy()" x-init="initStrategy()">
    <!-- Strategy Header -->
    <div class="strategy-header">
        <div class="header-content">
            <div class="tiktok-branding">
                <div class="tiktok-icon">📱</div>
                <div class="strategy-text">
                    <h1>Estrategia TikTok @dorasia.cl</h1>
                    <p class="strategy-subtitle">Plan integral para crecer en TikTok con contenido K-Drama</p>
                </div>
            </div>
            
            <div class="strategy-stats">
                <div class="stat-item">
                    <div class="stat-value" x-text="contentIdeas.length"></div>
                    <div class="stat-label">Ideas de Contenido</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" x-text="hashtagSets.length"></div>
                    <div class="stat-label">Sets de Hashtags</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" x-text="trendingTopics.length"></div>
                    <div class="stat-label">Tendencias Activas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="strategy-tabs">
        <button class="tab-btn" 
                :class="{'active': activeTab === 'overview'}"
                @click="activeTab = 'overview'">
            📊 Overview
        </button>
        <button class="tab-btn" 
                :class="{'active': activeTab === 'content'}"
                @click="activeTab = 'content'">
            🎬 Contenido
        </button>
        <button class="tab-btn" 
                :class="{'active': activeTab === 'hashtags'}"
                @click="activeTab = 'hashtags'">
            # Hashtags
        </button>
        <button class="tab-btn" 
                :class="{'active': activeTab === 'trends'}"
                @click="activeTab = 'trends'">
            🔥 Tendencias
        </button>
        <button class="tab-btn" 
                :class="{'active': activeTab === 'posting'}"
                @click="activeTab = 'posting'">
            📅 Planificación
        </button>
        <button class="tab-btn" 
                :class="{'active': activeTab === 'analytics'}"
                @click="activeTab = 'analytics'">
            📈 Analytics
        </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        
        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="tab-panel">
            <div class="overview-grid">
                
                <!-- Brand Identity -->
                <div class="strategy-card">
                    <div class="card-header">
                        <h3>🎭 Identidad de Marca</h3>
                    </div>
                    <div class="card-content">
                        <div class="brand-elements">
                            <div class="brand-item">
                                <div class="brand-label">Username</div>
                                <div class="brand-value">@dorasia.cl</div>
                            </div>
                            <div class="brand-item">
                                <div class="brand-label">Nicho</div>
                                <div class="brand-value">K-Dramas y Cultura Coreana</div>
                            </div>
                            <div class="brand-item">
                                <div class="brand-label">Público Objetivo</div>
                                <div class="brand-value">18-35 años, fanáticos K-Drama</div>
                            </div>
                            <div class="brand-item">
                                <div class="brand-label">Tono</div>
                                <div class="brand-value">Divertido, informativo, apasionado</div>
                            </div>
                        </div>
                        
                        <div class="brand-colors">
                            <h4>Paleta de Colores</h4>
                            <div class="color-palette">
                                <div class="color-item" style="background: #00d4ff;" title="Azul Dorasia"></div>
                                <div class="color-item" style="background: #7b68ee;" title="Púrpura"></div>
                                <div class="color-item" style="background: #ff6b6b;" title="Rosa/Rojo"></div>
                                <div class="color-item" style="background: #ffa500;" title="Naranja"></div>
                                <div class="color-item" style="background: #00ff88;" title="Verde"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Pillars -->
                <div class="strategy-card">
                    <div class="card-header">
                        <h3>🏛️ Pilares de Contenido</h3>
                    </div>
                    <div class="card-content">
                        <div class="pillars-grid">
                            <div class="pillar-item">
                                <div class="pillar-icon">📺</div>
                                <div class="pillar-content">
                                    <h4>Recomendaciones</h4>
                                    <p>Series trending, hidden gems, recomendaciones por mood</p>
                                    <div class="pillar-percentage">40%</div>
                                </div>
                            </div>
                            
                            <div class="pillar-item">
                                <div class="pillar-icon">🎭</div>
                                <div class="pillar-content">
                                    <h4>Análisis & Reviews</h4>
                                    <p>Reviews rápidos, análisis de personajes, teorías</p>
                                    <div class="pillar-percentage">25%</div>
                                </div>
                            </div>
                            
                            <div class="pillar-item">
                                <div class="pillar-icon">🇰🇷</div>
                                <div class="pillar-content">
                                    <h4>Cultura Coreana</h4>
                                    <p>Datos curiosos, idioma, tradiciones, comida</p>
                                    <div class="pillar-percentage">20%</div>
                                </div>
                            </div>
                            
                            <div class="pillar-item">
                                <div class="pillar-icon">💬</div>
                                <div class="pillar-content">
                                    <h4>Comunidad</h4>
                                    <p>Q&A, respuestas a comentarios, polls, memes</p>
                                    <div class="pillar-percentage">15%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Goals & KPIs -->
                <div class="strategy-card">
                    <div class="card-header">
                        <h3>🎯 Objetivos y KPIs</h3>
                    </div>
                    <div class="card-content">
                        <div class="goals-timeline">
                            <div class="goal-period">
                                <h4>30 Días</h4>
                                <ul>
                                    <li>1,000 seguidores</li>
                                    <li>50K views totales</li>
                                    <li>5% engagement rate</li>
                                </ul>
                            </div>
                            
                            <div class="goal-period">
                                <h4>90 Días</h4>
                                <ul>
                                    <li>5,000 seguidores</li>
                                    <li>200K views totales</li>
                                    <li>Primer video viral (100K+ views)</li>
                                </ul>
                            </div>
                            
                            <div class="goal-period">
                                <h4>6 Meses</h4>
                                <ul>
                                    <li>15,000 seguidores</li>
                                    <li>1M views totales</li>
                                    <li>Partnership con marcas K-beauty</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Tab -->
        <div x-show="activeTab === 'content'" class="tab-panel">
            
            <!-- Content Generator -->
            <div class="content-generator">
                <div class="generator-header">
                    <h3>🎬 Generador de Ideas de Contenido</h3>
                    <button @click="generateContentIdea()" class="generate-btn">
                        🎲 Generar Idea
                    </button>
                </div>
                
                <div class="generated-idea" x-show="generatedIdea">
                    <div class="idea-card">
                        <div class="idea-type" x-text="generatedIdea?.type"></div>
                        <h4 x-text="generatedIdea?.title"></h4>
                        <p x-text="generatedIdea?.description"></p>
                        <div class="idea-details">
                            <div class="idea-format">📱 <span x-text="generatedIdea?.format"></span></div>
                            <div class="idea-duration">⏱️ <span x-text="generatedIdea?.duration"></span></div>
                            <div class="idea-difficulty">⭐ <span x-text="generatedIdea?.difficulty"></span></div>
                        </div>
                        <div class="idea-hashtags">
                            <template x-for="tag in (generatedIdea?.hashtags || [])" :key="tag">
                                <span class="hashtag" x-text="'#' + tag"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Ideas Library -->
            <div class="content-library">
                <div class="library-header">
                    <h3>📚 Biblioteca de Ideas</h3>
                    <div class="library-filters">
                        <select x-model="contentFilter" @change="filterContent()" class="filter-select">
                            <option value="all">Todas las categorías</option>
                            <option value="recommendations">Recomendaciones</option>
                            <option value="reviews">Reviews</option>
                            <option value="culture">Cultura</option>
                            <option value="community">Comunidad</option>
                            <option value="trends">Tendencias</option>
                        </select>
                        
                        <select x-model="difficultyFilter" @change="filterContent()" class="filter-select">
                            <option value="all">Todas las dificultades</option>
                            <option value="easy">Fácil</option>
                            <option value="medium">Medio</option>
                            <option value="hard">Difícil</option>
                        </select>
                    </div>
                </div>
                
                <div class="ideas-grid">
                    <template x-for="idea in filteredContentIdeas" :key="idea.id">
                        <div class="idea-card-small" @click="selectContentIdea(idea)">
                            <div class="idea-header">
                                <div class="idea-icon" x-text="idea.icon"></div>
                                <div class="idea-category" x-text="idea.category"></div>
                            </div>
                            <h5 x-text="idea.title"></h5>
                            <p x-text="idea.description"></p>
                            <div class="idea-meta">
                                <span class="idea-difficulty" x-text="idea.difficulty"></span>
                                <span class="idea-viral-potential" :class="'potential-' + idea.viralPotential">
                                    🔥 <span x-text="idea.viralPotential"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Content Calendar Preview -->
            <div class="content-calendar-preview">
                <div class="calendar-header">
                    <h3>📅 Vista Previa del Calendario</h3>
                    <p class="calendar-subtitle">Próximos 7 días</p>
                </div>
                
                <div class="calendar-week">
                    <template x-for="day in getWeekPreview()" :key="day.date">
                        <div class="calendar-day">
                            <div class="day-header">
                                <div class="day-name" x-text="day.name"></div>
                                <div class="day-date" x-text="day.date"></div>
                            </div>
                            <div class="day-content" x-show="day.content">
                                <div class="content-type" x-text="day.content?.type"></div>
                                <div class="content-title" x-text="day.content?.title"></div>
                                <div class="content-time" x-text="day.content?.time"></div>
                            </div>
                            <div class="day-empty" x-show="!day.content">
                                <button @click="addContentToDay(day)" class="add-content-btn">+ Agregar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Hashtags Tab -->
        <div x-show="activeTab === 'hashtags'" class="tab-panel">
            
            <!-- Hashtag Strategy -->
            <div class="hashtag-strategy">
                <div class="strategy-explanation">
                    <h3>📝 Estrategia de Hashtags</h3>
                    <div class="hashtag-rules">
                        <div class="rule-item">
                            <div class="rule-icon">🎯</div>
                            <div class="rule-content">
                                <h4>Mezcla Estratégica</h4>
                                <p>Combina hashtags grandes (1M+), medianos (100K-1M) y pequeños (10K-100K)</p>
                            </div>
                        </div>
                        
                        <div class="rule-item">
                            <div class="rule-icon">🔥</div>
                            <div class="rule-content">
                                <h4>Tendencias Actuales</h4>
                                <p>Incluye 2-3 hashtags trending relacionados con K-dramas</p>
                            </div>
                        </div>
                        
                        <div class="rule-item">
                            <div class="rule-icon">🏷️</div>
                            <div class="rule-content">
                                <h4>Hashtags de Marca</h4>
                                <p>Siempre incluye #dorasia y hashtags únicos de la marca</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hashtag Sets -->
            <div class="hashtag-sets">
                <div class="sets-header">
                    <h3>📦 Sets de Hashtags Preparados</h3>
                    <button @click="generateHashtagSet()" class="generate-hashtag-btn">
                        🎲 Generar Set
                    </button>
                </div>
                
                <div class="hashtag-sets-grid">
                    <template x-for="set in hashtagSets" :key="set.id">
                        <div class="hashtag-set-card">
                            <div class="set-header">
                                <h4 x-text="set.name"></h4>
                                <div class="set-type" x-text="set.type"></div>
                            </div>
                            <div class="set-description" x-text="set.description"></div>
                            <div class="hashtags-list">
                                <template x-for="hashtag in set.hashtags" :key="hashtag">
                                    <span class="hashtag-tag" 
                                          :class="getHashtagClass(hashtag)"
                                          @click="copyHashtag(hashtag)">
                                        #<span x-text="hashtag"></span>
                                    </span>
                                </template>
                            </div>
                            <div class="set-actions">
                                <button @click="copyHashtagSet(set)" class="copy-set-btn">
                                    📋 Copiar Set
                                </button>
                                <button @click="customizeSet(set)" class="customize-btn">
                                    ✏️ Personalizar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Hashtag Research -->
            <div class="hashtag-research">
                <div class="research-header">
                    <h3>🔍 Investigación de Hashtags</h3>
                </div>
                
                <div class="research-grid">
                    <div class="trending-hashtags">
                        <h4>🔥 Trending Ahora</h4>
                        <div class="trending-list">
                            <template x-for="trend in trendingHashtags" :key="trend.tag">
                                <div class="trending-item">
                                    <div class="trending-hashtag">#<span x-text="trend.tag"></span></div>
                                    <div class="trending-stats">
                                        <span class="views" x-text="trend.views + ' views'"></span>
                                        <span class="growth" :class="'growth-' + trend.growth">
                                            <span x-text="trend.growth === 'up' ? '📈' : trend.growth === 'down' ? '📉' : '➖'"></span>
                                            <span x-text="trend.percentage + '%'"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="competitive-hashtags">
                        <h4>🎯 Competencia</h4>
                        <div class="competitive-list">
                            <div class="competitor-item">
                                <div class="competitor-name">@kdramaaddict</div>
                                <div class="competitor-hashtags">
                                    <span class="comp-hashtag">#kdrama</span>
                                    <span class="comp-hashtag">#koreandramas</span>
                                    <span class="comp-hashtag">#kdramaaddict</span>
                                </div>
                            </div>
                            
                            <div class="competitor-item">
                                <div class="competitor-name">@kdramareview</div>
                                <div class="competitor-hashtags">
                                    <span class="comp-hashtag">#kdramareview</span>
                                    <span class="comp-hashtag">#koreanactor</span>
                                    <span class="comp-hashtag">#dramarecommendation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Tab -->
        <div x-show="activeTab === 'trends'" class="tab-panel">
            
            <!-- Current Trends -->
            <div class="current-trends">
                <div class="trends-header">
                    <h3>🔥 Tendencias Actuales</h3>
                    <button @click="refreshTrends()" class="refresh-trends-btn">
                        🔄 Actualizar
                    </button>
                </div>
                
                <div class="trends-grid">
                    <template x-for="trend in trendingTopics" :key="trend.id">
                        <div class="trend-card">
                            <div class="trend-header">
                                <div class="trend-icon" x-text="trend.icon"></div>
                                <div class="trend-status" :class="'status-' + trend.status">
                                    <span x-text="getTrendStatusText(trend.status)"></span>
                                </div>
                            </div>
                            <h4 x-text="trend.name"></h4>
                            <p x-text="trend.description"></p>
                            <div class="trend-stats">
                                <div class="stat">
                                    <span class="stat-label">Views</span>
                                    <span class="stat-value" x-text="trend.views"></span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Growth</span>
                                    <span class="stat-value" x-text="trend.growth + '%'"></span>
                                </div>
                            </div>
                            <div class="trend-opportunity">
                                <h5>💡 Oportunidad</h5>
                                <p x-text="trend.opportunity"></p>
                            </div>
                            <div class="trend-actions">
                                <button @click="createContentFromTrend(trend)" class="create-from-trend-btn">
                                    🎬 Crear Contenido
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Trend Analysis -->
            <div class="trend-analysis">
                <h3>📊 Análisis de Tendencias</h3>
                
                <div class="analysis-grid">
                    <div class="analysis-card">
                        <h4>🎭 K-Drama Trends</h4>
                        <div class="trend-list">
                            <div class="trend-item">
                                <span class="trend-name">Series Históricas</span>
                                <span class="trend-momentum">🔥 Alto</span>
                            </div>
                            <div class="trend-item">
                                <span class="trend-name">Romance Oficina</span>
                                <span class="trend-momentum">📈 Creciendo</span>
                            </div>
                            <div class="trend-item">
                                <span class="trend-name">Remake Series</span>
                                <span class="trend-momentum">⚡ Pico</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="analysis-card">
                        <h4>🇰🇷 Cultura Coreana</h4>
                        <div class="trend-list">
                            <div class="trend-item">
                                <span class="trend-name">K-Beauty Routine</span>
                                <span class="trend-momentum">🔥 Alto</span>
                            </div>
                            <div class="trend-item">
                                <span class="trend-name">Korean Food</span>
                                <span class="trend-momentum">📈 Estable</span>
                            </div>
                            <div class="trend-item">
                                <span class="trend-name">Learn Korean</span>
                                <span class="trend-momentum">⚡ Viral</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posting Schedule Tab -->
        <div x-show="activeTab === 'posting'" class="tab-panel">
            
            <!-- Optimal Times -->
            <div class="optimal-times">
                <h3>⏰ Horarios Óptimos de Publicación</h3>
                
                <div class="times-grid">
                    <div class="time-zone">
                        <h4>🇨🇱 Chile (UTC-3)</h4>
                        <div class="best-times">
                            <div class="time-slot prime">
                                <div class="time">19:00 - 21:00</div>
                                <div class="label">Prime Time</div>
                                <div class="engagement">📈 85% engagement</div>
                            </div>
                            <div class="time-slot good">
                                <div class="time">12:00 - 14:00</div>
                                <div class="label">Lunch Break</div>
                                <div class="engagement">📊 70% engagement</div>
                            </div>
                            <div class="time-slot decent">
                                <div class="time">22:00 - 23:00</div>
                                <div class="label">Night Scroll</div>
                                <div class="engagement">📱 60% engagement</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="global-times">
                        <h4>🌍 Audiencia Global</h4>
                        <div class="global-schedule">
                            <div class="region">
                                <span class="flag">🇺🇸</span>
                                <span class="region-name">USA Este</span>
                                <span class="best-time">17:00 - 19:00</span>
                            </div>
                            <div class="region">
                                <span class="flag">🇰🇷</span>
                                <span class="region-name">Corea del Sur</span>
                                <span class="best-time">09:00 - 11:00</span>
                            </div>
                            <div class="region">
                                <span class="flag">🇪🇸</span>
                                <span class="region-name">España</span>
                                <span class="best-time">23:00 - 01:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Calendar -->
            <div class="content-calendar">
                <div class="calendar-header">
                    <h3>📅 Calendario de Contenido</h3>
                    <div class="calendar-controls">
                        <button @click="previousWeek()" class="nav-btn">← Semana Anterior</button>
                        <span class="current-week" x-text="getCurrentWeekText()"></span>
                        <button @click="nextWeek()" class="nav-btn">Siguiente Semana →</button>
                    </div>
                </div>
                
                <div class="calendar-grid">
                    <template x-for="day in getCurrentWeek()" :key="day.date">
                        <div class="calendar-day-full">
                            <div class="day-header-full">
                                <div class="day-name" x-text="day.name"></div>
                                <div class="day-date" x-text="day.date"></div>
                            </div>
                            
                            <div class="day-content-slots">
                                <template x-for="slot in day.slots" :key="slot.time">
                                    <div class="time-slot-full" :class="{'has-content': slot.content}">
                                        <div class="slot-time" x-text="slot.time"></div>
                                        <div x-show="slot.content" class="slot-content">
                                            <div class="content-type" x-text="slot.content?.type"></div>
                                            <div class="content-title" x-text="slot.content?.title"></div>
                                        </div>
                                        <div x-show="!slot.content" class="slot-empty">
                                            <button @click="addContentToSlot(day, slot)" class="add-slot-btn">+</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Posting Tips -->
            <div class="posting-tips">
                <h3>💡 Tips de Publicación</h3>
                
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">⏰</div>
                        <h4>Consistencia</h4>
                        <p>Publica al menos 1 video por día, idealmente 2-3 durante horarios pico</p>
                    </div>
                    
                    <div class="tip-card">
                        <div class="tip-icon">🎯</div>
                        <h4>Primeros 3 Segundos</h4>
                        <p>Captura la atención inmediatamente con un hook visual o textual fuerte</p>
                    </div>
                    
                    <div class="tip-card">
                        <div class="tip-icon">💬</div>
                        <h4>Engagement Temprano</h4>
                        <p>Responde comentarios en los primeros 30 minutos para impulsar el algoritmo</p>
                    </div>
                    
                    <div class="tip-card">
                        <div class="tip-icon">🔄</div>
                        <h4>Reutiliza Contenido</h4>
                        <p>Adapta videos exitosos con nuevos ángulos o actualizaciones</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div x-show="activeTab === 'analytics'" class="tab-panel">
            
            <!-- Performance Metrics -->
            <div class="performance-metrics">
                <h3>📊 Métricas de Rendimiento</h3>
                
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon">👥</div>
                        <div class="metric-value">12.5K</div>
                        <div class="metric-label">Seguidores</div>
                        <div class="metric-change positive">+15.2%</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon">👁️</div>
                        <div class="metric-value">850K</div>
                        <div class="metric-label">Views Totales</div>
                        <div class="metric-change positive">+28.7%</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon">❤️</div>
                        <div class="metric-value">7.8%</div>
                        <div class="metric-label">Engagement Rate</div>
                        <div class="metric-change positive">+2.1%</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon">🔄</div>
                        <div class="metric-value">42K</div>
                        <div class="metric-label">Shares</div>
                        <div class="metric-change positive">+35.4%</div>
                    </div>
                </div>
            </div>

            <!-- Content Performance -->
            <div class="content-performance">
                <h3>🎬 Rendimiento por Tipo de Contenido</h3>
                
                <div class="performance-chart">
                    <div class="chart-header">
                        <h4>Engagement por Categoría</h4>
                    </div>
                    <div class="chart-bars">
                        <div class="performance-bar">
                            <div class="bar-label">Recomendaciones</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: 85%"></div>
                            </div>
                            <div class="bar-value">8.5%</div>
                        </div>
                        
                        <div class="performance-bar">
                            <div class="bar-label">Reviews</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: 72%"></div>
                            </div>
                            <div class="bar-value">7.2%</div>
                        </div>
                        
                        <div class="performance-bar">
                            <div class="bar-label">Cultura Coreana</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: 91%"></div>
                            </div>
                            <div class="bar-value">9.1%</div>
                        </div>
                        
                        <div class="performance-bar">
                            <div class="bar-label">Comunidad</div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: 68%"></div>
                            </div>
                            <div class="bar-value">6.8%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Growth Insights -->
            <div class="growth-insights">
                <h3>🚀 Insights de Crecimiento</h3>
                
                <div class="insights-grid">
                    <div class="insight-card">
                        <div class="insight-header">
                            <span class="insight-icon">💡</span>
                            <span class="insight-type">Contenido</span>
                        </div>
                        <h4>Videos de cultura coreana</h4>
                        <p>Tienen 23% más engagement que el promedio. Considera aumentar frecuencia.</p>
                    </div>
                    
                    <div class="insight-card">
                        <div class="insight-header">
                            <span class="insight-icon">⏰</span>
                            <span class="insight-type">Timing</span>
                        </div>
                        <h4>Horario óptimo detectado</h4>
                        <p>19:30-20:30 genera 40% más views. Ajusta calendario de publicación.</p>
                    </div>
                    
                    <div class="insight-card">
                        <div class="insight-header">
                            <span class="insight-icon">🎯</span>
                            <span class="insight-type">Audiencia</span>
                        </div>
                        <h4>Audiencia joven creciendo</h4>
                        <p>+45% en grupo 16-24 años. Adapta contenido para esta demografía.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tiktok-strategy {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: #000;
    min-height: 100vh;
    color: white;
}

.strategy-header {
    background: linear-gradient(135deg, #ff0050 0%, #00f2ea 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.strategy-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(180deg); }
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.tiktok-branding {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.tiktok-icon {
    font-size: 4rem;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.strategy-text h1 {
    font-size: 3rem;
    font-weight: 900;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.strategy-subtitle {
    font-size: 1.2rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.strategy-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    text-align: center;
    background: rgba(255,255,255,0.1);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.strategy-tabs {
    display: flex;
    background: rgba(20,20,20,0.8);
    border-radius: 15px;
    padding: 0.5rem;
    margin-bottom: 2rem;
    overflow-x: auto;
    gap: 0.5rem;
}

.tab-btn {
    background: none;
    border: none;
    color: #ccc;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    white-space: nowrap;
}

.tab-btn:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

.tab-btn.active {
    background: linear-gradient(135deg, #ff0050 0%, #00f2ea 100%);
    color: white;
}

.tab-content {
    min-height: 500px;
}

.tab-panel {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.strategy-card {
    background: rgba(20,20,20,0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background: rgba(255,255,255,0.05);
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.card-content {
    padding: 2rem;
}

.brand-elements {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.brand-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
}

.brand-label {
    font-weight: 600;
    color: #ccc;
}

.brand-value {
    color: white;
    font-weight: 700;
}

.brand-colors h4 {
    margin: 0 0 1rem 0;
    color: white;
}

.color-palette {
    display: flex;
    gap: 0.5rem;
}

.color-item {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.color-item:hover {
    transform: scale(1.1);
}

.pillars-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.pillar-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
}

.pillar-icon {
    font-size: 2.5rem;
    line-height: 1;
}

.pillar-content h4 {
    margin: 0 0 0.5rem 0;
    color: white;
}

.pillar-content p {
    margin: 0 0 1rem 0;
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.4;
}

.pillar-percentage {
    background: linear-gradient(135deg, #ff0050 0%, #00f2ea 100%);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.9rem;
    display: inline-block;
}

.goals-timeline {
    display: flex;
    justify-content: space-between;
    gap: 2rem;
}

.goal-period {
    flex: 1;
    text-align: center;
    padding: 1.5rem;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
}

.goal-period h4 {
    margin: 0 0 1rem 0;
    color: #00f2ea;
    font-size: 1.2rem;
}

.goal-period ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.goal-period li {
    padding: 0.5rem 0;
    color: #ccc;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.goal-period li:last-child {
    border-bottom: none;
}

.content-generator {
    background: rgba(20,20,20,0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.generator-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.generator-header h3 {
    margin: 0;
    color: white;
}

.generate-btn {
    background: linear-gradient(135deg, #ff0050 0%, #00f2ea 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s ease;
}

.generate-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255,0,80,0.3);
}

.generated-idea {
    margin-top: 1.5rem;
}

.idea-card {
    background: linear-gradient(135deg, rgba(255,0,80,0.1) 0%, rgba(0,242,234,0.1) 100%);
    border: 1px solid rgba(255,0,80,0.3);
    border-radius: 15px;
    padding: 2rem;
}

.idea-type {
    background: rgba(255,0,80,0.2);
    color: #ff0050;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-block;
    margin-bottom: 1rem;
}

.idea-card h4 {
    margin: 0 0 1rem 0;
    color: white;
    font-size: 1.5rem;
}

.idea-card p {
    margin: 0 0 1.5rem 0;
    color: #ccc;
    line-height: 1.6;
}

.idea-details {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.idea-format,
.idea-duration,
.idea-difficulty {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #ccc;
    font-size: 0.9rem;
}

.idea-hashtags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.hashtag {
    background: rgba(0,242,234,0.2);
    color: #00f2ea;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Content Library Styles */
.content-library {
    background: rgba(20,20,20,0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.library-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.library-filters {
    display: flex;
    gap: 1rem;
}

.filter-select {
    background: rgba(40,40,40,0.8);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    color: white;
    padding: 0.7rem 1rem;
    font-size: 0.9rem;
}

.ideas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.idea-card-small {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.idea-card-small:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,0,80,0.3);
    transform: translateY(-2px);
}

.idea-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.idea-icon {
    font-size: 1.5rem;
}

.idea-category {
    background: rgba(255,0,80,0.2);
    color: #ff0050;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.idea-card-small h5 {
    margin: 0 0 0.8rem 0;
    color: white;
    font-size: 1.1rem;
}

.idea-card-small p {
    margin: 0 0 1rem 0;
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.4;
}

.idea-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.idea-difficulty {
    color: #ccc;
    font-size: 0.8rem;
}

.idea-viral-potential {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.potential-high { color: #ff0050; }
.potential-medium { color: #ffa500; }
.potential-low { color: #ccc; }

/* Calendar styles */
.content-calendar-preview,
.content-calendar {
    background: rgba(20,20,20,0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.calendar-week {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1rem;
}

.calendar-day {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1rem;
    min-height: 120px;
}

.day-header {
    text-align: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.day-name {
    font-weight: 700;
    color: white;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.day-date {
    color: #ccc;
    font-size: 0.8rem;
    margin-top: 0.3rem;
}

.day-content {
    text-align: center;
}

.content-type {
    background: rgba(255,0,80,0.2);
    color: #ff0050;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.content-title {
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.content-time {
    color: #ccc;
    font-size: 0.7rem;
}

.day-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 60px;
}

.add-content-btn {
    background: rgba(0,242,234,0.2);
    border: 1px dashed #00f2ea;
    color: #00f2ea;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.add-content-btn:hover {
    background: rgba(0,242,234,0.3);
}

@media (max-width: 768px) {
    .tiktok-strategy {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .strategy-stats {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .strategy-tabs {
        flex-wrap: wrap;
    }
    
    .overview-grid,
    .ideas-grid {
        grid-template-columns: 1fr;
    }
    
    .pillars-grid,
    .goals-timeline {
        flex-direction: column;
    }
    
    .calendar-week {
        grid-template-columns: 1fr;
    }
    
    .idea-details {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<script>
function tiktokStrategy() {
    return {
        // Tab state
        activeTab: 'overview',
        
        // Content data
        contentIdeas: [],
        hashtagSets: [],
        trendingTopics: [],
        trendingHashtags: [],
        generatedIdea: null,
        
        // Filters
        contentFilter: 'all',
        difficultyFilter: 'all',
        
        // Calendar
        currentWeek: 0,
        
        async initStrategy() {
            await this.loadStrategyData();
        },
        
        async loadStrategyData() {
            // Load content ideas
            this.contentIdeas = [
                {
                    id: 1,
                    title: "Top 5 K-Dramas para llorar",
                    description: "Recopilación de las series más emotivas con clips dramáticos",
                    category: "recommendations",
                    difficulty: "easy",
                    viralPotential: "high",
                    icon: "😭",
                    format: "Lista Top",
                    duration: "30-60s",
                    hashtags: ["kdrama", "tearjerker", "emotional", "dorasia"]
                },
                {
                    id: 2,
                    title: "¿Cómo se dice 'te amo' en coreano?",
                    description: "Tutorial rápido de frases románticas con pronunciación",
                    category: "culture",
                    difficulty: "easy",
                    viralPotential: "high",
                    icon: "💕",
                    format: "Tutorial",
                    duration: "15-30s",
                    hashtags: ["learnkorean", "korean", "saranghae", "dorasia"]
                },
                {
                    id: 3,
                    title: "Reacción a nuevo K-Drama",
                    description: "Primera reacción a episodio piloto de serie trending",
                    category: "reviews",
                    difficulty: "medium",
                    viralPotential: "medium",
                    icon: "😱",
                    format: "Reacción",
                    duration: "60-90s",
                    hashtags: ["kdramareaction", "newkdrama", "review", "dorasia"]
                }
            ];
            
            // Load hashtag sets
            this.hashtagSets = [
                {
                    id: 1,
                    name: "Recomendaciones Generales",
                    type: "Universal",
                    description: "Para videos de recomendaciones de series",
                    hashtags: ["kdrama", "koreandramas", "dramarecommendation", "kdramas2024", "kdramaaddict", "dorasia", "kdramaworld", "koreanculture", "dramalovers", "mustwatch"]
                },
                {
                    id: 2,
                    name: "Romance Focus",
                    type: "Nicho",
                    description: "Para contenido romántico y emotional",
                    hashtags: ["kdramaromance", "lovedramas", "romantic", "kdramacouple", "heartthrob", "dorasia", "romancekdrama", "kdramafeels", "couplegoals", "firstlove"]
                },
                {
                    id: 3,
                    name: "Cultura Coreana",
                    type: "Educativo",
                    description: "Para contenido cultural y de idioma",
                    hashtags: ["koreanculture", "learnkorean", "korea", "korean", "koreanlanguage", "dorasia", "culturecontent", "kpop", "koreanfood", "koreanbeauty"]
                }
            ];
            
            // Load trending topics
            this.trendingTopics = [
                {
                    id: 1,
                    name: "Series Históricas 2024",
                    icon: "🏛️",
                    status: "rising",
                    description: "Nuevo auge de dramas históricos coreanos",
                    views: "2.8M",
                    growth: "+125%",
                    opportunity: "Crear contenido sobre diferencias entre períodos históricos en K-dramas"
                },
                {
                    id: 2,
                    name: "K-Beauty Rutinas",
                    icon: "✨",
                    status: "peak",
                    description: "Rutinas de cuidado inspiradas en actrices",
                    views: "5.2M",
                    growth: "+89%",
                    opportunity: "Conectar skincare de actrices con sus personajes en dramas"
                },
                {
                    id: 3,
                    name: "Learn Korean Challenge",
                    icon: "🇰🇷",
                    status: "viral",
                    description: "Desafío de aprender frases de K-dramas",
                    views: "12.1M",
                    growth: "+287%",
                    opportunity: "Crear series educativa con frases icónicas de dramas"
                }
            ];
            
            // Load trending hashtags
            this.trendingHashtags = [
                { tag: "kdrama2024", views: "1.2B", growth: "up", percentage: "45" },
                { tag: "koreanromance", views: "890M", growth: "up", percentage: "32" },
                { tag: "dramatok", views: "2.1B", growth: "stable", percentage: "12" },
                { tag: "kdramaaddict", views: "1.8B", growth: "down", percentage: "8" }
            ];
        },
        
        generateContentIdea() {
            const ideas = [
                {
                    type: "TRENDING",
                    title: "¿Por qué todos hablan de este K-Drama?",
                    description: "Análisis rápido del drama más comentado de la semana con clips destacados",
                    format: "Análisis trending",
                    duration: "45-60s",
                    difficulty: "Medio",
                    hashtags: ["trending", "kdrama", "viral", "analysis", "dorasia"]
                },
                {
                    type: "EDUCATIONAL",
                    title: "5 palabras coreanas que escuchas en TODOS los K-Dramas",
                    description: "Tutorial de vocabulario esencial con ejemplos de series populares",
                    format: "Lista educativa",
                    duration: "30-45s",
                    difficulty: "Fácil",
                    hashtags: ["korean", "language", "tutorial", "kdrama", "dorasia"]
                },
                {
                    type: "RECOMMENDATION",
                    title: "K-Drama perfecto para tu personalidad",
                    description: "Quiz interactivo que recomienda series basado en respuestas rápidas",
                    format: "Quiz interactivo",
                    duration: "60-90s",
                    difficulty: "Medio",
                    hashtags: ["personality", "quiz", "recommendation", "kdrama", "dorasia"]
                }
            ];
            
            this.generatedIdea = ideas[Math.floor(Math.random() * ideas.length)];
        },
        
        get filteredContentIdeas() {
            let filtered = this.contentIdeas;
            
            if (this.contentFilter !== 'all') {
                filtered = filtered.filter(idea => idea.category === this.contentFilter);
            }
            
            if (this.difficultyFilter !== 'all') {
                filtered = filtered.filter(idea => idea.difficulty === this.difficultyFilter);
            }
            
            return filtered;
        },
        
        selectContentIdea(idea) {
            console.log('Selected content idea:', idea);
            // Implementation for selecting and using a content idea
        },
        
        getWeekPreview() {
            const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            const today = new Date();
            const preview = [];
            
            for (let i = 0; i < 7; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                
                preview.push({
                    name: days[date.getDay() === 0 ? 6 : date.getDay() - 1],
                    date: date.getDate(),
                    content: i % 3 === 0 ? {
                        type: "Recomendación",
                        title: "Top K-Drama del día",
                        time: "19:30"
                    } : null
                });
            }
            
            return preview;
        },
        
        getCurrentWeek() {
            // Implementation for full calendar week view
            return this.getWeekPreview().map(day => ({
                ...day,
                slots: [
                    { time: "12:00", content: null },
                    { time: "19:30", content: day.content },
                    { time: "22:00", content: null }
                ]
            }));
        },
        
        getCurrentWeekText() {
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay() + 1 + (this.currentWeek * 7));
            
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            
            return `${startOfWeek.getDate()}/${startOfWeek.getMonth() + 1} - ${endOfWeek.getDate()}/${endOfWeek.getMonth() + 1}`;
        },
        
        previousWeek() {
            this.currentWeek--;
        },
        
        nextWeek() {
            this.currentWeek++;
        },
        
        addContentToDay(day) {
            console.log('Adding content to day:', day);
            // Implementation for adding content to calendar day
        },
        
        addContentToSlot(day, slot) {
            console.log('Adding content to slot:', day, slot);
            // Implementation for adding content to specific time slot
        },
        
        generateHashtagSet() {
            const newSet = {
                id: Date.now(),
                name: "Set Personalizado",
                type: "Generado",
                description: "Set generado automáticamente",
                hashtags: ["kdrama", "trending", "viral", "dorasia", "korean", "drama"]
            };
            
            this.hashtagSets.unshift(newSet);
        },
        
        getHashtagClass(hashtag) {
            // Determine hashtag popularity class
            const popularTags = ["kdrama", "korean", "trending"];
            return popularTags.includes(hashtag.toLowerCase()) ? "popular" : "";
        },
        
        copyHashtag(hashtag) {
            navigator.clipboard.writeText(`#${hashtag}`);
            // Could add a toast notification here
        },
        
        copyHashtagSet(set) {
            const hashtagText = set.hashtags.map(tag => `#${tag}`).join(' ');
            navigator.clipboard.writeText(hashtagText);
            // Could add a toast notification here
        },
        
        customizeSet(set) {
            console.log('Customizing set:', set);
            // Implementation for customizing hashtag sets
        },
        
        refreshTrends() {
            // Refresh trending data
            this.loadStrategyData();
        },
        
        getTrendStatusText(status) {
            const statusTexts = {
                rising: 'En Alza',
                peak: 'En Pico',
                viral: 'Viral',
                declining: 'Decayendo'
            };
            return statusTexts[status] || status;
        },
        
        createContentFromTrend(trend) {
            console.log('Creating content from trend:', trend);
            // Implementation for creating content based on trend
        },
        
        filterContent() {
            // Filtering is handled by computed property
        }
    }
}
</script>