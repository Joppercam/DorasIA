#!/bin/bash

# DorasIA Production Sync Script
# ==============================
# Este script se ejecuta en el servidor para sincronizar con GitHub

# Configuración
BRANCH="production"
DEPLOYMENT_PATH="/home/tu_usuario/public_html"
LOG_FILE="/home/tu_usuario/deployment.log"

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Función de logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a $LOG_FILE
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR:${NC} $1" | tee -a $LOG_FILE
}

# Verificar que estamos en el directorio correcto
cd $DEPLOYMENT_PATH || exit 1

# Comprobar si hay cambios en el remoto
log "🔍 Verificando cambios en GitHub..."
git fetch origin $BRANCH

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/$BRANCH)

if [ $LOCAL = $REMOTE ]; then
    log "✅ No hay cambios nuevos. Todo actualizado."
    exit 0
fi

# Hay cambios, proceder con la actualización
log "🚀 Actualizando desde la rama $BRANCH..."

# Hacer backup del .env actual
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Pull de cambios
if git pull origin $BRANCH; then
    log "✅ Código actualizado correctamente"
else
    error "Error al actualizar el código"
    exit 1
fi

# Instalar/actualizar dependencias de Composer
log "📦 Actualizando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Instalar/actualizar dependencias de NPM y compilar assets
log "📦 Compilando assets..."
npm install --production
npm run build

# Ejecutar migraciones
log "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Limpiar y optimizar cachés
log "🧹 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Limpiar cachés antiguos
php artisan cache:clear

# Establecer permisos correctos
log "🔒 Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Reiniciar servicios si es necesario (depende del hosting)
# Si tienes acceso a systemctl o supervisord, puedes reiniciar servicios aquí

log "✅ ¡Actualización completada exitosamente!"
log "📊 Nueva versión: $(git rev-parse --short HEAD)"

# Opcional: Enviar notificación (email, Slack, etc.)
# echo "Deployment completado en $(hostname)" | mail -s "Deployment Success" tu@email.com