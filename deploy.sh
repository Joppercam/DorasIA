#!/bin/bash

# Script de despliegue para Dorasia
# Asegúrate de ejecutar este script en el directorio raíz del proyecto

echo "🚀 Iniciando despliegue de Dorasia..."

# Instalar dependencias de Composer
echo "📦 Instalando dependencias de Composer..."
composer install --optimize-autoloader --no-dev

# Generar clave de aplicación si no existe
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

# Copiar archivo de entorno
echo "📋 Configurando archivo de entorno..."
cp .env.production .env

# Limpiar cachés anteriores
echo "🧹 Limpiando cachés anteriores..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Ejecutar seeders importantes
echo "🌱 Ejecutando seeders..."
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=GenreSeeder --force

# Instalar dependencias de NPM y compilar assets
echo "📦 Instalando dependencias de NPM..."
npm install

echo "🏗️ Compilando assets para producción..."
npm run build

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link

# Establecer permisos correctos
echo "🔒 Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 public/.htaccess

# Cachear configuraciones para mejor rendimiento
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar y optimizar
echo "🎯 Optimizando autoloader..."
composer dump-autoload --optimize

echo "✅ ¡Despliegue completado!"
echo ""
echo "📝 Recuerda:"
echo "1. Actualizar el archivo .env con tus credenciales reales"
echo "2. Configurar tu servidor web para apuntar a la carpeta /public"
echo "3. Asegurarte de que las carpetas storage y bootstrap/cache tengan permisos de escritura"
echo "4. Configurar las claves API de TMDB"
echo ""
echo "🌟 ¡Dorasia está listo para usar!"