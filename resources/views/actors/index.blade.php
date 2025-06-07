@extends('layouts.app')

@section('title', 'Actores Coreanos - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 6rem 0 4rem;">
    <div class="hero-overlay" style="background: linear-gradient(45deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.6) 100%);"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            <h1 class="hero-title" style="font-size: 3rem; margin-bottom: 1rem;">🎭 Actores Coreanos</h1>
            <p class="hero-description" style="font-size: 1.1rem; margin-bottom: 2rem;">
                Descubre a los talentosos actores y actrices que dan vida a tus K-Dramas favoritos
            </p>
            
            <!-- Search Form -->
            <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem;">
                <form method="GET" action="{{ route('actors.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Buscar actores..." 
                        style="flex: 1; min-width: 200px; padding: 0.8rem 1rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.9); color: #333;">
                    
                    <select name="filter" style="padding: 0.8rem 1rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.9); color: #333;">
                        <option value="korean" {{ request('filter') === 'korean' ? 'selected' : '' }}>Actores Coreanos</option>
                        <option value="popular" {{ request('filter') === 'popular' ? 'selected' : '' }}>Más Populares</option>
                        <option value="" {{ !request('filter') ? 'selected' : '' }}>Todos</option>
                    </select>
                    
                    <button type="submit" class="btn-hero" style="padding: 0.8rem 1.5rem;">
                        🔍 Buscar
                    </button>
                </form>
            </div>

            <!-- Featured Actors Preview -->
            @if($featuredActors->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; max-width: 600px;">
                @foreach($featuredActors->take(6) as $actor)
                <div style="text-align: center;">
                    @if($actor->profile_path)
                    <img src="https://image.tmdb.org/t/p/w200{{ $actor->profile_path }}" 
                         alt="{{ $actor->name }}"
                         style="width: 80px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); margin-bottom: 0.5rem;">
                    @else
                    <div style="width: 80px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: white; font-size: 2rem;">👤</div>
                    @endif
                    <span style="font-size: 0.8rem; color: rgba(255,255,255,0.9); font-weight: 600;">{{ Str::limit($actor->name, 15) }}</span>
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
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
        @foreach($actors as $actor)
        <div class="cast-card" style="transition: transform 0.3s ease;">
            <a href="{{ route('actors.show', $actor->id) }}" style="text-decoration: none; color: inherit;">
                <div class="cast-image" style="text-align: center; margin-bottom: 1rem;">
                    @if($actor->profile_path)
                    <img src="https://image.tmdb.org/t/p/w300{{ $actor->profile_path }}" 
                         alt="{{ $actor->name }}"
                         class="cast-photo" style="width: 150px; height: 200px; object-fit: cover; border-radius: 12px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);">
                    @else
                    <div class="cast-placeholder" style="width: 150px; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem; margin: 0 auto;">
                        👤
                    </div>
                    @endif
                </div>
                
                <div class="cast-info">
                    <h3 class="cast-name" style="color: white; font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem; text-align: center;">
                        {{ $actor->name }}
                    </h3>
                    
                    @if($actor->biography)
                    <p class="cast-bio" style="color: rgba(255, 255, 255, 0.8); font-size: 0.85rem; line-height: 1.4; margin-bottom: 1rem; text-align: center;">
                        {{ Str::limit($actor->biography, 120) }}
                    </p>
                    @endif
                    
                    <div class="cast-details" style="display: flex; flex-direction: column; gap: 0.3rem; align-items: center;">
                        @if($actor->birthday)
                        <span class="cast-birth" style="color: rgba(255, 255, 255, 0.6); font-size: 0.8rem;">
                            🎂 {{ \Carbon\Carbon::parse($actor->birthday)->format('d/m/Y') }}
                        </span>
                        @endif
                        
                        @if($actor->place_of_birth)
                        <span class="cast-location" style="color: rgba(255, 255, 255, 0.6); font-size: 0.8rem; text-align: center;">
                            📍 {{ $actor->place_of_birth }}
                        </span>
                        @endif
                        
                        @if($actor->popularity)
                        <span style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); color: white; padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600; margin-top: 0.5rem;">
                            ⭐ {{ number_format($actor->popularity, 1) }}
                        </span>
                        @endif
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
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem !important;
    }
    
    .hero-description {
        font-size: 0.95rem !important;
    }
    
    .hero-info-box form {
        flex-direction: column !important;
    }
    
    .hero-info-box form input,
    .hero-info-box form select,
    .hero-info-box form button {
        width: 100% !important;
        min-width: unset !important;
    }
    
    .actors-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        gap: 1rem !important;
    }
    
    .cast-photo {
        width: 100px !important;
        height: 130px !important;
    }
    
    .cast-placeholder {
        width: 100px !important;
        height: 130px !important;
        font-size: 2.5rem !important;
    }
}
</style>
@endsection