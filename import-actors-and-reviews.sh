#!/bin/bash

echo "🎬 IMPORTACIÓN DE ACTORES Y CRÍTICAS PARA DORASIA"
echo "================================================"
echo ""

# Función para importar actores en lotes
import_actors_batch() {
    echo "📥 Importando actores para las primeras 20 series populares..."
    
    # Obtener IDs de las series más populares
    SERIES_IDS=$(php artisan tinker --execute "
        \$ids = App\Models\Series::whereNotNull('tmdb_id')
            ->orderBy('popularity', 'desc')
            ->take(20)
            ->pluck('id');
        echo implode(' ', \$ids->toArray());
    " | tail -1)
    
    for id in $SERIES_IDS; do
        echo "  → Importando actores para serie ID: $id"
        php artisan tmdb:import-actors --series=$id --quiet
    done
}

# Función para importar críticas en lotes
import_reviews_batch() {
    echo ""
    echo "📝 Importando críticas profesionales..."
    
    # Obtener IDs de las series que ya tienen actores
    SERIES_IDS=$(php artisan tinker --execute "
        \$ids = App\Models\Series::has('people')
            ->whereNotNull('tmdb_id')
            ->take(20)
            ->pluck('id');
        echo implode(' ', \$ids->toArray());
    " | tail -1)
    
    for id in $SERIES_IDS; do
        echo "  → Importando críticas para serie ID: $id"
        php artisan reviews:import --series=$id --quiet
    done
}

# Función para mostrar estadísticas
show_stats() {
    echo ""
    echo "📊 ESTADÍSTICAS FINALES:"
    echo "========================"
    
    php artisan tinker --execute "
        echo '👥 Actores: ' . App\Models\Person::count() . ' total, ' . 
             App\Models\Person::whereNotNull('biography')->count() . ' con biografía\n';
        echo '🎬 Críticas: ' . App\Models\ProfessionalReview::count() . ' total\n';
        echo '📺 Series con actores: ' . App\Models\Series::has('people')->count() . '\n';
        echo '📺 Series con críticas: ' . App\Models\Series::has('professionalReviews')->count() . '\n';
    "
}

# Ejecutar importaciones
import_actors_batch
import_reviews_batch
show_stats

echo ""
echo "✅ ¡Importación completada!"
echo ""
echo "Para importar TODAS las series (tardará mucho tiempo), ejecuta:"
echo "  php artisan tmdb:import-actors --series=all"
echo "  php artisan reviews:import --series=all"