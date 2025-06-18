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

    <!-- Contenido Exclusivo - Solo para usuarios registrados -->
    @auth
    @if($featuredContent->count() > 0 || $recentContent->count() > 0 || $exclusiveContent->count() > 0)
    <section class="content-section exclusive-content-section">
        <div class="exclusive-header">
            <h2 class="section-title">✨ Contenido Exclusivo de {{ $actor->display_name }}</h2>
            <div class="exclusive-badge">
                <span>🔐 Solo para usuarios registrados</span>
            </div>
        </div>

        <!-- Estadísticas de contenido -->
        @if(count($contentStats) > 0)
        <div class="content-stats">
            <h3 class="stats-title">📊 Contenido Disponible</h3>
            <div class="stats-grid">
                @foreach($contentStats as $type => $count)
                <div class="stat-item">
                    <a href="{{ route('actors.content.type', [$actor->id, $type]) }}" class="stat-link">
                        <div class="stat-icon">
                            @switch($type)
                                @case('interview') 🎤 @break
                                @case('behind_scenes') 🎬 @break
                                @case('biography') 📖 @break
                                @case('news') 📰 @break
                                @case('gallery') 🖼️ @break
                                @case('video') 📹 @break
                                @case('article') 📝 @break
                                @case('timeline') ⏰ @break
                                @case('trivia') 🧩 @break
                                @case('social') 📱 @break
                                @default 📄
                            @endswitch
                        </div>
                        <div class="stat-info">
                            <span class="stat-type">{{ \App\Models\ActorContent::TYPES[$type] ?? ucfirst($type) }}</span>
                            <span class="stat-count">{{ $count }} contenidos</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Contenido Destacado -->
        @if($featuredContent->count() > 0)
        <div class="featured-content">
            <h3 class="subsection-title">🌟 Contenido Destacado</h3>
            <div class="featured-content-grid">
                @foreach($featuredContent as $content)
                <div class="featured-content-card">
                    <a href="{{ route('actors.content.show', [$actor->id, $content->id]) }}" class="content-link">
                        @if($content->thumbnail_url)
                        <div class="content-thumbnail">
                            <img src="{{ $content->thumbnail_url }}" alt="{{ $content->title }}">
                            @if($content->duration)
                            <div class="content-duration">{{ $content->formatted_duration }}</div>
                            @endif
                            <div class="content-overlay">
                                <div class="content-play-btn">▶️</div>
                            </div>
                        </div>
                        @endif
                        <div class="content-info">
                            <div class="content-type-badge">{{ $content->type_name }}</div>
                            <h4 class="content-title">{{ $content->title }}</h4>
                            <div class="content-meta">
                                <span class="content-date">{{ $content->published_at->diffForHumans() }}</span>
                                <div class="content-stats">
                                    <span class="content-views">👁️ {{ number_format($content->view_count) }}</span>
                                    <span class="content-likes">❤️ {{ number_format($content->like_count) }}</span>
                                </div>
                            </div>
                            @if($content->content)
                            <p class="content-excerpt">{{ Str::limit($content->content, 120) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Contenido Reciente -->
        @if($recentContent->count() > 0)
        <div class="recent-content">
            <h3 class="subsection-title">🕐 Contenido Reciente</h3>
            <div class="recent-content-grid">
                @foreach($recentContent as $content)
                <div class="content-card">
                    <a href="{{ route('actors.content.show', [$actor->id, $content->id]) }}" class="content-link">
                        @if($content->thumbnail_url)
                        <div class="content-thumbnail-small">
                            <img src="{{ $content->thumbnail_url }}" alt="{{ $content->title }}">
                            @if($content->duration)
                            <div class="content-duration-small">{{ $content->formatted_duration }}</div>
                            @endif
                        </div>
                        @endif
                        <div class="content-info-small">
                            <div class="content-type-badge-small">{{ $content->type_name }}</div>
                            <h5 class="content-title-small">{{ $content->title }}</h5>
                            <div class="content-meta-small">
                                <span class="content-date-small">{{ $content->published_at->diffForHumans() }}</span>
                                <div class="content-stats-small">
                                    <span>👁️ {{ number_format($content->view_count) }}</span>
                                    <span>❤️ {{ number_format($content->like_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Ver Todo el Contenido -->
        @if($exclusiveContent->count() > 0)
        <div class="all-content-link">
            <a href="#all-content" class="view-all-btn">
                📚 Ver Todo el Contenido Exclusivo ({{ $exclusiveContent->total() }})
            </a>
        </div>
        @endif
    </section>
    @endif
    @else
    <!-- Prompt para usuarios no registrados -->
    <section class="content-section">
        <div class="exclusive-content-prompt">
            <div class="prompt-icon">🔐</div>
            <h3 class="prompt-title">Contenido Exclusivo Disponible</h3>
            <p class="prompt-description">
                {{ $actor->display_name }} tiene contenido exclusivo disponible incluyendo entrevistas, 
                behind-the-scenes, biografías detalladas y mucho más.
            </p>
            <div class="prompt-actions">
                <a href="{{ route('login') }}" class="prompt-btn primary">🔑 Iniciar Sesión</a>
                <a href="{{ route('register.simple.form') }}" class="prompt-btn secondary">✨ Registrarse Gratis</a>
            </div>
            <p class="prompt-note">
                ¡Es completamente gratis y tienes acceso inmediato!
            </p>
        </div>
    </section>
    @endauth

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

/* Exclusive Content Styles */
.exclusive-content-section {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(123, 104, 238, 0.05) 100%);
    border: 1px solid rgba(0, 212, 255, 0.1);
    border-radius: 20px;
    padding: 2.5rem;
    margin: 2rem 0;
}

.exclusive-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.exclusive-badge {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

/* Content Stats */
.content-stats {
    margin-bottom: 2.5rem;
}

.stats-title {
    color: white;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.stat-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
}

.stat-icon {
    font-size: 1.5rem;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-type {
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
}

.stat-count {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

/* Featured Content */
.featured-content {
    margin-bottom: 2.5rem;
}

.subsection-title {
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.featured-content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.featured-content-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.featured-content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
}

.content-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.content-thumbnail {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.content-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.content-duration {
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

.content-overlay {
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

.featured-content-card:hover .content-overlay {
    opacity: 1;
}

.content-play-btn {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.content-info {
    padding: 1.5rem;
}

.content-type-badge {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 0.8rem;
}

.content-title {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.content-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.content-date {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.content-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.content-excerpt {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0;
}

/* Recent Content */
.recent-content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.content-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.content-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.content-card .content-link {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    align-items: center;
}

.content-thumbnail-small {
    position: relative;
    width: 80px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
}

.content-thumbnail-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.content-duration-small {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.1rem 0.3rem;
    border-radius: 6px;
    font-size: 0.6rem;
    font-weight: 600;
}

.content-info-small {
    flex: 1;
}

.content-type-badge-small {
    background: rgba(0, 212, 255, 0.2);
    color: #00d4ff;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.65rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 0.3rem;
}

.content-title-small {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.3rem;
    line-height: 1.2;
}

.content-meta-small {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.7);
}

.content-stats-small {
    display: flex;
    gap: 0.5rem;
}

/* View All Button */
.all-content-link {
    text-align: center;
    margin-top: 2rem;
}

.view-all-btn {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
    text-decoration: none;
    color: white;
}

/* Exclusive Content Prompt */
.exclusive-content-prompt {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%);
    border: 2px solid rgba(0, 212, 255, 0.2);
    border-radius: 20px;
    padding: 3rem;
    text-align: center;
    margin: 2rem 0;
}

.prompt-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.prompt-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.prompt-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.prompt-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.prompt-btn {
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}

.prompt-btn.primary {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.prompt-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
    text-decoration: none;
    color: white;
}

.prompt-btn.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.prompt-btn.secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.prompt-note {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    font-style: italic;
}

/* Mobile Optimizations for Exclusive Content */
@media (max-width: 768px) {
    .exclusive-content-section {
        padding: 1.5rem;
        margin: 1rem 0;
    }
    
    .exclusive-header {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .featured-content-grid {
        grid-template-columns: 1fr;
    }
    
    .recent-content-grid {
        grid-template-columns: 1fr;
    }
    
    .content-card .content-link {
        flex-direction: column;
        text-align: center;
    }
    
    .content-thumbnail-small {
        width: 100%;
        height: 120px;
    }
    
    .prompt-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .prompt-btn {
        width: 100%;
        max-width: 300px;
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