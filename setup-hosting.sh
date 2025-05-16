#!/bin/bash

# =====================================================
# DorasIA - Script de Configuración para Hosting
# =====================================================

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}=== DorasIA - Configuración de Hosting ===${NC}"
echo ""

# Configuración
read -p "Nombre de usuario MySQL: " DB_USER
read -sp "Contraseña MySQL: " DB_PASS
echo ""
read -p "Nombre de la base de datos: " DB_NAME
read -p "Ruta de instalación (/home/usuario/public_html): " INSTALL_PATH
read -p "URL del dominio (https://ejemplo.com): " APP_URL

# Crear directorio si no existe
mkdir -p $INSTALL_PATH

# Clonar repositorio
echo -e "${YELLOW}Clonando repositorio...${NC}"
cd $INSTALL_PATH
git clone https://github.com/Joppercam/DorasIA.git .
git checkout production

# Instalar dependencias PHP
echo -e "${YELLOW}Instalando dependencias PHP...${NC}"
composer install --no-dev --optimize-autoloader

# Crear archivo .env
echo -e "${YELLOW}Configurando archivo .env...${NC}"
cp .env.production .env

# Actualizar valores en .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|g" .env

# Generar key de aplicación
php artisan key:generate

# Crear base de datos
echo -e "${YELLOW}Creando base de datos...${NC}"
mysql -u$DB_USER -p$DB_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
echo -e "${YELLOW}Ejecutando migraciones...${NC}"
php artisan migrate --force

# Crear link simbólico para storage
php artisan storage:link

# Configurar permisos
echo -e "${YELLOW}Configurando permisos...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# Instalar y compilar assets
echo -e "${YELLOW}Compilando assets...${NC}"
npm install --production
npm run build

# Optimizar aplicación
echo -e "${YELLOW}Optimizando aplicación...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Configurar cron jobs
echo -e "${YELLOW}Configurando cron jobs...${NC}"
(crontab -l 2>/dev/null; echo "* * * * * cd $INSTALL_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -
(crontab -l 2>/dev/null; echo "*/5 * * * * cd $INSTALL_PATH && git pull origin production >> /dev/null 2>&1") | crontab -

# Crear script de actualización
cat > $INSTALL_PATH/update.sh << 'EOF'
#!/bin/bash
cd $(dirname $0)
git pull origin production
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo "Actualización completada: $(date)"
EOF

chmod +x $INSTALL_PATH/update.sh

# Script de backup
cat > $INSTALL_PATH/backup.sh << EOF
#!/bin/bash
BACKUP_DIR=$INSTALL_PATH/backups
mkdir -p \$BACKUP_DIR
DATE=\$(date +%Y%m%d_%H%M%S)
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > \$BACKUP_DIR/backup_\$DATE.sql
tar -czf \$BACKUP_DIR/files_\$DATE.tar.gz --exclude='node_modules' --exclude='.git' --exclude='storage/logs/*' $INSTALL_PATH
find \$BACKUP_DIR -type f -mtime +7 -delete
echo "Backup completado: \$DATE"
EOF

chmod +x $INSTALL_PATH/backup.sh

# Configurar backup automático
(crontab -l 2>/dev/null; echo "0 3 * * * $INSTALL_PATH/backup.sh >> $INSTALL_PATH/backup.log 2>&1") | crontab -

echo -e "${GREEN}=== Instalación Completada ===${NC}"
echo -e "${GREEN}URL: $APP_URL${NC}"
echo -e "${GREEN}Para actualizar: ./update.sh${NC}"
echo -e "${GREEN}Para backup: ./backup.sh${NC}"
echo ""
echo -e "${YELLOW}Próximos pasos:${NC}"
echo "1. Configura tu servidor web para apuntar a: $INSTALL_PATH/public"
echo "2. Habilita HTTPS en tu dominio"
echo "3. Revisa los logs en: $INSTALL_PATH/storage/logs"
echo "4. Crea un usuario admin desde la interfaz web"
echo ""
echo -e "${GREEN}¡DorasIA está listo!${NC}"