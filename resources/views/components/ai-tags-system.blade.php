{{-- 
    Componente de Sistema de Tags IA - Dorasia
    Sistema visual de etiquetas automáticas con filtros inteligentes
--}}

<div class="ai-tags-system" x-data="aiTagsSystem()" x-init="initTagsSystem()">
    <!-- Tags Header -->
    <div class="tags-header">
        <div class="header-content">
            <div class="tags-branding">
                <div class="tags-icon">🏷️</div>
                <div class="tags-text">
                    <h2>Sistema de Tags IA</h2>
                    <p class="tags-subtitle">Descubre contenido con etiquetas inteligentes</p>
                </div>
            </div>
            
            <div class="tags-controls">
                <button @click="showTagsStats = !showTagsStats" class="stats-btn">
                    <span x-show="!showTagsStats">📊 Ver Estadísticas</span>
                    <span x-show="showTagsStats">📊 Ocultar Estadísticas</span>
                </button>
                
                <button @click="refreshTags()" 
                        :disabled="loading"
                        class="refresh-btn">
                    <span x-show="!loading">🔄 Actualizar</span>
                    <span x-show="loading" class="loading-spin">⚡ Cargando...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tags Statistics Panel -->
    <div x-show="showTagsStats" x-transition class="tags-stats-panel">
        <div class="stats-grid" x-show="tagsStats">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-value" x-text="tagsStats?.total_series_with_tags || 0"></div>
                    <div class="stat-label">Series con Tags IA</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-content">
                    <div class="stat-value" x-text="tagsStats?.total_unique_tags || 0"></div>
                    <div class="stat-label">Tags Únicos</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-content">
                    <div class="stat-value" x-text="getMostPopularTag()"></div>
                    <div class="stat-label">Tag Más Popular</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🔥</div>
                <div class="stat-content">
                    <div class="stat-value" x-text="getCategoryCount()"></div>
                    <div class="stat-label">Categorías Activas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading && !tagsData.length" class="tags-loading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Analizando tags con IA...</p>
        </div>
    </div>

    <!-- Main Tags Interface -->
    <div x-show="!loading || tagsData.length" class="tags-interface">
        
        <!-- Category Filters -->
        <div class="category-filters">
            <h3>Filtrar por Categoría</h3>
            <div class="category-chips">
                <button class="category-chip" 
                        :class="{'active': activeCategory === 'all'}"
                        @click="setActiveCategory('all')">
                    <span>🎯</span>
                    <span>Todos</span>
                    <span class="category-count" x-text="getAllTagsCount()"></span>
                </button>

                <template x-for="(count, category) in tagCategories" :key="category">
                    <button class="category-chip" 
                            :class="{'active': activeCategory === category}"
                            @click="setActiveCategory(category)">
                        <span x-text="getCategoryIcon(category)"></span>
                        <span x-text="getCategoryName(category)"></span>
                        <span class="category-count" x-text="count"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Tags Cloud -->
        <div class="tags-cloud-section">
            <div class="cloud-header">
                <h3>Nube de Tags</h3>
                <div class="cloud-controls">
                    <select x-model="cloudSortBy" @change="updateTagsCloud()" class="sort-select">
                        <option value="popularity">Popularidad</option>
                        <option value="alphabetical">Alfabético</option>
                        <option value="recent">Más Recientes</option>
                    </select>
                    
                    <button @click="toggleCloudStyle()" class="style-toggle">
                        <span x-show="cloudStyle === 'size'">📏 Tamaño</span>
                        <span x-show="cloudStyle === 'color'">🎨 Color</span>
                    </button>
                </div>
            </div>
            
            <div class="tags-cloud" :class="'style-' + cloudStyle">
                <template x-for="tag in getFilteredTags()" :key="tag.name">
                    <button class="tag-bubble" 
                            :class="getTagClasses(tag)"
                            :style="getTagStyle(tag)"
                            @click="toggleTagFilter(tag.name)"
                            :title="getTagTooltip(tag)">
                        <span x-text="tag.name"></span>
                        <span class="tag-count" x-text="tag.count"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Active Filters -->
        <div class="active-filters" x-show="activeTagFilters.length > 0">
            <div class="filters-header">
                <h4>Filtros Activos</h4>
                <button @click="clearAllFilters()" class="clear-all-btn">
                    ✕ Limpiar Todo
                </button>
            </div>
            
            <div class="active-filters-list">
                <template x-for="tag in activeTagFilters" :key="tag">
                    <div class="active-filter-chip">
                        <span x-text="tag"></span>
                        <button @click="removeTagFilter(tag)" class="remove-filter">✕</button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Filtered Results -->
        <div class="filtered-results" x-show="filteredSeries.length > 0 || (activeTagFilters.length > 0 && !searchingFiltered)">
            <div class="results-header">
                <h3>Resultados Filtrados</h3>
                <div class="results-info">
                    <span x-show="!searchingFiltered" x-text="filteredSeries.length + ' series encontradas'"></span>
                    <span x-show="searchingFiltered">Buscando...</span>
                </div>
            </div>
            
            <div x-show="searchingFiltered" class="search-loading">
                <div class="search-spinner"></div>
                <p>Filtrando series por tags...</p>
            </div>
            
            <div x-show="!searchingFiltered" class="results-grid">
                <template x-for="series in paginatedFilteredSeries" :key="series.id">
                    <div class="result-card" @click="openSeries(series.id)">
                        <div class="card-poster">
                            <img :src="series.poster_path || '/images/no-poster.jpg'" 
                                 :alt="series.title"
                                 loading="lazy">
                            
                            <!-- AI Tag Indicators -->
                            <div class="tag-indicators">
                                <template x-for="matchedTag in getMatchedTags(series)" :key="matchedTag">
                                    <span class="tag-indicator" x-text="matchedTag"></span>
                                </template>
                            </div>
                        </div>

                        <div class="card-content">
                            <h4 class="card-title" x-text="series.title"></h4>
                            
                            <div class="card-meta">
                                <span class="rating">⭐ <span x-text="series.rating"></span></span>
                                <span class="genres" x-text="series.genres"></span>
                            </div>
                            
                            <!-- AI Tags Preview -->
                            <div class="ai-tags-preview" x-show="series.ai_tags">
                                <div class="tags-preview-header">
                                    <span class="ai-icon">🧠</span>
                                    <span>Tags IA</span>
                                </div>
                                <div class="preview-tags">
                                    <template x-for="tag in getPreviewTags(series.ai_tags)" :key="tag">
                                        <span class="preview-tag" x-text="tag"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Pagination for filtered results -->
            <div class="filtered-pagination" x-show="filteredSeries.length > resultsPerPage">
                <button @click="previousFilteredPage()" 
                        :disabled="currentFilteredPage === 1"
                        class="page-btn">
                    ← Anterior
                </button>
                
                <div class="page-info">
                    <span x-text="'Página ' + currentFilteredPage + ' de ' + totalFilteredPages"></span>
                </div>
                
                <button @click="nextFilteredPage()" 
                        :disabled="currentFilteredPage === totalFilteredPages"
                        class="page-btn">
                    Siguiente →
                </button>
            </div>
        </div>

        <!-- Tag Explorer -->
        <div class="tag-explorer">
            <div class="explorer-header">
                <h3>Explorador de Tags</h3>
                <div class="explorer-controls">
                    <input type="text" 
                           x-model="tagSearchQuery"
                           @input="searchTags()"
                           placeholder="Buscar tags..."
                           class="tag-search-input">
                    
                    <select x-model="explorerCategory" @change="updateExplorer()" class="explorer-category-select">
                        <option value="all">Todas las categorías</option>
                        <option value="mood">Estados de Ánimo</option>
                        <option value="themes">Temas</option>
                        <option value="audience">Audiencia</option>
                        <option value="style">Estilo</option>
                        <option value="emotions">Emociones</option>
                        <option value="setting">Ambientación</option>
                    </select>
                </div>
            </div>
            
            <div class="explorer-content">
                <div class="explorer-grid">
                    <template x-for="category in getExplorerCategories()" :key="category.name">
                        <div class="explorer-category">
                            <div class="explorer-category-header">
                                <span class="category-icon" x-text="getCategoryIcon(category.name)"></span>
                                <h4 x-text="getCategoryName(category.name)"></h4>
                                <span class="category-tag-count" x-text="category.tags.length + ' tags'"></span>
                            </div>
                            
                            <div class="explorer-tags">
                                <template x-for="tag in category.tags.slice(0, showAllTags[category.name] ? category.tags.length : 6)" :key="tag.name">
                                    <button class="explorer-tag" 
                                            :class="{'active': activeTagFilters.includes(tag.name)}"
                                            @click="toggleTagFilter(tag.name)">
                                        <span x-text="tag.name"></span>
                                        <span class="explorer-tag-count" x-text="tag.count"></span>
                                    </button>
                                </template>
                                
                                <button x-show="category.tags.length > 6" 
                                        @click="toggleShowAllTags(category.name)"
                                        class="show-more-btn">
                                    <span x-show="!showAllTags[category.name]" x-text="'Ver ' + (category.tags.length - 6) + ' más'"></span>
                                    <span x-show="showAllTags[category.name]">Ver menos</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Popular Combinations -->
        <div class="popular-combinations">
            <div class="combinations-header">
                <h3>Combinaciones Populares</h3>
                <p class="combinations-subtitle">Tags que funcionan bien juntos</p>
            </div>
            
            <div class="combinations-grid">
                <template x-for="combo in popularCombinations" :key="combo.id">
                    <div class="combination-card" @click="applyTagCombination(combo.tags)">
                        <div class="combo-icon" x-text="combo.icon"></div>
                        <div class="combo-content">
                            <h4 x-text="combo.name"></h4>
                            <div class="combo-tags">
                                <template x-for="tag in combo.tags" :key="tag">
                                    <span class="combo-tag" x-text="tag"></span>
                                </template>
                            </div>
                            <div class="combo-stats">
                                <span x-text="combo.seriesCount + ' series'"></span>
                                <span class="combo-popularity" x-text="'✨ ' + combo.popularity + '% match'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="tags-error">
        <div class="error-content">
            <span class="error-icon">⚠️</span>
            <h3>Error al cargar tags</h3>
            <p>No se pudieron obtener los tags en este momento</p>
            <button @click="initTagsSystem()" class="retry-btn">Reintentar</button>
        </div>
    </div>
