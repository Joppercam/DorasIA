# Checklist de Despliegue para Dorasia

## 1. Archivos de Configuración

### .env (Crear en el hosting)
```env
APP_NAME=Dorasia
APP_ENV=production
APP_KEY=[Generar con: php artisan key:generate]
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_database
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# TMDB API
TMDB_API_KEY=tu_api_key_aqui
```

## 2. Comandos a Ejecutar en el Hosting

```bash
# 1. Instalar dependencias
composer install --optimize-autoloader --no-dev

# 2. Generar key de aplicación
php artisan key:generate

# 3. Ejecutar migraciones
php artisan migrate

# 4. Crear enlaces simbólicos para storage
php artisan storage:link

# 5. Limpiar y cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Dar permisos a carpetas
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 3. Archivos/Carpetas a Excluir del Upload

- `/node_modules`
- `/vendor` (se genera con composer install)
- `/.env` (crear manualmente en hosting)
- `/storage/logs/*`
- `/storage/framework/cache/*`
- `/storage/framework/sessions/*`
- `/storage/framework/views/*`
- `/.git`
- `/documentacion`

## 4. Configuraciones Importantes

### Reactivar el Middleware de Autenticación
En `bootstrap/app.php`, descomenta las líneas del StaticAuth si quieres login automático:
```php
$middleware->web(append: [
    \App\Http\Middleware\StaticAuth::class,
]);
```

### Base de Datos
- Asegúrate de tener todas las tablas necesarias ejecutando las migraciones
- La aplicación necesita datos de TMDB API para funcionar correctamente

### Cron Jobs (Opcional)
Si quieres ejecutar los comandos de importación automáticamente:
```
0 2 * * * cd /path/to/your/app && php artisan update:current-series >> /dev/null 2>&1
0 3 * * * cd /path/to/your/app && php artisan scan:upcoming-kdramas >> /dev/null 2>&1
```

## 5. Requisitos del Servidor

- PHP >= 8.1
- MySQL >= 5.7
- Extensiones PHP requeridas:
  - BCMath PHP Extension
  - Ctype PHP Extension
  - cURL PHP Extension
  - DOM PHP Extension
  - Fileinfo PHP Extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PCRE PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension

## 6. Notas Importantes

1. **TMDB API Key**: Necesitas una API key válida de TMDB para que funcione la importación de datos
2. **Importación Inicial**: Después del deploy, ejecuta:
   ```bash
   php artisan import:korean-series
   php artisan import:korean-movies
   ```
3. **SSL**: Asegúrate de tener HTTPS configurado
4. **Sesiones**: Las sesiones están configuradas para archivos, considera usar Redis o base de datos en producción

## 7. Testing Post-Deploy

1. Verificar que la página principal carga
2. Probar el login/logout
3. Verificar que las imágenes de series/películas cargan (TMDB)
4. Probar las funciones de calificación
5. Verificar que las búsquedas funcionan