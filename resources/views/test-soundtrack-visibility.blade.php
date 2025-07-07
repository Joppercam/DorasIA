<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Soundtrack Visibility</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #141414;
            color: white;
            padding: 20px;
            line-height: 1.6;
        }
        .test-section {
            background: #2a2a2a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border: 2px solid #00d4ff;
        }
        .highlight {
            background: #00d4ff;
            color: #000;
            padding: 2px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>🎵 Test de Visibilidad del Componente Soundtrack</h1>
    
    <div class="test-section">
        <h2>📊 Estadísticas del Sistema</h2>
        @php
            $moviesWithSoundtracks = \App\Models\Movie::with('soundtracks')->whereHas('soundtracks')->count();
            $seriesWithSoundtracks = \App\Models\Series::with('soundtracks')->whereHas('soundtracks')->count();
            $totalSoundtracks = \App\Models\Soundtrack::count();
            
            $testMovie = \App\Models\Movie::with('soundtracks')->whereHas('soundtracks')->first();
            $testSeries = \App\Models\Series::with('soundtracks')->whereHas('soundtracks')->first();
        @endphp
        
        <p><span class="highlight">{{ $moviesWithSoundtracks }}</span> películas con banda sonora</p>
        <p><span class="highlight">{{ $seriesWithSoundtracks }}</span> series con banda sonora</p>
        <p><span class="highlight">{{ $totalSoundtracks }}</span> soundtracks totales en el sistema</p>
    </div>

    <div class="test-section">
        <h2>🎬 Test Movie ({{ $testMovie->display_title ?? 'No movie found' }})</h2>
        @if($testMovie)
            <p>Movie ID: <span class="highlight">{{ $testMovie->id }}</span></p>
            <p>Soundtracks: <span class="highlight">{{ $testMovie->soundtracks->count() }}</span></p>
            <p>URL: <a href="/peliculas/{{ $testMovie->id }}" style="color: #00d4ff;">/peliculas/{{ $testMovie->id }}</a></p>
            
            <h3>🎵 Componente debería aparecer aquí:</h3>
            @include('components.mobile-soundtrack-accordion', ['movie' => $testMovie])
        @else
            <p>❌ No se encontraron películas con soundtrack</p>
        @endif
    </div>

    <div class="test-section">
        <h2>📺 Test Series ({{ $testSeries->display_title ?? 'No series found' }})</h2>
        @if($testSeries)
            <p>Series ID: <span class="highlight">{{ $testSeries->id }}</span></p>
            <p>Soundtracks: <span class="highlight">{{ $testSeries->soundtracks->count() }}</span></p>
            <p>URL: <a href="/series/{{ $testSeries->id }}" style="color: #00d4ff;">/series/{{ $testSeries->id }}</a></p>
            
            <h3>🎵 Componente debería aparecer aquí:</h3>
            @include('components.mobile-soundtrack-accordion', ['series' => $testSeries])
        @else
            <p>❌ No se encontraron series con soundtrack</p>
        @endif
    </div>

    <div class="test-section">
        <h2>🔧 Debug de Revisiones</h2>
        @php
            $totalReviews = \App\Models\ProfessionalReview::count();
            $englishReviews = \App\Models\ProfessionalReview::where('language', 'en')->count();
            $spanishReviews = \App\Models\ProfessionalReview::where('language', 'es')->count();
            
            // Test our filtering
            $allReviews = \App\Models\ProfessionalReview::limit(10)->get();
            $filteredReviews = $allReviews->filter(function($review) {
                return $review->hasSpanishContent();
            });
        @endphp
        
        <p>Total reviews: <span class="highlight">{{ $totalReviews }}</span></p>
        <p>Reviews marcadas como inglés: <span class="highlight">{{ $englishReviews }}</span></p>
        <p>Reviews marcadas como español: <span class="highlight">{{ $spanishReviews }}</span></p>
        <p>De 10 reviews aleatorias, filtradas a español: <span class="highlight">{{ $filteredReviews->count() }}</span></p>
        
        @if($filteredReviews->count() > 0)
            <h4>✅ Reviews en español encontradas:</h4>
            @foreach($filteredReviews->take(3) as $review)
                <div style="background: #1a1a1a; padding: 10px; margin: 5px 0; border-radius: 5px;">
                    <p><strong>ID:</strong> {{ $review->id }}</p>
                    <p><strong>Contenido:</strong> "{{ Str::limit($review->display_content ?? $review->display_excerpt ?? 'Sin contenido', 100) }}"</p>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        // Test de funcionamiento del acordeón
        function testAccordionFunction() {
            const accordion = document.querySelector('.mobile-soundtrack-accordion');
            if (accordion) {
                console.log('✅ Soundtrack accordion encontrado');
                const header = accordion.querySelector('.accordion-header');
                if (header) {
                    console.log('✅ Header del accordion encontrado');
                    // Simular click
                    header.click();
                    console.log('🔄 Click simulado en el header');
                } else {
                    console.log('❌ Header del accordion NO encontrado');
                }
            } else {
                console.log('❌ Soundtrack accordion NO encontrado');
            }
        }

        // Ejecutar test al cargar la página
        document.addEventListener('DOMContentLoaded', testAccordionFunction);
    </script>
</body>
</html>