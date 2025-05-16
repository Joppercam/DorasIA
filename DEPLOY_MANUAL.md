# Guía de Deployment Manual

Si prefieres hacer el deployment manualmente, sigue estos pasos:

## 1. Preparación Local

```bash
# Compilar assets
npm run build

# Optimizar Composer para producción
composer install --optimize-autoloader --no-dev

# Limpiar caché local
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 2. Archivos a Subir

Sube TODO excepto:
- `/node_modules`
- `/.git`
- `/.env` (configurar manualmente en el servidor)
- `/storage/logs/*`
- `/storage/framework/cache/*`
- `/storage/framework/sessions/*`
- `/storage/framework/views/*`
- `/tests`
- Archivos `.md`
- Scripts de deployment

## 3. Configuración en el Servidor

1. **Crear archivo .env** con las credenciales de producción:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Otros valores según necesites
```

2. **Ejecutar comandos en el servidor**:
```bash
# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Ejecutar migraciones
php artisan migrate

# Generar key si es necesario
php artisan key:generate

# Crear enlace simbólico para storage
php artisan storage:link

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permisos
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 4. Verificación

1. Visita tu sitio web
2. Revisa que las imágenes se carguen correctamente
3. Prueba la funcionalidad principal
4. Revisa los logs si hay errores: `storage/logs/laravel.log`

## Tips

- Siempre haz backup antes de actualizar
- Prueba primero en un entorno de staging si es posible
- Mantén un registro de cambios
- Configura un sistema de monitoreo para errores