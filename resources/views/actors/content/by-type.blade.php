@extends('layouts.app')

@section('title', $typeName . ' de ' . $actor->display_name . ' - Dorasia')

@section('content')
<div class="container content-by-type-page" style="padding-top: 80px; min-height: 100vh;">
    <!-- Header -->
    <div class="content-type-header">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            <span class="separator">/</span>
            <a href="{{ route('actors.index') }}">Actores</a>
            <span class="separator">/</span>
            <a href="{{ route('actors.show', $actor->id) }}">{{ $actor->display_name }}</a>
            <span class="separator">/</span>
            <span class="current">{{ $typeName }}</span>
        </div>
        
        <div class="type-header-content">
            <div class="actor-info">
                @if($actor->profile_path)
                <img src="https://image.tmdb.org/t/p/w185{{ $actor->profile_path }}" 
                     alt="{{ $actor->display_name }}"
                     class="actor-photo"
                     onerror="this.src='/images/no-actor-photo.svg'">
                @else
                <img src="/images/no-actor-photo.svg" 
                     alt="{{ $actor->display_name }}"
                     class="actor-photo">
                @endif
                
                <div class="actor-details">
                    <h1>{{ $typeName }} de {{ $actor->display_name }}</h1>
                    <p class="content-count">{{ $content->total() }} {{ Str::lower($typeName) }}{{ $content->total() != 1 ? 's' : '' }} disponibles</p>
                </div>
            </div>
            
            <a href="{{ route('actors.show', $actor->id) }}" class="back-button">
                ← Volver al perfil
            </a>
        </div>
    </div>
    
    <!-- Content Grid -->
    @if($content->count() > 0)
    <div class="content-grid">
        @foreach($content as $item)
        <article class="content-card type-{{ $item->type }}" onclick="window.location.href='{{ route('actors.content.show', [$actor->id, $item->id]) }}'">
            <!-- Thumbnail -->
            <div class="content-thumbnail">
                @if($actor->profile_path)
                <img src="https://image.tmdb.org/t/p/w500{{ $actor->profile_path }}" alt="{{ $actor->display_name }}" loading="lazy" onerror="this.src='/images/no-actor-photo.svg'">
                @else
                <img src="/images/no-actor-photo.svg" alt="{{ $actor->display_name }}" loading="lazy">
                @endif
                
                <!-- Content type indicator -->
                <div class="content-type-indicator">
                    {{ $item->getTypeIcon() }}
                </div>
                
                @if($item->duration_minutes)
                <span class="duration-badge">{{ $item->formatted_duration }}</span>
                @endif
                
                <div class="content-overlay">
                    <button class="play-button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Content Info -->
            <div class="content-info">
                <h3 class="content-title">{{ $item->title }}</h3>
                
                @if($item->description)
                <p class="content-description">{{ Str::limit($item->description, 150) }}</p>
                @endif
                
                <div class="content-meta">
                    <span class="publish-date">{{ $item->published_at->diffForHumans() }}</span>
                    <div class="content-stats">
                        <span class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                            {{ number_format($item->view_count) }}
                        </span>
                        <span class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            {{ number_format($item->like_count) }}
                        </span>
                    </div>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($content->hasPages())
    <div class="pagination-wrapper">
        {{ $content->links() }}
    </div>
    @endif
    
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No hay {{ Str::lower($typeName) }} disponibles</h3>
        <p>{{ $actor->display_name }} aún no tiene contenido de este tipo.</p>
        <a href="{{ route('actors.show', $actor->id) }}" class="btn btn-primary">
            Volver al perfil
        </a>
    </div>
    @endif
</div>

<style>
/* Content By Type Page Styles */
.content-by-type-page {
    background: #141414;
    color: white;
}

/* Breadcrumb */
.breadcrumb {
    font-size: 0.9rem;
    margin-bottom: 2rem;
    color: #999;
}

.breadcrumb a {
    color: #00d4ff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: #7b68ee;
}

