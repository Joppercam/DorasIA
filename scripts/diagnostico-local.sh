#!/bin/bash
# 🔍 Diagnóstico Completo - Dorasia Local

echo "🔍 DIAGNÓSTICO COMPLETO DE DORASIA LOCAL"
echo "======================================="

# 1. Configuración del sistema
echo ""
echo "📱 CONFIGURACIÓN DEL SISTEMA:"
echo "OS: $(uname -s)"
echo "PHP Version: $(php -r 'echo PHP_VERSION;')"
echo "Laravel Version: $(php artisan --version)"

# 2. Archivo hosts
echo ""
echo "🌐 CONFIGURACIÓN HOSTS:"
if grep -q "dorasia.local" /etc/hosts 2>/dev/null; then
    echo "✅ Hosts configurado correctamente:"
    grep "dorasia.local" /etc/hosts
else
    echo "❌ Hosts NO configurado"
    echo "Ejecutar: echo '127.0.0.1 dorasia.local admin.dorasia.local marketplace.dorasia.local' | sudo tee -a /etc/hosts"
fi

# 3. Configuración Laravel
echo ""
echo "⚙️ CONFIGURACIÓN LARAVEL:"
echo "APP_URL: $(php -r 'echo env("APP_URL");')"
echo "APP_ENV: $(php -r 'echo env("APP_ENV");')"
echo "APP_DEBUG: $(php -r 'echo env("APP_DEBUG") ? "true" : "false";')"

# 4. Base de datos
echo ""
echo "🗄️ BASE DE DATOS:"
if [ -f "database/database.sqlite" ]; then
    echo "✅ SQLite file exists"
    echo "Size: $(du -h database/database.sqlite | cut -f1)"
else
    echo "❌ SQLite file missing"
fi

SERIES_COUNT=$(php artisan tinker --execute="echo App\Models\Series::count();" 2>/dev/null || echo "Error")
USERS_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null || echo "Error")
PRODUCTS_COUNT=$(php artisan tinker --execute="echo App\Models\MarketplaceProduct::count();" 2>/dev/null || echo "Error")

echo "Series: $SERIES_COUNT"
echo "Users: $USERS_COUNT"  
echo "Products: $PRODUCTS_COUNT"

# 5. Assets
echo ""
echo "🎨 ASSETS:"
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Assets compilados (build exists)"
    echo "CSS: $(find public/build/assets -name "*.css" | wc -l) files"
    echo "JS: $(find public/build/assets -name "*.js" | wc -l) files"
else
    echo "❌ Assets no compilados"
    echo "Ejecutar: npm run build"
fi

# 6. Cache
echo ""
echo "💾 CACHE:"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "⚠️ Config cache activo (desarrollo debería estar limpio)"
else
    echo "✅ Config cache limpio"
fi

if [ -f "bootstrap/cache/routes.php" ]; then
    echo "⚠️ Route cache activo (desarrollo debería estar limpio)"
else
    echo "✅ Route cache limpio"
fi

# 7. Permisos
echo ""
echo "🔒 PERMISOS:"
if [ -w "storage" ]; then
    echo "✅ Storage writable"
else
    echo "❌ Storage no writable"
fi

if [ -w "bootstrap/cache" ]; then
    echo "✅ Bootstrap cache writable"  
else
    echo "❌ Bootstrap cache no writable"
fi

# 8. Servidor
echo ""
echo "🌐 CONECTIVIDAD:"
if curl -s -w "%{http_code}" -o /dev/null "http://localhost:8000" | grep -q "200"; then
    echo "✅ Servidor Laravel respondiendo en localhost:8000"
else
    echo "❌ Servidor Laravel NO responde en localhost:8000"
fi

if curl -s -w "%{http_code}" -o /dev/null "http://dorasia.local:8000" | grep -q "200"; then
    echo "✅ Servidor respondiendo en dorasia.local:8000"
else
    echo "❌ Servidor NO responde en dorasia.local:8000"
fi

# 9. URLs de prueba
echo ""
echo "🔗 URLS DE PRUEBA:"
echo "Principal: http://dorasia.local:8000"
echo "Admin: http://admin.dorasia.local:8000"
echo "Marketplace: http://marketplace.dorasia.local:8000"
echo "API: http://dorasia.local:8000/api/v1/"

# 10. Credenciales
echo ""
echo "🔑 CREDENCIALES:"
echo "Admin: admin@dorasia.com / admin123"

# 11. Comandos útiles
echo ""
echo "🛠️ COMANDOS ÚTILES:"
echo "Iniciar servidor: php artisan serve --host=0.0.0.0 --port=8000"
echo "Limpiar cache: php artisan optimize:clear"
echo "Ver logs: tail -f storage/logs/laravel.log"
echo "Tinker: php artisan tinker"

echo ""
echo "✅ Diagnóstico completado"