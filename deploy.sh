#!/bin/bash

# Script de deployment para Dorasia
# Ejecutar en el servidor después de hacer git pull

echo "🚀 Iniciando deployment de Dorasia..."

# 1. Instalar/actualizar dependencias de Composer
echo "📦 Instalando dependencias de Composer..."
composer install --optimize-autoloader --no-dev

# 2. Crear archivo .env si no existe
if [ ! -f .env ]; then
    echo "⚙️ Creando archivo .env..."
    cp .env.example .env
    php artisan key:generate
    echo "⚠️  Por favor, edita el archivo .env con tus configuraciones de base de datos y TMDB API"
    exit 1
fi

# 3. Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# 4. Limpiar cachés antiguos
echo "🧹 Limpiando cachés..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link

# 7. Establecer permisos correctos
echo "🔒 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 8. Optimizar autoloader
echo "🔧 Optimizando autoloader..."
composer dump-autoload --optimize

echo "✅ Deployment completado!"
echo ""
echo "📋 Siguientes pasos:"
echo "1. Asegúrate de que el archivo .env esté configurado correctamente"
echo "2. Verifica que la base de datos esté conectada"
echo "3. Si es la primera vez, ejecuta: php artisan import:korean-series"
echo "4. Configura el cron job para actualizaciones automáticas (opcional)"