.breadcrumb .separator {
    margin: 0 0.5rem;
    color: #666;
}

.breadcrumb .current {
    color: white;
}

/* Type Header */
.content-type-header {
    margin-bottom: 3rem;
}

.type-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.actor-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.actor-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #00d4ff;
}

.actor-details h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(to right, #00d4ff, #7b68ee);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.content-count {
    color: #999;
    font-size: 1.1rem;
}

.back-button {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: #00d4ff;
    color: #00d4ff;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

/* Content Card */
.content-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.content-card:hover {
    transform: translateY(-5px);
    border-color: #00d4ff;
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
}

/* Thumbnail */
.content-thumbnail {
    position: relative;
    padding-bottom: 75%; /* 4:3 Aspect Ratio para mostrar mejor las fotos de actores */
    background: #1a1a1a;
    overflow: hidden;
}

.content-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain; /* Cambiar a contain para mostrar la imagen completa */
    object-position: center;
}

.thumbnail-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #1a1a1a, #2a2a2a);
}

.content-icon {
    font-size: 3rem;
    opacity: 0.5;
}

.duration-badge {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    background: rgba(0, 0, 0, 0.8);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.content-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.content-card:hover .content-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(0, 212, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.play-button:hover {
    background: #00d4ff;
    transform: scale(1.1);
}

/* Content Info */
.content-info {
    padding: 1.5rem;
}

.content-title {
    font-size: 1.2rem;
    margin-bottom: 0.75rem;
    color: white;
    line-height: 1.4;
}

.content-description {
    color: #999;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.content-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
}

.publish-date {
    color: #666;
}

.content-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #999;
}

.stat-item svg {
    opacity: 0.7;
}

/* Content type indicator */
.content-type-indicator {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    z-index: 2;
}

/* Type-specific colors */
.type-interview .content-icon, .type-interview .content-type-indicator { color: #00d4ff; }
.type-behind_scenes .content-icon, .type-behind_scenes .content-type-indicator { color: #ff6b6b; }
.type-biography .content-icon, .type-biography .content-type-indicator { color: #4caf50; }
.type-news .content-icon, .type-news .content-type-indicator { color: #ff9800; }
.type-gallery .content-icon, .type-gallery .content-type-indicator { color: #e91e63; }
.type-video .content-icon, .type-video .content-type-indicator { color: #9c27b0; }
.type-article .content-icon, .type-article .content-type-indicator { color: #2196f3; }
.type-timeline .content-icon, .type-timeline .content-type-indicator { color: #00bcd4; }
.type-trivia .content-icon, .type-trivia .content-type-indicator { color: #ffc107; }
.type-social .content-icon, .type-social .content-type-indicator { color: #3f51b5; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: white;
}

.empty-state p {
    color: #999;
    margin-bottom: 2rem;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

/* Responsive */
@media (max-width: 768px) {
    .type-header-content {
        flex-direction: column;
    }
    
    .actor-info {
        flex-direction: column;
        text-align: center;
    }
    
    .actor-details h1 {
        font-size: 1.5rem;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .back-button {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
}
</style>

<script>
// Agregar método getTypeIcon si no existe
@if(!method_exists(\App\Models\ActorContent::class, 'getTypeIcon'))
<script>
const typeIcons = {
    'interview': '🎤',
    'behind_scenes': '🎬',
    'biography': '📖',
    'news': '📰',
    'gallery': '📸',
    'video': '🎥',
    'article': '📝',
    'timeline': '📅',
    'trivia': '💡',
    'social': '📱'
};

document.querySelectorAll('.content-icon').forEach(icon => {
    const card = icon.closest('.content-card');
    const classes = Array.from(card.classList);
    const typeClass = classes.find(c => c.startsWith('type-'));
    if (typeClass) {
        const type = typeClass.replace('type-', '');
        icon.textContent = typeIcons[type] || '📄';
    }
});
</script>
@endif
</script>
@endsection