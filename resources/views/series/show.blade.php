@extends('layouts.app')

@section('title', $series->title . ' - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background-image: url('{{ $series->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $series->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=' . urlencode($series->title) }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ $series->display_title }}</h1>
        @if($series->original_title && $series->original_title !== $series->display_title)
        <p style="font-size: 1.1rem; color: #ccc; margin-bottom: 1rem;">{{ $series->original_title }}</p>
        @endif
        
        <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            @if($series->vote_average > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-weight: 600;">{{ number_format($series->vote_average, 1) }}/10</span>
            </div>
            @endif
            
            @if($series->first_air_date)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span>{{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}</span>
            </div>
            @endif
            
            @if($series->vote_count > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span>{{ number_format($series->vote_count) }} votos</span>
            </div>
            @endif
        </div>
        
        @if($series->display_overview)
        <p class="hero-description">{{ $series->display_overview }}</p>
        @endif
        
        <div class="hero-buttons">
            <a href="#" class="btn btn-primary">
                Ver Ahora
            </a>
            <a href="#info" class="btn btn-secondary">
                Más Información
            </a>
        </div>
    </div>
</section>

<!-- Series Information -->
<div style="margin-top: -100px; position: relative; z-index: 20;" id="info">
    
    <!-- Main Info -->
    <section class="content-section">
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 3rem; align-items: start;">
            <div>
                @if($series->poster_path)
                <img src="https://image.tmdb.org/t/p/w500{{ $series->poster_path }}" 
                     alt="{{ $series->display_title }}"
                     style="width: 100%; border-radius: 8px; box-shadow: 0 8px 25px rgba(0,0,0,0.5);">
                @else
                <div style="width: 100%; height: 450px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                    📺
                </div>
                @endif
            </div>
            
            <div>
                <!-- Genres -->
                @if($series->genres->count() > 0)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin-bottom: 1rem;">Géneros</h3>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @foreach($series->genres as $genre)
                        <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                            {{ $genre->display_name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Details -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin-bottom: 1rem;">Detalles</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        @if($series->first_air_date)
                        <div>
                            <strong style="color: #ccc;">Fecha de Estreno:</strong><br>
                            {{ \Carbon\Carbon::parse($series->first_air_date)->format('d/m/Y') }}
                        </div>
                        @endif
                        
                        @if($series->status)
                        <div>
                            <strong style="color: #ccc;">Estado:</strong><br>
                            {{ $series->status === 'Ended' ? 'Finalizada' : ($series->status === 'Returning Series' ? 'En Emisión' : $series->status) }}
                        </div>
                        @endif
                        
                        @if($series->number_of_seasons)
                        <div>
                            <strong style="color: #ccc;">Temporadas:</strong><br>
                            {{ $series->number_of_seasons }}
                        </div>
                        @endif
                        
                        @if($series->number_of_episodes)
                        <div>
                            <strong style="color: #ccc;">Episodios:</strong><br>
                            {{ $series->number_of_episodes }}
                        </div>
                        @endif
                        
                        @if($series->original_language)
                        <div>
                            <strong style="color: #ccc;">Idioma:</strong><br>
                            {{ $series->original_language === 'ko' ? 'Coreano' : strtoupper($series->original_language) }}
                        </div>
                        @endif
                        
                        @if($series->origin_country)
                        <div>
                            <strong style="color: #ccc;">País:</strong><br>
                            {{ $series->origin_country === 'KR' ? 'Corea del Sur' : $series->origin_country }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cast -->
    @if($series->people->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Reparto</h2>
        <div class="carousel-container">
            <div class="carousel">
                @foreach($series->people->take(20) as $person)
                <div style="min-width: 150px; text-align: center;">
                    @if($person->profile_path)
                    <img src="https://image.tmdb.org/t/p/w185{{ $person->profile_path }}" 
                         alt="{{ $person->name }}"
                         style="width: 150px; height: 225px; object-fit: cover; border-radius: 8px; margin-bottom: 0.5rem;">
                    @else
                    <div style="width: 150px; height: 225px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; margin-bottom: 0.5rem;">
                        👤
                    </div>
                    @endif
                    <div style="color: white; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem;">
                        {{ $person->name }}
                    </div>
                    @if($person->pivot->character)
                    <div style="color: #ccc; font-size: 0.8rem;">
                        {{ $person->pivot->character }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Seasons and Episodes -->
    @if($series->seasons->count() > 0)
    <section class="content-section">
        <h2 class="section-title">Temporadas y Episodios</h2>
        @foreach($series->seasons as $season)
        <div style="background: rgba(255,255,255,0.05); border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
            <h3 style="color: white; margin-bottom: 1rem;">
                {{ $season->name }}
                @if($season->episode_count)
                <span style="color: #ccc; font-weight: normal; font-size: 0.9rem;">({{ $season->episode_count }} episodios)</span>
                @endif
            </h3>
            
            @if($season->overview)
            <p style="color: #ccc; margin-bottom: 1rem;">{{ $season->overview }}</p>
            @endif
            
            @if($season->episodes->count() > 0)
            <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                @foreach($season->episodes->take(5) as $episode)
                <div style="display: flex; gap: 1rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 6px;">
                    @if($episode->still_path)
                    <img src="https://image.tmdb.org/t/p/w300{{ $episode->still_path }}" 
                         alt="Episodio {{ $episode->episode_number }}"
                         style="width: 120px; height: 68px; object-fit: cover; border-radius: 4px;">
                    @else
                    <div style="width: 120px; height: 68px; background: rgba(255,255,255,0.1); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                        📺
                    </div>
                    @endif
                    
                    <div style="flex: 1;">
                        <h4 style="color: white; margin-bottom: 0.5rem; font-size: 1rem;">
                            {{ $episode->episode_number }}. {{ $episode->name }}
                        </h4>
                        @if($episode->overview)
                        <p style="color: #ccc; font-size: 0.9rem; line-height: 1.4;">
                            {{ Str::limit($episode->overview, 200) }}
                        </p>
                        @endif
                        @if($episode->air_date)
                        <p style="color: #999; font-size: 0.8rem; margin-top: 0.5rem;">
                            {{ \Carbon\Carbon::parse($episode->air_date)->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
                
                @if($season->episodes->count() > 5)
                <div style="text-align: center; padding: 1rem;">
                    <span style="color: #ccc;">Y {{ $season->episodes->count() - 5 }} episodios más...</span>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

</div>

<style>
@media (max-width: 768px) {
    .content-section > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
    
    .content-section > div:first-child > div:first-child {
        text-align: center;
    }
    
    .content-section > div:first-child > div:first-child img,
    .content-section > div:first-child > div:first-child > div {
        max-width: 250px;
        margin: 0 auto;
    }
}
</style>
@endsection