#!/bin/bash
# 🔄 Script para gestión de Dorasia en desarrollo

ACTION=${1:-status}

case $ACTION in
    "build"|"compile")
        echo "📦 Compilando assets..."
        npm run build
        echo "✅ Assets compilados en public/build/"
        ;;
        
    "dev"|"watch")
        echo "🔥 Iniciando Vite en modo desarrollo..."
        echo "ℹ️  Nota: Los templates usan assets compilados por defecto"
        echo "   Para hot reload, necesitarías configurar @vite directives"
        npm run dev
        ;;
        
    "serve"|"start")
        echo "🚀 Iniciando servidor Laravel..."
        echo ""
        echo "🌐 URLs disponibles:"
        echo "   Principal: http://dorasia.local:8000"
        echo "   Admin: http://admin.dorasia.local:8000"  
        echo "   Marketplace: http://marketplace.dorasia.local:8000"
        echo ""
        echo "👤 Admin: admin@dorasia.com / admin123"
        echo ""
        php artisan serve --host=0.0.0.0 --port=8000
        ;;
        
    "clear"|"clean")
        echo "🧹 Limpiando cache..."
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        php artisan cache:clear
        echo "✅ Cache limpiado"
        ;;
        
    "status"|*)
        echo "🔍 ESTADO DE DORASIA"
        echo "==================="
        echo ""
        echo "📱 Configuración:"
        echo "   APP_ENV: $(grep APP_ENV .env | cut -d'=' -f2)"
        echo "   APP_DEBUG: $(grep APP_DEBUG .env | cut -d'=' -f2)"
        echo "   APP_URL: $(grep APP_URL .env | cut -d'=' -f2)"
        echo ""
        echo "🎨 Assets:"
        if [ -d "public/build" ]; then
            echo "   ✅ Assets compilados disponibles"
            echo "   CSS: $(find public/build/assets -name "*.css" | wc -l | tr -d ' ') files"
            echo "   JS: $(find public/build/assets -name "*.js" | wc -l | tr -d ' ') files"
        else
            echo "   ❌ Assets no compilados - ejecuta: ./dev-mode.sh build"
        fi
        echo ""
        echo "🗄️ Base de datos:"
        if [ -f "database/database.sqlite" ]; then
            echo "   ✅ SQLite disponible ($(du -h database/database.sqlite | cut -f1))"
        else
            echo "   ❌ Base de datos no encontrada"
        fi
        echo ""
        echo "🔧 Comandos disponibles:"
        echo "   ./dev-mode.sh build     - Compilar assets"
        echo "   ./dev-mode.sh serve     - Iniciar servidor"
        echo "   ./dev-mode.sh clear     - Limpiar cache"
        echo "   ./dev-mode.sh dev       - Vite dev mode"
        echo "   ./dev-mode.sh status    - Mostrar este estado"
        ;;
esac