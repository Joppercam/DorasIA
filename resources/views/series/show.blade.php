@extends('layouts.app')

@section('title', $series->title . ' - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background-image: url('{{ $series->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $series->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=' . urlencode($series->title) }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            
            <h1 class="hero-title">{{ $series->display_title }}</h1>
            @if($series->original_title && $series->original_title !== $series->display_title)
            <p class="hero-original-title">{{ $series->original_title }}</p>
            @endif
            
            <!-- Rating and Meta -->
            <div class="hero-meta">
                @if($series->vote_average > 0)
                <div class="hero-rating">
                    <span class="rating-stars">⭐</span>
                    <span class="rating-number">{{ number_format($series->vote_average, 1) }}</span>
                </div>
                @endif
                @if($series->first_air_date)
                <span class="hero-year">{{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}</span>
                @endif
                @if($series->number_of_episodes)
                <span class="hero-episodes">{{ $series->number_of_episodes }} episodios</span>
                @endif
                @if($series->number_of_seasons)
                <span class="hero-seasons">{{ $series->number_of_seasons }} temporadas</span>
                @endif
            </div>
            
            @if($series->display_overview)
            <p class="hero-description">{{ $series->display_overview }}</p>
            @endif
            
            <!-- User Actions -->
            @auth
            <div class="hero-actions">
                @include('components.rating-buttons', ['series' => $series])
                @include('components.watchlist-button', ['series' => $series])
            </div>
            @endauth
            
            <!-- Mobile Actions Bar -->
            <div class="mobile-actions-bar">
                @auth
                    @include('components.rating-buttons', ['series' => $series])
                    @include('components.watchlist-button', ['series' => $series])
                @else
                    <div class="mobile-auth-prompt">
                        <a href="{{ route('login') }}" class="mobile-login-btn">
                            🔑 Inicia Sesión para Calificar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Series Information -->
<div style="margin-top: -100px; position: relative; z-index: 20;" id="info">
    
    <!-- Main Info -->
    <section class="content-section">
        <div class="series-detail-container">
            <div class="series-poster">
                @if($series->poster_path)
                <img src="https://image.tmdb.org/t/p/w500{{ $series->poster_path }}" 
                     alt="{{ $series->display_title }}"
                     class="detail-poster-img">
                @else
                <div class="detail-poster-placeholder">
                    📺
                </div>
                @endif
            </div>
            
            <div class="series-info">
                <!-- Genres -->
                @if($series->genres->count() > 0)
                <div class="detail-section">
                    <h3 class="detail-section-title">Géneros</h3>
                    <div class="detail-genres">
                        @foreach($series->genres as $genre)
                        <span class="detail-genre-tag">{{ $genre->display_name ?: $genre->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Streaming Platforms -->
                <div class="detail-section">
                    <div class="streaming-platforms">
                        <div class="platform-item">
                            <span class="platform-name">Netflix</span>
                        </div>
                        <div class="platform-item">
                            <span class="platform-name">Viki</span>
                        </div>
                        <div class="platform-item">
                            <span class="platform-name">Disney+</span>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics - Horizontal Layout -->
                <div class="detail-section">
                    @include('components.series-stats-detail', ['series' => $series])
                </div>
                
                <!-- Details -->
                <div class="detail-section">
                    <h3 class="detail-section-title">Información</h3>
                    <div class="detail-grid">
                        @if($series->first_air_date)
                        <div class="detail-item">
                            <span class="detail-label">📅 Estreno</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($series->first_air_date)->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        
                        @if($series->status)
                        <div class="detail-item">
                            <span class="detail-label">📊 Estado</span>
                            <span class="detail-value">{{ $series->status === 'Ended' ? 'Finalizada' : ($series->status === 'Returning Series' ? 'En Emisión' : $series->status) }}</span>
                        </div>
                        @endif
                        
                        @if($series->number_of_seasons)
                        <div class="detail-item">
                            <span class="detail-label">📺 Temporadas</span>
                            <span class="detail-value">{{ $series->number_of_seasons }}</span>
                        </div>
                        @endif
                        
                        @if($series->number_of_episodes)
                        <div class="detail-item">
                            <span class="detail-label">🎬 Episodios</span>
                            <span class="detail-value">{{ $series->number_of_episodes }}</span>
                        </div>
                        @endif
                        
                        @if($series->original_language)
                        <div class="detail-item">
                            <span class="detail-label">🗣️ Idioma</span>
                            <span class="detail-value">{{ $series->original_language === 'ko' ? 'Coreano' : strtoupper($series->original_language) }}</span>
                        </div>
                        @endif
                        
                        @if($series->origin_country)
                        <div class="detail-item">
                            <span class="detail-label">🌍 País</span>
                            <span class="detail-value">{{ $series->origin_country === 'KR' ? 'Corea del Sur' : $series->origin_country }}</span>
                        </div>
                        @endif
                        
                        @if($series->vote_count > 0)
                        <div class="detail-item">
                            <span class="detail-label">👥 Votos</span>
                            <span class="detail-value">{{ number_format($series->vote_count) }}</span>
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
        <h2 class="section-title">🎭 Reparto Principal</h2>
        <div class="cast-grid">
            @foreach($series->people->where('pivot.department', 'Acting')->take(12) as $person)
            <div class="cast-card">
                <!-- Actor Detail Button -->
                <a href="{{ route('actors.show', $person->id) }}" class="actor-detail-btn" title="Ver perfil de {{ $person->name }}">
                    👤
                </a>
                
                <div class="cast-image">
                    @if($person->profile_path)
                    <img src="https://image.tmdb.org/t/p/w300{{ $person->profile_path }}" 
                         alt="{{ $person->name }}"
                         class="cast-photo">
                    @else
                    <div class="cast-placeholder">
                        👤
                    </div>
                    @endif
                </div>
                <div class="cast-info">
                    <h4 class="cast-name">{{ $person->name }}</h4>
                    @if($person->pivot->character)
                    <p class="cast-character">{{ $person->pivot->character }}</p>
                    @endif
                    @if($person->display_biography)
                    <p class="cast-bio">{{ Str::limit($person->display_biography, 100) }}</p>
                    @endif
                    <div class="cast-details">
                        @if($person->birthday)
                        <span class="cast-birth">🎂 {{ \Carbon\Carbon::parse($person->birthday)->format('d/m/Y') }}</span>
                        @endif
                        @if($person->display_place_of_birth)
                        <span class="cast-location">📍 {{ $person->display_place_of_birth }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Professional Reviews Section -->
    @if($positiveReviews->count() > 0 || $negativeReviews->count() > 0)
    <section class="content-section">
        <h2 class="section-title">🎬 Críticas Profesionales</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Positive Reviews -->
            @if($positiveReviews->count() > 0)
            <div>
                <h3 style="color: #28a745; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">👍</span> Críticas Positivas
                </h3>
                @foreach($positiveReviews as $review)
                <div class="detail-section" style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                        <div>
                            <h4 style="color: white; margin: 0; font-size: 1rem;">{{ $review->source }}</h4>
                            @if($review->author)
                            <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">por {{ $review->author }}</span>
                            @endif
                        </div>
                        @if($review->rating)
                        <div style="background: rgba(40, 167, 69, 0.2); color: #28a745; padding: 0.3rem 0.8rem; border-radius: 12px; font-weight: 600;">
                            ⭐ {{ $review->rating }}/{{ $review->max_rating }}
                        </div>
                        @endif
                    </div>
                    
                    <p style="color: rgba(255,255,255,0.9); line-height: 1.5; margin: 0.5rem 0;">
                        "{{ $review->display_excerpt }}"
                    </p>
                    
                    @if($review->source_url)
                    <a href="{{ $review->source_url }}" target="_blank" style="color: #00d4ff; text-decoration: none; font-size: 0.85rem;">
                        Leer crítica completa →
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
            
            <!-- Negative/Mixed Reviews -->
            @if($negativeReviews->count() > 0)
            <div>
                <h3 style="color: #dc3545; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">👎</span> Críticas Mixtas
                </h3>
                @foreach($negativeReviews as $review)
                <div class="detail-section" style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                        <div>
                            <h4 style="color: white; margin: 0; font-size: 1rem;">{{ $review->source }}</h4>
                            @if($review->author)
                            <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">por {{ $review->author }}</span>
                            @endif
                        </div>
                        @if($review->rating)
                        <div style="background: rgba(220, 53, 69, 0.2); color: #dc3545; padding: 0.3rem 0.8rem; border-radius: 12px; font-weight: 600;">
                            ⭐ {{ $review->rating }}/{{ $review->max_rating }}
                        </div>
                        @endif
                    </div>
                    
                    <p style="color: rgba(255,255,255,0.9); line-height: 1.5; margin: 0.5rem 0;">
                        "{{ $review->display_excerpt }}"
                    </p>
                    
                    @if($review->source_url)
                    <a href="{{ $review->source_url }}" target="_blank" style="color: #00d4ff; text-decoration: none; font-size: 0.85rem;">
                        Leer crítica completa →
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Comments Section -->
    <section class="content-section">
        <h2 class="section-title">💬 Comentarios ({{ $comments->total() }})</h2>
        
        @auth
        <!-- Comment Form -->
        <div class="comment-form-container">
            <form id="commentForm" class="comment-form">
                @csrf
                <textarea 
                    id="commentContent" 
                    placeholder="¿Qué te pareció esta serie? Comparte tu opinión..."
                    class="comment-textarea"
                    maxlength="1000"
                    required></textarea>
                
                <div class="comment-form-actions">
                    <label class="spoiler-checkbox">
                        <input type="checkbox" id="isSpoiler">
                        <span>⚠️ Contiene spoilers</span>
                    </label>
                    <button type="submit" class="comment-submit-btn">
                        📝 Publicar Comentario
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="auth-prompt">
            <p>💡 <a href="{{ route('login') }}">Inicia sesión</a> para participar en la conversación</p>
        </div>
        @endauth

        <!-- Comments List - Visible to everyone -->
        <div class="comments-container">
            @forelse($comments as $comment)
            <div class="comment {{ $comment->is_spoiler ? 'spoiler-comment' : '' }}">
                <div class="comment-header">
                    <div class="comment-user">
                        @if($comment->user->avatar)
                        <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="comment-avatar">
                        @else
                        <div class="comment-avatar-placeholder">{{ substr($comment->user->name, 0, 1) }}</div>
                        @endif
                        <div class="comment-user-info">
                            <span class="comment-username">{{ $comment->user->name }}</span>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($comment->is_spoiler)
                    <span class="spoiler-badge">⚠️ Spoiler</span>
                    @endif
                </div>
                
                <div class="comment-content {{ $comment->is_spoiler ? 'spoiler-hidden' : '' }}">
                    {{ $comment->content }}
                    @if($comment->is_spoiler)
                    <div class="spoiler-overlay">
                        <button class="reveal-spoiler-btn">👁️ Mostrar spoiler</button>
                    </div>
                    @endif
                </div>
                
                @if($comment->replies->count() > 0)
                <div class="comment-replies">
                    @foreach($comment->replies as $reply)
                    <div class="comment reply">
                        <div class="comment-header">
                            <div class="comment-user">
                                @if($reply->user->avatar)
                                <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}" class="comment-avatar">
                                @else
                                <div class="comment-avatar-placeholder">{{ substr($reply->user->name, 0, 1) }}</div>
                                @endif
                                <div class="comment-user-info">
                                    <span class="comment-username">{{ $reply->user->name }}</span>
                                    <span class="comment-date">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="comment-content">{{ $reply->content }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="no-comments">
                <p>🤔 ¡Sé el primero en comentar sobre esta serie!</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($comments->hasPages())
        <div class="comments-pagination">
            {{ $comments->links() }}
        </div>
        @endif
    </section>

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
/* Streaming Platforms Styling */
.streaming-platforms {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 0;
}

.platform-item {
    background: rgba(220, 38, 127, 0.2);
    color: #ff6b9d;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid rgba(220, 38, 127, 0.3);
    transition: all 0.3s ease;
}

.platform-item:hover {
    background: rgba(220, 38, 127, 0.3);
    border-color: rgba(220, 38, 127, 0.5);
    transform: translateY(-1px);
}

.platform-name {
    display: block;
}

/* Mobile Actions Bar */
.mobile-actions-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    z-index: 1000;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
}

.mobile-auth-prompt {
    text-align: center;
}

.mobile-login-btn {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.mobile-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    /* Show mobile actions bar */
    .mobile-actions-bar {
        display: block;
    }
    
    /* Hide desktop actions */
    .hero-actions {
        display: none !important;
    }
    
    /* Add bottom padding to avoid overlap */
    body {
        padding-bottom: 80px;
    }
    
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
    
    .streaming-platforms {
        justify-content: center;
    }
    
    .platform-item {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
    
    /* Mobile optimizations for series detail */
    .series-detail-container {
        display: block !important;
        gap: 1.5rem !important;
    }
    
    .series-poster {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .detail-poster-img {
        max-width: 200px !important;
        height: auto !important;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    }
    
    .hero-section {
        min-height: 60vh !important;
    }
    
    .hero-info-box {
        max-width: 100% !important;
        padding: 1.5rem !important;
        margin: 0 1rem !important;
    }
    
    .hero-title {
        font-size: 1.8rem !important;
        line-height: 1.2 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .hero-original-title {
        font-size: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .hero-meta {
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
        justify-content: center !important;
        margin-bottom: 1rem !important;
    }
    
    .hero-description {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
        text-align: center !important;
    }
    
    /* Cast grid mobile optimization */
    .cast-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .cast-card {
        padding: 1rem !important;
    }
    
    .cast-name {
        font-size: 0.9rem !important;
    }
    
    .cast-character {
        font-size: 0.8rem !important;
    }
    
    .cast-bio {
        display: none !important;
    }
    
    /* Professional reviews mobile */
    .content-section > div[style*="grid-template-columns: 1fr 1fr"] {
        display: block !important;
    }
    
    .content-section > div[style*="grid-template-columns: 1fr 1fr"] > div:first-child {
        margin-bottom: 2rem;
    }
    
    /* Comments mobile optimization */
    .comment-form-container {
        margin-bottom: 1.5rem;
    }
    
    .comment-textarea {
        min-height: 100px !important;
        font-size: 1rem !important;
    }
    
    .comment-form-actions {
        flex-direction: column !important;
        gap: 1rem !important;
        align-items: stretch !important;
    }
    
    .comment-submit-btn {
        width: 100% !important;
        padding: 1rem !important;
        font-size: 1rem !important;
    }
    
    .spoiler-checkbox {
        order: -1;
        text-align: center;
    }
}
</style>
@endsection