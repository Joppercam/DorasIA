#!/bin/bash
# 🏠 Setup Local para Dorasia con Subdominios

set -e

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🏠 Configurando Dorasia para desarrollo local...${NC}"

# 1. Verificar directorio
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Ejecutar desde el directorio raíz de Laravel${NC}"
    exit 1
fi

# 2. Detectar OS
OS="unknown"
if [[ "$OSTYPE" == "darwin"* ]]; then
    OS="macos"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="linux"
elif [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" ]]; then
    OS="windows"
fi

echo -e "${YELLOW}🔍 Sistema detectado: $OS${NC}"

# 3. Configurar hosts
echo -e "${GREEN}🔧 Configurando archivo hosts...${NC}"

HOSTS_FILE="/etc/hosts"
if [[ "$OS" == "windows" ]]; then
    HOSTS_FILE="/c/Windows/System32/drivers/etc/hosts"
fi

if ! grep -q "dorasia.local" $HOSTS_FILE 2>/dev/null; then
    if [[ "$OS" == "windows" ]]; then
        echo -e "${YELLOW}⚠️  En Windows, ejecuta como Administrador:${NC}"
        echo "echo 127.0.0.1   dorasia.local >> C:\\Windows\\System32\\drivers\\etc\\hosts"
        echo "echo 127.0.0.1   admin.dorasia.local >> C:\\Windows\\System32\\drivers\\etc\\hosts"
        echo "echo 127.0.0.1   marketplace.dorasia.local >> C:\\Windows\\System32\\drivers\\etc\\hosts"
    else
        echo "127.0.0.1   dorasia.local" | sudo tee -a $HOSTS_FILE
        echo "127.0.0.1   admin.dorasia.local" | sudo tee -a $HOSTS_FILE
        echo "127.0.0.1   marketplace.dorasia.local" | sudo tee -a $HOSTS_FILE
        echo -e "${GREEN}✅ Hosts configurados${NC}"
    fi
else
    echo -e "${GREEN}✅ Hosts ya configurados${NC}"
fi

# 4. Configurar .env
echo -e "${GREEN}📝 Configurando .env...${NC}"
if [ -f ".env" ]; then
    # Backup del .env original
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    
    # Actualizar APP_URL
    if [[ "$OS" == "macos" ]]; then
        sed -i '' 's|APP_URL=.*|APP_URL=http://dorasia.local:8000|' .env
    else
        sed -i 's|APP_URL=.*|APP_URL=http://dorasia.local:8000|' .env
    fi
    echo -e "${GREEN}✅ APP_URL actualizado${NC}"
else
    echo -e "${RED}❌ Archivo .env no encontrado${NC}"
    exit 1
fi

# 5. Verificar dependencias
echo -e "${GREEN}📦 Verificando dependencias...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP no está instalado${NC}"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}⚠️  Composer no encontrado, pero no es crítico${NC}"
fi

# 6. Instalar dependencias si es necesario
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}📥 Instalando dependencias de Composer...${NC}"
    composer install
fi

# 7. Verificar .env key
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}🔐 Generando clave de aplicación...${NC}"
    php artisan key:generate
fi

# 8. Configurar base de datos si no existe
if [ ! -f "database/database.sqlite" ]; then
    echo -e "${YELLOW}🗄️  Creando base de datos...${NC}"
    touch database/database.sqlite
    php artisan migrate --force
    php artisan db:seed --class=AdminUserSeeder
    php artisan db:seed --class=MarketplaceSeeder
fi

# 9. Limpiar cache
echo -e "${GREEN}🧹 Limpiando cache...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 10. Configurar permisos
echo -e "${GREEN}🔒 Configurando permisos...${NC}"
chmod -R 755 storage/ 2>/dev/null || true
chmod -R 755 bootstrap/cache/ 2>/dev/null || true

# 11. Verificar puerto disponible
PORT=8000
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo -e "${YELLOW}⚠️  Puerto $PORT ocupado, intentando puerto 8001...${NC}"
    PORT=8001
fi

# 12. Detectar Valet (macOS)
if [[ "$OS" == "macos" ]] && command -v valet &> /dev/null; then
    echo -e "${GREEN}🍻 Laravel Valet detectado${NC}"
    echo -e "${YELLOW}¿Quieres usar Valet en lugar de artisan serve? (y/n)${NC}"
    read -r use_valet
    if [[ $use_valet == "y" || $use_valet == "Y" ]]; then
        valet link dorasia
        echo -e "${GREEN}✅ Valet configurado${NC}"
        echo ""
        echo -e "${GREEN}🌐 URLs disponibles con Valet:${NC}"
        echo "   Principal: http://dorasia.test"
        echo "   Admin: http://admin.dorasia.test"
        echo "   Marketplace: http://marketplace.dorasia.test"
        echo ""
        echo -e "${GREEN}👤 Usuario admin: admin@dorasia.com / admin123${NC}"
        exit 0
    fi
fi

# 13. Mostrar información
echo ""
echo -e "${GREEN}✅ Configuración completada${NC}"
echo ""
echo -e "${GREEN}🌐 URLs disponibles:${NC}"
echo "   Principal: http://dorasia.local:$PORT"
echo "   Admin: http://admin.dorasia.local:$PORT"
echo "   Marketplace: http://marketplace.dorasia.local:$PORT"
echo ""
echo -e "${GREEN}👤 Usuario admin: admin@dorasia.com / admin123${NC}"
echo ""
echo -e "${YELLOW}📝 Notas importantes:${NC}"
echo "   • En Windows, configurar hosts manualmente como Administrador"
echo "   • Para acceso móvil, usar IP local en lugar de .local"
echo "   • Cache limpiado, listo para desarrollo"
echo ""

# 14. Preguntar si iniciar servidor
echo -e "${YELLOW}¿Iniciar servidor de desarrollo ahora? (y/n)${NC}"
read -r start_server

if [[ $start_server == "y" || $start_server == "Y" ]]; then
    echo -e "${GREEN}🚀 Iniciando servidor en puerto $PORT...${NC}"
    echo -e "${YELLOW}Presiona Ctrl+C para detener el servidor${NC}"
    echo ""
    php artisan serve --host=0.0.0.0 --port=$PORT
else
    echo ""
    echo -e "${GREEN}Para iniciar el servidor manualmente:${NC}"
    echo "   php artisan serve --host=0.0.0.0 --port=$PORT"
    echo ""
fi