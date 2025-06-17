@extends('layouts.app')

@section('title', $actor->display_name . ' - Actor - Dorasia')

@section('content')
<!-- Actor Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, rgba(20,20,20,0.9) 0%, rgba(40,40,40,0.9) 100%), url('{{ $actor->profile_path ? 'https://image.tmdb.org/t/p/original' . $actor->profile_path : '' }}'); background-size: cover; background-position: center; min-height: 100vh; padding-top: 120px; padding-bottom: 120px;">
    <div class="hero-overlay"></div>
    <div class="hero-content" style="height: 100%; display: flex; align-items: center; justify-content: center; padding: 2rem 0;">
        <div class="hero-info-box" style="max-width: 1000px; width: 100%;">
            <div style="display: grid; grid-template-columns: auto 1fr; gap: 3rem; align-items: center; min-height: 400px;">
                <!-- Actor Photo -->
                <div style="text-align: center;">
                    @if($actor->profile_path)
                    <img src="https://image.tmdb.org/t/p/w300{{ $actor->profile_path }}" 
                         alt="{{ $actor->display_name }}"
                         style="width: 280px; height: 380px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7); border: 4px solid rgba(255,255,255,0.1);">
                    @else
                    <div style="width: 280px; height: 380px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 6rem; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);">
                        👤
                    </div>
                    @endif
                </div>
                
                <!-- Actor Info -->
                <div style="padding-left: 1rem;">
                    <h1 class="hero-title" style="margin-bottom: 1rem; font-size: 3.5rem; font-weight: 700;">{{ $actor->display_name }}</h1>
                    
                    <!-- Actor Meta -->
                    <div class="hero-meta" style="margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 0.8rem;">
                        @if($actor->birthday)
                        <span class="hero-category" style="background: rgba(255, 215, 0, 0.15); border-color: rgba(255, 215, 0, 0.3); padding: 0.6rem 1rem; font-size: 0.9rem;">
                            🎂 {{ \Carbon\Carbon::parse($actor->birthday)->format('d/m/Y') }}
                            @if(\Carbon\Carbon::parse($actor->birthday)->diffInYears(\Carbon\Carbon::now()) > 0)
                            ({{ \Carbon\Carbon::parse($actor->birthday)->age }} años)
                            @endif
                        </span>
                        @endif
                        
                        @if($actor->display_place_of_birth)
                        <span class="hero-category" style="background: rgba(40, 167, 69, 0.15); border-color: rgba(40, 167, 69, 0.3); padding: 0.6rem 1rem; font-size: 0.9rem;">
                            📍 {{ $actor->display_place_of_birth }}
                        </span>
                        @endif
                        
                        @if($actor->popularity)
                        <span class="hero-category" style="background: rgba(0, 212, 255, 0.15); border-color: rgba(0, 212, 255, 0.3); padding: 0.6rem 1rem; font-size: 0.9rem;">
                            ⭐ Popularidad: {{ number_format($actor->popularity, 1) }}
                        </span>
                        @endif
                        
                        @if($actor->known_for_department)
                        <span class="hero-category" style="background: rgba(156, 39, 176, 0.15); border-color: rgba(156, 39, 176, 0.3); padding: 0.6rem 1rem; font-size: 0.9rem;">
                            🎭 {{ $actor->known_for_department }}
                        </span>
                        @endif
                        
                        @if($actor->gender)
                        <span class="hero-category" style="background: rgba(233, 30, 99, 0.15); border-color: rgba(233, 30, 99, 0.3); padding: 0.6rem 1rem; font-size: 0.9rem;">
                            {{ $actor->gender == 1 ? '👩 Femenino' : ($actor->gender == 2 ? '👨 Masculino' : '⚪ Otro') }}
                        </span>
                        @endif
                    </div>
                    
                    @if($actor->display_biography)
                    <p class="hero-description" style="line-height: 1.7; font-size: 1.1rem; margin-bottom: 2rem; max-width: 600px;">
                        {{ Str::limit($actor->display_biography, 400) }}
                    </p>
                    @endif
                    
                    <!-- Follow Button -->
                    @auth
                    <div style="margin: 2rem 0;">
                        <button id="followBtn" class="follow-btn" data-actor-id="{{ $actor->id }}" 
                                data-following="{{ auth()->user()->isFollowingActor($actor->id) ? 'true' : 'false' }}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                            </svg>
                            <span class="follow-text">
                                {{ auth()->user()->isFollowingActor($actor->id) ? 'Siguiendo' : 'Seguir' }}
                            </span>
                            <span class="followers-count">({{ $actor->followers()->count() }} seguidores)</span>
                        </button>
                    </div>
                    @endauth
                    
                    @if($actor->also_known_as)
                    <div style="margin-top: 2rem;">
                        <h4 style="color: rgba(255,255,255,0.8); font-size: 1rem; margin-bottom: 0.8rem; font-weight: 600;">También conocido como:</h4>
                        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                            @foreach(array_slice($actor->also_known_as, 0, 3) as $alias)
                            <span style="background: rgba(255,255,255,0.1); padding: 0.4rem 1rem; border-radius: 16px; font-size: 0.85rem; color: rgba(255,255,255,0.9); font-weight: 500;">
                                {{ $alias }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Actor Information -->
<div style="margin-top: -100px; position: relative; z-index: 20;" id="info">
    
    <!-- Actor Details -->
    <section class="content-section">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Personal Information -->
            <div class="detail-section">
                <h3 class="detail-section-title">👤 Información Personal</h3>
                <div class="detail-grid" style="grid-template-columns: 1fr;">
                    @if($actor->gender)
                    <div class="detail-item">
                        <span class="detail-label">🚻 Género</span>
                        <span class="detail-value">{{ $actor->gender == 1 ? 'Femenino' : ($actor->gender == 2 ? 'Masculino' : 'No especificado') }}</span>
                    </div>
                    @endif
                    
                    @if($actor->birthday)
                    <div class="detail-item">
                        <span class="detail-label">🎂 Fecha de Nacimiento</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($actor->birthday)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    
                    @if($actor->deathday)
                    <div class="detail-item">
                        <span class="detail-label">🕊️ Fecha de Fallecimiento</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($actor->deathday)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    
                    @if($actor->display_place_of_birth)
                    <div class="detail-item">
                        <span class="detail-label">📍 Lugar de Nacimiento</span>
                        <span class="detail-value">{{ $actor->display_place_of_birth }}</span>
                    </div>
                    @endif
                    
                    @if($actor->known_for_department)
                    <div class="detail-item">
                        <span class="detail-label">🎭 Conocido por</span>
                        <span class="detail-value">{{ $actor->known_for_department === 'Acting' ? 'Actuación' : $actor->known_for_department }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Career Stats -->
            <div class="detail-section">
                <h3 class="detail-section-title">📊 Estadísticas de Carrera</h3>
                <div class="detail-grid" style="grid-template-columns: 1fr;">
                    @if($actor->popularity)
                    <div class="detail-item">
                        <span class="detail-label">⭐ Popularidad TMDB</span>
                        <span class="detail-value">{{ number_format($actor->popularity, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($popularSeries->count() > 0)
                    <div class="detail-item">
                        <span class="detail-label">📺 Dramas Principales</span>
                        <span class="detail-value">{{ $popularSeries->count() }} series</span>
                    </div>
                    @endif
                    
                    @if($actor->birthday)
                    <div class="detail-item">
                        <span class="detail-label">🎬 Años de Carrera</span>
                        <span class="detail-value">
                            @php
                                $startYear = \Carbon\Carbon::parse($actor->birthday)->addYears(18)->year;
                                $currentYear = \Carbon\Carbon::now()->year;
                                $careerYears = max(0, $currentYear - $startYear);
                            @endphp
                            {{ $careerYears }}+ años
                        </span>
                    </div>
                    @endif
                    
                    @if($actor->also_known_as && count($actor->also_known_as) > 0)
                    <div class="detail-item">
                        <span class="detail-label">📝 Nombres Alternativos</span>
                        <span class="detail-value">{{ count($actor->also_known_as) }} nombres</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Series/Dramas -->
    @if($popularSeries->count() > 0)
    <section class="content-section">
        <h2 class="section-title">🎭 Dramas Populares</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($popularSeries as $series)
            <div style="background: rgba(20, 20, 20, 0.4); backdrop-filter: blur(10px); border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.05); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <a href="{{ route('series.show', $series->id) }}" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        @if($series->poster_path)
                        <img src="https://image.tmdb.org/t/p/w200{{ $series->poster_path }}" 
                             alt="{{ $series->title }}"
                             style="width: 80px; height: 120px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                        @else
                        <div style="width: 80px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; flex-shrink: 0;">
                            📺
                        </div>
                        @endif
                        
                        <div style="flex: 1;">
                            <h4 style="color: white; font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; line-height: 1.3;">
                                {{ $series->display_title }}
                            </h4>
                            
                            @if($series->pivot && $series->pivot->character)
                            <p style="color: rgba(0, 212, 255, 0.9); font-size: 0.85rem; font-weight: 500; margin-bottom: 0.5rem;">
                                Personaje: {{ $series->pivot->character }}
                            </p>
                            @endif
                            
                            @if($series->first_air_date)
                            <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.8rem; margin-bottom: 0.5rem;">
                                📅 {{ \Carbon\Carbon::parse($series->first_air_date)->format('Y') }}
                            </p>
                            @endif
                            
                            @if($series->vote_average > 0)
                            <div style="display: flex; align-items: center; gap: 0.3rem;">
                                <span style="background: rgba(255, 215, 0, 0.2); color: #ffd700; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600;">
                                    ⭐ {{ number_format($series->vote_average, 1) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Biography Extended -->
    @if($actor->display_biography && strlen($actor->display_biography) > 300)
    <section class="content-section">
        <div class="detail-section">
            <h3 class="detail-section-title">📖 Biografía Completa</h3>
            <div style="color: rgba(255, 255, 255, 0.9); line-height: 1.7; font-size: 1rem; text-align: justify;">
                {{ $actor->display_biography }}
            </div>
        </div>
    </section>
    @endif

    <!-- Comments Section -->
    <section class="content-section">
        <h2 class="section-title">💬 Comentarios sobre {{ $actor->display_name }} ({{ $comments->total() }})</h2>
        
        @auth
        <!-- Comment Form -->
        <div class="comment-form-container">
            <form id="actorCommentForm" class="comment-form">
                @csrf
                <textarea 
                    id="actorCommentContent" 
                    placeholder="¿Qué opinas sobre {{ $actor->display_name }}? Comparte tu comentario..."
                    class="comment-textarea"
                    maxlength="1000"
                    required></textarea>
                
                <div class="comment-form-actions">
                    <label class="spoiler-checkbox">
                        <input type="checkbox" id="actorIsSpoiler">
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
                <p>🤔 ¡Sé el primero en comentar sobre {{ $actor->display_name }}!</p>
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

</div>

<style>
/* Mobile optimizations */
@media (max-width: 768px) {
    /* Hero Section Mobile */
    .hero-section {
        min-height: auto !important;
        padding-top: 80px !important;
        padding-bottom: 60px !important;
    }
    
    .hero-content {
        padding: 1rem !important;
    }
    
    .hero-info-box {
        padding: 1.5rem !important;
    }
    
    /* Actor layout mobile */
    .hero-info-box > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
        text-align: center !important;
        min-height: auto !important;
    }
    
    /* Actor photo mobile */
    .hero-info-box img,
    .hero-info-box > div:first-child > div:first-child > div {
        width: 150px !important;
        height: 200px !important;
        margin: 0 auto !important;
        border-radius: 12px !important;
    }
    
    /* Actor info mobile */
    .hero-info-box > div:first-child > div:last-child {
        padding-left: 0 !important;
    }
    
    .hero-title {
        font-size: 1.8rem !important;
        text-align: center !important;
        margin-bottom: 0.5rem !important;
    }
    
    .hero-meta {
        justify-content: center !important;
        gap: 0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .hero-meta .hero-category {
        font-size: 0.75rem !important;
        padding: 0.4rem 0.6rem !important;
    }
    
    .hero-description {
        font-size: 0.85rem !important;
        text-align: center !important;
        line-height: 1.5 !important;
        margin-bottom: 1rem !important;
    }
    
    /* Also known as section mobile */
    .hero-info-box h4 {
        font-size: 0.9rem !important;
        text-align: center !important;
        margin-bottom: 0.5rem !important;
    }
    
    .hero-info-box > div:first-child > div:last-child > div:last-child > div {
        justify-content: center !important;
        gap: 0.4rem !important;
    }
    
    .hero-info-box > div:first-child > div:last-child > div:last-child span {
        font-size: 0.75rem !important;
        padding: 0.3rem 0.6rem !important;
    }
    
    /* Content sections mobile */
    #info {
        margin-top: -50px !important;
    }
    
    .content-section {
        padding: 0 0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    /* Details grid mobile */
    .content-section > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .detail-section {
        padding: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .detail-section-title {
        font-size: 1.1rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    .detail-item {
        margin-bottom: 0.8rem !important;
    }
    
    .detail-label {
        font-size: 0.75rem !important;
        margin-bottom: 0.2rem !important;
    }
    
    .detail-value {
        font-size: 0.85rem !important;
    }
    
    /* Series grid mobile */
    .series-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.8rem !important;
    }
    
    .series-card {
        margin-bottom: 0 !important;
    }
    
    .series-card .card {
        min-width: unset !important;
        width: 100% !important;
        aspect-ratio: 2/3 !important;
    }
    
    .series-card .card-title {
        font-size: 0.8rem !important;
        line-height: 1.2 !important;
    }
    
    .series-card .card-meta {
        font-size: 0.65rem !important;
    }
    
    /* Comments section mobile */
    .comments-section {
        padding: 1rem !important;
    }
    
    .comments-section h2 {
        font-size: 1.2rem !important;
        margin-bottom: 1rem !important;
    }
    
    .comment-form-card {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .comment-form-card h3 {
        font-size: 1rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    .comment-textarea {
        font-size: 0.85rem !important;
        padding: 0.6rem !important;
    }
    
    .comment-options {
        flex-direction: column !important;
        gap: 0.8rem !important;
        align-items: stretch !important;
    }
    
    .spoiler-checkbox {
        font-size: 0.8rem !important;
    }
    
    .comment-submit-btn {
        font-size: 0.85rem !important;
        padding: 0.6rem 1rem !important;
        width: 100% !important;
    }
    
    /* Comments list mobile */
    .comment {
        padding: 1rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    .comment-header {
        margin-bottom: 0.8rem !important;
    }
    
    .comment-avatar,
    .comment-avatar-placeholder {
        width: 30px !important;
        height: 30px !important;
        font-size: 0.8rem !important;
    }
    
    .comment-username {
        font-size: 0.85rem !important;
    }
    
    .comment-date {
        font-size: 0.7rem !important;
    }
    
    .comment-content {
        font-size: 0.85rem !important;
        line-height: 1.4 !important;
    }
    
    .spoiler-badge {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.5rem !important;
    }
    
    /* Reply comments mobile */
    .comment.reply {
        margin-left: 1rem !important;
        padding: 0.8rem !important;
    }
    
    /* Auth prompt mobile */
    .auth-prompt {
        padding: 1.5rem 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .auth-prompt h3 {
        font-size: 1rem !important;
    }
    
    .auth-prompt p {
        font-size: 0.85rem !important;
    }
}

/* Very small phones */
@media (max-width: 480px) {
    /* Single column for series on very small screens */
    .series-grid {
        grid-template-columns: 1fr !important;
    }
    
    .hero-meta {
        flex-direction: column !important;
    }
    
    .hero-meta .hero-category {
        width: 100% !important;
        text-align: center !important;
    }
}

/* Follow Button Styles */
.follow-btn {
    background: linear-gradient(135deg, #0099ff 0%, #0066cc 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 153, 255, 0.3);
}

.follow-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0, 153, 255, 0.4);
}

.follow-btn.following {
    background: linear-gradient(135deg, #46d369 0%, #2ea54b 100%);
    box-shadow: 0 4px 20px rgba(70, 211, 105, 0.3);
}

.follow-btn.following:hover {
    box-shadow: 0 6px 25px rgba(70, 211, 105, 0.4);
}

.follow-btn .followers-count {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-left: 0.5rem;
}

.follow-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 768px) {
    .follow-btn {
        width: 100%;
        justify-content: center;
        padding: 1.2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actor Comment Form Handler
    const actorCommentForm = document.getElementById('actorCommentForm');
    if (actorCommentForm) {
        actorCommentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = document.getElementById('actorCommentContent').value.trim();
            const isSpoiler = document.getElementById('actorIsSpoiler').checked;
            const submitBtn = this.querySelector('.comment-submit-btn');
            
            if (!content) {
                alert('Por favor escribe un comentario');
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = '📤 Enviando...';
            
            // Send AJAX request
            fetch('{{ route("actors.comments.store", $actor->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    content: content,
                    is_spoiler: isSpoiler
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form
                    document.getElementById('actorCommentContent').value = '';
                    document.getElementById('actorIsSpoiler').checked = false;
                    
                    // Add new comment to the list
                    addCommentToList(data.comment);
                    
                    // Show success message
                    showNotification('¡Comentario publicado exitosamente!', 'success');
                } else {
                    showNotification('Error al publicar el comentario', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = '📝 Publicar Comentario';
            });
        });
    }
    
    // Spoiler reveal functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('reveal-spoiler-btn')) {
            const spoilerContent = e.target.closest('.comment-content');
            spoilerContent.classList.remove('spoiler-hidden');
            e.target.remove();
        }
    });
    
    function addCommentToList(comment) {
        const commentsContainer = document.querySelector('.comments-container');
        const noComments = commentsContainer.querySelector('.no-comments');
        
        if (noComments) {
            noComments.remove();
        }
        
        const commentHtml = `
            <div class="comment ${comment.is_spoiler ? 'spoiler-comment' : ''}">
                <div class="comment-header">
                    <div class="comment-user">
                        ${comment.user.avatar ? 
                            `<img src="${comment.user.avatar}" alt="${comment.user.name}" class="comment-avatar">` :
                            `<div class="comment-avatar-placeholder">${comment.user.name.charAt(0)}</div>`
                        }
                        <div class="comment-user-info">
                            <span class="comment-username">${comment.user.name}</span>
                            <span class="comment-date">${comment.created_at}</span>
                        </div>
                    </div>
                    ${comment.is_spoiler ? '<span class="spoiler-badge">⚠️ Spoiler</span>' : ''}
                </div>
                
                <div class="comment-content ${comment.is_spoiler ? 'spoiler-hidden' : ''}">
                    ${comment.content}
                    ${comment.is_spoiler ? '<div class="spoiler-overlay"><button class="reveal-spoiler-btn">👁️ Mostrar spoiler</button></div>' : ''}
                </div>
            </div>
        `;
        
        commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
    }
    
    function showNotification(message, type) {
        // Simple notification - you can enhance this
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 9999;
            background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #dc3545, #fd7e14)'};
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Follow Button Functionality
    const followBtn = document.getElementById('followBtn');
    if (followBtn) {
        followBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const actorId = this.dataset.actorId;
            const isFollowing = this.dataset.following === 'true';
            
            // Disable button during request
            this.disabled = true;
            
            const url = isFollowing ? `/actors/${actorId}/unfollow` : `/actors/${actorId}/follow`;
            const method = isFollowing ? 'DELETE' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    this.dataset.following = data.is_following;
                    
                    // Update button text and style
                    const followText = this.querySelector('.follow-text');
                    const followersCount = this.querySelector('.followers-count');
                    
                    followText.textContent = data.is_following ? 'Siguiendo' : 'Seguir';
                    followersCount.textContent = `(${data.followers_count} seguidores)`;
                    
                    // Update button class
                    if (data.is_following) {
                        this.classList.add('following');
                    } else {
                        this.classList.remove('following');
                    }
                    
                    // Show notification
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error al procesar la solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'error');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
        
        // Set initial state
        if (followBtn.dataset.following === 'true') {
            followBtn.classList.add('following');
        }
    }
});
</script>
@endsection