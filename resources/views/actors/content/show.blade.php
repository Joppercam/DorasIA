@extends('layouts.app')

@section('title', $content->title . ' - ' . $actor->display_name . ' - Dorasia')

@section('content')
<!-- Content Hero Section -->
<section class="content-hero">
    <div class="container">
        <div class="content-hero-layout">
            <!-- Content Media -->
            <div class="content-media">
                @if($content->hasExternalVideo())
                <!-- Video externo real (TikTok, YouTube, Instagram) -->
                <div class="external-video-player">
                    <div class="external-video-header">
                        <div class="video-platform-badge">
                            @switch($content->external_video_type)
                                @case('tiktok')
                                    🎵 TikTok
                                    @break
                                @case('youtube')
                                    ▶️ YouTube
                                    @break
                                @case('instagram')
                                    📷 Instagram
                                    @break
                                @default
                                    🎥 Video
                            @endswitch
                        </div>
                        @if($content->duration)
                        <div class="video-duration">{{ $content->formatted_duration }}</div>
                        @endif
                    </div>
                    <div class="external-video-container">
                        {!! $content->getExternalVideoEmbed() !!}
                    </div>
                </div>
                @elseif($content->type === 'video' && $content->media_url && str_contains($content->media_url, '.mp4'))
                <!-- Solo mostrar video local si tenemos URL válida -->
                <div class="video-player">
                    <video controls poster="{{ $content->thumbnail_url }}" class="main-video">
                        <source src="{{ $content->media_url }}" type="video/mp4">
                        Tu navegador no soporta el elemento video.
                    </video>
                    @if($content->duration)
                    <div class="video-duration">{{ $content->formatted_duration }}</div>
                    @endif
                </div>
                @elseif(in_array($content->type, ['video', 'interview', 'behind_scenes']))
                <!-- Placeholder para contenido de video sin archivo real -->
                <div class="content-preview-card video-preview">
                    <div class="preview-header">
                        <div class="content-type-icon">{{ $content->getTypeIcon() }}</div>
                        <div class="preview-info">
                            <span class="preview-type">{{ $content->type_name }}</span>
                            @if($content->duration)
                            <span class="preview-duration">⏱️ {{ $content->formatted_duration }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="preview-content">
                        <h3>🎬 Contenido Audiovisual Exclusivo</h3>
                        <p>Este {{ strtolower($content->type_name) }} está disponible exclusivamente para miembros registrados de Dorasia.</p>
                        <div class="preview-features">
                            <div class="feature-item">
                                <span class="feature-icon">🎥</span>
                                <span>Calidad HD</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🎧</span>
                                <span>Audio Cristalino</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">📱</span>
                                <span>Subtítulos Disponibles</span>
                            </div>
                        </div>
                        <button class="watch-button" onclick="showVideoModal()">
                            <span>▶️ Ver Contenido</span>
                        </button>
                    </div>
                </div>
                @elseif($content->type === 'gallery')
                <!-- Preview para galerías -->
                <div class="content-preview-card gallery-preview">
                    <div class="preview-header">
                        <div class="content-type-icon">📸</div>
                        <div class="preview-info">
                            <span class="preview-type">{{ $content->type_name }}</span>
                            <span class="preview-count">{{ rand(8, 25) }} fotos</span>
                        </div>
                    </div>
                    <div class="gallery-grid">
                        @for($i = 1; $i <= 6; $i++)
                        <div class="gallery-thumb">
                            <img src="https://picsum.photos/200/200?random={{ rand(100, 999) }}" alt="Foto {{ $i }}">
                            @if($i === 6)
                            <div class="more-indicator">+{{ rand(5, 20) }}</div>
                            @endif
                        </div>
                        @endfor
                    </div>
                    <button class="view-gallery-button" onclick="openGallery()">
                        <span>🖼️ Ver Galería Completa</span>
                    </button>
                </div>
                @else
                <!-- Preview para contenido de texto (noticias, biografías, artículos) -->
                <div class="content-preview-card text-preview">
                    <div class="preview-header">
                        <div class="content-type-icon">{{ $content->getTypeIcon() }}</div>
                        <div class="preview-info">
                            <span class="preview-type">{{ $content->type_name }}</span>
                            <span class="reading-time">📖 {{ ceil(str_word_count($content->content) / 200) }} min lectura</span>
                        </div>
                    </div>
                    <div class="article-image">
                        @if($content->actor->profile_path)
                        <img src="https://image.tmdb.org/t/p/w500{{ $content->actor->profile_path }}" alt="{{ $content->actor->display_name }}" class="main-image" onerror="this.src='/images/no-actor-photo.svg'">
                        @else
                        <img src="/images/no-actor-photo.svg" alt="{{ $content->actor->display_name }}" class="main-image">
                        @endif
                    </div>
                    <div class="content-summary">
                        <h3>📰 Contenido Exclusivo</h3>
                        <p>{{ Str::limit($content->content, 200) }}</p>
                        <div class="content-highlights">
                            <div class="highlight-item">
                                <span class="highlight-icon">⭐</span>
                                <span>Contenido Verificado</span>
                            </div>
                            <div class="highlight-item">
                                <span class="highlight-icon">🔒</span>
                                <span>Acceso Exclusivo</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Content Info -->
            <div class="content-info-panel">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="content-type-badge">{{ $content->type_name }}</div>
                    <h1 class="content-title">{{ $content->title }}</h1>
                    
                    <!-- Actor Info -->
                    <div class="actor-link">
                        <a href="{{ route('actors.show', $actor->id) }}" class="actor-profile">
                            @if($actor->profile_path)
                            <img src="https://image.tmdb.org/t/p/w92{{ $actor->profile_path }}" alt="{{ $actor->display_name }}" class="actor-avatar">
                            @else
                            <div class="actor-avatar-placeholder">{{ substr($actor->display_name, 0, 1) }}</div>
                            @endif
                            <span class="actor-name">{{ $actor->display_name }}</span>
                        </a>
                    </div>
                </div>

                <!-- Content Meta -->
                <div class="content-meta">
                    <div class="meta-item">
                        <span class="meta-label">📅 Publicado</span>
                        <span class="meta-value">{{ $content->published_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($content->source)
                    <div class="meta-item">
                        <span class="meta-label">📰 Fuente</span>
                        <span class="meta-value">{{ $content->source }}</span>
                    </div>
                    @endif
                    
                    <div class="meta-item">
                        <span class="meta-label">👁️ Vistas</span>
                        <span class="meta-value">{{ number_format($content->view_count) }}</span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">❤️ Me gusta</span>
                        <span class="meta-value">{{ number_format($content->like_count) }}</span>
                    </div>
                </div>

                <!-- Content Actions -->
                <div class="content-actions">
                    <button id="likeBtn" class="action-btn like-btn {{ $hasLiked ? 'liked' : '' }}" 
                            data-content-id="{{ $content->id }}" data-actor-id="{{ $actor->id }}">
                        <span class="btn-icon">{{ $hasLiked ? '❤️' : '🤍' }}</span>
                        <span class="btn-text">{{ $hasLiked ? 'Te gusta' : 'Me gusta' }}</span>
                        <span class="like-count">({{ number_format($content->like_count) }})</span>
                    </button>
                    
                    <button class="action-btn share-btn" onclick="shareContent()">
                        <span class="btn-icon">📤</span>
                        <span class="btn-text">Compartir</span>
                    </button>
                </div>

                <!-- Content Tags -->
                @if($content->tags && count($content->tags) > 0)
                <div class="content-tags">
                    <h4 class="tags-title">Etiquetas:</h4>
                    <div class="tags-list">
                        @foreach($content->tags as $tag)
                        <span class="tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Content Description -->
@if($content->content)
<section class="content-description">
    <div class="container">
        <div class="description-content">
            <h3 class="description-title">📄 Descripción</h3>
            <div class="description-text">
                {!! nl2br(e($content->content)) !!}
            </div>
        </div>
    </div>
</section>
@endif

<!-- Social Section: Reactions and Comments -->
<section class="social-section">
    <div class="container">
        <!-- Reactions -->
        <div class="reactions-section">
            <h3 class="section-title">💭 ¿Qué opinas?</h3>
            <div class="reactions-container">
                <div class="reactions-buttons">
                    <button class="reaction-btn {{ $userReaction === 'like' ? 'active' : '' }}" 
                            data-type="like" onclick="toggleReaction('like')">
                        <span class="reaction-emoji">👍</span>
                        <span class="reaction-text">Me gusta</span>
                        <span class="reaction-count">{{ $reactionCounts['like'] ?? 0 }}</span>
                    </button>
                    
                    <button class="reaction-btn {{ $userReaction === 'love' ? 'active' : '' }}" 
                            data-type="love" onclick="toggleReaction('love')">
                        <span class="reaction-emoji">❤️</span>
                        <span class="reaction-text">Me encanta</span>
                        <span class="reaction-count">{{ $reactionCounts['love'] ?? 0 }}</span>
                    </button>
                    
                    <button class="reaction-btn {{ $userReaction === 'dislike' ? 'active' : '' }}" 
                            data-type="dislike" onclick="toggleReaction('dislike')">
                        <span class="reaction-emoji">👎</span>
                        <span class="reaction-text">No me gusta</span>
                        <span class="reaction-count">{{ $reactionCounts['dislike'] ?? 0 }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h3 class="section-title">💬 Comentarios ({{ $comments->count() }})</h3>
            
            <!-- Add Comment Form -->
            <div class="add-comment-form">
                <div class="comment-form-header">
                    <div class="user-avatar">
                        <div class="avatar-placeholder">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    </div>
                    <div class="user-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span class="user-badge">Miembro de Dorasia</span>
                    </div>
                </div>
                <form id="comment-form" onsubmit="addComment(event)">
                    <textarea id="comment-content" 
                              placeholder="Comparte tu opinión sobre este contenido..." 
                              maxlength="1000" 
                              rows="3"></textarea>
                    <div class="comment-form-actions">
                        <div class="char-counter">
                            <span id="char-count">0</span>/1000
                        </div>
                        <button type="submit" class="submit-comment-btn">
                            💬 Comentar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div class="comments-list" id="comments-list">
                @foreach($comments as $comment)
                <div class="comment" data-comment-id="{{ $comment->id }}">
                    <div class="comment-header">
                        <div class="comment-avatar">
                            <div class="avatar-placeholder">{{ substr($comment->user->name, 0, 1) }}</div>
                        </div>
                        <div class="comment-info">
                            <strong class="comment-author">{{ $comment->user->name }}</strong>
                            <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                            @if($comment->is_edited)
                            <span class="edited-badge">editado</span>
                            @endif
                        </div>
                        @if($comment->user_id === auth()->id())
                        <div class="comment-actions">
                            <button class="action-btn" onclick="editComment({{ $comment->id }})">✏️</button>
                            <button class="action-btn" onclick="deleteComment({{ $comment->id }})">🗑️</button>
                        </div>
                        @endif
                    </div>
                    <div class="comment-content">{{ $comment->content }}</div>
                    <div class="comment-footer">
                        <button class="reply-btn" onclick="showReplyForm({{ $comment->id }})">
                            💬 Responder
                        </button>
                    </div>
                    
                    <!-- Replies -->
                    @if($comment->replies->count() > 0)
                    <div class="replies">
                        @foreach($comment->replies as $reply)
                        <div class="reply" data-comment-id="{{ $reply->id }}">
                            <div class="comment-header">
                                <div class="comment-avatar">
                                    <div class="avatar-placeholder">{{ substr($reply->user->name, 0, 1) }}</div>
                                </div>
                                <div class="comment-info">
                                    <strong class="comment-author">{{ $reply->user->name }}</strong>
                                    <span class="comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                    @if($reply->is_edited)
                                    <span class="edited-badge">editado</span>
                                    @endif
                                </div>
                                @if($reply->user_id === auth()->id())
                                <div class="comment-actions">
                                    <button class="action-btn" onclick="editComment({{ $reply->id }})">✏️</button>
                                    <button class="action-btn" onclick="deleteComment({{ $reply->id }})">🗑️</button>
                                </div>
                                @endif
                            </div>
                            <div class="comment-content">{{ $reply->content }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Reply Form (hidden by default) -->
                    <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                        <form onsubmit="addReply(event, {{ $comment->id }})">
                            <textarea placeholder="Escribe tu respuesta..." maxlength="1000" rows="2"></textarea>
                            <div class="reply-form-actions">
                                <button type="button" onclick="hideReplyForm({{ $comment->id }})">Cancelar</button>
                                <button type="submit">Responder</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Related Content -->
@if($relatedContent->count() > 0)
<section class="related-content">
    <div class="container">
        <h3 class="section-title">🔗 Más contenido de {{ $actor->display_name }}</h3>
        
        <div class="related-grid">
            @foreach($relatedContent as $related)
            <div class="related-card">
                <a href="{{ route('actors.content.show', [$actor->id, $related->id]) }}" class="related-link">
                    @if($related->thumbnail_url)
                    <div class="related-thumbnail">
                        <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}">
                        @if($related->duration)
                        <div class="related-duration">{{ $related->formatted_duration }}</div>
                        @endif
                        <div class="related-overlay">
                            <div class="related-play-btn">▶️</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="related-info">
                        <div class="related-type-badge">{{ $related->type_name }}</div>
                        <h4 class="related-title">{{ $related->title }}</h4>
                        <div class="related-meta">
                            <span class="related-date">{{ $related->published_at->diffForHumans() }}</span>
                            <div class="related-stats">
                                <span>👁️ {{ number_format($related->view_count) }}</span>
                                <span>❤️ {{ number_format($related->like_count) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        
        <div class="view-more-link">
            <a href="{{ route('actors.show', $actor->id) }}#exclusive-content" class="view-more-btn">
                📚 Ver todo el contenido de {{ $actor->display_name }}
            </a>
        </div>
    </div>
</section>
@endif

<style>
/* Content Hero Styles */
.content-hero {
    background: linear-gradient(135deg, #141414 0%, #2a2a2a 100%);
    color: white;
    padding: 120px 0 60px 0;
    min-height: 70vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.content-hero-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: start;
}

/* Content Media */
.content-media {
    position: relative;
}

/* Content Preview Cards */
.content-preview-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.content-type-icon {
    font-size: 2.5rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 15px;
    border: 2px solid rgba(0, 212, 255, 0.3);
}

.preview-info {
    flex: 1;
}

.preview-type {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #00d4ff;
    margin-bottom: 0.3rem;
}

.preview-duration, .preview-count, .reading-time {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Video Preview Styles */
.video-preview .preview-content h3 {
    color: white;
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.video-preview .preview-content p {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.preview-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.feature-icon {
    font-size: 1.2rem;
}

.watch-button, .view-gallery-button {
    width: 100%;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.watch-button:hover, .view-gallery-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
}

/* Gallery Preview Styles */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-radius: 15px;
    overflow: hidden;
}

.gallery-thumb {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
}

.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-thumb:hover img {
    transform: scale(1.1);
}

.more-indicator {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
}

/* Text Preview Styles */
.article-image {
    margin: 1.5rem 0;
    border-radius: 15px;
    overflow: hidden;
}

.article-image .main-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.content-summary h3 {
    color: white;
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.content-summary p {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.content-highlights {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.highlight-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    background: rgba(0, 212, 255, 0.1);
    border-radius: 20px;
    border: 1px solid rgba(0, 212, 255, 0.3);
    font-size: 0.9rem;
    color: #00d4ff;
    font-weight: 600;
}

.highlight-icon {
    font-size: 1rem;
}

/* External Video Player */
.external-video-player {
    position: relative;
    width: 100%;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.external-video-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: rgba(0, 0, 0, 0.3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.video-platform-badge {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.external-video-container {
    padding: 0;
    background: #000;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.external-video-container iframe,
.external-video-container blockquote {
    max-width: 100%;
    border-radius: 0;
}

/* TikTok específico */
.external-video-container .tiktok-embed {
    margin: 0 auto;
    background: transparent;
}

/* YouTube específico */
.external-video-container .youtube-embed {
    width: 100%;
    max-width: 100%;
}

.video-player {
    position: relative;
    width: 100%;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.main-video {
    width: 100%;
    height: auto;
    min-height: 300px;
}

.video-placeholder {
    width: 100%;
    height: 300px;
    background: linear-gradient(135deg, #333 0%, #555 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
}

.placeholder-content {
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
}

.placeholder-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.image-display {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.main-image {
    width: 100%;
    height: auto;
    display: block;
}

/* Content Info Panel */
.content-info-panel {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(10px);
    height: fit-content;
}

.content-header {
    margin-bottom: 2rem;
}

.content-type-badge {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.content-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.3;
    color: white;
}

.actor-link {
    margin-bottom: 1.5rem;
}

.actor-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.actor-profile:hover {
    color: #00d4ff;
    text-decoration: none;
}

.actor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.actor-avatar-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    color: white;
}

.actor-name {
    font-weight: 600;
    font-size: 1.1rem;
}

/* Content Meta */
.content-meta {
    margin-bottom: 2rem;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.meta-item:last-child {
    border-bottom: none;
}

.meta-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.meta-value {
    color: white;
    font-weight: 600;
}

/* Content Actions */
.content-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    text-decoration: none;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.like-btn.liked {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
    border-color: #ff6b6b;
}

.like-btn.liked:hover {
    background: linear-gradient(135deg, #ff5252 0%, #ff7979 100%);
}

/* Content Tags */
.content-tags {
    margin-top: 2rem;
}

.tags-title {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Content Description */
.content-description {
    background: rgba(255, 255, 255, 0.02);
    padding: 3rem 0;
}

.description-content {
    max-width: 800px;
    margin: 0 auto;
}

.description-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.description-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.7;
    text-align: justify;
}

/* Related Content */
.related-content {
    padding: 3rem 0;
}

.section-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.related-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
}

.related-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.related-thumbnail {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.related-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.related-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.related-card:hover .related-overlay {
    opacity: 1;
}

.related-play-btn {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.related-info {
    padding: 1.5rem;
}

.related-type-badge {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 0.8rem;
}

.related-title {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.related-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.related-stats {
    display: flex;
    gap: 1rem;
}

.view-more-link {
    text-align: center;
    margin-top: 2rem;
}

.view-more-btn {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}

.view-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
    text-decoration: none;
    color: white;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .content-hero {
        padding: 100px 0 40px 0;
    }
    
    .container {
        padding: 0 1rem;
    }
    
    .content-hero-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .content-info-panel {
        padding: 1.5rem;
    }
    
    .content-title {
        font-size: 1.4rem;
    }
    
    .content-actions {
        flex-direction: column;
    }
    
    .action-btn {
        justify-content: center;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
    
    .description-content {
        padding: 0 1rem;
    }
    
    /* Mobile preview cards */
    .content-preview-card {
        padding: 1.5rem;
    }
    
    .content-type-icon {
        width: 50px;
        height: 50px;
        font-size: 2rem;
    }
    
    .preview-type {
        font-size: 1rem;
    }
    
    .preview-features {
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }
    
    .content-highlights {
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .highlight-item {
        justify-content: center;
    }
    
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .article-image .main-image {
        height: 150px;
    }
}

/* Social Section Styles */
.social-section {
    background: #1a1a1a;
    padding: 3rem 0;
    margin: 2rem 0;
}

/* Reactions Styles */
.reactions-section {
    margin-bottom: 3rem;
}

.reactions-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.reaction-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.reaction-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: #00d4ff;
    transform: translateY(-2px);
}

.reaction-btn.active {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    border-color: #00d4ff;
    transform: scale(1.05);
}

.reaction-emoji {
    font-size: 1.2rem;
}

.reaction-text {
    font-weight: 600;
}

.reaction-count {
    background: rgba(0, 0, 0, 0.3);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: bold;
}

/* Comments Styles */
.comments-section {
    border-top: 2px solid rgba(255, 255, 255, 0.1);
    padding-top: 2rem;
}

.add-comment-form {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.comment-form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.user-avatar, .comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.user-info {
    flex: 1;
}

.user-badge {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.8rem;
    margin-left: 0.5rem;
}

#comment-content, .reply-form textarea {
    width: 100%;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: white;
    resize: vertical;
    font-family: inherit;
}

#comment-content:focus, .reply-form textarea:focus {
    border-color: #00d4ff;
    outline: none;
    box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
}

.comment-form-actions, .reply-form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.char-counter {
    color: #999;
    font-size: 0.9rem;
}

.submit-comment-btn, .reply-form button[type="submit"] {
    background: linear-gradient(135deg, #00d4ff, #7b68ee);
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.submit-comment-btn:hover, .reply-form button[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 212, 255, 0.4);
}

/* Comment Items */
.comment, .reply {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.reply {
    margin-left: 2rem;
    background: rgba(255, 255, 255, 0.03);
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.comment-info {
    flex: 1;
}

.comment-author {
    color: #00d4ff;
    margin-right: 0.5rem;
}

.comment-time {
    color: #999;
    font-size: 0.9rem;
}

.edited-badge {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    padding: 0.1rem 0.3rem;
    border-radius: 5px;
    font-size: 0.7rem;
    margin-left: 0.5rem;
}

.comment-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    padding: 0.3rem 0.5rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.comment-content {
    color: white;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.comment-footer {
    display: flex;
    justify-content: flex-end;
}

.reply-btn {
    background: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #00d4ff;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.reply-btn:hover {
    background: rgba(0, 212, 255, 0.1);
    border-color: #00d4ff;
}

.reply-form {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.reply-form-actions button[type="button"] {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
}

/* Responsive Design */
@media (max-width: 768px) {
    .reactions-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .reaction-btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .reply {
        margin-left: 1rem;
    }
    
    .comment-header {
        flex-wrap: wrap;
    }
    
    .comment-actions {
        width: 100%;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Character counter for comment form
document.getElementById('comment-content').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('char-count').textContent = charCount;
    
    if (charCount > 900) {
        document.getElementById('char-count').style.color = '#ff6b6b';
    } else {
        document.getElementById('char-count').style.color = '#999';
    }
});

// Reaction functionality
async function toggleReaction(type) {
    try {
        const response = await fetch(`/actor-content/{{ $content->id }}/reaction`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ type: type })
        });

        const data = await response.json();
        
        if (data.success) {
            // Update button states
            document.querySelectorAll('.reaction-btn').forEach(btn => {
                btn.classList.remove('active');
                const btnType = btn.getAttribute('data-type');
                const countSpan = btn.querySelector('.reaction-count');
                countSpan.textContent = data.counts[btnType];
            });
            
            // Activate current reaction if not removed
            if (data.userReaction) {
                document.querySelector(`[data-type="${data.userReaction}"]`).classList.add('active');
            }
        }
    } catch (error) {
        console.error('Error al procesar reacción:', error);
    }
}

// Comment functionality
async function addComment(event) {
    event.preventDefault();
    
    const content = document.getElementById('comment-content').value.trim();
    if (!content) return;
    
    try {
        const response = await fetch(`/actor-content/{{ $content->id }}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ content: content })
        });

        const data = await response.json();
        
        if (data.success) {
            // Clear form
            document.getElementById('comment-content').value = '';
            document.getElementById('char-count').textContent = '0';
            
            // Add comment to list
            addCommentToDOM(data.comment);
            
            // Update comments count
            updateCommentsCount();
        }
    } catch (error) {
        console.error('Error al agregar comentario:', error);
    }
}

// Add reply
async function addReply(event, parentId) {
    event.preventDefault();
    
    const form = event.target;
    const content = form.querySelector('textarea').value.trim();
    if (!content) return;
    
    try {
        const response = await fetch(`/actor-content/{{ $content->id }}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                content: content,
                parent_id: parentId
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Clear and hide form
            form.querySelector('textarea').value = '';
            hideReplyForm(parentId);
            
            // Add reply to DOM
            const parentComment = document.querySelector(`[data-comment-id="${parentId}"]`);
            let repliesContainer = parentComment.querySelector('.replies');
            if (!repliesContainer) {
                repliesContainer = document.createElement('div');
                repliesContainer.className = 'replies';
                parentComment.appendChild(repliesContainer);
            }
            
            const replyHTML = createReplyHTML(data.comment);
            repliesContainer.insertAdjacentHTML('beforeend', replyHTML);
        }
    } catch (error) {
        console.error('Error al agregar respuesta:', error);
    }
}

// Show/hide reply form
function showReplyForm(commentId) {
    hideAllReplyForms();
    document.getElementById(`reply-form-${commentId}`).style.display = 'block';
}

function hideReplyForm(commentId) {
    document.getElementById(`reply-form-${commentId}`).style.display = 'none';
}

function hideAllReplyForms() {
    document.querySelectorAll('.reply-form').forEach(form => {
        form.style.display = 'none';
    });
}

// Delete comment
async function deleteComment(commentId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) return;
    
    try {
        const response = await fetch(`/actor-content/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            document.querySelector(`[data-comment-id="${commentId}"]`).remove();
            updateCommentsCount();
        }
    } catch (error) {
        console.error('Error al eliminar comentario:', error);
    }
}

// Helper functions
function addCommentToDOM(comment) {
    const commentHTML = createCommentHTML(comment);
    document.getElementById('comments-list').insertAdjacentHTML('afterbegin', commentHTML);
}

function createCommentHTML(comment) {
    return `
        <div class="comment" data-comment-id="${comment.id}">
            <div class="comment-header">
                <div class="comment-avatar">
                    <div class="avatar-placeholder">${comment.user.name.charAt(0)}</div>
                </div>
                <div class="comment-info">
                    <strong class="comment-author">${comment.user.name}</strong>
                    <span class="comment-time">${comment.created_at}</span>
                </div>
                <div class="comment-actions">
                    <button class="action-btn" onclick="editComment(${comment.id})">✏️</button>
                    <button class="action-btn" onclick="deleteComment(${comment.id})">🗑️</button>
                </div>
            </div>
            <div class="comment-content">${comment.content}</div>
            <div class="comment-footer">
                <button class="reply-btn" onclick="showReplyForm(${comment.id})">💬 Responder</button>
            </div>
            <div class="reply-form" id="reply-form-${comment.id}" style="display: none;">
                <form onsubmit="addReply(event, ${comment.id})">
                    <textarea placeholder="Escribe tu respuesta..." maxlength="1000" rows="2"></textarea>
                    <div class="reply-form-actions">
                        <button type="button" onclick="hideReplyForm(${comment.id})">Cancelar</button>
                        <button type="submit">Responder</button>
                    </div>
                </form>
            </div>
        </div>
    `;
}

function createReplyHTML(reply) {
    const canEdit = reply.user.id === {{ auth()->id() }};
    return `
        <div class="reply" data-comment-id="${reply.id}">
            <div class="comment-header">
                <div class="comment-avatar">
                    <div class="avatar-placeholder">${reply.user.name.charAt(0)}</div>
                </div>
                <div class="comment-info">
                    <strong class="comment-author">${reply.user.name}</strong>
                    <span class="comment-time">${reply.created_at}</span>
                </div>
                ${canEdit ? `
                <div class="comment-actions">
                    <button class="action-btn" onclick="editComment(${reply.id})">✏️</button>
                    <button class="action-btn" onclick="deleteComment(${reply.id})">🗑️</button>
                </div>
                ` : ''}
            </div>
            <div class="comment-content">${reply.content}</div>
        </div>
    `;
}

function updateCommentsCount() {
    const count = document.querySelectorAll('.comment').length;
    document.querySelector('.comments-section h3').textContent = `💬 Comentarios (${count})`;
}

// Edit comment functionality (placeholder)
function editComment(commentId) {
    // Implementation for editing comments
    console.log('Editar comentario:', commentId);
}

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like Button Functionality
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const contentId = this.dataset.contentId;
            const actorId = this.dataset.actorId;
            
            this.disabled = true;
            
            fetch(`/actores/${actorId}/contenido/${contentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('.btn-icon');
                    const text = this.querySelector('.btn-text');
                    const count = this.querySelector('.like-count');
                    
                    if (data.liked) {
                        this.classList.add('liked');
                        icon.textContent = '❤️';
                        text.textContent = 'Te gusta';
                    } else {
                        this.classList.remove('liked');
                        icon.textContent = '🤍';
                        text.textContent = 'Me gusta';
                    }
                    
                    count.textContent = `(${data.like_count.toLocaleString()})`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    }
});

function shareContent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $content->title }}',
            text: 'Mira este contenido exclusivo de {{ $actor->display_name }} en Dorasia',
            url: window.location.href
        });
    } else {
        // Fallback para navegadores que no soportan Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('¡Enlace copiado al portapapeles!');
        });
    }
}

function showVideoModal() {
    // Simular modal de video premium
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(10px);
    `;
    
    modal.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 2px solid #00d4ff;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            color: white;
            max-width: 500px;
            margin: 2rem;
            position: relative;
        ">
            <button onclick="this.closest('div').remove()" style="
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
            ">✕</button>
            
            <div style="font-size: 3rem; margin-bottom: 1rem;">🎬</div>
            <h3 style="color: #00d4ff; margin-bottom: 1rem;">Contenido Premium Disponible</h3>
            <p style="margin-bottom: 2rem; line-height: 1.6;">
                Este {{ strtolower($content->type_name) }} de {{ $actor->display_name }} está disponible 
                como parte de tu membresía de Dorasia. El contenido incluye calidad HD, 
                subtítulos y acceso sin anuncios.
            </p>
            <div style="background: rgba(0, 212, 255, 0.1); padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                <p style="color: #00d4ff; font-weight: 600;">
                    ⏱️ Duración: {{ $content->duration ? $content->formatted_duration : '15-30 min' }}<br>
                    📺 Calidad: HD 1080p<br>
                    🎧 Audio: Estéreo + Subtítulos
                </p>
            </div>
            <button onclick="window.scrollTo({top: document.querySelector('.description-content').offsetTop, behavior: 'smooth'}); this.closest('div').remove();" style="
                background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 10px;
                font-weight: 600;
                cursor: pointer;
                margin-right: 1rem;
            ">📖 Leer Contenido Completo</button>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
}

function openGallery() {
    // Simular galería de fotos
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(10px);
    `;
    
    const photoCount = Math.floor(Math.random() * 15) + 8;
    const galleryGrid = Array.from({length: photoCount}, (_, i) => 
        `<img src="https://picsum.photos/400/400?random=${Math.floor(Math.random() * 1000)}" 
              style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; cursor: pointer;"
              onclick="this.style.transform = this.style.transform ? '' : 'scale(2.5)'">`
    ).join('');
    
    modal.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 2px solid #00d4ff;
            border-radius: 20px;
            padding: 2rem;
            color: white;
            max-width: 90vw;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        ">
            <button onclick="this.closest('div').remove()" style="
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                z-index: 1;
            ">✕</button>
            
            <h3 style="color: #00d4ff; margin-bottom: 2rem; text-align: center;">📸 Galería de {{ $actor->display_name }}</h3>
            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
                max-height: 60vh;
                overflow-y: auto;
            ">
                ${galleryGrid}
            </div>
            <p style="text-align: center; margin-top: 2rem; color: #999; font-size: 0.9rem;">
                Haz clic en cualquier imagen para ampliarla
            </p>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
}
</script>
@endsection