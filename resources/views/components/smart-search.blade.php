{{-- 
    Componente de Búsqueda Inteligente - Dorasia
    Sistema de búsqueda semántica con autocompletado y filtros IA
--}}

<div class="smart-search-container" x-data="smartSearch()" x-init="initSearch()">
    <!-- Search Header -->
    <div class="search-header">
        <div class="search-branding">
            <div class="search-icon">🔍</div>
            <div class="search-text">
                <h2>Búsqueda Inteligente</h2>
                <p class="search-subtitle">Encuentra tu próxima serie perfecta con IA</p>
            </div>
        </div>
    </div>

    <!-- Main Search Bar -->
    <div class="search-bar-container">
        <div class="search-input-wrapper" :class="{'focused': searchFocused, 'has-results': searchResults.length > 0}">
            <div class="search-input-inner">
                <span class="search-icon-inner">🔍</span>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    @input="handleSearchInput"
                    @focus="searchFocused = true"
                    @blur="handleSearchBlur"
                    @keydown.enter="performSearch"
                    @keydown.arrow-down="navigateResults('down')"
                    @keydown.arrow-up="navigateResults('up')"
                    @keydown.escape="clearSearch"
                    placeholder="Busca por título, género, actor, mood, tema..."
                    class="search-input"
                    autocomplete="off">
                
                <div class="search-actions">
                    <button @click="toggleVoiceSearch" 
                            class="voice-btn" 
                            :class="{'active': voiceListening}"
                            x-show="voiceSupported">
                        <span x-show="!voiceListening">🎤</span>
                        <span x-show="voiceListening" class="pulse">🔴</span>
                    </button>
                    
                    <button @click="clearSearch" 
                            x-show="searchQuery.length > 0"
                            class="clear-btn">
                        ✕
                    </button>
                </div>
            </div>
            
            <!-- Search Type Indicator -->
            <div class="search-type-indicator" x-show="searchIntent && searchQuery.length > 0">
                <span class="intent-icon" x-text="getIntentIcon(searchIntent)"></span>
                <span class="intent-text" x-text="getIntentText(searchIntent)"></span>
            </div>
        </div>

        <!-- Autocomplete Results -->
        <div class="autocomplete-dropdown" 
             x-show="showAutocomplete && autocompleteResults.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            
            <div class="autocomplete-header">
                <span>Sugerencias</span>
                <span class="autocomplete-count" x-text="autocompleteResults.length + ' resultados'"></span>
            </div>
            
            <div class="autocomplete-results">
                <template x-for="(result, index) in autocompleteResults" :key="result.id || index">
                    <div class="autocomplete-item" 
                         :class="{'selected': selectedSuggestion === index}"
                         @click="selectSuggestion(result)"
                         @mouseenter="selectedSuggestion = index">
                        
                        <div class="suggestion-icon" x-text="getSuggestionIcon(result.type)"></div>
                        
                        <div class="suggestion-content">
                            <div class="suggestion-main" x-text="result.title || result.text"></div>
                            <div class="suggestion-meta" x-show="result.meta">
                                <span x-text="result.meta"></span>
                            </div>
                        </div>
                        
                        <div class="suggestion-score" x-show="result.score">
                            <span x-text="Math.round(result.score * 100) + '%'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Search Filters -->
    <div class="search-filters" x-show="showFilters">
        <div class="filters-row">
            <!-- Quick Filters -->
            <div class="quick-filters">
                <h4>Filtros Rápidos</h4>
                <div class="filter-chips">
                    <template x-for="filter in quickFilters" :key="filter.key">
                        <button class="filter-chip" 
                                :class="{'active': activeFilters.includes(filter.key)}"
                                @click="toggleFilter(filter.key)">
                            <span x-text="filter.icon"></span>
                            <span x-text="filter.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- AI Tags -->
            <div class="ai-tags-filter" x-show="popularTags.length > 0">
                <h4>Tags IA Populares</h4>
                <div class="tag-chips">
                    <template x-for="tag in popularTags.slice(0, 8)" :key="tag.name">
                        <button class="tag-chip"
                                :class="{'active': activeTags.includes(tag.name)}"
                                @click="toggleTag(tag.name)">
                            <span x-text="tag.name"></span>
                            <span class="tag-count" x-text="tag.count"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="advanced-toggle">
            <button @click="showAdvancedFilters = !showAdvancedFilters" class="advanced-btn">
                <span x-show="!showAdvancedFilters">⚙️ Filtros Avanzados</span>
                <span x-show="showAdvancedFilters">⚙️ Ocultar Avanzados</span>
            </button>
        </div>

        <!-- Advanced Filters -->
        <div class="advanced-filters" x-show="showAdvancedFilters" x-transition>
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Rating Mínimo</label>
                    <input type="range" 
                           x-model="filters.minRating" 
                           min="0" max="10" step="0.1"
                           @input="updateFilters">
                    <span x-text="filters.minRating"></span>
                </div>

                <div class="filter-group">
                    <label>Año</label>
                    <select x-model="filters.year" @change="updateFilters">
                        <option value="">Cualquier año</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2010s">2010-2019</option>
                        <option value="older">Antes de 2010</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Duración de Episodios</label>
                    <select x-model="filters.episodeCount" @change="updateFilters">
                        <option value="">Cualquier duración</option>
                        <option value="short">Corta (≤12 eps)</option>
                        <option value="medium">Media (13-20 eps)</option>
                        <option value="long">Larga (>20 eps)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Estado</label>
                    <select x-model="filters.status" @change="updateFilters">
                        <option value="">Cualquier estado</option>
                        <option value="completed">Finalizada</option>
                        <option value="ongoing">En emisión</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="search-results-container" x-show="searchResults.length > 0 || searchPerformed">
        
        <!-- Results Header -->
        <div class="results-header" x-show="searchPerformed">
            <div class="results-info">
                <h3>Resultados de búsqueda</h3>
                <p x-show="searchResults.length > 0">
                    <span x-text="searchResults.length"></span> series encontradas
                    <span x-show="searchQuery" x-text="'para \"' + searchQuery + '\"'"></span>
                </p>
                <p x-show="searchResults.length === 0" class="no-results">
                    No se encontraron series con esos criterios
                </p>
            </div>
            
            <div class="results-actions">
                <button @click="toggleFilters" class="filter-toggle-btn">
                    <span x-show="!showFilters">🔧 Filtros</span>
                    <span x-show="showFilters">🔧 Ocultar</span>
                </button>
                
                <select x-model="sortBy" @change="sortResults" class="sort-select">
                    <option value="relevance">Relevancia</option>
                    <option value="rating">Rating</option>
                    <option value="year">Año</option>
                    <option value="title">Título</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="searchLoading" class="search-loading">
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p>Analizando tu búsqueda con IA...</p>
            </div>
        </div>

        <!-- Results Grid -->
        <div class="search-results-grid" x-show="!searchLoading">
            <template x-for="result in paginatedResults" :key="result.id">
                <div class="search-result-card" @click="openSeries(result.id)">
                    <div class="result-poster">
                        <img :src="result.poster_path || '/images/no-poster.jpg'" 
                             :alt="result.title"
                             loading="lazy">
                        
                        <!-- Relevance Score -->
                        <div class="relevance-badge" x-show="result.relevance_score">
                            <span class="score-icon">🎯</span>
                            <span x-text="Math.round(result.relevance_score * 100) + '%'"></span>
                        </div>
                        
                        <!-- Match Reasons -->
                        <div class="match-reasons" x-show="result.match_reasons">
                            <template x-for="reason in result.match_reasons.slice(0, 2)" :key="reason">
                                <span class="reason-tag" x-text="reason"></span>
                            </template>
                        </div>
                    </div>

                    <div class="result-content">
                        <h4 class="result-title" x-text="result.title"></h4>
                        
                        <div class="result-meta">
                            <span class="rating">⭐ <span x-text="result.rating"></span></span>
                            <span class="year" x-text="result.year"></span>
                            <span x-show="result.episodes" class="episodes" x-text="result.episodes + ' eps'"></span>
                        </div>
                        
                        <p class="result-genres" x-show="result.genres" x-text="result.genres"></p>
                        
                        <!-- AI Insights -->
                        <div class="ai-insights" x-show="result.ai_tags">
                            <div class="insights-header">
                                <span class="ai-icon-small">🧠</span>
                                <span>Por qué coincide</span>
                            </div>
                            <div class="insight-tags">
                                <template x-for="tag in getTopTags(result.ai_tags)" :key="tag">
                                    <span class="insight-tag" x-text="tag"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="search-pagination" x-show="searchResults.length > resultsPerPage">
            <button @click="previousPage" 
                    :disabled="currentPage === 1"
                    class="page-btn">
                ← Anterior
            </button>
            
            <div class="page-info">
                <span x-text="'Página ' + currentPage + ' de ' + totalPages"></span>
            </div>
            
            <button @click="nextPage" 
                    :disabled="currentPage === totalPages"
                    class="page-btn">
                Siguiente →
            </button>
        </div>
    </div>

    <!-- Search Suggestions for Empty State -->
    <div class="search-suggestions" x-show="!searchPerformed && searchQuery.length === 0">
        <div class="suggestions-grid">
            <div class="suggestion-category">
                <h4>🎭 Busca por Género</h4>
                <div class="suggestion-items">
                    <template x-for="genre in suggestedGenres" :key="genre">
                        <button @click="quickSearch(genre)" class="suggestion-item" x-text="genre"></button>
                    </template>
                </div>
            </div>

            <div class="suggestion-category">
                <h4>😊 Busca por Mood</h4>
                <div class="suggestion-items">
                    <template x-for="mood in suggestedMoods" :key="mood">
                        <button @click="quickSearch(mood)" class="suggestion-item" x-text="mood"></button>
                    </template>
                </div>
            </div>

            <div class="suggestion-category">
                <h4>🔥 Trending Ahora</h4>
                <div class="suggestion-items">
                    <template x-for="trend in trendingSearches" :key="trend">
                        <button @click="quickSearch(trend)" class="suggestion-item" x-text="trend"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.smart-search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.search-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%);
    border-radius: 16px;
    border: 1px solid rgba(0, 212, 255, 0.2);
}

