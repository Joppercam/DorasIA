<!-- Mobile-First Reviews/Comments Accordion -->
@if((isset($movie) && $movie->professionalReviews && $movie->professionalReviews->count() > 0) || 
    (isset($series) && $series->professionalReviews && $series->professionalReviews->count() > 0))

@php
    $content = $movie ?? $series;
    $contentType = isset($movie) ? 'película' : 'serie';
    $reviews = $content->professionalReviews->filter(function($review) {
        return $review->hasSpanishContent();
    });
@endphp

@if($reviews->count() > 0)
<div class="mobile-reviews-accordion">
    <div class="accordion-header" onclick="toggleReviewsAccordion()">
        <div class="accordion-title">
            <span class="reviews-icon">📝</span>
            <h3>Críticas y Reseñas</h3>
            <span class="review-count-badge">{{ $reviews->count() }}</span>
        </div>
        <div class="accordion-toggle">
            <span class="click-hint">Tocar para abrir</span>
            <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>

    <div class="accordion-content" id="reviews-accordion-content">
        <div class="reviews-list">
            @foreach($reviews as $index => $review)
            <div class="review-card" data-review-id="{{ $review->id }}">
                <div class="review-header">
                    <div class="review-source">
                        @if($review->source_url)
                            <a href="{{ $review->source_url }}" target="_blank" rel="noopener">
                                {{ $review->source }}
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 4px;">
                                    <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
                                </svg>
                            </a>
                        @else
                            {{ $review->source }}
                        @endif
                    </div>
                    @if($review->rating && $review->max_rating)
                    <div class="review-rating">
                        @php
                            $stars = round(($review->rating / $review->max_rating) * 5);
                        @endphp
                        @for($i = 0; $i < 5; $i++)
                            <span class="star {{ $i < $stars ? 'filled' : '' }}">★</span>
                        @endfor
                        <span class="rating-text">({{ $review->rating }}/{{ $review->max_rating }})</span>
                    </div>
                    @endif
                </div>
                
                <div class="review-content">
                    <p class="review-excerpt">
                        "{{ $review->display_excerpt ?: Str::limit($review->display_content, 200) }}"
                    </p>
                    @if($review->author)
                    <p class="review-author">— {{ $review->author }}</p>
                    @endif
                </div>

                @if($review->display_content && strlen($review->display_content) > 200)
                <button class="read-more-btn" onclick="toggleFullReview({{ $review->id }})">
                    <span class="read-more-text">Leer más</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                
                <div class="full-review-content" id="full-review-{{ $review->id }}" style="display: none;">
                    <p>"{{ $review->display_content }}"</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.mobile-reviews-accordion {
    background: rgba(20, 20, 20, 0.95);
    border-radius: 12px;
    margin: 1rem 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.accordion-header {
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.accordion-header:hover {
    background: rgba(255, 255, 255, 0.05);
}

.accordion-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.reviews-icon {
    font-size: 1.5rem;
}

.accordion-title h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
}

.review-count-badge {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.accordion-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.click-hint {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
    transition: color 0.3s ease;
}

.accordion-header:hover .click-hint {
    color: #ff6b6b;
}

.accordion-toggle .chevron-icon {
    color: #ff6b6b;
    transition: transform 0.3s ease;
}

.accordion-toggle.active .chevron-icon {
    transform: rotate(180deg);
}

.accordion-toggle.active .click-hint {
    display: none;
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
    opacity: 0;
    visibility: hidden;
}

.accordion-content.open {
    max-height: 2000px;
    opacity: 1;
    visibility: visible;
}

.reviews-list {
    padding: 1rem;
}

.review-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: all 0.3s ease;
}

.review-card:last-child {
    margin-bottom: 0;
}

.review-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 107, 107, 0.3);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.review-source {
    font-weight: 600;
    color: #ff6b6b;
}

