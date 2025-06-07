#!/bin/bash

echo "🎬 DORASIA - Importador de Contenido en Español"
echo "=============================================="
echo ""

# Función para mostrar ayuda
show_help() {
    echo "Uso: $0 [opciones]"
    echo ""
    echo "Opciones:"
    echo "  --series [páginas]     Importar series K-Drama en español (por defecto: 10 páginas)"
    echo "  --actors              Actualizar información de actores en español"
    echo "  --all [páginas]       Importar series y actualizar actores (por defecto: 10 páginas)"
    echo "  --quick               Importación rápida (5 páginas de series + actores)"
    echo "  --help                Mostrar esta ayuda"
    echo ""
    echo "Ejemplos:"
    echo "  $0 --series 5         # Importar 5 páginas de series"
    echo "  $0 --actors           # Actualizar solo actores"
    echo "  $0 --all 15           # Importar 15 páginas de series + actores"
    echo "  $0 --quick            # Importación rápida"
    echo ""
}

# Variables por defecto
PAGES=10
IMPORT_SERIES=false
IMPORT_ACTORS=false

# Procesar argumentos
while [[ $# -gt 0 ]]; do
    case $1 in
        --series)
            IMPORT_SERIES=true
            if [[ $2 =~ ^[0-9]+$ ]]; then
                PAGES=$2
                shift
            fi
            shift
            ;;
        --actors)
            IMPORT_ACTORS=true
            shift
            ;;
        --all)
            IMPORT_SERIES=true
            IMPORT_ACTORS=true
            if [[ $2 =~ ^[0-9]+$ ]]; then
                PAGES=$2
                shift
            fi
            shift
            ;;
        --quick)
            IMPORT_SERIES=true
            IMPORT_ACTORS=true
            PAGES=5
            shift
            ;;
        --help)
            show_help
            exit 0
            ;;
        *)
            echo "❌ Opción desconocida: $1"
            echo "Usa --help para ver las opciones disponibles."
            exit 1
            ;;
    esac
done

# Si no se especifica nada, mostrar ayuda
if [[ $IMPORT_SERIES == false && $IMPORT_ACTORS == false ]]; then
    echo "❌ Debes especificar al menos una opción."
    echo ""
    show_help
    exit 1
fi

# Verificar que estamos en el directorio correcto
if [[ ! -f "artisan" ]]; then
    echo "❌ Error: No se encontró el archivo 'artisan'."
    echo "Asegúrate de ejecutar este script desde el directorio raíz del proyecto Laravel."
    exit 1
fi

echo "📋 Configuración:"
echo "   - Importar series: $([ $IMPORT_SERIES == true ] && echo "Sí ($PAGES páginas)" || echo "No")"
echo "   - Actualizar actores: $([ $IMPORT_ACTORS == true ] && echo "Sí" || echo "No")"
echo ""

# Confirmar antes de proceder
read -p "¿Continuar con la importación? (s/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
    echo "❌ Importación cancelada."
    exit 0
fi

echo ""
echo "🚀 Iniciando importación..."
echo ""

# Ejecutar importación
if [[ $IMPORT_SERIES == true && $IMPORT_ACTORS == true ]]; then
    echo "📺🎭 Importando series y actualizando actores..."
    php artisan dorasia:import-spanish --pages=$PAGES
elif [[ $IMPORT_SERIES == true ]]; then
    echo "📺 Importando series..."
    php artisan dorasia:import-spanish --series --pages=$PAGES
elif [[ $IMPORT_ACTORS == true ]]; then
    echo "🎭 Actualizando actores..."
    php artisan dorasia:import-spanish --actors
fi

# Verificar si el comando se ejecutó correctamente
if [[ $? -eq 0 ]]; then
    echo ""
    echo "✅ ¡Importación completada exitosamente!"
    echo ""
    echo "📊 Estadísticas actuales:"
    php artisan tinker --execute="
        echo 'Series totales: ' . \App\Models\Series::count() . \"\n\";
        echo 'Series con título en español: ' . \App\Models\Series::whereNotNull('title_es')->count() . \"\n\";
        echo 'Actores totales: ' . \App\Models\Person::count() . \"\n\";
        echo 'Géneros: ' . \App\Models\Genre::count() . \"\n\";
    "
else
    echo ""
    echo "❌ Error durante la importación."
    echo "Revisa los logs para más detalles."
    exit 1
fi

echo ""
echo "🎉 ¡Proceso completado!"
echo "Ahora puedes utilizar la búsqueda en español en tu aplicación DORASIA."