{{-- resources/views/catalog/discover.blade.php --}}
@extends('layouts.app')

@section('title', 'Descubre el mejor contenido asiático')
@section('meta_description', 'Explora las mejores películas y series asiáticas: doramas coreanos, series chinas, anime japonés y más, todo en un solo lugar.')

@section('content')
<!-- Hero Banner con Carrusel -->
<div class="hero-banner position-relative mb-5">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($featuredContent as $index => $item)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                 style="background-image: url('{{ $item->backdrop_path ? asset('storage/' . $item->backdrop_path) : asset('images/default-backdrop.jpg') }}')">
                <div class="carousel-overlay"></div>
                <div class="container">
                    <div class="carousel-caption text-start">
                        <span class="badge bg-primary mb-2">{{ $item->type }}</span>
                        <h1>{{ $item->title }}</h1>
                        <p class="d-none d-md-block">{{ Str::limit($item->overview, 150) }}</p>
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ $item->link }}" class="btn btn-primary">
                                <i class="bi bi-info-circle"></i> Ver detalles
                            </a>
                            <button class="btn btn-outline-light" 
                                    onclick="toggleWatchlist('{{ $item->id }}', '{{ $item->type === 'Película' ? 'movie' : 'tv-show' }}')">
                                <i class="bi bi-plus-circle"></i> Mi Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
        
        <!-- Indicadores discretos abajo -->
        <div class="carousel-indicators">
            @foreach($featuredContent as $index => $item)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                    aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    </div>
</div>

