@auth
    <!-- Rating buttons for movies -->
    <div class="movie-rating-buttons">
        @php
            $userRating = $movie->userRating(Auth::id());
            $currentRating = $userRating ? $userRating->rating_type : null;
            $ratingCounts = $movie->getRatingCounts();
        @endphp
        
        <div class="rating-button-with-count">
            <button class="movie-rating-btn dislike {{ $currentRating === 'dislike' ? 'active' : '' }}" 
                    onclick="rateMovie({{ $movie->id }}, 'dislike', this)"
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
            <button class="movie-rating-btn like {{ $currentRating === 'like' ? 'active' : '' }}" 
                    onclick="rateMovie({{ $movie->id }}, 'like', this)"
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
            <button class="movie-rating-btn love {{ $currentRating === 'love' ? 'active' : '' }}" 
                    onclick="rateMovie({{ $movie->id }}, 'love', this)"
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
            @php
                $inWatchlist = auth()->check() ? $movie->isInWatchlist(auth()->id()) : false;
            @endphp
            <button class="movie-rating-btn watchlist {{ $inWatchlist ? 'active' : '' }}" 
                    onclick="toggleMovieWatchlist({{ $movie->id }}, this)"
                    title="{{ $inWatchlist ? 'Quitar de Mi lista' : 'Agregar a Mi lista' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
            </button>
            <span class="rating-count watchlist-count">
                <span class="count-label">Mi lista</span>
            </span>
        </div>
        
        <!-- Share Button -->
        <div class="rating-button-with-count">
            <button class="movie-rating-btn share" 
                    onclick="shareContent('{{ $movie->title }}', '{{ route('movies.show', $movie->id) }}')"
                    title="Compartir película">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 16.08C17.24 16.08 16.56 16.38 16.04 16.85L8.91 12.7C8.96 12.47 9 12.24 9 12S8.96 11.53 8.91 11.3L15.96 7.19C16.5 7.69 17.21 8 18 8C19.66 8 21 6.66 21 5S19.66 2 18 2 15 3.34 15 5C15 5.24 15.04 5.47 15.09 5.7L8.04 9.81C7.5 9.31 6.79 9 6 9C4.34 9 3 10.34 3 12S4.34 15 6 15C6.79 15 7.5 14.69 8.04 14.19L15.16 18.34C15.11 18.55 15.08 18.77 15.08 19C15.08 20.61 16.39 21.92 18 21.92S20.92 20.61 20.92 19C20.92 17.39 19.61 16.08 18 16.08Z"/>
                </svg>
            </button>
            <span class="rating-count share-count">
                <span class="count-label">Compartir</span>
            </span>
        </div>
    </div>
@endauth

@guest
    <!-- Share Button for non-authenticated users -->
    <div class="movie-rating-buttons guest-share">
        <div class="rating-button-with-count">
            <button class="movie-rating-btn share" 
                    onclick="shareContent('{{ $movie->title }}', '{{ route('movies.show', $movie->id) }}')"
                    title="Compartir película">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 16.08C17.24 16.08 16.56 16.38 16.04 16.85L8.91 12.7C8.96 12.47 9 12.24 9 12S8.96 11.53 8.91 11.3L15.96 7.19C16.5 7.69 17.21 8 18 8C19.66 8 21 6.66 21 5S19.66 2 18 2 15 3.34 15 5C15 5.24 15.04 5.47 15.09 5.7L8.04 9.81C7.5 9.31 6.79 9 6 9C4.34 9 3 10.34 3 12S4.34 15 6 15C6.79 15 7.5 14.69 8.04 14.19L15.16 18.34C15.11 18.55 15.08 18.77 15.08 19C15.08 20.61 16.39 21.92 18 21.92S20.92 20.61 20.92 19C20.92 17.39 19.61 16.08 18 16.08Z"/>
                </svg>
            </button>
            <span class="rating-count share-count">
                <span class="count-label">Compartir</span>
            </span>
        </div>
        <div class="guest-login-hint" style="margin-top: 1rem;">
            <a href="{{ route('login') }}" style="color: #00d4ff; text-decoration: none;">
                🔑 Inicia sesión para calificar
            </a>
        </div>
    </div>
@endguest

<style>
    .movie-rating-btn.share {
        background: #00d4ff !important;
        color: white !important;
    }
    
    .guest-share {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .guest-share .rating-button-with-count {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .guest-share .movie-rating-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
</style>