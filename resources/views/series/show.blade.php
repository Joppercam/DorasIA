@extends('layouts.app')

@section('title', $series->title . ' - Dorasia')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background-image: url('{{ $series->backdrop_path ? 'https://image.tmdb.org/t/p/original' . $series->backdrop_path : 'https://via.placeholder.com/1920x1080/333/666?text=' . urlencode($series->title) }}')">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-info-box">
            <!-- Mobile Poster -->
            <div class="d-block d-md-none">
                @if($series->poster_path)
                <img src="https://image.tmdb.org/t/p/w500{{ $series->poster_path }}" 
                     alt="{{ $series->display_title }}"
                     class="series-mobile-poster">
                @else
                <div class="series-mobile-poster-placeholder">
                    📺
                </div>
                @endif
            </div>
            
            <!-- Mobile Genres - right after poster -->
            <div class="d-block d-md-none mobile-genres">
                @if($series->genres->count() > 0)
                    @foreach($series->genres->take(3) as $genre)
                        <span class="mobile-genre-tag">{{ $genre->display_name ?: $genre->name }}</span>
                    @endforeach
                @endif
            </div>
            
            <!-- Mobile Streaming Platforms -->
            <div class="d-block d-md-none mobile-streaming">
                <div class="streaming-platforms-mobile">
                    <div class="platform-item-mobile">
                        <span class="platform-name-mobile">Netflix</span>
                    </div>
                    <div class="platform-item-mobile">
                        <span class="platform-name-mobile">Viki</span>
                    </div>
                    <div class="platform-item-mobile">
                        <span class="platform-name-mobile">Disney+</span>
                    </div>
                </div>
            </div>
            
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
            
            <!-- User Actions for Series - MOBILE -->
            @auth
            <div class="movie-actions d-block d-md-none">
                @include('components.movie-rating-buttons', ['movie' => $series])
            </div>
            @endauth
            
            <!-- User Actions -->
            @auth
            <div class="hero-actions d-none d-md-flex">
                @include('components.rating-buttons', ['series' => $series])
            </div>
            @endauth
            
            <style>
                /* FORCE DIFFERENT COLORS FOR SERIES BUTTONS */
                @media (max-width: 768px) {
                    .movie-actions button.movie-rating-btn.dislike {
                        background: #dc3545 !important;
                        background-color: #dc3545 !important;
                    }
                    
                    .movie-actions button.movie-rating-btn.like {
                        background: #28a745 !important;
                        background-color: #28a745 !important;
                    }
                    
                    .movie-actions button.movie-rating-btn.love {
                        background: #e91e63 !important;
                        background-color: #e91e63 !important;
                    }
                    
                    .movie-actions button.movie-rating-btn.watchlist {
                        background: #6f42c1 !important;
                        background-color: #6f42c1 !important;
                    }
                    
                    .movie-actions button.movie-rating-btn {
                        border: none !important;
                        color: white !important;
                    }
                    .hero-actions {
                        display: flex !important;
                        justify-content: center !important;
                        margin-top: 1.5rem !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                    }
                    
                    .hero-actions .card-rating-buttons {
                        display: flex !important;
                        gap: 2rem !important;
                        justify-content: space-around !important;
                        width: 100% !important;
                        max-width: 300px !important;
                    }
                    
                    .hero-actions button.rating-btn {
                        display: flex !important;
                        border: none !important;
                        color: white !important;
                        width: 50px !important;
                        height: 50px !important;
                        border-radius: 50% !important;
                        align-items: center !important;
                        justify-content: center !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                    }
                    
                    .hero-actions button.rating-btn.dislike,
                    button.rating-btn.dislike {
                        background: #dc3545 !important;
                        background-color: #dc3545 !important;
                    }
                    
                    .hero-actions button.rating-btn.like,
                    button.rating-btn.like {
                        background: #28a745 !important;
                        background-color: #28a745 !important;
                    }
                    
                    .hero-actions button.rating-btn.love,
                    button.rating-btn.love {
                        background: #e91e63 !important;
                        background-color: #e91e63 !important;
                    }
                    
                    .hero-actions button.rating-btn.watched,
                    button.rating-btn.watched {
                        background: #6f42c1 !important;
                        background-color: #6f42c1 !important;
                    }
                    
                    .hero-actions .rating-button-with-count {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        gap: 0.5rem !important;
                    }
                    
                    .hero-actions .rating-count {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                    }
                    
                    .hero-actions .count-number {
                        font-size: 0.9rem !important;
                        font-weight: bold !important;
                        color: white !important;
                    }
                    
                    .hero-actions .count-label {
                        font-size: 0.7rem !important;
                        color: rgba(255, 255, 255, 0.7) !important;
                    }
                }
            </style>
            
            <script>
                // FORCE DIFFERENT COLORS FOR SERIES MOBILE BUTTONS
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        const buttons = document.querySelectorAll('.movie-actions button.movie-rating-btn');
                        buttons.forEach(function(btn) {
                            if (btn.classList.contains('dislike')) {
                                btn.style.backgroundColor = '#dc3545';
                                btn.style.background = '#dc3545';
                            } else if (btn.classList.contains('like')) {
                                btn.style.backgroundColor = '#28a745';
                                btn.style.background = '#28a745';
                            } else if (btn.classList.contains('love')) {
                                btn.style.backgroundColor = '#e91e63';
                                btn.style.background = '#e91e63';
                            }
                            btn.style.border = 'none';
                            btn.style.color = 'white';
                            btn.style.display = 'flex';
                            btn.style.alignItems = 'center';
                            btn.style.justifyContent = 'center';
                        });
                    }, 100);
                });
            </script>
            
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

                <!-- Episode Progress - Only for authenticated users -->
                @auth
                <div class="detail-section">
                    @include('components.episode-progress', ['series' => $series, 'showEpisodes' => false])
                </div>
                @endauth
                
                <!-- Enhanced Series Details -->
                <div class="series-details-modern">
                    <div class="details-cards-grid">
                        @if($series->first_air_date)
                        <div class="detail-card premiere-card">
                            <div class="detail-icon">📅</div>
                            <div class="detail-content">
                                <span class="detail-label">Estreno</span>
                                <span class="detail-value">{{ \Carbon\Carbon::parse($series->first_air_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($series->status)
                        <div class="detail-card status-card">
                            <div class="detail-icon">{{ $series->status === 'Ended' ? '🏁' : '📡' }}</div>
                            <div class="detail-content">
                                <span class="detail-label">Estado</span>
                                <span class="detail-value status-{{ strtolower(str_replace(' ', '-', $series->status)) }}">
                                    {{ $series->status === 'Ended' ? 'Finalizada' : ($series->status === 'Returning Series' ? 'En Emisión' : $series->status) }}
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        @if($series->number_of_seasons && $series->number_of_episodes)
                        <div class="detail-card episodes-card">
                            <div class="detail-icon">📺</div>
                            <div class="detail-content">
                                <span class="detail-label">Contenido</span>
                                <span class="detail-value">{{ $series->number_of_seasons }} temp. • {{ $series->number_of_episodes }} ep.</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($series->original_language)
                        <div class="detail-card language-card">
                            <div class="detail-icon">🎌</div>
                            <div class="detail-content">
                                <span class="detail-label">Origen</span>
                                <span class="detail-value">{{ $series->original_language === 'ko' ? 'Coreano' : strtoupper($series->original_language) }} • {{ $series->origin_country === 'KR' ? 'Corea del Sur' : $series->origin_country }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($series->vote_average > 0)
                        <div class="detail-card rating-card">
                            <div class="detail-icon">⭐</div>
                            <div class="detail-content">
                                <span class="detail-label">Calificación</span>
                                <span class="detail-value rating-score">{{ number_format($series->vote_average, 1) }}/10 <small>({{ number_format($series->vote_count) }} votos)</small></span>
                            </div>
                        </div>
                        @endif
                        
                        @if($series->first_air_date)
                        <div class="detail-card year-card">
                            <div class="detail-icon">🗓️</div>
                            <div class="detail-content">
                                <span class="detail-label">Año</span>
                                <span class="detail-value">{{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cast Accordion -->
    @if($series->people->count() > 0)
    <section class="content-section">
        <div class="cast-accordion-container" x-data="{ open: false }">
            <!-- Cast Header -->
            <div class="cast-header" @click="open = !open">
                <div class="cast-header-info">
                    <h2 class="cast-title">
                        🎭 Reparto Principal
                        <span class="cast-count">({{ $series->people->where('pivot.department', 'Acting')->count() }} actores)</span>
                    </h2>
                    <p class="cast-subtitle">Conoce a los talentosos actores de {{ $series->display_title }}</p>
                </div>
                <div class="cast-toggle">
                    <span x-text="open ? '▼' : '▶'" class="toggle-icon"></span>
                </div>
            </div>

            <!-- Cast Content -->
            <div class="cast-content" x-show="open" x-transition>
                <div class="cast-grid-compact">
                    @foreach($series->people->where('pivot.department', 'Acting')->take(16) as $person)
                    <div class="actor-card-simple">
                        <div class="actor-image-simple">
                            @if($person->profile_path)
                            <img src="https://image.tmdb.org/t/p/w200{{ $person->profile_path }}" alt="{{ $person->name }}">
                            @else
                            <div class="actor-placeholder-simple">👤</div>
                            @endif
                        </div>
                        <div class="actor-info-simple">
                            <p class="actor-name-simple">{{ $person->name }}</p>
                            @if($person->pivot->character)
                            <p class="actor-role-simple">{{ $person->pivot->character }}</p>
                            @endif
                        </div>
                        
                        <!-- Action buttons -->
                        <div class="actor-actions">
                            <a href="{{ route('actors.show', $person->id) }}" class="actor-btn actor-btn-view" title="Ver perfil">
                                Ver
                            </a>
                            @auth
                            <button onclick="followActor({{ $person->id }}, this)" class="actor-btn actor-btn-follow" title="Seguir actor">
                                Seguir
                            </button>
                            @endauth
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($series->people->where('pivot.department', 'Acting')->count() > 16)
                <div class="cast-footer">
                    <p class="cast-more-info">
                        🎬 Mostrando 16 de {{ $series->people->where('pivot.department', 'Acting')->count() }} actores principales
                    </p>
                </div>
                @endif
            </div>
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

    <!-- Comments Accordion -->
    <section class="content-section">
        <div class="comments-accordion-container" x-data="{ open: false }">
            <!-- Comments Header -->
            <div class="comments-header-accordion" @click="open = !open">
                <div class="comments-header-info">
                    <h2 class="comments-title-accordion">
                        💬 Comentarios de la Comunidad
                        <span class="comments-count-badge">{{ $comments->total() }}</span>
                    </h2>
                    <p class="comments-subtitle-accordion">{{ $comments->total() > 0 ? 'Descubre qué opina la comunidad sobre ' . $series->display_title : 'Sé el primero en comentar sobre ' . $series->display_title }}</p>
                </div>
                <div class="comments-toggle">
                    <span x-text="open ? '▼' : '▶'" class="toggle-icon"></span>
                </div>
            </div>

            <!-- Comments Content -->
            <div class="comments-content-accordion" x-show="open" x-transition>
                @auth
                <!-- Compact Comment Form -->
                <div class="comment-form-compact">
                    <div class="form-user-compact">
                        @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="user-avatar-compact">
                        @else
                        <div class="user-avatar-compact-placeholder">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        @endif
                        <span class="user-name-compact">{{ auth()->user()->name }}</span>
                    </div>
                    
                    <form id="commentForm" class="form-compact">
                        @csrf
                        <div class="textarea-compact-container">
                            <textarea 
                                id="commentContent" 
                                placeholder="Comparte tu opinión sobre {{ $series->display_title }}..."
                                class="textarea-compact"
                                maxlength="1000"
                                rows="3"
                                required></textarea>
                        </div>
                        
                        <div class="form-actions-compact">
                            <label class="spoiler-compact">
                                <input type="checkbox" id="isSpoiler">
                                <span class="spoiler-check"></span>
                                <span class="spoiler-label">⚠️ Spoiler</span>
                            </label>
                            <button type="submit" class="submit-compact">
                                <span>✨ Comentar</span>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="auth-prompt-compact">
                    <div class="auth-content-compact">
                        <span class="auth-icon-compact">🔑</span>
                        <span class="auth-text-compact">Inicia sesión para participar en la conversación</span>
                        <a href="{{ route('login') }}" class="auth-btn-compact">
                            Iniciar Sesión
                        </a>
                    </div>
                </div>
                @endauth

                <!-- Compact Comments List -->
                <div class="comments-list-compact">
                    @forelse($comments as $comment)
                    <div class="comment-compact {{ $comment->is_spoiler ? 'has-spoiler' : '' }}">
                        <div class="comment-avatar-compact">
                            @if($comment->user && $comment->user->avatar)
                            <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="avatar-img-compact">
                            @elseif($comment->user)
                            <div class="avatar-placeholder-compact">{{ substr($comment->user->name, 0, 1) }}</div>
                            @else
                            <div class="avatar-placeholder-compact">?</div>
                            @endif
                        </div>
                        
                        <div class="comment-body-compact">
                            <div class="comment-header-compact">
                                <span class="author-compact">{{ $comment->user ? $comment->user->name : 'Usuario eliminado' }}</span>
                                <span class="time-compact">{{ $comment->created_at->diffForHumans() }}</span>
                                @if($comment->is_spoiler)
                                <span class="spoiler-badge-compact">⚠️</span>
                                @endif
                            </div>
                            
                            <div class="comment-text-compact {{ $comment->is_spoiler ? 'spoiler-protected' : '' }}">
                                @if($comment->is_spoiler)
                                <div class="spoiler-guard-compact">
                                    <span class="spoiler-warning-compact">⚠️ Contiene spoilers</span>
                                    <button class="reveal-spoiler-compact">👁️ Mostrar</button>
                                </div>
                                <div class="spoiler-content-hidden">
                                    {{ $comment->content }}
                                </div>
                                @else
                                <p>{{ $comment->content }}</p>
                                @endif
                            </div>
                            
                            <div class="comment-actions-compact">
                                <button class="action-compact like">
                                    <span>👍 Me gusta</span>
                                </button>
                                <button class="action-compact reply">
                                    <span>💬 Responder</span>
                                </button>
                            </div>
                            
                            @if($comment->replies->count() > 0)
                            <div class="replies-compact">
                                @foreach($comment->replies->take(3) as $reply)
                                <div class="reply-compact">
                                    <div class="reply-avatar-compact">
                                        @if($reply->user && $reply->user->avatar)
                                        <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}" class="reply-avatar-img">
                                        @elseif($reply->user)
                                        <div class="reply-avatar-placeholder">{{ substr($reply->user->name, 0, 1) }}</div>
                                        @else
                                        <div class="reply-avatar-placeholder">?</div>
                                        @endif
                                    </div>
                                    <div class="reply-body-compact">
                                        <div class="reply-header-compact">
                                            <span class="reply-author-compact">{{ $reply->user ? $reply->user->name : 'Usuario eliminado' }}</span>
                                            <span class="reply-time-compact">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="reply-text-compact">{{ $reply->content }}</p>
                                    </div>
                                </div>
                                @endforeach
                                @if($comment->replies->count() > 3)
                                <div class="more-replies-compact">
                                    <span>+ {{ $comment->replies->count() - 3 }} respuestas más</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="no-comments-compact">
                        <div class="empty-compact">
                            <span class="empty-icon-compact">💭</span>
                            <span class="empty-text-compact">Aún no hay comentarios. ¡Sé el primero!</span>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($comments->hasPages())
                <div class="pagination-compact">
                    {{ $comments->links() }}
                </div>
                @endif
                
                @if($comments->total() > 10)
                <div class="comments-footer-compact">
                    <p class="comments-total-info">
                        💬 Mostrando {{ $comments->count() }} de {{ $comments->total() }} comentarios
                    </p>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Enhanced Seasons and Episodes -->
    @if($series->seasons->count() > 0)
    <section class="content-section">
        <h2 class="section-title">📺 Temporadas y Episodios</h2>
        <div class="seasons-container" x-data="seasonsViewer()">
            @foreach($series->seasons as $seasonIndex => $season)
            <div class="season-accordion" x-data="{ open: false }">
                <!-- Season Header -->
                <div class="season-header" @click="open = !open">
                    <div class="season-info">
                        <h3 class="season-title">
                            {{ $season->name }}
                            @if($season->episode_count)
                            <span class="season-episode-count">({{ $season->episode_count }} episodios)</span>
                            @endif
                        </h3>
                        @if($season->air_date)
                        <span class="season-year">{{ \Carbon\Carbon::parse($season->air_date)->format('Y') }}</span>
                        @endif
                        @if($season->vote_average > 0)
                        <span class="season-rating">⭐ {{ number_format($season->vote_average, 1) }}</span>
                        @endif
                    </div>
                    <div class="season-toggle">
                        <span x-text="open ? '▼' : '▶'" class="toggle-icon"></span>
                    </div>
                </div>

                <!-- Season Overview -->
                @if($season->overview)
                <div class="season-overview" x-show="open" x-transition>
                    <p>{{ $season->overview }}</p>
                </div>
                @endif
                
                <!-- Episodes List -->
                @if($season->episodes->count() > 0)
                <div class="episodes-container" x-show="open" x-transition>
                    @foreach($season->episodes as $episode)
                    <div class="episode-card" x-data="episodeCard({ episodeId: {{ $episode->id }}, seriesId: {{ $series->id }} })">
                        <!-- Episode Image -->
                        <div class="episode-image">
                            @if($episode->still_path)
                            <img src="https://image.tmdb.org/t/p/w400{{ $episode->still_path }}" 
                                 alt="Episodio {{ $episode->episode_number }}: {{ $episode->name }}"
                                 class="episode-thumbnail"
                                 loading="lazy">
                            @else
                            <div class="episode-placeholder">
                                📺
                            </div>
                            @endif
                            
                            <!-- Episode Number Badge -->
                            <div class="episode-number-badge">
                                {{ $episode->episode_number }}
                            </div>
                            
                            <!-- Watch Status Badge -->
                            @auth
                            <div class="episode-watch-status" x-show="watchStatus">
                                <span x-show="watchStatus === 'completed'" class="status-completed">✓</span>
                                <span x-show="watchStatus === 'watching'" class="status-watching">⏸</span>
                            </div>
                            @endauth
                        </div>

                        <!-- Episode Details -->
                        <div class="episode-details">
                            <div class="episode-header">
                                <h4 class="episode-title">{{ $episode->name }}</h4>
                                <div class="episode-meta">
                                    @if($episode->air_date)
                                    <span class="episode-date">📅 {{ \Carbon\Carbon::parse($episode->air_date)->format('d/m/Y') }}</span>
                                    @endif
                                    @if($episode->runtime)
                                    <span class="episode-runtime">⏱️ {{ $episode->runtime }}min</span>
                                    @endif
                                    @if($episode->vote_average > 0)
                                    <span class="episode-rating">⭐ {{ number_format($episode->vote_average, 1) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Episode Synopsis -->
                            @if($episode->overview)
                            <div class="episode-synopsis">
                                <p class="synopsis-text" x-data="{ expanded: false }">
                                    <span x-text="expanded ? '{{ addslashes($episode->overview) }}' : '{{ addslashes(Str::limit($episode->overview, 150)) }}'"></span>
                                    @if(strlen($episode->overview) > 150)
                                    <button @click="expanded = !expanded" class="synopsis-toggle">
                                        <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                                    </button>
                                    @endif
                                </p>
                            </div>
                            @endif

                            <!-- Episode Actions -->
                            @auth
                            <div class="episode-actions">
                                <button @click="toggleWatched()" 
                                        class="btn-episode-action" 
                                        :class="watchStatus === 'completed' ? 'btn-unwatched' : 'btn-watched'"
                                        :disabled="loading">
                                    <span x-show="loading">⏳</span>
                                    <span x-show="!loading && watchStatus === 'completed'">✗ Marcar como no visto</span>
                                    <span x-show="!loading && watchStatus !== 'completed'">✓ Marcar como visto</span>
                                </button>
                            </div>
                            @else
                            <div class="episode-auth-prompt">
                                <a href="{{ route('login') }}" class="btn-login-episode">
                                    🔑 Inicia sesión para seguir progreso
                                </a>
                            </div>
                            @endauth

                            <!-- Episode Progress Bar -->
                            @auth
                            <div class="episode-progress" x-show="watchProgress > 0 && watchProgress < 100">
                                <div class="progress-bar">
                                    <div class="progress-fill" :style="`width: ${watchProgress}%`"></div>
                                </div>
                                <span class="progress-text" x-text="`${watchProgress}% visto`"></span>
                            </div>
                            @endauth
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="no-episodes" x-show="open" x-transition>
                    <p>📭 No hay episodios disponibles para esta temporada</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
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

/* Modern Series Details Cards */
.series-details-modern {
    margin: 1.5rem 0;
}

.details-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.detail-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1.2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.detail-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #00d4ff, #7b68ee);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.detail-card:hover {
    transform: translateY(-3px);
    background: linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.06));
    border-color: rgba(0, 212, 255, 0.3);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.detail-card:hover::before {
    opacity: 1;
}

.detail-icon {
    font-size: 2rem;
    background: rgba(0, 212, 255, 0.15);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid rgba(0, 212, 255, 0.2);
}

.detail-content {
    flex: 1;
    min-width: 0;
}

.detail-label {
    display: block;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    display: block;
    color: white;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

/* Specific card styling */
.premiere-card .detail-icon {
    background: rgba(76, 175, 80, 0.15);
    border-color: rgba(76, 175, 80, 0.2);
}

.status-card .detail-icon {
    background: rgba(255, 193, 7, 0.15);
    border-color: rgba(255, 193, 7, 0.2);
}

.episodes-card .detail-icon {
    background: rgba(156, 39, 176, 0.15);
    border-color: rgba(156, 39, 176, 0.2);
}

.language-card .detail-icon {
    background: rgba(255, 87, 34, 0.15);
    border-color: rgba(255, 87, 34, 0.2);
}

.rating-card .detail-icon {
    background: rgba(255, 193, 7, 0.15);
    border-color: rgba(255, 193, 7, 0.2);
}

.year-card .detail-icon {
    background: rgba(96, 125, 139, 0.15);
    border-color: rgba(96, 125, 139, 0.2);
}

/* Status-specific styling */
.status-ended {
    color: #ff6b6b !important;
}

.status-returning-series {
    color: #4ecdc4 !important;
}

.rating-score small {
    color: rgba(255,255,255,0.6);
    font-weight: 400;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .details-cards-grid {
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }
    
    .detail-card {
        padding: 1rem;
    }
    
    .detail-icon {
        width: 40px;
        height: 40px;
        font-size: 1.5rem;
    }
    
    .detail-value {
        font-size: 0.9rem;
    }
    
    .detail-label {
        font-size: 0.8rem;
    }
}

/* Enhanced Seasons and Episodes Styles */
.seasons-container {
    margin-top: 1rem;
}

.season-accordion {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
}

.season-header {
    padding: 1.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.season-header:hover {
    background: rgba(255,255,255,0.05);
}

.season-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.season-title {
    color: white;
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.season-episode-count {
    color: #ccc;
    font-weight: normal;
    font-size: 0.9rem;
}

.season-year, .season-rating {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.season-rating {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.toggle-icon {
    color: #00d4ff;
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.season-overview {
    padding: 0 1.5rem 1rem;
    color: #ccc;
    line-height: 1.6;
}

.episodes-container {
    padding: 0 1.5rem 1.5rem;
    display: grid;
    gap: 1rem;
}

.episode-card {
    background: rgba(255,255,255,0.03);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.05);
}

.episode-card:hover {
    background: rgba(255,255,255,0.08);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.episode-image {
    position: relative;
    flex-shrink: 0;
}

.episode-thumbnail {
    width: 160px;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.episode-placeholder {
    width: 160px;
    height: 90px;
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 2rem;
}

.episode-number-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.episode-watch-status {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.status-completed {
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 50%;
    font-size: 0.8rem;
}

.status-watching {
    background: rgba(255, 193, 7, 0.9);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 50%;
    font-size: 0.8rem;
}

.episode-details {
    flex: 1;
    min-width: 0;
}

.episode-header {
    margin-bottom: 0.8rem;
}

.episode-title {
    color: white;
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.episode-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.episode-date, .episode-runtime, .episode-rating {
    color: #ccc;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.episode-synopsis {
    margin-bottom: 1rem;
}

.synopsis-text {
    color: #ccc;
    line-height: 1.5;
    margin: 0;
}

.synopsis-toggle {
    background: none;
    border: none;
    color: #00d4ff;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0;
    margin-left: 0.5rem;
    text-decoration: underline;
}

.synopsis-toggle:hover {
    color: #7b68ee;
}

.episode-actions {
    margin-bottom: 0.8rem;
}

.btn-episode-action {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-episode-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

.btn-unwatched {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.btn-unwatched:hover {
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.episode-auth-prompt {
    margin-bottom: 0.8rem;
}

.btn-login-episode {
    background: rgba(255,255,255,0.1);
    color: #ccc;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.9rem;
    display: inline-block;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.2);
}

.btn-login-episode:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    text-decoration: none;
}

.episode-progress {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.progress-bar {
    flex: 1;
    height: 4px;
    background: rgba(255,255,255,0.2);
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00d4ff 0%, #7b68ee 100%);
    transition: width 0.3s ease;
}

.progress-text {
    color: #ccc;
    font-size: 0.8rem;
    min-width: 60px;
}

.no-episodes {
    padding: 2rem;
    text-align: center;
    color: #ccc;
}

/* Mobile Optimizations for Episodes */
@media (max-width: 768px) {
    .episode-card {
        flex-direction: column;
        gap: 1rem;
    }
    
    .episode-image {
        align-self: center;
    }
    
    .episode-thumbnail, .episode-placeholder {
        width: 100%;
        max-width: 300px;
        height: auto;
        aspect-ratio: 16/9;
    }
    
    .episode-meta {
        justify-content: center;
        text-align: center;
    }
    
    .episode-actions {
        text-align: center;
    }
    
    .btn-episode-action {
        width: 100%;
        padding: 0.8rem;
    }
    
    .season-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .season-title {
        font-size: 1.1rem;
    }
}

/* Comments Accordion Styles */
.comments-accordion-container {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
}

.comments-header-accordion {
    padding: 1.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comments-header-accordion:hover {
    background: rgba(255,255,255,0.05);
}

.comments-header-info {
    flex: 1;
}

.comments-title-accordion {
    color: white;
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.comments-count-badge {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.comments-subtitle-accordion {
    color: rgba(255,255,255,0.7);
    margin: 0;
    font-size: 0.9rem;
}

.comments-toggle {
    margin-left: 1rem;
}

.comments-content-accordion {
    padding: 1.5rem;
}

/* Compact Comment Form */
.comment-form-compact {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.form-user-compact {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.user-avatar-compact, .user-avatar-compact-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid rgba(0, 212, 255, 0.2);
    object-fit: cover;
}

.user-avatar-compact-placeholder {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 0.9rem;
}

.user-name-compact {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.textarea-compact {
    width: 100%;
    background: rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 0.8rem;
    color: white;
    font-size: 0.9rem;
    resize: vertical;
    transition: all 0.3s ease;
    font-family: inherit;
    margin-bottom: 0.8rem;
}

.textarea-compact:focus {
    outline: none;
    border-color: rgba(0, 212, 255, 0.3);
    background: rgba(0,0,0,0.3);
}

.textarea-compact::placeholder {
    color: rgba(255,255,255,0.5);
}

.form-actions-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.spoiler-compact {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    user-select: none;
}

.spoiler-compact input[type="checkbox"] {
    display: none;
}

.spoiler-check {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.spoiler-compact input:checked + .spoiler-check {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
    border-color: #ff6b9d;
}

.spoiler-compact input:checked + .spoiler-check::after {
    content: '✓';
    position: absolute;
    top: -2px;
    left: 2px;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.spoiler-label {
    color: rgba(255,255,255,0.8);
    font-size: 0.8rem;
}

.submit-compact {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-compact:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

/* Compact Auth Prompt */
.auth-prompt-compact {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.auth-content-compact {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: space-between;
    flex-wrap: wrap;
}

.auth-icon-compact {
    font-size: 1.5rem;
}

.auth-text-compact {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
    flex: 1;
}

.auth-btn-compact {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.auth-btn-compact:hover {
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

/* Compact Comments List */
.comments-list-compact {
    space-y: 1rem;
}

.comment-compact {
    background: rgba(255,255,255,0.03);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255,255,255,0.08);
    display: flex;
    gap: 0.8rem;
    transition: all 0.3s ease;
}

.comment-compact:hover {
    background: rgba(255,255,255,0.06);
    border-color: rgba(0, 212, 255, 0.1);
}

.comment-avatar-compact {
    flex-shrink: 0;
}

.avatar-img-compact, .avatar-placeholder-compact {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.2);
    object-fit: cover;
}

.avatar-placeholder-compact {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 1rem;
}

.comment-body-compact {
    flex: 1;
    min-width: 0;
}

.comment-header-compact {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.author-compact {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.time-compact {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
}

.spoiler-badge-compact {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.comment-text-compact p {
    color: rgba(255,255,255,0.9);
    line-height: 1.5;
    margin: 0 0 0.8rem 0;
    font-size: 0.9rem;
}

/* Compact Spoiler Protection */
.spoiler-guard-compact {
    background: rgba(255, 107, 157, 0.1);
    border: 1px dashed rgba(255, 107, 157, 0.3);
    border-radius: 8px;
    padding: 0.8rem;
    text-align: center;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.spoiler-warning-compact {
    color: rgba(255,255,255,0.8);
    font-size: 0.8rem;
    font-weight: 500;
}

.reveal-spoiler-compact {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
    border: none;
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.reveal-spoiler-compact:hover {
    transform: scale(1.05);
}

.comment-actions-compact {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.action-compact {
    background: none;
    border: none;
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.action-compact:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

/* Compact Replies */
.replies-compact {
    margin-top: 0.8rem;
    padding-left: 1rem;
    border-left: 2px solid rgba(0, 212, 255, 0.2);
}

.reply-compact {
    display: flex;
    gap: 0.6rem;
    padding: 0.8rem;
    background: rgba(0,0,0,0.1);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.reply-avatar-compact {
    flex-shrink: 0;
}

.reply-avatar-img, .reply-avatar-placeholder {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.2);
    object-fit: cover;
}

.reply-avatar-placeholder {
    background: linear-gradient(135deg, #7b68ee, #667eea);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 0.7rem;
}

.reply-body-compact {
    flex: 1;
    min-width: 0;
}

.reply-header-compact {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.3rem;
}

.reply-author-compact {
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
}

.reply-time-compact {
    color: rgba(255,255,255,0.6);
    font-size: 0.7rem;
}

.reply-text-compact {
    color: rgba(255,255,255,0.9);
    line-height: 1.4;
    margin: 0;
    font-size: 0.8rem;
}

.more-replies-compact {
    text-align: center;
    padding: 0.5rem;
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
    cursor: pointer;
}

.more-replies-compact:hover {
    color: #00d4ff;
}

/* Empty State Compact */
.no-comments-compact {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-compact {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
}

.empty-icon-compact {
    font-size: 2.5rem;
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-text-compact {
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}

/* Comments Footer */
.comments-footer-compact {
    text-align: center;
    padding: 1rem 0 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: 1rem;
}

.comments-total-info {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    margin: 0;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .comments-header-accordion {
        padding: 1rem;
    }
    
    .comments-title-accordion {
        font-size: 1.1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .comments-content-accordion {
        padding: 1rem;
    }
    
    .form-actions-compact {
        flex-direction: column;
        align-items: stretch;
        gap: 0.8rem;
    }
    
    .submit-compact {
        width: 100%;
        padding: 0.8rem;
    }
    
    .auth-content-compact {
        flex-direction: column;
        text-align: center;
        gap: 0.8rem;
    }
    
    .comment-compact {
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .comment-header-compact {
        justify-content: space-between;
    }
    
    .comment-actions-compact {
        justify-content: space-around;
    }
    
    .spoiler-guard-compact {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .textarea-compact-container {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    .textarea-compact {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 0.6rem !important;
        font-size: 0.85rem !important;
    }
    
    .form-compact {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    .comment-form-compact {
        width: 100%;
        max-width: 100%;
        padding: 0.8rem !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box;
    }
}

/* Share Button Styles */
.share-button-container {
    position: relative;
    display: inline-block;
}

.share-btn {
    background: rgba(123, 104, 238, 0.1);
    border: 1px solid rgba(123, 104, 238, 0.3);
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: rgba(123, 104, 238, 0.8);
}

.share-btn:hover {
    background: rgba(123, 104, 238, 0.2);
    border-color: rgba(123, 104, 238, 0.5);
    color: rgba(123, 104, 238, 1);
    transform: scale(1.05);
}

.share-menu {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(20, 20, 20, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    min-width: 150px;
    z-index: 1000;
    backdrop-filter: blur(10px);
}

.share-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    color: white;
    font-size: 0.9rem;
}

.share-option:hover {
    background: rgba(255, 255, 255, 0.1);
}

.share-icon {
    font-size: 1rem;
}

/* Cast Accordion Styles */
.cast-accordion-container {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
}

.cast-header {
    padding: 1.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cast-header:hover {
    background: rgba(255,255,255,0.05);
}

.cast-header-info {
    flex: 1;
}

.cast-title {
    color: white;
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.cast-count {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.cast-subtitle {
    color: rgba(255,255,255,0.7);
    margin: 0;
    font-size: 0.9rem;
}

.cast-toggle {
    margin-left: 1rem;
}

.toggle-icon {
    color: #00d4ff;
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.cast-content {
    padding: 1.5rem;
}

/* Compact Grid Layout */
.cast-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.actor-card-compact {
    background: linear-gradient(145deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02));
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    flex-direction: column;
}

.actor-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    border-color: rgba(0, 212, 255, 0.2);
}

/* Override card hover when buttons are hovered */
.actor-card-compact:has(.actor-top-actions:hover) {
    transform: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

/* Compact Photo */
.actor-photo-compact {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
}

.actor-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.actor-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: rgba(255,255,255,0.4);
}

/* Overlay Actions */
.actor-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(0,0,0,0.5));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.actor-card-compact:hover .actor-overlay {
    opacity: 1;
}

.actor-card-compact:hover .actor-image {
    transform: scale(1.1);
}

.overlay-actions {
    display: flex;
    gap: 0.8rem;
}

.overlay-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    color: white;
}

.overlay-btn.view {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
}

.overlay-btn.view:hover {
    transform: scale(1.15);
    color: white;
    text-decoration: none;
}

.overlay-btn.follow.not-following {
    background: linear-gradient(135deg, #ff6b9d 0%, #ff8e8e 100%);
    box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
}

.overlay-btn.follow.following {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

.overlay-btn.follow.guest {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.4);
}

.overlay-btn.follow:hover {
    transform: scale(1.15);
}

/* Compact Actor Info */
.actor-info-compact {
    padding: 1rem;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 0.5rem;
}

.actor-name-compact {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 0.3rem 0;
    line-height: 1.3;
}

.actor-role {
    color: #00d4ff;
    font-size: 0.8rem;
    font-weight: 500;
    margin: 0 0 1rem 0;
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Simple Actor Cards */
.actor-card-simple {
    display: block;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.actor-card-simple:hover {
    transform: translateY(-2px);
    border-color: rgba(0, 212, 255, 0.3);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.actor-image-simple {
    width: 100%;
    aspect-ratio: 3/4;
    overflow: hidden;
    position: relative;
}

@media (max-width: 768px) {
    .actor-card-simple {
        border-radius: 8px !important;
        overflow: hidden !important;
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
    }
    
    .actor-image-simple {
        width: 100% !important;
        height: 160px !important;
        aspect-ratio: 2/3 !important;
    }
    
    .actor-info-simple {
        padding: 0.6rem !important;
        text-align: center !important;
    }
    
    .actor-name-simple {
        font-size: 0.8rem !important;
        margin-bottom: 0.2rem !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
    }
    
    .actor-role-simple {
        font-size: 0.7rem !important;
        color: rgba(255,255,255,0.6) !important;
        margin-bottom: 0.6rem !important;
    }
    
    .actor-actions {
        margin-top: 0.6rem !important;
        gap: 0.3rem !important;
    }
    
    .actor-btn {
        padding: 0.3rem 0.5rem !important;
        font-size: 0.7rem !important;
    }
}

.actor-image-simple img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.actor-placeholder-simple {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.1);
    font-size: 3rem;
}

.actor-info-simple {
    padding: 0.8rem;
    text-align: center;
}

@media (max-width: 768px) {
    .actor-info-simple {
        padding: 0.5rem;
    }
}

.actor-name-simple {
    color: white;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 0 0 0.3rem 0;
}

.actor-role-simple {
    color: rgba(255,255,255,0.6);
    font-size: 0.75rem;
    margin: 0 0 0.8rem 0;
}

.actor-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.8rem;
    width: 100%;
}

.actor-btn {
    flex: 1;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    color: white;
}

@media (max-width: 768px) {
    .actor-actions {
        gap: 0.3rem;
        margin-top: 0.5rem;
    }
    
    .actor-btn {
        padding: 0.3rem 0.5rem;
        font-size: 0.7rem;
        border-radius: 4px;
    }
}

.actor-btn-view {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
}

.actor-btn-view:hover {
    transform: scale(1.05);
    color: white;
    text-decoration: none;
}

.actor-btn-follow {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
}

.actor-btn-follow:hover {
    transform: scale(1.05);
}

.actor-btn-follow.following {
    background: linear-gradient(135deg, #4caf50, #81c784);
}

/* Netflix Style Actor Cards - Horizontal Layout v2.0 */
.netflix-actor-card {
    background: rgba(255,255,255,0.05) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    padding: 0.8rem !important;
    height: 120px !important;
    width: 100% !important;
    flex-direction: row !important;
}

.netflix-actor-card:hover {
    transform: translateY(-2px);
    border-color: rgba(0, 212, 255, 0.3);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.netflix-actor-image {
    width: 80px !important;
    height: 100px !important;
    overflow: hidden !important;
    position: relative !important;
    border-radius: 6px !important;
    flex-shrink: 0 !important;
    margin-right: 1rem !important;
}

.netflix-actor-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.netflix-actor-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.1);
    font-size: 2rem;
    color: rgba(255,255,255,0.4);
}

.netflix-actor-info {
    flex: 1;
    padding: 0;
    text-align: left;
}

.netflix-actor-name {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 0.3rem 0;
    line-height: 1.2;
}

.netflix-actor-role {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
    margin: 0 0 0.8rem 0;
}

.netflix-actor-actions {
    display: flex;
    gap: 0.4rem;
    margin-top: 0.8rem;
    width: 100%;
}

.netflix-btn {
    flex: 1;
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    color: white;
}

.netflix-btn-view {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
}

.netflix-btn-view:hover {
    transform: scale(1.05);
    color: white;
    text-decoration: none;
}

.netflix-btn-follow {
    background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
}

.netflix-btn-follow:hover {
    transform: scale(1.05);
}

.netflix-btn-follow.following {
    background: linear-gradient(135deg, #4caf50, #81c784);
}

@media (max-width: 768px) {
    .cast-grid-compact {
        grid-template-columns: 1fr !important;
        gap: 0.8rem !important;
    }
}

/* Cast Footer */
.cast-footer {
    text-align: center;
    padding: 1rem 0 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: 1rem;
}

.cast-more-info {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    margin: 0;
}

/* Desktop Specific */
@media (min-width: 769px) {
    .actor-overlay {
        display: flex;
    }
    
    .mobile-actions-compact {
        display: none !important;
    }
}

/* Mobile Specific */
@media (max-width: 768px) {
    .cast-grid-compact {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }
    
    .actor-overlay {
        display: none !important;
    }
    
    .new-actor-image {
        height: 150px;
    }
    
    .new-actor-info {
        padding: 0.8rem;
    }
    
    .new-actor-name {
        font-size: 0.85rem;
    }
    
    .new-actor-role {
        font-size: 0.75rem;
    }
    
    .new-actor-buttons {
        padding: 0.6rem 0.8rem;
        gap: 0.4rem;
    }
    
    .new-btn-ver {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .new-btn-follow {
        padding: 0.5rem;
        min-width: 36px;
    }
    
    .actor-card-compact:hover {
        transform: none;
    }
    
    .cast-header {
        padding: 1rem;
    }
    
    .cast-title {
        font-size: 1.1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .cast-content {
        padding: 1rem;
    }
    
    .actor-name-compact {
        font-size: 0.85rem;
    }
    
    .actor-role {
        font-size: 0.75rem;
        margin-bottom: 0.8rem;
    }
    
    .compact-btn {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
}

/* Small screens */
@media (max-width: 480px) {
    .cast-grid-compact {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.6rem;
    }
    
    .actor-card-simple {
        border-radius: 4px;
    }
    
    .actor-image-simple {
        height: 70px !important;
        aspect-ratio: 16/9 !important;
    }
    
    .actor-info-simple {
        padding: 0.3rem !important;
    }
    
    .actor-name-simple {
        font-size: 0.7rem !important;
        margin-bottom: 0.2rem !important;
    }
    
    .actor-role-simple {
        font-size: 0.65rem !important;
        margin-bottom: 0.3rem !important;
    }
    
    .actor-actions {
        margin-top: 0.3rem !important;
        gap: 0.2rem !important;
    }
    
    .actor-btn {
        padding: 0.25rem 0.4rem !important;
        font-size: 0.65rem !important;
    }
    
    .mobile-actions-compact {
        flex-direction: column;
        gap: 0.4rem;
    }
    
    .compact-btn {
        padding: 0.6rem;
        font-size: 0.8rem;
    }
}
</style>

<script>
// Seasons viewer functionality
function seasonsViewer() {
    return {
        init() {
            // Initialize any global season functionality here
        }
    }
}

// Actor card functionality
function actorCard(config) {
    return {
        actorId: config.actorId,
        isFollowing: config.isFollowing,
        loading: false,

        async toggleFollow() {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const endpoint = `/actors/${this.actorId}/follow`;
                const method = this.isFollowing ? 'DELETE' : 'POST';
                
                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.isFollowing = data.is_following;
                    
                    // Show temporary success message
                    this.showNotification(data.message, 'success');
                } else {
                    this.showNotification(data.message || 'Error al actualizar seguimiento', 'error');
                }
            } catch (error) {
                console.error('Error toggling actor follow:', error);
                this.showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
            } finally {
                this.loading = false;
            }
        },

        showNotification(message, type) {
            // Create a simple notification
            const notification = document.createElement('div');
            notification.className = `actor-notification ${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #dc3545, #c82333)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                z-index: 9999;
                font-weight: 600;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }
}

// Individual episode card functionality
function episodeCard(config) {
    return {
        episodeId: config.episodeId,
        seriesId: config.seriesId,
        watchStatus: null,
        watchProgress: 0,
        loading: false,

        async init() {
            // Load initial watch status for authenticated users
            @auth
            await this.loadWatchStatus();
            @endauth
        },

        async loadWatchStatus() {
            try {
                const response = await fetch(`/series/${this.seriesId}/episodes`);
                const data = await response.json();
                if (data.success) {
                    const episode = data.episodes.find(ep => ep.id === this.episodeId);
                    if (episode && episode.user_progress) {
                        this.watchStatus = episode.user_progress.status;
                        this.watchProgress = episode.user_progress.progress_percentage || 0;
                    }
                }
            } catch (error) {
                console.error('Error cargando estado del episodio:', error);
            }
        },

        async toggleWatched() {
            if (this.loading) return;
            
            this.loading = true;
            try {
                const isCompleted = this.watchStatus === 'completed';
                const endpoint = `/episodes/${this.episodeId}/watched`;
                const method = isCompleted ? 'DELETE' : 'POST';

                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    if (isCompleted) {
                        this.watchStatus = null;
                        this.watchProgress = 0;
                    } else {
                        this.watchStatus = 'completed';
                        this.watchProgress = 100;
                    }
                    
                    // Update the main episode progress component if it exists
                    if (window.episodeProgressComponent) {
                        await window.episodeProgressComponent.loadProgress();
                        await window.episodeProgressComponent.loadEpisodes();
                    }
                }
            } catch (error) {
                console.error('Error actualizando estado del episodio:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}

// Enhanced Comments functionality
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for textarea
    const textarea = document.getElementById('commentContent');
    const charCounter = document.querySelector('.char-counter');
    
    if (textarea && charCounter) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = `${length}/1000`;
            
            if (length > 900) {
                charCounter.style.color = '#ff6b6b';
            } else if (length > 800) {
                charCounter.style.color = '#ffa500';
            } else {
                charCounter.style.color = 'rgba(255,255,255,0.6)';
            }
        });
    }

    // Enhanced spoiler reveal functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('reveal-spoiler-modern')) {
            const spoilerGuard = e.target.closest('.spoiler-guard');
            const spoilerContent = e.target.closest('.comment-content-modern').querySelector('.spoiler-content-hidden');
            
            if (spoilerGuard && spoilerContent) {
                spoilerGuard.style.display = 'none';
                spoilerContent.style.display = 'block';
                spoilerContent.classList.add('revealed');
            }
        }
    });

    // Enhanced comment form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.submit-button');
            const originalText = submitBtn.innerHTML;
            const content = document.getElementById('commentContent').value.trim();
            const isSpoiler = document.getElementById('isSpoiler').checked;
            
            if (!content) {
                textarea.focus();
                return;
            }
            
            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>⏳</span><span>Publicando...</span>';
            
            try {
                const response = await fetch(`/series/{{ $series->id }}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        content: content,
                        is_spoiler: isSpoiler
                    })
                });

                if (response.ok) {
                    // Show success state
                    submitBtn.innerHTML = '<span>✅</span><span>¡Publicado!</span>';
                    submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
                    
                    // Reload after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error('Error del servidor');
                }
            } catch (error) {
                console.error('Error enviando comentario:', error);
                
                // Show error state
                submitBtn.innerHTML = '<span>❌</span><span>Error. Reintentar</span>';
                submitBtn.style.background = 'linear-gradient(135deg, #dc3545, #c82333)';
                
                // Reset after 3 seconds
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    submitBtn.style.background = 'linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%)';
                }, 3000);
            }
        });
    }

    // Comment actions functionality (placeholders for future features)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            // TODO: Implement like functionality
            console.log('Like clicked');
        }
        
        if (e.target.closest('.reply-btn')) {
            e.preventDefault();
            // TODO: Implement reply functionality  
            console.log('Reply clicked');
        }
    });

    // Smooth scroll animation for comments section
    if (window.location.hash === '#comments') {
        setTimeout(() => {
            const commentsSection = document.querySelector('.comments-section-modern');
            if (commentsSection) {
                commentsSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 100);
    }
});

// Share functionality
function toggleShareMenu(seriesId, button) {
    const shareMenu = button.parentElement.querySelector('.share-menu');
    const isVisible = shareMenu.style.display === 'block';
    
    // Close all other share menus
    document.querySelectorAll('.share-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Toggle this menu
    shareMenu.style.display = isVisible ? 'none' : 'block';
    
    // Close menu when clicking outside
    setTimeout(() => {
        const closeOnClickOutside = (e) => {
            if (!button.parentElement.contains(e.target)) {
                shareMenu.style.display = 'none';
                document.removeEventListener('click', closeOnClickOutside);
            }
        };
        document.addEventListener('click', closeOnClickOutside);
    }, 100);
}

function shareToFacebook(seriesId, seriesTitle) {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`¡Mira esta serie: ${seriesTitle}!`);
    const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`;
    window.open(facebookUrl, '_blank', 'width=600,height=400');
}

function shareToTwitter(seriesId, seriesTitle) {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`¡Mira esta serie K-Drama: ${seriesTitle}! 📺✨`);
    const twitterUrl = `https://twitter.com/intent/tweet?text=${text}&url=${url}&hashtags=KDrama,DORASIA`;
    window.open(twitterUrl, '_blank', 'width=600,height=400');
}

function shareToWhatsApp(seriesId, seriesTitle) {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`¡Mira esta serie K-Drama: ${seriesTitle}! 📺✨\n\n${window.location.href}`);
    const whatsappUrl = `https://wa.me/?text=${text}`;
    window.open(whatsappUrl, '_blank');
}

async function copySeriesLink(seriesId, seriesTitle) {
    try {
        await navigator.clipboard.writeText(window.location.href);
        
        // Show success message
        const shareOption = event.target.closest('.share-option');
        const originalContent = shareOption.innerHTML;
        shareOption.innerHTML = '<span class="share-icon">✅</span><span>¡Copiado!</span>';
        shareOption.style.color = '#28a745';
        
        setTimeout(() => {
            shareOption.innerHTML = originalContent;
            shareOption.style.color = '';
        }, 2000);
        
        // Close menu
        document.querySelector('.share-menu[style*="block"]').style.display = 'none';
    } catch (err) {
        console.error('Error al copiar:', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        alert('Enlace copiado al portapapeles');
    }
}

// Follow actor functionality
function followActor(actorId, button) {
    if (!actorId) return;
    
    const isFollowing = button.classList.contains('following');
    const originalText = button.textContent;
    
    // Update button state immediately for better UX
    button.disabled = true;
    button.textContent = isFollowing ? 'Dejando...' : 'Siguiendo...';
    
    // Simulate API call (replace with actual endpoint when implemented)
    setTimeout(() => {
        if (isFollowing) {
            button.classList.remove('following');
            button.textContent = 'Seguir';
        } else {
            button.classList.add('following');
            button.textContent = '✓ Siguiendo';
        }
        button.disabled = false;
    }, 800);
    
    // TODO: Implement actual API call
    // fetch(`/actors/${actorId}/follow`, { method: 'POST' })
    //     .then(response => response.json())
    //     .then(data => {
    //         // Handle response
    //     });
}
</script>
@endsection