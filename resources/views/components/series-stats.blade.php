@php
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        } elseif ($number == 0) {
            return '-';
        }
        return (string) $number;
    }
}
@endphp

{{-- Serie Statistics Component - Horizontal --}}
<div class="series-stats">
    <div class="stats-row">
        {{-- Likes stats --}}
        <div class="stat-item likes">
            <span class="stat-icon">👍</span>
            <span class="stat-count">{{ formatNumber($series->like_count ?? 0) }}</span>
        </div>
        
        <div class="stat-item dislikes">
            <span class="stat-icon">👎</span>
            <span class="stat-count">{{ formatNumber($series->dislike_count ?? 0) }}</span>
        </div>
        
        <div class="stat-item loves">
            <span class="stat-icon">❤️</span>
            <span class="stat-count">{{ formatNumber($series->love_count ?? 0) }}</span>
        </div>
        
        {{-- Comments count --}}
        <div class="stat-item comments">
            <span class="stat-icon">💬</span>
            <span class="stat-count">{{ formatNumber($series->comments_count ?? 0) }}</span>
        </div>
    </div>
</div>

<style>
.series-stats {
    position: absolute;
    bottom: 8px;
    left: 8px;
    right: 8px;
    z-index: 12;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.stats-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 4px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
    flex: 1;
    min-width: 0;
}

.stat-icon {
    font-size: 11px;
    line-height: 1;
    filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.8)) grayscale(1) brightness(10);
}

.stat-count {
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9);
    text-align: center;
    white-space: nowrap;
    letter-spacing: 0.2px;
}

/* Efectos hover */
.card:hover .series-stats {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .series-stats {
        bottom: 6px;
        left: 6px;
        right: 6px;
    }
    
    .stats-row {
        gap: 2px;
    }
    
    .stat-icon {
        font-size: 12px;
    }
    
    .stat-count {
        font-size: 9px;
    }
}

/* Color único para todos los iconos */
.stat-item .stat-icon { 
    color: #ffffff !important;
}
</style>