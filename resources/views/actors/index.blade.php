@extends('layouts.app')

@section('title', 'Actores Coreanos - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section actors-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="hero-overlay" style="background: linear-gradient(45deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.6) 100%);"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            <h1 class="hero-title">🎭 Actores Coreanos</h1>
            <p class="hero-description">
                Descubre a los talentosos actores y actrices que dan vida a tus K-Dramas favoritos
            </p>
            
            <!-- Search Form -->
            <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem;">
                <form method="GET" action="{{ route('actors.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <div style="position: relative; flex: 1; min-width: 200px;">
                        <input 
                            type="text" 
                            name="search" 
                            id="actorSearch"
                            value="{{ request('search') }}"
                            placeholder="Buscar actores..." 
                            style="width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.9); color: #333;"
                            autocomplete="off">
                        <div id="autocompleteResults" style="
                            position: absolute; 
                            top: 100%; 
                            left: 0; 
                            right: 0; 
                            background: white; 
                            border-radius: 8px; 
                            box-shadow: 0 4px 20px rgba(0,0,0,0.3); 
                            max-height: 300px; 
                            overflow-y: auto; 
                            z-index: 1000; 
                            display: none;
                        "></div>
                    </div>
                    
                    <select name="filter" style="padding: 0.8rem 1rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.9); color: #333; min-width: 180px;">
                        <option value="korean" {{ request('filter', 'korean') === 'korean' ? 'selected' : '' }}>🇰🇷 Actores Coreanos</option>
                        <option value="popular" {{ request('filter') === 'popular' ? 'selected' : '' }}>⭐ Más Populares</option>
                        <option value="trending" {{ request('filter') === 'trending' ? 'selected' : '' }}>🔥 Trending</option>
                        <option value="actresses" {{ request('filter') === 'actresses' ? 'selected' : '' }}>✨ Actrices</option>
                        <option value="actors" {{ request('filter') === 'actors' ? 'selected' : '' }}>🎭 Actores</option>
                        <option value="young" {{ request('filter') === 'young' ? 'selected' : '' }}>🌟 Jóvenes (-35)</option>
                        <option value="veteran" {{ request('filter') === 'veteran' ? 'selected' : '' }}>👑 Veteranos (+45)</option>
                        <option value="all" {{ request('filter') === 'all' ? 'selected' : '' }}>📺 Todos</option>
                    </select>
                    
                    <select name="sort" style="padding: 0.8rem 1rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.9); color: #333; min-width: 150px;">
                        <option value="popularity" {{ request('sort', 'popularity') === 'popularity' ? 'selected' : '' }}>📈 Popularidad</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>🔤 Nombre A-Z</option>
                        <option value="birthday" {{ request('sort') === 'birthday' ? 'selected' : '' }}>🎂 Más Jóvenes</option>
                    </select>
                    
                    <button type="submit" class="btn-hero" style="padding: 0.8rem 1.5rem;">
                        🔍 Buscar
                    </button>
                </form>
            </div>

            <!-- Featured Actors Preview -->
            @if($featuredActors->count() > 0)
            <div class="featured-actors-grid">
                @foreach($featuredActors->take(6) as $actor)
                <div class="featured-actor-item">
                    @if($actor->profile_path)
                    <img src="https://image.tmdb.org/t/p/w200{{ $actor->profile_path }}" 
                         alt="{{ $actor->display_name }}"
                         class="featured-actor-img"
                         loading="lazy">
                    @else
                    <div class="featured-actor-placeholder">👤</div>
                    @endif
                    <span class="featured-actor-name">{{ Str::limit($actor->display_name, 15) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Actors Grid -->
<section class="content-section" style="margin-top: -50px; position: relative; z-index: 20;">
    
    <!-- Filter Results Info -->
    <div style="background: rgba(20, 20, 20, 0.4); backdrop-filter: blur(10px); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid rgba(255, 255, 255, 0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.2rem;">
                    📊 {{ $actors->total() }} actores encontrados
                </h2>
                @if(request('search'))
                <p style="color: rgba(255,255,255,0.7); margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                    Búsqueda: "{{ request('search') }}"
                </p>
                @endif
            </div>
            
            @if(request()->hasAny(['search', 'filter']))
            <a href="{{ route('actors.index') }}" style="background: rgba(220, 53, 69, 0.8); color: white; padding: 0.5rem 1rem; border-radius: 20px; text-decoration: none; font-size: 0.9rem;">
                🗑️ Limpiar filtros
            </a>
            @endif
        </div>
    </div>

    <!-- Actors Grid -->
    @if($actors->count() > 0)
    <div class="actors-grid">
        @foreach($actors as $actor)
        <div class="cast-card">
            <a href="{{ route('actors.show', $actor->id) }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                <div class="cast-image">
                    @if($actor->profile_path)
                    <img src="https://image.tmdb.org/t/p/w300{{ $actor->profile_path }}" 
                         alt="{{ $actor->display_name }}"
                         class="cast-photo"
                         loading="lazy">
                    @else
                    <div class="cast-placeholder">
                        👤
                    </div>
                    @endif
                </div>
                
                <div class="cast-info">
                    <h3 class="cast-name">
                        {{ $actor->display_name }}
                    </h3>
                    
                    <p class="cast-bio">
                        @if($actor->biography)
                            {{ Str::limit($actor->biography, 60) }}
                        @else
                            Actor coreano
                        @endif
                    </p>
                    
                    <div class="cast-details">
                        @if($actor->birthday)
                        <span class="cast-birth">
                            🎂 {{ \Carbon\Carbon::parse($actor->birthday)->format('d/m/Y') }}
                        </span>
                        @endif
                        
                        @if($actor->popularity)
                        <span class="cast-popularity">
                            ⭐ {{ number_format($actor->popularity, 1) }}
                        </span>
                        @endif
                    </div>
                    
                    <div class="cast-view-btn">
                        Ver
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($actors->hasPages())
    <div style="margin-top: 3rem; display: flex; justify-content: center;">
        <div style="background: rgba(20, 20, 20, 0.4); backdrop-filter: blur(10px); border-radius: 16px; padding: 1rem; border: 1px solid rgba(255, 255, 255, 0.05);">
            {{ $actors->links() }}
        </div>
    </div>
    @endif

    @else
    <!-- No Results -->
    <div style="text-align: center; padding: 4rem 2rem; background: rgba(20, 20, 20, 0.4); backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.05);">
        <div style="font-size: 4rem; margin-bottom: 1rem;">😔</div>
        <h3 style="color: white; margin-bottom: 1rem;">No se encontraron actores</h3>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 2rem;">
            @if(request('search'))
                No hay resultados para "{{ request('search') }}". Intenta con otra búsqueda.
            @else
                No hay actores disponibles en este momento.
            @endif
        </p>
        @if(request()->hasAny(['search', 'filter']))
        <a href="{{ route('actors.index') }}" class="btn-hero">
            🔄 Ver todos los actores
        </a>
        @endif
    </div>
    @endif

</section>

<style>
/* Desktop styling */
.actors-hero {
    padding: 4rem 0 3rem;
}

.actors-hero .hero-title {
    font-size: 2.5rem;
    margin-bottom: 0.8rem;
}

.actors-hero .hero-description {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* Actors grid */
.actors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* Cast card styling */
.cast-card {
    background: rgba(20, 20, 20, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.3s ease;
}

.cast-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.cast-image {
    text-align: center;
    margin-bottom: 0.8rem;
}

.cast-photo {
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.cast-placeholder {
    width: 120px;
    height: 160px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 3rem;
}

.cast-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.cast-name {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.4rem;
    text-align: center;
    line-height: 1.2;
}

.cast-bio {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.8rem;
    line-height: 1.4;
    margin-bottom: 0.8rem;
    text-align: center;
    flex: 1;
}

.cast-details {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    align-items: center;
    margin-top: auto;
}

.cast-birth,
.cast-location {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
}

.cast-popularity {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.25rem 0.6rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-top: 0.3rem;
}

/* View button */
.cast-view-btn {
    margin-top: 0.8rem;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.cast-view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

/* Featured actors grid */
.featured-actors-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    max-width: 600px;
    margin: 0 auto;
}

.featured-actor-item {
    text-align: center;
}

.featured-actor-img,
.featured-actor-placeholder {
    width: 70px;
    height: 90px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid rgba(255,255,255,0.2);
    margin-bottom: 0.4rem;
}

.featured-actor-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.4rem;
    color: white;
    font-size: 1.5rem;
}

.featured-actor-name {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.9);
    font-weight: 600;
    display: block;
    line-height: 1.2;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    /* Hero Section Mobile */
    .actors-hero {
        padding: 1.5rem 0 1rem !important;
        min-height: auto !important;
    }
    
    .hero-content {
        padding: 0 0.5rem !important;
    }
    
    .hero-info-box {
        padding: 1rem !important;
    }
    
    .actors-hero .hero-title {
        font-size: 1.5rem !important;
        margin-bottom: 0.4rem !important;
    }
    
    .actors-hero .hero-description {
        font-size: 0.85rem !important;
        margin-bottom: 0.8rem !important;
        line-height: 1.3 !important;
    }
    
    /* Search form mobile */
    .hero-info-box > div:first-of-type {
        padding: 0.8rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    .hero-info-box form {
        flex-direction: column !important;
        gap: 0.6rem !important;
    }
    
    .hero-info-box form input,
    .hero-info-box form select,
    .hero-info-box form button {
        width: 100% !important;
        min-width: unset !important;
        font-size: 0.85rem !important;
        padding: 0.6rem 0.8rem !important;
    }
    
    /* Featured actors preview mobile */
    .featured-actors-grid {
        display: none !important;
    }
    
    /* Content section mobile */
    .content-section {
        margin-top: -20px !important;
        padding: 0 0.3rem !important;
    }
    
    /* Filter info mobile */
    .content-section > div:first-child {
        padding: 0.8rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    .content-section > div:first-child h2 {
        font-size: 0.9rem !important;
    }
    
    .content-section > div:first-child p {
        font-size: 0.75rem !important;
    }
    
    /* Actors grid mobile - wider layout */
    .actors-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.3rem !important;
        padding: 0 !important;
    }
    
    /* Actor card mobile */
    .cast-card {
        padding: 0.6rem !important;
        margin-bottom: 0 !important;
        border-radius: 8px !important;
        min-height: 250px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }
    
    .cast-image {
        margin-bottom: 0.3rem !important;
        flex-shrink: 0 !important;
    }
    
    .cast-photo,
    .cast-placeholder {
        width: 100% !important;
        max-width: none !important;
        height: 110px !important;
        margin: 0 auto !important;
        display: block !important;
        border-radius: 6px !important;
        object-fit: cover !important;
    }
    
    .cast-placeholder {
        font-size: 1.8rem !important;
    }
    
    .cast-info {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        text-align: center !important;
        align-items: center !important;
    }
    
    .cast-name {
        font-size: 0.8rem !important;
        margin-bottom: 0.2rem !important;
        line-height: 1.1 !important;
        text-align: center !important;
        width: 100% !important;
    }
    
    .cast-bio {
        font-size: 0.65rem !important;
        line-height: 1.2 !important;
        margin-bottom: 0.3rem !important;
        display: block !important;
        flex: 1 !important;
        text-align: center !important;
        width: 100% !important;
    }
    
    .cast-details {
        gap: 0.15rem !important;
        margin-bottom: 0.3rem !important;
        width: 100% !important;
        text-align: center !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    
    .cast-birth {
        font-size: 0.6rem !important;
        line-height: 1.1 !important;
        text-align: center !important;
    }
    
    .cast-popularity {
        font-size: 0.55rem !important;
        padding: 0.15rem 0.3rem !important;
        margin-top: 0.15rem !important;
        text-align: center !important;
        display: inline-block !important;
    }
    
    .cast-view-btn {
        margin-top: 0.3rem !important;
        padding: 0.3rem 0.6rem !important;
        font-size: 0.65rem !important;
        border-radius: 15px !important;
        margin-top: auto !important;
        width: 80% !important;
        text-align: center !important;
    }
    
    /* Pagination mobile */
    .content-section > div:last-child {
        margin-top: 2rem !important;
    }
    
    .content-section > div:last-child > div {
        padding: 0.5rem !important;
    }
    
    /* Clean filters button mobile */
    a[href*="actors"]:not(.cast-card a) {
        font-size: 0.8rem !important;
        padding: 0.4rem 0.8rem !important;
    }
    
    /* No results mobile */
    .content-section > div > div:has(h3) {
        padding: 2rem 1rem !important;
    }
    
    .content-section > div > div:has(h3) > div:first-child {
        font-size: 3rem !important;
    }
    
    .content-section > div > div:has(h3) h3 {
        font-size: 1.2rem !important;
    }
    
    .content-section > div > div:has(h3) p {
        font-size: 0.9rem !important;
    }
}

/* Very small phones */
@media (max-width: 480px) {
    /* Single column for very small screens */
    .content-section > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
    
    .cast-photo,
    .cast-placeholder {
        max-width: 150px !important;
        height: 200px !important;
    }
    
    /* Show only 4 featured actors */
    .hero-info-box > div:last-child > div:nth-child(n+5) {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('actorSearch');
    const autocompleteResults = document.getElementById('autocompleteResults');
    let searchTimeout;

    if (searchInput && autocompleteResults) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                autocompleteResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                performActorSearch(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#actorSearch') && !e.target.closest('#autocompleteResults')) {
                autocompleteResults.style.display = 'none';
            }
        });
    }

    function performActorSearch(query) {
        autocompleteResults.innerHTML = '<div style="padding: 1rem; color: #666; text-align: center;">🔍 Buscando...</div>';
        autocompleteResults.style.display = 'block';

        fetch(`/api/actors/autocomplete?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayAutocompleteResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
                autocompleteResults.innerHTML = '<div style="padding: 1rem; color: #666; text-align: center;">Error en la búsqueda</div>';
            });
    }

    function displayAutocompleteResults(actors) {
        if (actors.length === 0) {
            autocompleteResults.innerHTML = '<div style="padding: 1rem; color: #666; text-align: center;">No se encontraron actores</div>';
            return;
        }

        let html = '';
        actors.forEach(actor => {
            const profileUrl = actor.profile_path ? 
                `https://image.tmdb.org/t/p/w92${actor.profile_path}` : 
                'https://via.placeholder.com/92x138/333/666?text=Actor';
            
            html += `
                <div style="
                    display: flex; 
                    align-items: center; 
                    padding: 0.75rem 1rem; 
                    border-bottom: 1px solid #eee; 
                    cursor: pointer; 
                    transition: background-color 0.2s;
                " 
                onmouseover="this.style.backgroundColor='#f5f5f5'" 
                onmouseout="this.style.backgroundColor='white'"
                onclick="selectActor('${actor.name}')">
                    <img src="${profileUrl}" 
                         alt="${actor.name}" 
                         style="width: 40px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 1rem;">
                    <div>
                        <div style="font-weight: 600; color: #333; margin-bottom: 0.25rem;">${actor.name}</div>
                        <div style="font-size: 0.85rem; color: #666;">
                            ⭐ ${actor.popularity || 'N/A'} 
                            ${actor.place_of_birth ? ` • ${actor.place_of_birth.substring(0, 30)}` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        autocompleteResults.innerHTML = html;
    }

    window.selectActor = function(actorName) {
        searchInput.value = actorName;
        autocompleteResults.style.display = 'none';
        // Auto-submit the form
        searchInput.closest('form').submit();
    };
});
</script>
@endsection