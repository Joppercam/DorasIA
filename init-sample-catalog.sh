#!/bin/bash

# Script para inicializar un catálogo de muestra para Dorasia

echo "Inicializando catálogo de muestra para Dorasia..."
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: Este script debe ejecutarse desde el directorio raíz del proyecto Dorasia."
    exit 1
fi

# Configuración inicial
echo "1. Ejecutando migraciones de base de datos..."
php artisan migrate:fresh

echo "2. Ejecutando seeders básicos..."
php artisan db:seed

echo "3. Generando imágenes de muestra..."
cd public
php import-placeholder-images.php
cd ..

echo "4. Creando títulos de muestra..."
php artisan seed:sample-titles --type=all --count=3

echo ""
echo "✅ Catálogo de muestra creado correctamente."
echo ""
echo "Puedes acceder a tu catálogo en: http://localhost:8000/catalog"
echo ""
echo "Credenciales de prueba:"
echo "   Email: test@example.com"
echo "   Password: password"
echo ""
echo "Para ejecutar el servidor web:"
echo "   php artisan serve"
echo ""