@auth
    @php
        $inWatchlist = false;
        $currentStatus = 'want_to_watch';
        
        if (isset($series->watchlistItems) && $series->watchlistItems->isNotEmpty()) {
            $watchlistItem = $series->watchlistItems->first();
            $inWatchlist = true;
            $currentStatus = $watchlistItem->status;
        }
    @endphp
    
    <div class="watchlist-button-container" data-series-id="{{ $series->id }}">
        <button class="watchlist-btn {{ $inWatchlist ? 'in-list' : '' }}" 
                onclick="toggleWatchlist({{ $series->id }}, this)"
                title="{{ $inWatchlist ? 'En tu lista' : 'Agregar a mi lista' }}">
            @if($inWatchlist)
                <span class="watchlist-icon">✅</span>
            @else
                <span class="watchlist-icon">➕</span>
            @endif
        </button>
        
        @if($inWatchlist)
            <div class="watchlist-status-menu" style="display: none;">
                <div class="status-option {{ $currentStatus === 'want_to_watch' ? 'active' : '' }}" 
                     onclick="updateWatchlistStatus({{ $series->id }}, 'want_to_watch', this)">
                    🎯 Pendiente
                </div>
                <div class="status-option {{ $currentStatus === 'watching' ? 'active' : '' }}" 
                     onclick="updateWatchlistStatus({{ $series->id }}, 'watching', this)">
                    👀 Viendo
                </div>
                <div class="status-option {{ $currentStatus === 'completed' ? 'active' : '' }}" 
                     onclick="updateWatchlistStatus({{ $series->id }}, 'completed', this)">
                    ✅ Completada
                </div>
                <div class="status-option {{ $currentStatus === 'on_hold' ? 'active' : '' }}" 
                     onclick="updateWatchlistStatus({{ $series->id }}, 'on_hold', this)">
                    ⏸️ En Pausa
                </div>
                <div class="status-option {{ $currentStatus === 'dropped' ? 'active' : '' }}" 
                     onclick="updateWatchlistStatus({{ $series->id }}, 'dropped', this)">
                    ❌ Abandonada
                </div>
                <hr style="margin: 0.5rem 0; border-color: rgba(255,255,255,0.2);">
                <div class="status-option remove" onclick="removeFromWatchlist({{ $series->id }}, this)">
                    🗑️ Eliminar de mi lista
                </div>
            </div>
        @endif
    </div>
@endauth