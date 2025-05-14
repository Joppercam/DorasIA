#!/bin/bash

echo "🚀 Iniciando Dorasia - Plataforma de Streaming de Contenido Coreano"
echo "===================================================================="
echo ""

# Verificar si estamos en la carpeta correcta
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de ejecutar este script desde la raíz del proyecto."
    exit 1
fi

# Verificar dependencias
echo "🔍 Verificando dependencias..."
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias de Node.js..."
    npm install
    npm run build
fi

# Comprobar si existe el archivo .env
if [ ! -f ".env" ]; then
    echo "🔧 Creando archivo .env..."
    cp .env.example .env
    php artisan key:generate
fi

# Comprobar si existe la base de datos
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️ Creando base de datos SQLite..."
    touch database/database.sqlite
    php artisan migrate
fi

# Iniciar el servidor
echo ""
echo "🌐 Iniciando servidor en http://localhost:8000"
echo "Presiona Ctrl+C para detener el servidor"
echo ""
php artisan serve