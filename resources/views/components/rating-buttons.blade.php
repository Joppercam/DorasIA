<!-- Rating buttons and counters - visible to all users -->
<div class="card-rating-buttons">
    
    <!-- Mobile inline styles for colors -->
    <style>
        @media (max-width: 768px) {
            .card-rating-buttons button.rating-btn.dislike { background: #dc3545 !important; }
            .card-rating-buttons button.rating-btn.like { background: #28a745 !important; }
            .card-rating-buttons button.rating-btn.love { background: #e91e63 !important; }
            .card-rating-buttons button.rating-btn.watched { background: #6f42c1 !important; }
            .card-rating-buttons button.rating-btn.share { background: #00d4ff !important; }
            .card-rating-buttons { display: flex !important; gap: 1.5rem !important; justify-content: space-around !important; flex-wrap: wrap !important; }
            .card-rating-buttons .rating-button-with-count { display: flex !important; flex-direction: column !important; align-items: center !important; }
            .card-rating-buttons button.rating-btn { width: 50px !important; height: 50px !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; border: none !important; color: white !important; }
        }
        
        /* Styles for guest users (non-clickable) */
        .card-rating-buttons button.rating-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .guest-login-hint {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            text-align: center;
            margin-top: 0.5rem;
        }
    </style>
    @php
        $userRating = null;
        $currentRating = null;
        $hasWatched = false;
        
        if (Auth::check()) {
            $userRating = $series->userRating(Auth::id());
            $currentRating = $userRating ? $userRating->rating_type : null;
            $watchHistory = Auth::user()->watchHistory()->where('series_id', $series->id)->where('status', 'completed')->first();
            $hasWatched = $watchHistory !== null;
        }
        
        $ratingCounts = $series->getRatingCounts();
        
        // Count total users who have watched this series
        $watchedCount = \App\Models\WatchHistory::where('series_id', $series->id)
            ->where('status', 'completed')
            ->distinct('user_id')
            ->count();
    @endphp
        
    <div class="rating-button-with-count">
        <button class="rating-btn dislike {{ $currentRating === 'dislike' ? 'active' : '' }}" 
                @if(Auth::check())
                    onclick="rateSeries({{ $series->id }}, 'dislike', this)"
                    title="No me gusta"
                @else
                    disabled
                    title="Inicia sesión para calificar"
                @endif>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15 3H6C5.17 3 4.46 3.5 4.16 4.22L1.14 11.27C1.05 11.46 1 11.67 1 11.88V13C1 14.1 1.9 15 3 15H9.31L8.36 19.77C8.34 19.86 8.33 19.96 8.33 20.06C8.33 20.55 8.5 21.01 8.77 21.36L9.83 22.71L16.58 15.96C16.84 15.7 17 15.35 17 14.97V5C17 3.9 16.1 3 15 3Z"/>
                <path d="M19 3V15H23V3H19Z"/>
            </svg>
        </button>
        <span class="rating-count dislike-count">
            <span class="count-number">{{ $ratingCounts['dislike'] }}</span>
            <span class="count-label">No me gusta</span>
        </span>
    </div>
    
    <div class="rating-button-with-count">
        <button class="rating-btn like {{ $currentRating === 'like' ? 'active' : '' }}" 
                @if(Auth::check())
                    onclick="rateSeries({{ $series->id }}, 'like', this)"
                    title="Me gusta"
                @else
                    disabled
                    title="Inicia sesión para calificar"
                @endif>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 21H18C18.83 21 19.54 20.5 19.84 19.78L22.86 12.73C22.95 12.54 23 12.33 23 12.12V11C23 9.9 22.1 9 21 9H14.69L15.64 4.23C15.66 4.14 15.67 4.04 15.67 3.94C15.67 3.45 15.5 2.99 15.23 2.64L14.17 1.29L7.42 8.04C7.16 8.3 7 8.65 7 9.03V19C7 20.1 7.9 21 9 21Z"/>
                <path d="M5 21V9H1V21H5Z"/>
            </svg>
        </button>
        <span class="rating-count like-count">
            <span class="count-number">{{ $ratingCounts['like'] }}</span>
            <span class="count-label">Me gusta</span>
        </span>
    </div>
    
    <div class="rating-button-with-count">
        <button class="rating-btn love {{ $currentRating === 'love' ? 'active' : '' }}" 
                @if(Auth::check())
                    onclick="rateSeries({{ $series->id }}, 'love', this)"
                    title="Me encanta"
                @else
                    disabled
                    title="Inicia sesión para calificar"
                @endif>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z"/>
            </svg>
        </button>
        <span class="rating-count love-count">
            <span class="count-number">{{ $ratingCounts['love'] }}</span>
            <span class="count-label">Me encanta</span>
        </span>
    </div>
    
    <div class="rating-button-with-count">
        <button class="rating-btn watched {{ $hasWatched ? 'active' : '' }}" 
                @if(Auth::check())
                    onclick="markAsWatched({{ $series->id }}, this)"
                    title="{{ $hasWatched ? 'Ya la viste' : 'Marcar como vista' }}"
                @else
                    disabled
                    title="Inicia sesión para marcar como vista"
                    onclick="window.location.href='{{ route('login') }}'"
                @endif>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z"/>
            </svg>
        </button>
        <span class="rating-count watched-count">
            <span class="count-number">{{ $watchedCount }}</span>
            <span class="count-label">Vistas</span>
        </span>
    </div>
    
    <!-- Share Button -->
    <div class="rating-button-with-count">
        <button class="rating-btn share" 
                onclick="shareContent('{{ $series->display_title }}', '{{ route('series.show', $series->id) }}')"
                title="Compartir serie">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 16.08C17.24 16.08 16.56 16.38 16.04 16.85L8.91 12.7C8.96 12.47 9 12.24 9 12S8.96 11.53 8.91 11.3L15.96 7.19C16.5 7.69 17.21 8 18 8C19.66 8 21 6.66 21 5S19.66 2 18 2 15 3.34 15 5C15 5.24 15.04 5.47 15.09 5.7L8.04 9.81C7.5 9.31 6.79 9 6 9C4.34 9 3 10.34 3 12S4.34 15 6 15C6.79 15 7.5 14.69 8.04 14.19L15.16 18.34C15.11 18.55 15.08 18.77 15.08 19C15.08 20.61 16.39 21.92 18 21.92S20.92 20.61 20.92 19C20.92 17.39 19.61 16.08 18 16.08Z"/>
            </svg>
        </button>
        <span class="rating-count share-count">
            <span class="count-label">Compartir</span>
        </span>
    </div>
    
    @guest
    <!-- Share Button for non-authenticated users -->
    <div class="rating-button-with-count">
        <button class="rating-btn share" 
                onclick="shareContent('{{ $series->display_title }}', '{{ route('series.show', $series->id) }}')"
                title="Compartir serie">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 16.08C17.24 16.08 16.56 16.38 16.04 16.85L8.91 12.7C8.96 12.47 9 12.24 9 12S8.96 11.53 8.91 11.3L15.96 7.19C16.5 7.69 17.21 8 18 8C19.66 8 21 6.66 21 5S19.66 2 18 2 15 3.34 15 5C15 5.24 15.04 5.47 15.09 5.7L8.04 9.81C7.5 9.31 6.79 9 6 9C4.34 9 3 10.34 3 12S4.34 15 6 15C6.79 15 7.5 14.69 8.04 14.19L15.16 18.34C15.11 18.55 15.08 18.77 15.08 19C15.08 20.61 16.39 21.92 18 21.92S20.92 20.61 20.92 19C20.92 17.39 19.61 16.08 18 16.08Z"/>
            </svg>
        </button>
        <span class="rating-count share-count">
            <span class="count-label">Compartir</span>
        </span>
    </div>
    
    <div class="guest-login-hint">
        <a href="{{ route('login') }}" style="color: #00d4ff; text-decoration: none;">
            🔑 Inicia sesión para calificar
        </a>
    </div>
    @endguest
</div>

<style>
    @media (max-width: 768px) {
        .card-rating-buttons button.rating-btn.share { background: #00d4ff !important; }
    }
</style>