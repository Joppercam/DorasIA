#!/bin/bash

# 🚀 DORASIA - Script de Deploy para Hosting
# Versión: 1.0

echo "🤖 DORASIA - Deploy Script Iniciado"
echo "=================================="

# Variables de configuración
PROJECT_DIR="/home/n91a0e5/dorasia.cl"
BACKUP_DIR="/home/n91a0e5/backups/dorasia_$(date +%Y%m%d_%H%M%S)"

# Función para mostrar mensajes
show_message() {
    echo "✅ $1"
}

error_message() {
    echo "❌ ERROR: $1"
    exit 1
}

# 1. Verificar dependencias
show_message "Verificando dependencias..."
command -v php >/dev/null 2>&1 || error_message "PHP no está instalado"
command -v composer >/dev/null 2>&1 || error_message "Composer no está instalado"

# 2. Crear backup si existe instalación previa
if [ -d "$PROJECT_DIR" ]; then
    show_message "Creando backup en $BACKUP_DIR"
    mkdir -p "$BACKUP_DIR"
    cp -r "$PROJECT_DIR" "$BACKUP_DIR" 2>/dev/null || true
fi

# 3. Crear directorio del proyecto
show_message "Preparando directorio del proyecto..."
mkdir -p "$PROJECT_DIR"

# 4. Instalar dependencias
show_message "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Configurar permisos
show_message "Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
# En hosting compartido, el usuario ya tiene los permisos correctos

# 6. Configurar base de datos
show_message "Configurando base de datos..."
if [ ! -f ".env" ]; then
    cp .env.production .env
    show_message "⚠️  IMPORTANTE: Configura tu archivo .env con los datos de tu hosting"
fi

# 7. Generar clave de aplicación
show_message "Generando clave de aplicación..."
php artisan key:generate --force

# 8. Ejecutar migraciones
show_message "Ejecutando migraciones..."
php artisan migrate --force

# 9. Optimizar aplicación
show_message "Optimizando aplicación para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Crear cron job para importación continua (opcional)
show_message "Para importación continua, agrega este cron job:"
echo "0 2 * * * cd /home/n91a0e5/dorasia.cl && php artisan import:korean-dramas --pages=10"

# 11. Crear .htaccess para Apache
show_message "Creando archivo .htaccess..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

echo ""
echo "🎉 ¡Deploy completado exitosamente!"
echo "================================="
echo ""
echo "📋 PASOS FINALES:"
echo "1. Configura tu archivo .env con los datos de tu hosting"
echo "2. Asegúrate de que el DocumentRoot apunte a la carpeta 'public'"
echo "3. Importa el contenido inicial: php artisan import:korean-dramas --pages=50"
echo "4. (Opcional) Configura el cron job para importación automática"
echo ""
echo "🌐 Tu sitio estará disponible en: https://tu-dominio.com"
echo ""
echo "🤖 DORAS[IA] - Powered by AI ✨"