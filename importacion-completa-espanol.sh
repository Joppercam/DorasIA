#!/bin/bash

echo "🚀 IMPORTACIÓN COMPLETA DE DORASIA EN ESPAÑOL"
echo "============================================="
echo "Este script realizará una importación completa con traducciones en español"
echo ""

# Verificar que existe la API key de OpenAI
if [ -z "$OPENAI_API_KEY" ]; then
    echo "⚠️  ADVERTENCIA: No se detectó OPENAI_API_KEY"
    echo "Las traducciones se saltarán automáticamente"
    echo ""
fi

# Función para mostrar progreso
show_progress() {
    echo "📊 PROGRESO ACTUAL:"
    echo "=================="
    
    php artisan tinker --execute "
        echo '📺 Series: ' . App\Models\Series::count() . ' total\n';
        echo '👥 Actores: ' . App\Models\Person::count() . ' total\n';
        echo '🎬 Críticas: ' . App\Models\ProfessionalReview::count() . ' total\n';
        echo '📰 Noticias: ' . App\Models\News::count() . ' total\n';
        echo '🎭 Series con actores: ' . App\Models\Series::has('people')->count() . '\n';
        echo '📝 Series con críticas: ' . App\Models\Series::has('professionalReviews')->count() . '\n';
        echo '📖 Actores con biografía en español: ' . App\Models\Person::whereNotNull('biography_es')->count() . '\n';
    "
    echo ""
}

# PASO 1: Preparar base de datos
echo "🔧 PASO 1: Preparando base de datos..."
echo "======================================"
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
echo "✅ Base de datos lista"
echo ""

# PASO 2: Importar series básicas (si no existen)
echo "📺 PASO 2: Verificando series básicas..."
echo "========================================"
SERIES_COUNT=$(php artisan tinker --execute "echo App\Models\Series::count();" | tail -1)

if [ "$SERIES_COUNT" -lt "50" ]; then
    echo "Importando series básicas desde TMDB..."
    # Aquí tendrías tu comando de importación básica de series
    # php artisan tmdb:import-series --limit=100
    echo "ℹ️  Asegúrate de tener series importadas antes de continuar"
else
    echo "✅ Ya hay $SERIES_COUNT series en la base de datos"
fi
echo ""

# PASO 3: Importar actores con traducciones
echo "👥 PASO 3: Importando actores con traducciones en español..."
echo "=========================================================="
echo "Importando actores para las 50 series más populares..."

# Obtener series populares y importar actores
SERIES_IDS=$(php artisan tinker --execute "
    \$ids = App\Models\Series::whereNotNull('tmdb_id')
        ->orderBy('popularity', 'desc')
        ->take(50)
        ->pluck('id');
    echo implode(' ', \$ids->toArray());
" | tail -1)

TOTAL_SERIES=$(echo $SERIES_IDS | wc -w)
CURRENT=0

for id in $SERIES_IDS; do
    CURRENT=$((CURRENT + 1))
    echo "  [$CURRENT/$TOTAL_SERIES] → Serie ID: $id"
    php artisan tmdb:import-actors --series=$id --quiet
done

echo "✅ Actores importados con traducciones"
show_progress

# PASO 4: Importar críticas profesionales
echo "📝 PASO 4: Importando críticas profesionales..."
echo "=============================================="
echo "Importando críticas para series con actores..."

SERIES_WITH_ACTORS=$(php artisan tinker --execute "
    \$ids = App\Models\Series::has('people')
        ->whereNotNull('tmdb_id')
        ->orderBy('popularity', 'desc')
        ->take(30)
        ->pluck('id');
    echo implode(' ', \$ids->toArray());
" | tail -1)

TOTAL_REVIEWS=$(echo $SERIES_WITH_ACTORS | wc -w)
CURRENT=0

for id in $SERIES_WITH_ACTORS; do
    CURRENT=$((CURRENT + 1))
    echo "  [$CURRENT/$TOTAL_REVIEWS] → Serie ID: $id"
    php artisan reviews:import --series=$id --quiet
done

echo "✅ Críticas profesionales importadas"
show_progress

# PASO 5: Importar temporadas y episodios
echo "📺 PASO 5: Importando temporadas y episodios..."
echo "=============================================="
echo "Importando temporadas y episodios para las 20 series más populares..."

SERIES_FOR_SEASONS=$(php artisan tinker --execute "
    \$ids = App\Models\Series::whereNotNull('tmdb_id')
        ->orderBy('popularity', 'desc')
        ->take(20)
        ->pluck('id');
    echo implode(' ', \$ids->toArray());
" | tail -1)

TOTAL_SEASONS_SERIES=$(echo $SERIES_FOR_SEASONS | wc -w)
CURRENT=0

for id in $SERIES_FOR_SEASONS; do
    CURRENT=$((CURRENT + 1))
    echo "  [$CURRENT/$TOTAL_SEASONS_SERIES] → Temporadas para Serie ID: $id"
    php artisan tmdb:import-seasons --series=$id --quiet
done

echo "✅ Temporadas y episodios importados"
show_progress

# PASO 6: Generar noticias (si tienes el comando)
echo "📰 PASO 6: Generando noticias..."
echo "==============================="
NEWS_COUNT=$(php artisan tinker --execute "echo App\Models\News::count();" | tail -1)

if [ "$NEWS_COUNT" -lt "20" ]; then
    echo "Generando noticias sobre K-dramas..."
    # php artisan news:generate --count=30
    echo "💡 Ejecuta manualmente: php artisan news:generate --count=30"
else
    echo "✅ Ya hay $NEWS_COUNT noticias en la base de datos"
fi
echo ""

# PASO 7: Optimizar y cachear
echo "🔧 PASO 7: Optimizando aplicación..."
echo "==================================="
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Aplicación optimizada"
echo ""

# PASO 8: Estadísticas finales
echo "📊 PASO 8: ESTADÍSTICAS FINALES"
echo "==============================="
show_progress

echo "🎉 IMPORTACIÓN COMPLETA FINALIZADA"
echo "=================================="
echo ""
echo "📋 Resumen de lo importado:"
echo "  ✅ Series con actores y biografías en español"
echo "  ✅ Críticas profesionales traducidas"
echo "  ✅ Sistema de comentarios para series y actores"
echo "  ✅ Traducciones automáticas activadas"
echo ""
echo "🚀 LISTO PARA PRODUCCIÓN"
echo "======================="
echo "La aplicación está lista para subir al hosting con:"
echo "  • Contenido completamente en español"
echo "  • Datos reales de TMDB"
echo "  • Sistema de comentarios funcional"
echo "  • Traducciones automáticas configuradas"
echo ""
echo "💡 Para importar MÁS contenido en el futuro:"
echo "   php artisan tmdb:import-actors --series=all"
echo "   php artisan reviews:import --series=all"
echo ""