<!-- Filtros Rápidos / Categorías -->
<div class="container mb-5">
    <div class="categories-wrapper p-3 bg-dark rounded">
        <h2 class="h5 mb-4">Explorar por categoría</h2>
        
        <div class="row g-3">
            <!-- Países -->
            <div class="col-12 col-md-4">
                <div class="category-group">
                    <h3 class="h6 mb-3">Países</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('catalog.index', ['country' => 'KR']) }}" class="category-pill">
                            <span class="flag-icon flag-kr me-1"></span> Corea
                        </a>
                        <a href="{{ route('catalog.index', ['country' => 'JP']) }}" class="category-pill">
                            <span class="flag-icon flag-jp me-1"></span> Japón
                        </a>
                        <a href="{{ route('catalog.index', ['country' => 'CN']) }}" class="category-pill">
                            <span class="flag-icon flag-cn me-1"></span> China
                        </a>
                        <a href="{{ route('catalog.index', ['country' => 'TW']) }}" class="category-pill">
                            <span class="flag-icon flag-tw me-1"></span> Taiwán
                        </a>
                        <a href="{{ route('catalog.index', ['country' => 'TH']) }}" class="category-pill">
                            <span class="flag-icon flag-th me-1"></span> Tailandia
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Tipos -->
            <div class="col-12 col-md-4">
                <div class="category-group">
                    <h3 class="h6 mb-3">Tipos</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('catalog.movies') }}" class="category-pill">
                            <i class="bi bi-film me-1"></i> Películas
                        </a>
                        <a href="{{ route('catalog.tvShows', ['type' => 'drama']) }}" class="category-pill">
                            <i class="bi bi-tv me-1"></i> Series/Doramas
                        </a>
                        <a href="{{ route('catalog.tvShows', ['type' => 'anime']) }}" class="category-pill">
                            <i class="bi bi-star me-1"></i> Anime
                        </a>
                        <a href="{{ route('catalog.tvShows', ['type' => 'variety']) }}" class="category-pill">
                            <i class="bi bi-mic me-1"></i> Variety Shows
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Géneros Populares -->
            <div class="col-12 col-md-4">
                <div class="category-group">
                    <h3 class="h6 mb-3">Géneros Populares</h3>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularGenres as $genre)
                        <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}" class="category-pill">
                            {{ $genre->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Barra de búsqueda -->
        <div class="search-bar mt-4">
            <form action="{{ route('catalog.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Buscar títulos, actores, directores...">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tendencias Actuales -->
<div class="container mb-5">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up-arrow me-2"></i> Tendencias</h2>
        <a href="{{ route('catalog.index', ['sort' => 'popularity']) }}" class="btn btn-outline-primary btn-sm">
            Ver todo
        </a>
    </div>
    
    <div data-vue>
        <content-carousel 
            title="" 
            :items='@json($trendingContent)'
            view-all-link="{{ route('catalog.index', ['sort' => 'popularity']) }}">
        </content-carousel>
    </div>
</div>

<!-- Estrenos Recientes -->
<div class="container mb-5">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-event me-2"></i> Últimos Estrenos</h2>
        <a href="{{ route('catalog.index', ['sort' => 'newest']) }}" class="btn btn-outline-primary btn-sm">
            Ver todo
        </a>
    </div>
    
    <div data-vue>
        <content-carousel 
            title="" 
            :items='@json($latestReleases)'
            view-all-link="{{ route('catalog.index', ['sort' => 'newest']) }}">
        </content-carousel>
    </div>
</div>

<!-- Secciones por País -->
<!-- Sección Corea -->
<div class="country-section korea-section py-5 mb-5">
    <div class="container">
        <div class="country-header mb-4">
            <h2 class="d-flex align-items-center">
                <span class="flag-icon flag-kr me-2"></span> Corea
            </h2>
            <p class="text-muted">Descubre los mejores k-dramas y películas coreanas</p>
        </div>
        
        <!-- Tabs de categorías -->
        <ul class="nav nav-tabs mb-4" id="koreaTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="k-series-tab" data-bs-toggle="tab" data-bs-target="#k-series" type="button" role="tab" aria-selected="true">
                    Doramas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="k-movies-tab" data-bs-toggle="tab" data-bs-target="#k-movies" type="button" role="tab" aria-selected="false">
                    Películas
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="koreaTabContent">
            <div class="tab-pane fade show active" id="k-series" role="tabpanel" aria-labelledby="k-series-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($koreanDramas)'
                        view-all-link="{{ route('catalog.tvShows', ['country' => 'KR', 'type' => 'drama']) }}">
                    </content-carousel>
                </div>
            </div>
            <div class="tab-pane fade" id="k-movies" role="tabpanel" aria-labelledby="k-movies-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($koreanMovies)'
                        view-all-link="{{ route('catalog.movies', ['country' => 'KR']) }}">
                    </content-carousel>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección Japón -->
<div class="country-section japan-section py-5 mb-5">
    <div class="container">
        <div class="country-header mb-4">
            <h2 class="d-flex align-items-center">
                <span class="flag-icon flag-jp me-2"></span> Japón
            </h2>
            <p class="text-muted">El mejor anime y contenido japonés</p>
        </div>
        
        <!-- Tabs de categorías -->
        <ul class="nav nav-tabs mb-4" id="japanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="anime-tab" data-bs-toggle="tab" data-bs-target="#anime" type="button" role="tab" aria-selected="true">
                    Anime
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="j-dramas-tab" data-bs-toggle="tab" data-bs-target="#j-dramas" type="button" role="tab" aria-selected="false">
                    J-Dramas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="j-movies-tab" data-bs-toggle="tab" data-bs-target="#j-movies" type="button" role="tab" aria-selected="false">
                    Películas
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="japanTabContent">
            <div class="tab-pane fade show active" id="anime" role="tabpanel" aria-labelledby="anime-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($animeShows)'
                        view-all-link="{{ route('catalog.tvShows', ['country' => 'JP', 'type' => 'anime']) }}">
                    </content-carousel>
                </div>
            </div>
            <div class="tab-pane fade" id="j-dramas" role="tabpanel" aria-labelledby="j-dramas-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($japaneseDramas)'
                        view-all-link="{{ route('catalog.tvShows', ['country' => 'JP', 'type' => 'drama']) }}">
                    </content-carousel>
                </div>
            </div>
            <div class="tab-pane fade" id="j-movies" role="tabpanel" aria-labelledby="j-movies-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($japaneseMovies)'
                        view-all-link="{{ route('catalog.movies', ['country' => 'JP']) }}">
                    </content-carousel>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección China -->
<div class="country-section china-section py-5 mb-5">
    <div class="container">
        <div class="country-header mb-4">
            <h2 class="d-flex align-items-center">
                <span class="flag-icon flag-cn me-2"></span> China
            </h2>
            <p class="text-muted">C-dramas y películas chinas</p>
        </div>
        
        <!-- Tabs de categorías -->
        <ul class="nav nav-tabs mb-4" id="chinaTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="c-dramas-tab" data-bs-toggle="tab" data-bs-target="#c-dramas" type="button" role="tab" aria-selected="true">
                    C-Dramas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="c-movies-tab" data-bs-toggle="tab" data-bs-target="#c-movies" type="button" role="tab" aria-selected="false">
                    Películas
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="chinaTabContent">
            <div class="tab-pane fade show active" id="c-dramas" role="tabpanel" aria-labelledby="c-dramas-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($chineseDramas)'
                        view-all-link="{{ route('catalog.tvShows', ['country' => 'CN', 'type' => 'drama']) }}">
                    </content-carousel>
                </div>
            </div>
            <div class="tab-pane fade" id="c-movies" role="tabpanel" aria-labelledby="c-movies-tab" tabindex="0">
                <div data-vue>
                    <content-carousel 
                        title="" 
                        :items='@json($chineseMovies)'
                        view-all-link="{{ route('catalog.movies', ['country' => 'CN']) }}">
                    </content-carousel>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mejor Valorados -->
<div class="container mb-5">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-star-fill me-2"></i> Mejor Valorados</h2>
        <a href="{{ route('catalog.index', ['sort' => 'rating']) }}" class="btn btn-outline-primary btn-sm">
            Ver todo
        </a>
    </div>
    
    <div data-vue>
        <content-carousel 
            title="" 
            :items='@json($topRated)'
            view-all-link="{{ route('catalog.index', ['sort' => 'rating']) }}">
        </content-carousel>
    </div>
</div>

<!-- Plataformas disponibles -->
<div class="container mb-5">
    <h2 class="mb-4"><i class="bi bi-collection-play me-2"></i> Disponible en estas plataformas</h2>
    
    <div class="row platforms-container justify-content-center">
        @foreach($platforms as $platform)
        <div class="col-4 col-md-2 mb-4 text-center">
            <a href="{{ route('catalog.platform', $platform->slug) }}" class="platform-link">
                <img src="{{ $platform->logo_path ? asset('storage/' . $platform->logo_path) : asset('images/platform-placeholder.png') }}" 
                     alt="{{ $platform->name }}" class="img-fluid platform-logo mb-2">
                <span class="platform-name d-block">{{ $platform->name }}</span>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Géneros Populares -->
<div class="container mb-5">
    <h2 class="mb-4"><i class="bi bi-tags me-2"></i> Explorar por género</h2>
    
    <div class="row g-3">
        @foreach($genres as $genre)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('catalog.genre', $genre->slug) }}" class="genre-card d-block p-4 text-center rounded h-100">
                <span class="genre-name">{{ $genre->name }}</span>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Hero Banner */
    .hero-banner {
        margin-top: -1.5rem;
    }
    
    .carousel-item {
        height: 70vh;
        min-height: 400px;
        max-height: 700px;
        background-size: cover;
        background-position: center center;
        position: relative;
    }
    
    .carousel-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            to right,
            rgba(0, 0, 0, 0.8) 0%,
            rgba(0, 0, 0, 0.4) 50%,
            rgba(0, 0, 0, 0.1) 100%
        );
    }
    
    .carousel-caption {
        position: absolute;
        right: auto;
        bottom: 20%;
        left: 5%;
        z-index: 10;
        max-width: 500px;
        text-align: left;
    }
    
    /* Categorías y filtros */
    .categories-wrapper {
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .category-pill {
        display: inline-block;
        padding: 0.4rem 1rem;
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border-radius: 50px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .category-pill:hover {
        background-color: var(--bs-primary);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    /* Secciones de países */
    .country-section {
        position: relative;
    }
    
    .korea-section {
        background-color: rgba(25, 55, 109, 0.1);
    }
    
    .japan-section {
        background-color: rgba(188, 0, 45, 0.1);
    }
    
    .china-section {
        background-color: rgba(222, 41, 16, 0.1);
    }
    
    .flag-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    /* Plataformas */
    .platform-link {
        display: block;
        padding: 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .platform-link:hover {
        transform: translateY(-5px);
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .platform-logo {
        height: 40px;
        object-fit: contain;
        margin-bottom: 0.5rem;
        filter: grayscale(30%);
        transition: all 0.3s ease;
    }
    
    .platform-link:hover .platform-logo {
        filter: grayscale(0%);
    }
    
    .platform-name {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.7);
    }
    
    /* Géneros */
    .genre-card {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.3), rgba(var(--bs-primary-rgb), 0.1));
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .genre-card:hover {
        transform: translateY(-5px);
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.5), rgba(var(--bs-primary-rgb), 0.2));
        text-decoration: none;
    }
    
    .genre-name {
        color: #fff;
        font-weight: 500;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .carousel-item {
            height: 50vh;
        }
        
        .carousel-caption {
            bottom: 30%;
            max-width: 80%;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Función para manejar la adición a la lista de seguimiento
    function toggleWatchlist(contentId, contentType) {
        // Verificar si el usuario está autenticado
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        
        if (!isAuthenticated) {
            window.location.href = '{{ route("login") }}';
            return;
        }
        
        // Aquí iría la lógica para usar tu store de Pinia
        if (window.userContentStore) {
            window.userContentStore.toggleWatchlist(contentId, contentType);
        } else {
            // Fallback para cuando no está disponible el store
            fetch('{{ route("watchlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content_id: contentId,
                    content_type: contentType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar notificación 
                    alert(data.added ? 'Añadido a tu lista' : 'Eliminado de tu lista');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
    
    // Detectar cuando se cambia de tab para reinicializar carruseles
    document.addEventListener('DOMContentLoaded', function() {
        const tabElements = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabElements.forEach(tab => {
            tab.addEventListener('shown.bs.tab', event => {
                window.dispatchEvent(new Event('resize'));
            });
        });
    });
</script>
@endsection