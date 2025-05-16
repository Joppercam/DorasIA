#!/bin/bash

# Dorasia Deployment Script
# Este script automatiza el despliegue de cambios al hosting

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Iniciando deploy de Dorasia...${NC}"

# Variables de configuración
SERVER_USER="tu_usuario"  # Cambiar por tu usuario SSH
SERVER_HOST="tu_servidor.com"  # Cambiar por tu dominio o IP
SERVER_PATH="/ruta/al/proyecto"  # Cambiar por la ruta en el servidor
LOCAL_PATH="$(pwd)"

# Función para mostrar progreso
function show_progress() {
    echo -e "${YELLOW}➜ $1${NC}"
}

# Función para mostrar error
function show_error() {
    echo -e "${RED}✗ Error: $1${NC}"
    exit 1
}

# Función para mostrar éxito
function show_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

# 1. Verificar cambios en git
show_progress "Verificando cambios en git..."
if ! git diff-index --quiet HEAD --; then
    show_error "Hay cambios sin commitear. Por favor, commitea o stashea tus cambios primero."
fi

# 2. Compilar assets
show_progress "Compilando assets..."
npm run build || show_error "Falló la compilación de assets"
show_success "Assets compilados correctamente"

# 3. Optimizar composer
show_progress "Optimizando dependencias de Composer..."
composer install --optimize-autoloader --no-dev || show_error "Falló la optimización de Composer"
show_success "Dependencias optimizadas"

# 4. Limpiar caché
show_progress "Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
show_success "Caché limpiado"

# 5. Sincronizar archivos con el servidor
show_progress "Subiendo archivos al servidor..."
rsync -avz --exclude-from='.deployignore' \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude '.env' \
    --exclude 'storage/logs/*' \
    --exclude 'storage/framework/cache/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --delete \
    "${LOCAL_PATH}/" "${SERVER_USER}@${SERVER_HOST}:${SERVER_PATH}/" \
    || show_error "Falló la sincronización de archivos"

show_success "Archivos sincronizados"

# 6. Ejecutar comandos en el servidor
show_progress "Ejecutando comandos post-deploy en el servidor..."
ssh "${SERVER_USER}@${SERVER_HOST}" bash << EOF || show_error "Falló la ejecución remota"
    cd "${SERVER_PATH}"
    
    # Instalar dependencias de Composer en producción
    composer install --no-interaction --optimize-autoloader --no-dev
    
    # Ejecutar migraciones
    php artisan migrate --force
    
    # Optimizar Laravel
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Reiniciar queue workers si es necesario
    # php artisan queue:restart
    
    # Ajustar permisos
    chmod -R 755 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
    
    echo "Comandos post-deploy ejecutados correctamente"
EOF

show_success "Deploy completado exitosamente!"

# 7. Verificar el sitio
show_progress "Verificando que el sitio esté funcionando..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://${SERVER_HOST}")
if [ "$HTTP_STATUS" = "200" ]; then
    show_success "El sitio está funcionando correctamente (HTTP ${HTTP_STATUS})"
else
    show_error "El sitio devolvió un código HTTP ${HTTP_STATUS}"
fi

echo -e "${GREEN}🎉 Deploy completado con éxito!${NC}"