@extends('layouts.app')

@section('title', 'Películas K-Drama - Dorasia')

@section('content')
<div style="margin-top: -1rem;">

    <!-- Hero Section -->
    <section class="movies-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-info-box">
                <h1 class="hero-title">🎬 Películas K-Drama</h1>
                <p class="hero-description">
                    Descubre las mejores películas coreanas. Desde romances emotivos hasta thrillers intensos, 
                    encuentra tu próxima película favorita en nuestra colección de cine coreano.
                </p>
            </div>
        </div>
    </section>

    <!-- Filtros y Búsqueda -->
    <section class="content-section" style="margin-top: 2rem;">
        <div class="filters-container">
            <form method="GET" action="{{ route('movies.index') }}" class="filters-form">
                <div class="filters-row">
                    <!-- Búsqueda -->
                    <div class="filter-group">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Buscar películas..." 
                               class="search-input">
                    </div>
                    
                    <!-- Género -->
                    <div class="filter-group">
                        <select name="genre" class="filter-select">
                            <option value="">Todos los géneros</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->display_name ?: $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Año -->
                    <div class="filter-group">
                        <select name="year" class="filter-select">
                            <option value="">Todos los años</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Ordenar -->
                    <div class="filter-group">
                        <select name="sort" class="filter-select">
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Más populares</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Mejor calificadas</option>
                            <option value="release_date" {{ request('sort') == 'release_date' ? 'selected' : '' }}>Más recientes</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Título A-Z</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-submit-btn">Filtrar</button>
                    
                    @if(request()->hasAny(['search', 'genre', 'year', 'sort']))
                        <a href="{{ route('movies.index') }}" class="filter-clear-btn">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- Resultados -->
    <section class="content-section">
        <div class="results-header">
            <h2 class="section-title">
                @if(request('search'))
                    Resultados para "{{ request('search') }}"
                @elseif(request('genre'))
                    @php $selectedGenre = $genres->find(request('genre')) @endphp
                    {{ $selectedGenre ? $selectedGenre->display_name : 'Género seleccionado' }}
                @elseif(request('year'))
                    Películas de {{ request('year') }}
                @else
                    Todas las películas
                @endif
                <span class="results-count">({{ $movies->total() }} películas)</span>
            </h2>
        </div>

        @if($movies->count() > 0)
            <div class="movies-grid">
                @foreach($movies as $movie)
                    <div class="movie-card">
                        <a href="{{ route('movies.show', $movie) }}" class="movie-link">
                            <div class="movie-poster">
                                @if($movie->poster_path)
                                    <img src="https://image.tmdb.org/t/p/w500{{ $movie->poster_path }}" 
                                         alt="{{ $movie->display_title ?: $movie->title }}"
                                         loading="lazy">
                                @else
                                    <div class="movie-placeholder">
                                        🎬
                                    </div>
                                @endif
                                
                                <!-- Rating badge -->
                                @if($movie->vote_average > 0)
                                    <div class="movie-rating">
                                        ⭐ {{ number_format($movie->vote_average, 1) }}
                                    </div>
                                @endif
                                
                                <!-- Año -->
                                @if($movie->year)
                                    <div class="movie-year">{{ $movie->year }}</div>
                                @endif
                            </div>
                            
                            <div class="movie-info">
                                <h3 class="movie-title">{{ $movie->display_title ?: $movie->title }}</h3>
                                
                                @if($movie->genres->count() > 0)
                                    <div class="movie-genres">
                                        @foreach($movie->genres->take(2) as $genre)
                                            <span class="genre-tag">{{ $genre->display_name ?: $genre->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($movie->formatted_runtime)
                                    <div class="movie-runtime">{{ $movie->formatted_runtime }}</div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pagination-container">
                {{ $movies->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-results">
                <div class="no-results-icon">🎬</div>
                <h3>No se encontraron películas</h3>
                <p>Intenta cambiar los filtros o buscar con otros términos.</p>
                <a href="{{ route('movies.index') }}" class="btn-primary">Ver todas las películas</a>
            </div>
        @endif
    </section>

</div>

<style>
/* Movies Hero */
.movies-hero {
    position: relative;
    height: 40vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.movies-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at center, rgba(0, 212, 255, 0.1) 0%, transparent 70%);
}

/* Filters */
.filters-container {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.filters-row {
    display: grid;
    grid-template-columns: 2fr repeat(3, 1fr) auto auto;
    gap: 1rem;
    align-items: center;
}

.search-input, .filter-select {
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.8rem 1rem;
    color: white;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-input:focus, .filter-select:focus {
    outline: none;
    border-color: rgba(0, 212, 255, 0.5);
    background: rgba(0,0,0,0.5);
}

.search-input::placeholder {
    color: rgba(255,255,255,0.5);
}

.filter-submit-btn, .filter-clear-btn {
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.filter-submit-btn {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    color: white;
    border: none;
    cursor: pointer;
}

.filter-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

.filter-clear-btn {
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
}

.filter-clear-btn:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
}

/* Results */
.results-header {
    margin-bottom: 2rem;
}

.results-count {
    color: rgba(255,255,255,0.6);
    font-weight: normal;
    font-size: 0.9rem;
}

/* Movies Grid */
.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.movie-card {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    border-color: rgba(0, 212, 255, 0.3);
}

.movie-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.movie-poster {
    position: relative;
    width: 100%;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.movie-card:hover .movie-poster img {
    transform: scale(1.05);
}

.movie-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(255,255,255,0.3);
}

.movie-rating {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0,0,0,0.8);
    color: #ffd700;
    padding: 0.3rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.movie-year {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: rgba(0, 212, 255, 0.9);
    color: white;
    padding: 0.3rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.movie-info {
    padding: 1rem;
}

.movie-title {
    color: white;
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.movie-genres {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.genre-tag {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.movie-runtime {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 3rem;
    color: rgba(255,255,255,0.8);
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.no-results h3 {
    color: white;
    margin-bottom: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
    text-decoration: none;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .movies-hero {
        height: 30vh;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }
    
    .movies-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .movie-info {
        padding: 0.8rem;
    }
    
    .movie-title {
        font-size: 0.85rem;
    }
}
</style>

@endsection