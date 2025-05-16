#!/bin/bash

# Script simplificado de deployment
# Para usar cuando tienes acceso SSH pero quieres algo más simple

echo "🚀 Deploy simplificado de Dorasia"

# Configuración (cambiar estos valores)
SERVER="usuario@servidor.com"
PATH_REMOTO="/home/usuario/public_html"

# 1. Compilar assets
echo "📦 Compilando assets..."
npm run build

# 2. Subir archivos con rsync
echo "📤 Subiendo archivos..."
rsync -avz \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude '.env' \
    --exclude 'storage/logs/*' \
    --exclude 'storage/framework/cache/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --delete \
    ./ $SERVER:$PATH_REMOTO/

# 3. Ejecutar comandos en el servidor
echo "⚙️ Ejecutando comandos en el servidor..."
ssh $SERVER << 'EOF'
    cd $PATH_REMOTO
    composer install --no-dev --optimize-autoloader
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link
    echo "✅ Comandos ejecutados"
EOF

echo "✅ Deploy completado!"