</div>

<style>
.ai-tags-system {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.tags-header {
    background: linear-gradient(135deg, rgba(123, 104, 238, 0.1) 0%, rgba(255, 107, 107, 0.1) 100%);
    border: 1px solid rgba(123, 104, 238, 0.2);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tags-branding {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.tags-icon {
    font-size: 3rem;
    animation: tagsPulse 2s ease-in-out infinite alternate;
}

@keyframes tagsPulse {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.tags-text h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.tags-subtitle {
    color: #ccc;
    margin: 0.5rem 0 0 0;
    font-size: 1rem;
}

.tags-controls {
    display: flex;
    gap: 1rem;
}

.stats-btn,
.refresh-btn {
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    color: white;
    border: none;
    padding: 0.7rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.stats-btn:hover,
.refresh-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(123, 104, 238, 0.3);
}

.refresh-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.loading-spin {
    animation: spin 1s linear infinite;
}

.tags-stats-panel {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-icon {
    font-size: 2rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    line-height: 1;
}

.stat-label {
    color: #ccc;
    font-size: 0.9rem;
    margin-top: 0.3rem;
}

.tags-loading {
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
    width: 40px;
    height: 40px;
    border: 3px solid rgba(123, 104, 238, 0.3);
    border-top: 3px solid #7b68ee;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.category-filters {
    margin-bottom: 2rem;
}

.category-filters h3 {
    color: white;
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
}

.category-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
}

.category-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.8rem 1.2rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}

.category-chip:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.category-chip.active {
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    border-color: transparent;
    color: white;
}

.category-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.8rem;
}

.category-chip.active .category-count {
    background: rgba(255, 255, 255, 0.3);
}

.tags-cloud-section {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.cloud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.cloud-header h3 {
    color: white;
    margin: 0;
    font-size: 1.3rem;
}

.cloud-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.sort-select,
.explorer-category-select {
    background: rgba(40, 40, 40, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
}

.style-toggle {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.style-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
}

.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: flex-start;
}

.tag-bubble {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.tag-bubble:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-2px);
}

.tag-bubble.active {
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    border-color: transparent;
    color: white;
}

.tag-bubble.popular {
    border-color: #ffa500;
    background: rgba(255, 165, 0, 0.1);
}

.tag-bubble.trending {
    border-color: #ff6b6b;
    background: rgba(255, 107, 107, 0.1);
}

.tags-cloud.style-size .tag-bubble {
    font-size: calc(0.8rem + var(--tag-popularity, 0) * 0.4rem);
}

.tags-cloud.style-color .tag-bubble {
    background: var(--tag-color, rgba(255, 255, 255, 0.05));
}

.tag-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.4rem;
    border-radius: 8px;
    font-size: 0.7rem;
}

.active-filters {
    background: rgba(123, 104, 238, 0.1);
    border: 1px solid rgba(123, 104, 238, 0.3);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.filters-header h4 {
    color: white;
    margin: 0;
    font-size: 1.1rem;
}

.clear-all-btn {
    background: rgba(255, 107, 107, 0.2);
    border: 1px solid #ff6b6b;
    color: #ff6b6b;
    padding: 0.4rem 1rem;
    border-radius: 16px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.clear-all-btn:hover {
    background: rgba(255, 107, 107, 0.3);
}

.active-filters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.active-filter-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(123, 104, 238, 0.2);
    border: 1px solid #7b68ee;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.remove-filter {
    background: none;
    border: none;
    color: #ff6b6b;
    cursor: pointer;
    font-weight: 700;
    font-size: 0.9rem;
}

.filtered-results {
    margin-bottom: 3rem;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.results-header h3 {
    color: white;
    margin: 0;
    font-size: 1.3rem;
}

.results-info {
    color: #ccc;
    font-size: 0.9rem;
}

.search-loading {
    text-align: center;
    padding: 2rem;
}

.search-spinner {
    width: 30px;
    height: 30px;
    border: 2px solid rgba(123, 104, 238, 0.3);
    border-top: 2px solid #7b68ee;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.result-card {
    background: rgba(20, 20, 20, 0.8);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.result-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(123, 104, 238, 0.2);
    border-color: rgba(123, 104, 238, 0.4);
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

.result-card:hover .card-poster img {
    transform: scale(1.05);
}

.tag-indicators {
    position: absolute;
    top: 8px;
    left: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    max-width: calc(100% - 16px);
}

.tag-indicator {
    background: rgba(123, 104, 238, 0.9);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: capitalize;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.card-content {
    padding: 1.2rem;
}

.card-title {
    font-size: 1rem;
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

.ai-tags-preview {
    background: rgba(123, 104, 238, 0.05);
    border: 1px solid rgba(123, 104, 238, 0.2);
    border-radius: 8px;
    padding: 0.8rem;
}

.tags-preview-header {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    color: #7b68ee;
    font-weight: 600;
}

.preview-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.preview-tag {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.4rem;
    border-radius: 8px;
    font-size: 0.7rem;
    color: #ccc;
}

.filtered-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
}

.page-btn {
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(123, 104, 238, 0.3);
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-info {
    color: white;
    font-weight: 600;
}

.tag-explorer {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.explorer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.explorer-header h3 {
    color: white;
    margin: 0;
    font-size: 1.3rem;
}

.explorer-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.tag-search-input {
    background: rgba(40, 40, 40, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    width: 200px;
}

.tag-search-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.explorer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.explorer-category {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
}

.explorer-category-header {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.category-icon {
    font-size: 1.5rem;
}

.explorer-category-header h4 {
    color: white;
    margin: 0;
    flex: 1;
    font-size: 1rem;
}

.category-tag-count {
    color: #ccc;
    font-size: 0.8rem;
}

.explorer-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.explorer-tag {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.explorer-tag:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.explorer-tag.active {
    background: linear-gradient(135deg, #7b68ee 0%, #ff6b6b 100%);
    border-color: transparent;
}

.explorer-tag-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.3rem;
    border-radius: 6px;
    font-size: 0.7rem;
}

.show-more-btn {
    background: rgba(123, 104, 238, 0.2);
    border: 1px solid #7b68ee;
    color: #7b68ee;
    padding: 0.4rem 0.8rem;
    border-radius: 16px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.show-more-btn:hover {
    background: rgba(123, 104, 238, 0.3);
}

.popular-combinations {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
}

.combinations-header {
    margin-bottom: 2rem;
}

.combinations-header h3 {
    color: white;
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
}

.combinations-subtitle {
    color: #ccc;
    margin: 0;
    font-size: 0.9rem;
}

.combinations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.combination-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.combination-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(123, 104, 238, 0.4);
    transform: translateY(-2px);
}

.combo-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.combo-content h4 {
    color: white;
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
}

.combo-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
    margin-bottom: 1rem;
}

.combo-tag {
    background: rgba(123, 104, 238, 0.2);
    color: #7b68ee;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.8rem;
}

.combo-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #ccc;
    font-size: 0.8rem;
}

.combo-popularity {
    color: #ffa500;
}

.tags-error {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(255, 100, 100, 0.3);
    border-radius: 16px;
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

@media (max-width: 768px) {
    .ai-tags-system {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .tags-controls {
        flex-direction: column;
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .category-chips {
        justify-content: center;
    }
    
    .cloud-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .cloud-controls {
        justify-content: center;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
    }
    
    .explorer-grid,
    .combinations-grid {
        grid-template-columns: 1fr;
    }
    
    .explorer-controls {
        flex-direction: column;
        width: 100%;
    }
    
    .tag-search-input {
        width: 100%;
    }
}
</style>

<script>
function aiTagsSystem() {
    return {
        // System state
        loading: false,
        error: false,
        tagsStats: null,
        tagsData: [],
        tagCategories: {},
        
        // UI state
        showTagsStats: false,
        activeCategory: 'all',
        cloudSortBy: 'popularity',
        cloudStyle: 'size',
        
        // Filtering
        activeTagFilters: [],
        filteredSeries: [],
        searchingFiltered: false,
        currentFilteredPage: 1,
        resultsPerPage: 12,
        
        // Explorer
        tagSearchQuery: '',
        explorerCategory: 'all',
        showAllTags: {},
        
        // Popular combinations
        popularCombinations: [
            {
                id: 1,
                name: 'Romance Moderno',
                icon: '💕',
                tags: ['romántico', 'moderno', 'dulce'],
                seriesCount: 15,
                popularity: 85
            },
            {
                id: 2,
                name: 'Thriller Intenso',
                icon: '🔥',
                tags: ['thriller', 'intenso', 'suspenso'],
                seriesCount: 12,
                popularity: 78
            },
            {
                id: 3,
                name: 'Comedia Familiar',
                icon: '😄',
                tags: ['comedia', 'familiar', 'divertido'],
                seriesCount: 18,
                popularity: 92
            },
            {
                id: 4,
                name: 'Drama Histórico',
                icon: '🏛️',
                tags: ['drama', 'histórico', 'épico'],
                seriesCount: 8,
                popularity: 73
            }
        ],
        
        async initTagsSystem() {
            this.loading = true;
            this.error = false;
            
            try {
                await Promise.all([
                    this.loadTagsStats(),
                    this.loadPopularTags()
                ]);
            } catch (error) {
                console.error('Error initializing tags system:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },
        
        async loadTagsStats() {
            try {
                const response = await fetch('/api/ai/tags/stats');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.tagsStats = data;
                        this.processTagsData(data);
                    }
                }
            } catch (error) {
                console.error('Error loading tags stats:', error);
            }
        },
        
        async loadPopularTags() {
            // This would typically load from the API
            // For now, we'll use mock data
            this.tagsData = [
                { name: 'romántico', count: 45, category: 'mood', popularity: 0.9 },
                { name: 'intenso', count: 38, category: 'emotions', popularity: 0.8 },
                { name: 'divertido', count: 42, category: 'mood', popularity: 0.85 },
                { name: 'histórico', count: 25, category: 'time_period', popularity: 0.6 },
                { name: 'familia', count: 33, category: 'themes', popularity: 0.7 },
                { name: 'venganza', count: 18, category: 'themes', popularity: 0.5 },
                { name: 'medicina', count: 22, category: 'themes', popularity: 0.55 },
                { name: 'escuela', count: 29, category: 'setting', popularity: 0.65 },
                { name: 'moderno', count: 41, category: 'time_period', popularity: 0.82 }
            ];
        },
        
        processTagsData(data) {
            if (!data.category_distribution) return;
            
            this.tagCategories = data.category_distribution;
        },
        
        async refreshTags() {
            await this.initTagsSystem();
        },
        
        getMostPopularTag() {
            if (!this.tagsStats?.most_popular_tags) return 'N/A';
            
            const tags = this.tagsStats.most_popular_tags;
            const topTag = Object.keys(tags)[0];
            return topTag || 'N/A';
        },
        
        getCategoryCount() {
            return Object.keys(this.tagCategories).length;
        },
        
        getAllTagsCount() {
            return Object.values(this.tagCategories).reduce((sum, count) => sum + count, 0);
        },
        
        setActiveCategory(category) {
            this.activeCategory = category;
            this.updateTagsCloud();
        },
        
        updateTagsCloud() {
            // Filter and sort tags based on current settings
            // Implementation would depend on the actual data structure
        },
        
        toggleCloudStyle() {
            this.cloudStyle = this.cloudStyle === 'size' ? 'color' : 'size';
        },
        
        getFilteredTags() {
            let filtered = [...this.tagsData];
            
            if (this.activeCategory !== 'all') {
                filtered = filtered.filter(tag => tag.category === this.activeCategory);
            }
            
            // Sort based on cloudSortBy
            switch (this.cloudSortBy) {
                case 'popularity':
                    filtered.sort((a, b) => b.count - a.count);
                    break;
                case 'alphabetical':
                    filtered.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'recent':
                    // Would need timestamp data
                    break;
            }
            
            return filtered;
        },
        
        getTagClasses(tag) {
            const classes = [];
            
            if (this.activeTagFilters.includes(tag.name)) {
                classes.push('active');
            }
            
            if (tag.count > 35) {
                classes.push('popular');
            }
            
            if (tag.popularity > 0.8) {
                classes.push('trending');
            }
            
            return classes.join(' ');
        },
        
        getTagStyle(tag) {
            const styles = {};
            
            if (this.cloudStyle === 'size') {
                styles['--tag-popularity'] = tag.popularity || 0;
            } else if (this.cloudStyle === 'color') {
                const hue = (tag.popularity || 0) * 120; // 0 (red) to 120 (green)
                styles['--tag-color'] = `hsla(${hue}, 70%, 50%, 0.2)`;
            }
            
            return styles;
        },
        
        getTagTooltip(tag) {
            return `${tag.name} - ${tag.count} series - ${Math.round((tag.popularity || 0) * 100)}% popularidad`;
        },
        
        async toggleTagFilter(tagName) {
            const index = this.activeTagFilters.indexOf(tagName);
            
            if (index > -1) {
                this.activeTagFilters.splice(index, 1);
            } else {
                this.activeTagFilters.push(tagName);
            }
            
            if (this.activeTagFilters.length > 0) {
                await this.searchByTags();
            } else {
                this.filteredSeries = [];
            }
        },
        
        async searchByTags() {
            if (this.activeTagFilters.length === 0) return;
            
            this.searchingFiltered = true;
            this.currentFilteredPage = 1;
            
            try {
                const response = await fetch('/api/ai/tags/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        tags: this.activeTagFilters,
                        limit: 100
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.filteredSeries = data.results || [];
                    }
                }
            } catch (error) {
                console.error('Error searching by tags:', error);
            } finally {
                this.searchingFiltered = false;
            }
        },
        
        removeTagFilter(tagName) {
            this.toggleTagFilter(tagName);
        },
        
        clearAllFilters() {
            this.activeTagFilters = [];
            this.filteredSeries = [];
        },
        
        getMatchedTags(series) {
            if (!series.ai_tags) return [];
            
            const tags = typeof series.ai_tags === 'string' ? 
                JSON.parse(series.ai_tags) : series.ai_tags;
            
            const allTags = tags.all || [];
            
            return this.activeTagFilters.filter(filter => 
                allTags.some(tag => tag.toLowerCase().includes(filter.toLowerCase()))
            ).slice(0, 2);
        },
        
        getPreviewTags(aiTags) {
            if (!aiTags) return [];
            
            const tags = typeof aiTags === 'string' ? JSON.parse(aiTags) : aiTags;
            const allTags = tags.all || [];
            
            return allTags.slice(0, 4);
        },
        
        openSeries(seriesId) {
            window.location.href = `/series/${seriesId}`;
        },
        
        get paginatedFilteredSeries() {
            const start = (this.currentFilteredPage - 1) * this.resultsPerPage;
            const end = start + this.resultsPerPage;
            return this.filteredSeries.slice(start, end);
        },
        
        get totalFilteredPages() {
            return Math.ceil(this.filteredSeries.length / this.resultsPerPage);
        },
        
        previousFilteredPage() {
            if (this.currentFilteredPage > 1) {
                this.currentFilteredPage--;
            }
        },
        
        nextFilteredPage() {
            if (this.currentFilteredPage < this.totalFilteredPages) {
                this.currentFilteredPage++;
            }
        },
        
        searchTags() {
            // Implement tag search functionality
        },
        
        updateExplorer() {
            // Update explorer based on category selection
        },
        
        getExplorerCategories() {
            const categories = [
                {
                    name: 'mood',
                    tags: this.tagsData.filter(tag => tag.category === 'mood')
                },
                {
                    name: 'themes',
                    tags: this.tagsData.filter(tag => tag.category === 'themes')
                },
                {
                    name: 'emotions',
                    tags: this.tagsData.filter(tag => tag.category === 'emotions')
                },
                {
                    name: 'setting',
                    tags: this.tagsData.filter(tag => tag.category === 'setting')
                }
            ];
            
            return categories.filter(cat => cat.tags.length > 0);
        },
        
        toggleShowAllTags(categoryName) {
            this.showAllTags[categoryName] = !this.showAllTags[categoryName];
        },
        
        applyTagCombination(tags) {
            this.activeTagFilters = [...tags];
            this.searchByTags();
        },
        
        getCategoryIcon(category) {
            const icons = {
                mood: '😊',
                themes: '🎭',
                audience: '👥',
                style: '🎨',
                emotions: '💫',
                setting: '🏢',
                time_period: '⏰',
                pace: '⚡',
                complexity: '🧩',
                relationship: '💕',
                ai_analysis: '🤖'
            };
            return icons[category] || '🏷️';
        },
        
        getCategoryName(category) {
            const names = {
                mood: 'Estado de Ánimo',
                themes: 'Temas',
                audience: 'Audiencia',
                style: 'Estilo',
                emotions: 'Emociones',
                setting: 'Ambientación',
                time_period: 'Período',
                pace: 'Ritmo',
                complexity: 'Complejidad',
                relationship: 'Relaciones',
                ai_analysis: 'Análisis IA'
            };
            return names[category] || category;
        }
    }
}
</script>