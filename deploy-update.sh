#!/bin/bash

# 🚀 DORASIA - Script de Actualización para Producción
# Versión: 2.0
# Fecha: 2025-01-08

echo "🤖 DORASIA - Script de Actualización para Producción"
echo "===================================================="
echo ""

# Variables de configuración
PROJECT_DIR="/home/n91a0e5/dorasia.cl"
BACKUP_DIR="/home/n91a0e5/backups/dorasia_$(date +%Y%m%d_%H%M%S)"
GITHUB_REPO="https://github.com/Joppercam/DorasIA.git"

# Función para mostrar mensajes
show_message() {
    echo "✅ $1"
}

error_message() {
    echo "❌ ERROR: $1"
    exit 1
}

warning_message() {
    echo "⚠️  ADVERTENCIA: $1"
}

# 1. Verificar que estamos en el servidor
echo "📍 Directorio actual: $(pwd)"
echo "🏠 Directorio del proyecto: $PROJECT_DIR"
echo ""

# 2. Crear backup COMPLETO antes de actualizar
show_message "Creando backup completo del proyecto..."
if [ -d "$PROJECT_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
    cp -r "$PROJECT_DIR" "$BACKUP_DIR" || error_message "No se pudo crear el backup"
    
    # Backup específico de la base de datos
    if [ -f "$PROJECT_DIR/database/database.sqlite" ]; then
        cp "$PROJECT_DIR/database/database.sqlite" "$BACKUP_DIR/database_backup.sqlite"
        show_message "Base de datos respaldada en: $BACKUP_DIR/database_backup.sqlite"
    fi
else
    error_message "El directorio del proyecto no existe"
fi

# 3. Ir al directorio del proyecto
cd "$PROJECT_DIR" || error_message "No se pudo acceder al directorio del proyecto"

# 4. Poner la aplicación en modo mantenimiento
show_message "Activando modo mantenimiento..."
php artisan down --message="🔧 Actualizando el sistema... Volvemos en unos minutos" --retry=60

# 5. Obtener los últimos cambios de GitHub
show_message "Descargando últimos cambios desde GitHub..."
git fetch origin main || error_message "No se pudo conectar con GitHub"
git reset --hard origin/main || error_message "No se pudieron aplicar los cambios"

# 6. Instalar/actualizar dependencias
show_message "Actualizando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction || error_message "Error al instalar dependencias"

# 7. Ejecutar las nuevas migraciones
show_message "Ejecutando nuevas migraciones..."
php artisan migrate --force || {
    warning_message "Error en las migraciones. Intentando solucionar..."
    
    # Si falla, intentar recrear la base de datos
    read -p "¿Deseas recrear la base de datos? (PERDERÁS TODOS LOS DATOS) [s/N]: " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        show_message "Recreando base de datos..."
        rm -f database/database.sqlite
        touch database/database.sqlite
        chmod 664 database/database.sqlite
        php artisan migrate:fresh --force
        
        # Importar datos iniciales
        show_message "Importando datos iniciales..."
        php artisan import:korean-dramas --pages=10
    else
        error_message "Migraciones canceladas. Restaura desde el backup si es necesario."
    fi
}

# 8. Limpiar y regenerar cachés
show_message "Limpiando cachés..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

show_message "Regenerando cachés para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Configurar permisos
show_message "Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 664 database/database.sqlite

# 10. Verificar que las imágenes estén accesibles
show_message "Verificando enlaces simbólicos..."
php artisan storage:link 2>/dev/null || warning_message "El enlace simbólico ya existe"

# 11. Desactivar modo mantenimiento
show_message "Desactivando modo mantenimiento..."
php artisan up

# 12. Ejecutar comandos de optimización específicos
show_message "Ejecutando optimizaciones adicionales..."

# Corregir traducciones al español
php artisan dorasia:fix-translations || warning_message "No se pudieron corregir algunas traducciones"

# Importar contenido en español si no existe
SERIES_COUNT=$(php artisan tinker --execute="echo \App\Models\Series::count();" 2>/dev/null)
if [ "$SERIES_COUNT" -lt "100" ]; then
    warning_message "Pocas series detectadas. Considera ejecutar:"
    echo "php artisan dorasia:import-spanish --all --pages=10"
fi

echo ""
echo "🎉 ¡Actualización completada exitosamente!"
echo "=========================================="
echo ""
echo "📋 RESUMEN DE LA ACTUALIZACIÓN:"
echo "✅ Código actualizado desde GitHub"
echo "✅ Base de datos migrada"
echo "✅ Cachés regeneradas"
echo "✅ Permisos configurados"
echo "✅ Aplicación en línea"
echo ""
echo "📁 Backup guardado en: $BACKUP_DIR"
echo ""
echo "🔍 VERIFICACIONES RECOMENDADAS:"
echo "1. Revisa que el menú móvil sea visible"
echo "2. Prueba la búsqueda en español"
echo "3. Verifica que las traducciones estén correctas"
echo "4. Confirma que los usuarios puedan iniciar sesión"
echo ""
echo "⚡ COMANDOS ÚTILES:"
echo "- Ver logs: tail -f storage/logs/laravel.log"
echo "- Importar más contenido: php artisan dorasia:import-spanish --all"
echo "- Restaurar backup: cp -r $BACKUP_DIR/* $PROJECT_DIR/"
echo ""
echo "🤖 DORAS[IA] - Actualización completada ✨"