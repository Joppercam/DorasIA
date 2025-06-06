@auth
    <!-- Rating buttons for authenticated users -->
    <div class="card-rating-buttons">
        @php
            $userRating = $series->userRating(Auth::id());
            $currentRating = $userRating ? $userRating->rating_type : null;
        @endphp
        
        <button class="rating-btn dislike {{ $currentRating === 'dislike' ? 'active' : '' }}" 
                onclick="rateSeries({{ $series->id }}, 'dislike', this)"
                title="No me gusta">
            👎
        </button>
        
        <button class="rating-btn like {{ $currentRating === 'like' ? 'active' : '' }}" 
                onclick="rateSeries({{ $series->id }}, 'like', this)"
                title="Me gusta">
            👍
        </button>
        
        <button class="rating-btn love {{ $currentRating === 'love' ? 'active' : '' }}" 
                onclick="rateSeries({{ $series->id }}, 'love', this)"
                title="Me encanta">
            ❤️
        </button>
    </div>
@endauth