.review-source a {
    color: #ff6b6b;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}

.review-source a:hover {
    color: #ff8e53;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.star {
    color: rgba(255, 255, 255, 0.3);
    font-size: 1rem;
}

.star.filled {
    color: #ffd700;
}

.rating-text {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    margin-left: 0.3rem;
}

.review-content {
    margin-bottom: 0.5rem;
}

.review-excerpt {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0 0 0.5rem 0;
    font-style: italic;
}

.review-author {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    margin: 0;
    text-align: right;
}

.read-more-btn {
    background: none;
    border: none;
    color: #ff6b6b;
    font-size: 0.85rem;
    padding: 0.25rem 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: color 0.3s ease;
}

.read-more-btn:hover {
    color: #ff8e53;
}

.read-more-btn svg {
    transition: transform 0.3s ease;
}

.read-more-btn.expanded svg {
    transform: rotate(180deg);
}

.full-review-content {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    animation: slideDown 0.3s ease;
}

.full-review-content p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 480px) {
    .accordion-header {
        padding: 0.75rem;
    }
    
    .accordion-title h3 {
        font-size: 1rem;
    }
    
    .reviews-list {
        padding: 0.75rem;
    }
    
    .review-card {
        padding: 0.75rem;
    }
    
    .review-excerpt {
        font-size: 0.9rem;
    }
}
</style>

<script>
let isReviewsAccordionOpen = false;

function toggleReviewsAccordion() {
    const content = document.getElementById('reviews-accordion-content');
    const toggle = document.querySelector('.mobile-reviews-accordion .accordion-toggle');
    const hint = document.querySelector('.mobile-reviews-accordion .click-hint');
    
    isReviewsAccordionOpen = !isReviewsAccordionOpen;
    
    if (isReviewsAccordionOpen) {
        content.classList.add('open');
        toggle.classList.add('active');
        if (hint) hint.textContent = 'Tocar para cerrar';
    } else {
        content.classList.remove('open');
        toggle.classList.remove('active');
        if (hint) hint.textContent = 'Tocar para abrir';
    }
}

function toggleFullReview(reviewId) {
    const fullContent = document.getElementById(`full-review-${reviewId}`);
    const btn = event.currentTarget;
    const btnText = btn.querySelector('.read-more-text');
    
    if (fullContent.style.display === 'none') {
        fullContent.style.display = 'block';
        btn.classList.add('expanded');
        btnText.textContent = 'Leer menos';
    } else {
        fullContent.style.display = 'none';
        btn.classList.remove('expanded');
        btnText.textContent = 'Leer más';
    }
}
</script>
@endif

@endif

<!-- Mobile-First Community Comments Accordion -->
@php
    $content = $content ?? ($movie ?? $series);
    $contentType = $contentType ?? (isset($movie) ? 'película' : 'serie');
@endphp
<div class="mobile-comments-accordion">
    <div class="accordion-header" onclick="toggleCommentsAccordion()">
        <div class="accordion-title">
            <span class="comments-icon">💬</span>
            <h3>Comentarios de la Comunidad</h3>
            @if(isset($content) && $content->comments && $content->comments->count() > 0)
            <span class="comment-count-badge">{{ $content->comments->count() }}</span>
            @endif
        </div>
        <div class="accordion-toggle">
            <span class="click-hint">Tocar para abrir</span>
            <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>

    <div class="accordion-content" id="comments-accordion-content">
        <div class="comments-section">
            @auth
                <div class="add-comment-box">
                    <form method="POST" action="{{ route(isset($movie) ? 'movies.comments.store' : 'series.comments.store', $content->id) }}">
                        @csrf
                        <textarea 
                            name="comment" 
                            placeholder="Comparte tu opinión sobre esta {{ $contentType }}..."
                            class="comment-input"
                            rows="3"
                            required
                        ></textarea>
                        <button type="submit" class="submit-comment-btn">
                            Publicar comentario
                        </button>
                    </form>
                </div>
            @else
                <div class="login-prompt">
                    <p>Para comentar necesitas iniciar sesión</p>
                    <a href="{{ route('login') }}" class="login-btn">Iniciar sesión</a>
                </div>
            @endauth

            <div class="comments-list">
                @if(isset($content) && $content->comments && $content->comments->count() > 0)
                    @foreach($content->comments->sortByDesc('created_at') as $comment)
                    <div class="comment-card">
                        <div class="comment-header">
                            <div class="comment-author">
                                <span class="author-avatar">👤</span>
                                <span class="author-name">{{ $comment->user->name }}</span>
                            </div>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="comment-text">{{ $comment->content }}</p>
                        @if($comment->user_id === auth()->id())
                        <form method="POST" action="{{ route('comments.delete', $comment->id) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-comment-btn" onclick="return confirm('¿Eliminar este comentario?')">
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="no-comments">
                        <div class="no-comments-icon">💭</div>
                        <p>Aún no hay comentarios</p>
                        <p class="no-comments-sub">¡Sé el primero en compartir tu opinión!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.mobile-comments-accordion {
    background: rgba(20, 20, 20, 0.95);
    border-radius: 12px;
    margin: 1rem 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.mobile-comments-accordion .accordion-header {
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.mobile-comments-accordion .accordion-header:hover {
    background: rgba(255, 255, 255, 0.05);
}

.mobile-comments-accordion .accordion-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.comments-icon {
    font-size: 1.5rem;
}

.mobile-comments-accordion .accordion-title h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
}

.comment-count-badge {
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.mobile-comments-accordion .accordion-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mobile-comments-accordion .click-hint {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
    transition: color 0.3s ease;
}

.mobile-comments-accordion .accordion-header:hover .click-hint {
    color: #00d4ff;
}

.mobile-comments-accordion .accordion-toggle .chevron-icon {
    color: #00d4ff;
    transition: transform 0.3s ease;
}

.mobile-comments-accordion .accordion-toggle.active .chevron-icon {
    transform: rotate(180deg);
}

.mobile-comments-accordion .accordion-toggle.active .click-hint {
    display: none;
}

.mobile-comments-accordion .accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
    opacity: 0;
    visibility: hidden;
}

.mobile-comments-accordion .accordion-content.open {
    max-height: 2000px;
    opacity: 1;
    visibility: visible;
}

.comments-section {
    padding: 1rem;
}

.add-comment-box {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(0, 212, 255, 0.2);
}

.comment-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.75rem;
    border-radius: 6px;
    font-size: 0.95rem;
    resize: vertical;
    transition: all 0.3s ease;
}

