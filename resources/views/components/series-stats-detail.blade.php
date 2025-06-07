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

{{-- Serie Statistics Component for Detail Page - Simplified --}}
<div class="series-stats-detail">
    <div class="stats-simple-row">
        <div class="stat-simple like">
            <span class="stat-icon">👍</span>
            <span class="stat-number">{{ formatNumber($series->like_count ?? 0) }}</span>
            <span class="stat-text">Me gusta</span>
        </div>
        
        <div class="stat-simple dislike">
            <span class="stat-icon">👎</span>
            <span class="stat-number">{{ formatNumber($series->dislike_count ?? 0) }}</span>
            <span class="stat-text">No me gusta</span>
        </div>
        
        <div class="stat-simple love">
            <span class="stat-icon">❤️</span>
            <span class="stat-number">{{ formatNumber($series->love_count ?? 0) }}</span>
            <span class="stat-text">Me encanta</span>
        </div>
        
        <div class="stat-simple comments">
            <span class="stat-icon">💬</span>
            <span class="stat-number">{{ formatNumber($series->comments_count ?? 0) }}</span>
            <span class="stat-text">Comentarios</span>
        </div>
    </div>
</div>

<style>
.series-stats-detail {
    margin-top: 0;
}

.stats-simple-row {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
}

.stat-simple {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    flex: 1;
    min-width: 80px;
    padding: 1rem 0.8rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: default;
}

.stat-simple:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 1.4rem;
    line-height: 1;
}

.stat-number {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
}

.stat-text {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    text-align: center;
    line-height: 1.2;
}

/* Color coding for different stats */
.stat-simple.like:hover {
    background: rgba(40, 167, 69, 0.1);
    border-color: rgba(40, 167, 69, 0.3);
}

.stat-simple.like .stat-number {
    color: #28a745;
}

.stat-simple.dislike:hover {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
}

.stat-simple.dislike .stat-number {
    color: #dc3545;
}

.stat-simple.love:hover {
    background: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.3);
}

.stat-simple.love .stat-number {
    color: #ffc107;
}

.stat-simple.comments:hover {
    background: rgba(23, 162, 184, 0.1);
    border-color: rgba(23, 162, 184, 0.3);
}

.stat-simple.comments .stat-number {
    color: #17a2b8;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-simple-row {
        gap: 1rem;
        justify-content: center;
    }
    
    .stat-simple {
        min-width: 55px;
        padding: 0.6rem 0.4rem;
    }
    
    .stat-icon {
        font-size: 1rem;
    }
    
    .stat-number {
        font-size: 0.9rem;
    }
    
    .stat-text {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .stats-simple-row {
        gap: 0.8rem;
    }
    
    .stat-simple {
        min-width: 50px;
        padding: 0.5rem 0.3rem;
    }
    
    .stat-text {
        font-size: 0.65rem;
    }
}
</style>