.search-branding {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.search-icon {
    font-size: 3rem;
    animation: searchPulse 2s ease-in-out infinite alternate;
}

@keyframes searchPulse {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.search-text h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.search-subtitle {
    color: #ccc;
    margin: 0.5rem 0 0 0;
    font-size: 1rem;
}

.search-bar-container {
    position: relative;
    margin-bottom: 2rem;
}

.search-input-wrapper {
    background: rgba(20, 20, 20, 0.8);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.search-input-wrapper.focused {
    border-color: rgba(0, 212, 255, 0.5);
    box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
}

.search-input-wrapper.has-results {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.search-input-inner {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    gap: 1rem;
}

.search-icon-inner {
    font-size: 1.2rem;
    color: #ccc;
}

.search-input {
    flex: 1;
    background: none;
    border: none;
    color: white;
    font-size: 1.1rem;
    outline: none;
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-actions {
    display: flex;
    gap: 0.5rem;
}

.voice-btn, .clear-btn {
    background: none;
    border: none;
    color: #ccc;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.voice-btn:hover, .clear-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.voice-btn.active {
    color: #ff4444;
}

.pulse {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.search-type-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.5rem;
    background: rgba(0, 212, 255, 0.1);
    border-top: 1px solid rgba(0, 212, 255, 0.2);
    font-size: 0.9rem;
    color: #00d4ff;
}

.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(20, 20, 20, 0.95);
    border: 2px solid rgba(0, 212, 255, 0.3);
    border-top: none;
    border-radius: 0 0 16px 16px;
    backdrop-filter: blur(10px);
    z-index: 100;
    max-height: 400px;
    overflow-y: auto;
}

.autocomplete-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem 0.5rem;
    font-size: 0.9rem;
    color: #ccc;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.autocomplete-count {
    color: #00d4ff;
}

.autocomplete-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    cursor: pointer;
    transition: background 0.2s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.autocomplete-item:hover,
.autocomplete-item.selected {
    background: rgba(0, 212, 255, 0.1);
}

.suggestion-icon {
    font-size: 1.2rem;
    width: 2rem;
    text-align: center;
}

.suggestion-content {
    flex: 1;
}

.suggestion-main {
    color: white;
    font-weight: 600;
}

.suggestion-meta {
    color: #ccc;
    font-size: 0.8rem;
    margin-top: 0.2rem;
}

.suggestion-score {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.search-filters {
    background: rgba(20, 20, 20, 0.6);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.quick-filters h4,
.ai-tags-filter h4 {
    color: white;
    margin: 0 0 1rem 0;
    font-size: 1rem;
}

.filter-chips,
.tag-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-chip,
.tag-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.filter-chip:hover,
.tag-chip:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}

.filter-chip.active,
.tag-chip.active {
    background: rgba(0, 212, 255, 0.2);
    border-color: #00d4ff;
    color: #00d4ff;
}

.tag-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.1rem 0.4rem;
    border-radius: 8px;
    font-size: 0.7rem;
}

.advanced-toggle {
    text-align: center;
    margin: 1rem 0;
}

.advanced-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.advanced-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.advanced-filters {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1.5rem;
    margin-top: 1.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.filter-group label {
    display: block;
    color: white;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.filter-group input,
.filter-group select {
    width: 100%;
    background: rgba(40, 40, 40, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    padding: 0.6rem;
    font-size: 0.9rem;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: rgba(0, 212, 255, 0.5);
}

.search-results-container {
    margin-top: 2rem;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(20, 20, 20, 0.6);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.results-info h3 {
    color: white;
    margin: 0 0 0.5rem 0;
}

.results-info p {
    color: #ccc;
    margin: 0;
}

.no-results {
    color: #ff9999 !important;
}

.results-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.filter-toggle-btn,
.sort-select {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.search-loading {
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
    border: 3px solid rgba(0, 212, 255, 0.3);
    border-top: 3px solid #00d4ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.search-result-card {
    background: rgba(20, 20, 20, 0.8);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.search-result-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 212, 255, 0.2);
    border-color: rgba(0, 212, 255, 0.4);
}

.result-poster {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.result-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.search-result-card:hover .result-poster img {
    transform: scale(1.05);
}

.relevance-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.8);
    padding: 0.4rem 0.8rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #00ff88;
    border: 1px solid #00ff88;
}

.match-reasons {
    position: absolute;
    bottom: 12px;
    left: 12px;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.reason-tag {
    background: rgba(0, 212, 255, 0.8);
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.result-content {
    padding: 1.5rem;
}

.result-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 0.8rem 0;
    color: white;
    line-height: 1.3;
}

.result-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.8rem;
    font-size: 0.8rem;
    color: #ccc;
}

.result-genres {
    color: #aaa;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.ai-insights {
    background: rgba(0, 212, 255, 0.05);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 8px;
    padding: 1rem;
}

.insights-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #00d4ff;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
}

.insight-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.insight-tag {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    color: #ccc;
}

.search-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
}

.page-btn {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
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
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-info {
    color: white;
    font-weight: 600;
}

.search-suggestions {
    padding: 2rem 0;
}

.suggestions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.suggestion-category {
    background: rgba(20, 20, 20, 0.6);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.suggestion-category h4 {
    color: white;
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
}

.suggestion-items {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.suggestion-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.suggestion-item:hover {
    background: rgba(0, 212, 255, 0.2);
    border-color: #00d4ff;
    color: #00d4ff;
}

@media (max-width: 768px) {
    .smart-search-container {
        padding: 1rem;
    }
    
    .search-branding {
        flex-direction: column;
        text-align: center;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .results-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .search-results-grid {
        grid-template-columns: 1fr;
    }
    
    .suggestions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function smartSearch() {
    return {
        // Search state
        searchQuery: '',
        searchFocused: false,
        searchLoading: false,
        searchPerformed: false,
        searchIntent: null,
        
        // Results
        searchResults: [],
        autocompleteResults: [],
        showAutocomplete: false,
        selectedSuggestion: -1,
        
        // Filters
        showFilters: false,
        showAdvancedFilters: false,
        activeFilters: [],
        activeTags: [],
        filters: {
            minRating: 0,
            year: '',
            episodeCount: '',
            status: ''
        },
        
        // Pagination
        currentPage: 1,
        resultsPerPage: 12,
        
        // Voice search
        voiceSupported: false,
        voiceListening: false,
        
        // Suggestions and data
        popularTags: [],
        quickFilters: [
            { key: 'high-rated', label: 'Mejor Rating', icon: '⭐' },
            { key: 'recent', label: 'Recientes', icon: '🆕' },
            { key: 'short', label: 'Series Cortas', icon: '⚡' },
            { key: 'completed', label: 'Finalizadas', icon: '✅' },
            { key: 'romance', label: 'Romance', icon: '💕' },
            { key: 'thriller', label: 'Thriller', icon: '🔥' }
        ],
        
        suggestedGenres: ['Romance', 'Drama', 'Comedy', 'Thriller', 'Fantasy', 'Historical'],
        suggestedMoods: ['Romántico', 'Divertido', 'Intenso', 'Relajante', 'Emocionante', 'Nostálgico'],
        trendingSearches: ['K-Drama 2024', 'Romance histórico', 'Thriller psicológico', 'Comedia romántica'],
        
        // Sorting
        sortBy: 'relevance',
        
        async initSearch() {
            this.checkVoiceSupport();
            await this.loadPopularTags();
        },
        
        checkVoiceSupport() {
            this.voiceSupported = 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window;
        },
        
        async loadPopularTags() {
            try {
                const response = await fetch('/api/ai/tags/stats');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.popularTags = Object.entries(data.most_popular_tags || {})
                            .map(([name, count]) => ({ name, count }))
                            .slice(0, 12);
                    }
                }
            } catch (error) {
                console.error('Error loading popular tags:', error);
            }
        },
        
        async handleSearchInput() {
            if (this.searchQuery.length < 2) {
                this.autocompleteResults = [];
                this.showAutocomplete = false;
                this.searchIntent = null;
                return;
            }
            
            // Analyze search intent
            this.analyzeSearchIntent();
            
            // Debounced autocomplete
            clearTimeout(this.autocompleteTimeout);
            this.autocompleteTimeout = setTimeout(() => {
                this.getAutocomplete();
            }, 300);
        },
        
        analyzeSearchIntent() {
            const query = this.searchQuery.toLowerCase();
            
            if (query.includes('romántic') || query.includes('amor')) {
                this.searchIntent = 'romance';
            } else if (query.includes('divertid') || query.includes('gracioso') || query.includes('comedia')) {
                this.searchIntent = 'comedy';
            } else if (query.includes('suspenso') || query.includes('thriller') || query.includes('misterio')) {
                this.searchIntent = 'thriller';
            } else if (query.includes('triste') || query.includes('llorar') || query.includes('drama')) {
                this.searchIntent = 'drama';
            } else if (query.includes('2024') || query.includes('nuevo') || query.includes('reciente')) {
                this.searchIntent = 'recent';
            } else if (query.includes('mejor') || query.includes('top') || query.includes('rating')) {
                this.searchIntent = 'top_rated';
            } else {
                this.searchIntent = 'general';
            }
        },
        
        async getAutocomplete() {
            try {
                const response = await fetch(`/api/search/autocomplete?q=${encodeURIComponent(this.searchQuery)}`);
                if (response.ok) {
                    const data = await response.json();
                    this.autocompleteResults = data.suggestions || [];
                    this.showAutocomplete = this.autocompleteResults.length > 0;
                }
            } catch (error) {
                console.error('Error getting autocomplete:', error);
            }
        },
        
        async performSearch() {
            if (this.searchQuery.length < 2) return;
            
            this.searchLoading = true;
            this.searchPerformed = true;
            this.showAutocomplete = false;
            this.currentPage = 1;
            
            try {
                const params = new URLSearchParams({
                    q: this.searchQuery,
                    intent: this.searchIntent || 'general',
                    filters: JSON.stringify(this.getActiveFilters()),
                    sort: this.sortBy,
                    limit: 50
                });
                
                const response = await fetch(`/api/smart-search?${params}`);
                if (response.ok) {
                    const data = await response.json();
                    this.searchResults = data.results || [];
                } else {
                    this.searchResults = [];
                }
            } catch (error) {
                console.error('Error performing search:', error);
                this.searchResults = [];
            } finally {
                this.searchLoading = false;
            }
        },
        
        getActiveFilters() {
            const filters = { ...this.filters };
            
            // Add quick filters
            this.activeFilters.forEach(filter => {
                switch (filter) {
                    case 'high-rated':
                        filters.minRating = Math.max(filters.minRating, 8.0);
                        break;
                    case 'recent':
                        filters.year = filters.year || '2023,2024';
                        break;
                    case 'short':
                        filters.episodeCount = 'short';
                        break;
                    case 'completed':
                        filters.status = 'completed';
                        break;
                    case 'romance':
                        filters.genres = (filters.genres || []).concat(['Romance']);
                        break;
                    case 'thriller':
                        filters.genres = (filters.genres || []).concat(['Thriller']);
                        break;
                }
            });
            
            // Add active tags
            if (this.activeTags.length > 0) {
                filters.ai_tags = this.activeTags;
            }
            
            return filters;
        },
        
        selectSuggestion(suggestion) {
            if (suggestion.type === 'series') {
                this.openSeries(suggestion.id);
            } else {
                this.searchQuery = suggestion.title || suggestion.text;
                this.performSearch();
            }
        },
        
        navigateResults(direction) {
            if (!this.showAutocomplete) return;
            
            if (direction === 'down') {
                this.selectedSuggestion = Math.min(
                    this.selectedSuggestion + 1, 
                    this.autocompleteResults.length - 1
                );
            } else {
                this.selectedSuggestion = Math.max(this.selectedSuggestion - 1, -1);
            }
        },
        
        handleSearchBlur() {
            setTimeout(() => {
                this.searchFocused = false;
                this.showAutocomplete = false;
            }, 200);
        },
        
        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.autocompleteResults = [];
            this.showAutocomplete = false;
            this.searchPerformed = false;
            this.searchIntent = null;
            this.selectedSuggestion = -1;
        },
        
        quickSearch(term) {
            this.searchQuery = term;
            this.performSearch();
        },
        
        toggleFilters() {
            this.showFilters = !this.showFilters;
        },
        
        toggleFilter(filterKey) {
            const index = this.activeFilters.indexOf(filterKey);
            if (index > -1) {
                this.activeFilters.splice(index, 1);
            } else {
                this.activeFilters.push(filterKey);
            }
            
            if (this.searchPerformed) {
                this.performSearch();
            }
        },
        
        toggleTag(tag) {
            const index = this.activeTags.indexOf(tag);
            if (index > -1) {
                this.activeTags.splice(index, 1);
            } else {
                this.activeTags.push(tag);
            }
            
            if (this.searchPerformed) {
                this.performSearch();
            }
        },
        
        updateFilters() {
            if (this.searchPerformed) {
                this.performSearch();
            }
        },
        
        sortResults() {
            if (this.searchPerformed) {
                this.performSearch();
            }
        },
        
        openSeries(seriesId) {
            window.location.href = `/series/${seriesId}`;
        },
        
        toggleVoiceSearch() {
            if (!this.voiceSupported) return;
            
            if (this.voiceListening) {
                // Stop listening
                this.voiceListening = false;
            } else {
                // Start listening
                this.startVoiceRecognition();
            }
        },
        
        startVoiceRecognition() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.lang = 'es-ES';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;
            
            recognition.onstart = () => {
                this.voiceListening = true;
            };
            
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                this.searchQuery = transcript;
                this.performSearch();
            };
            
            recognition.onerror = () => {
                this.voiceListening = false;
            };
            
            recognition.onend = () => {
                this.voiceListening = false;
            };
            
            recognition.start();
        },
        
        getIntentIcon(intent) {
            const icons = {
                romance: '💕',
                comedy: '😄',
                thriller: '🔥',
                drama: '😢',
                recent: '🆕',
                top_rated: '⭐',
                general: '🔍'
            };
            return icons[intent] || '🔍';
        },
        
        getIntentText(intent) {
            const texts = {
                romance: 'Búsqueda romántica',
                comedy: 'Búsqueda de comedia',
                thriller: 'Búsqueda de suspenso',
                drama: 'Búsqueda dramática',
                recent: 'Búsqueda de series recientes',
                top_rated: 'Búsqueda de mejor rating',
                general: 'Búsqueda general'
            };
            return texts[intent] || 'Búsqueda inteligente';
        },
        
        getSuggestionIcon(type) {
            const icons = {
                series: '📺',
                actor: '👤',
                genre: '🎭',
                tag: '🏷️',
                search: '🔍'
            };
            return icons[type] || '📺';
        },
        
        getTopTags(aiTags) {
            if (!aiTags) return [];
            
            const parsed = typeof aiTags === 'string' ? JSON.parse(aiTags) : aiTags;
            const allTags = parsed.all || [];
            
            return allTags.slice(0, 3);
        },
        
        get paginatedResults() {
            const start = (this.currentPage - 1) * this.resultsPerPage;
            const end = start + this.resultsPerPage;
            return this.searchResults.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.searchResults.length / this.resultsPerPage);
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        }
    }
}
</script>