.comment-input:focus {
    outline: none;
    border-color: #00d4ff;
    background: rgba(255, 255, 255, 0.08);
}

.comment-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.submit-comment-btn {
    margin-top: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-comment-btn:hover {
    background: linear-gradient(135deg, #0099cc, #0077aa);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

.login-prompt {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    margin-bottom: 1rem;
}

.login-prompt p {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 1rem;
}

.login-btn {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #00d4ff, #0099cc);
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.login-btn:hover {
    background: linear-gradient(135deg, #0099cc, #0077aa);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

.comments-list {
    margin-top: 1rem;
}

.comment-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: all 0.3s ease;
}

.comment-card:hover {
    background: rgba(255, 255, 255, 0.08);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.comment-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-avatar {
    font-size: 1.2rem;
}

.author-name {
    font-weight: 600;
    color: #00d4ff;
}

.comment-date {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
}

.comment-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0;
}

.delete-form {
    margin-top: 0.5rem;
}

.delete-comment-btn {
    background: none;
    border: none;
    color: #ff6b6b;
    font-size: 0.8rem;
    cursor: pointer;
    transition: color 0.3s ease;
}

.delete-comment-btn:hover {
    color: #ff4444;
    text-decoration: underline;
}

.no-comments {
    text-align: center;
    padding: 3rem 1rem;
}

.no-comments-icon {
    font-size: 3rem;
    opacity: 0.5;
    margin-bottom: 1rem;
}

.no-comments p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0.5rem 0;
}

.no-comments-sub {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 480px) {
    .mobile-comments-accordion .accordion-header {
        padding: 0.75rem;
    }
    
    .mobile-comments-accordion .accordion-title h3 {
        font-size: 1rem;
    }
    
    .comments-section {
        padding: 0.75rem;
    }
    
    .add-comment-box,
    .comment-card {
        padding: 0.75rem;
    }
}
</style>

<script>
let isCommentsAccordionOpen = false;

function toggleCommentsAccordion() {
    const content = document.getElementById('comments-accordion-content');
    const toggle = document.querySelector('.mobile-comments-accordion .accordion-toggle');
    const hint = document.querySelector('.mobile-comments-accordion .click-hint');
    
    isCommentsAccordionOpen = !isCommentsAccordionOpen;
    
    if (isCommentsAccordionOpen) {
        content.classList.add('open');
        toggle.classList.add('active');
        if (hint) hint.textContent = 'Tocar para cerrar';
    } else {
        content.classList.remove('open');
        toggle.classList.remove('active');
        if (hint) hint.textContent = 'Tocar para abrir';
    }
}
</script>