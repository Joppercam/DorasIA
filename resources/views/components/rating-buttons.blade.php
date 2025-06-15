@auth
    <!-- Rating buttons for authenticated users -->
    <div class="card-rating-buttons">
        
        <!-- Mobile inline styles for colors -->
        <style>
            @media (max-width: 768px) {
                .card-rating-buttons button.rating-btn.dislike { background: #dc3545 !important; }
                .card-rating-buttons button.rating-btn.like { background: #28a745 !important; }
                .card-rating-buttons button.rating-btn.love { background: #e91e63 !important; }
                .card-rating-buttons button.rating-btn.watched { background: #6f42c1 !important; }
                .card-rating-buttons { display: flex !important; gap: 2rem !important; justify-content: space-around !important; }
                .card-rating-buttons .rating-button-with-count { display: flex !important; flex-direction: column !important; align-items: center !important; }
                .card-rating-buttons button.rating-btn { width: 50px !important; height: 50px !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; border: none !important; color: white !important; }
            }
        </style>
        @php
            $userRating = $series->userRating(Auth::id());
            $currentRating = $userRating ? $userRating->rating_type : null;
            $ratingCounts = $series->getRatingCounts();
            
            // Check if user has watched this series
            $hasWatched = false;
            if (Auth::check()) {
                $watchHistory = Auth::user()->watchHistory()->where('series_id', $series->id)->where('status', 'completed')->first();
                $hasWatched = $watchHistory !== null;
            }
        @endphp
        
        <div class="rating-button-with-count">
            <button class="rating-btn dislike {{ $currentRating === 'dislike' ? 'active' : '' }}" 
                    onclick="rateSeries({{ $series->id }}, 'dislike', this)"
                    title="No me gusta">
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
                    onclick="rateSeries({{ $series->id }}, 'like', this)"
                    title="Me gusta">
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
                    onclick="rateSeries({{ $series->id }}, 'love', this)"
                    title="Me encanta">
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
                    onclick="markAsWatched({{ $series->id }}, this)"
                    title="{{ $hasWatched ? 'Ya la viste' : 'Marcar como vista' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z"/>
                </svg>
            </button>
            <span class="rating-count watched-count">
                <span class="count-number">0</span>
                <span class="count-label">Vistas</span>
            </span>
        </div>
    </div>
@endauth