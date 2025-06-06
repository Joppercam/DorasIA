<?php

$filePath = '/Users/juanpablobasualdo/Desktop/Dorasia/resources/views/home.blade.php';
$content = file_get_contents($filePath);

// Template para la nueva estructura de cards
$newCardTemplate = '                @foreach($SERIES_VAR as $series)
                <div class="card" 
                     style="background-image: url(\'{{ $series->poster_path ? \'https://image.tmdb.org/t/p/w500\' . $series->poster_path : \'https://via.placeholder.com/200x300/333/666?text=K-Drama\' }}\')"
                     onclick="window.location.href=\'{{ route(\'series.show\', $series->id) }}\'">
                    <div class="card-overlay">
                        <div class="card-title">{{ $series->display_title }}</div>
                        <div class="card-meta">
                            @if($series->vote_average > 0)
                            <span class="card-rating">{{ number_format($series->vote_average, 1) }}</span>
                            @endif
                            @if($series->first_air_date)
                            <span class="card-year">{{ $series->first_air_date->format(\'Y\') }}</span>
                            @endif
                            @if($series->number_of_episodes)
                            <span class="card-episodes">{{ $series->number_of_episodes }} ep.</span>
                            @endif
                        </div>
                        @if($series->genres->count() > 0)
                        <div>
                            <span class="card-genre">{{ $series->genres->first()->display_name }}</span>
                        </div>
                        @endif
                        @if($series->display_overview)
                        <div class="card-synopsis">{{ Str::limit($series->display_overview, 80) }}</div>
                        @endif
                        @if($series->actors->count() > 0)
                        <div class="card-actors">
                            {{ $series->actors->take(3)->pluck(\'name\')->join(\', \') }}
                        </div>
                        @endif
                        @if($series->status)
                        <div class="card-status">
                            {{ $series->status === \'Ended\' ? \'Finalizada\' : ($series->status === \'Returning Series\' ? \'En emisión\' : $series->status) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach';

// Lista de variables de series a actualizar
$seriesVars = [
    'topRatedSeries',
    'romanceSeries', 
    'dramasSeries',
    'comedySeries',
    'actionSeries',
    'mysterySeries',
    'historicalSeries',
    'recentSeries'
];

foreach ($seriesVars as $seriesVar) {
    // Buscar y reemplazar el carousel-container
    $pattern = '/(\s+<div class="carousel-container">\s+<div class="carousel">\s+)@foreach\(\$' . $seriesVar . ' as \$series\).*?@endforeach/s';
    
    $replacement = '        <div class="carousel-container">
            <button class="carousel-nav prev" onclick="slideCarousel(this, -1)">‹</button>
            <button class="carousel-nav next" onclick="slideCarousel(this, 1)">›</button>
            <div class="carousel" data-current="0">
' . str_replace('SERIES_VAR', $seriesVar, $newCardTemplate) . '
            </div>';
    
    $content = preg_replace($pattern, $replacement, $content);
}

file_put_contents($filePath, $content);
echo "✅ Todas las secciones de carrusel actualizadas con botones y nuevo formato de cards\n";