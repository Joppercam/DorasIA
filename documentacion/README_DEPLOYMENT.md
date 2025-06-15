# Dorasia - Guía de Despliegue con Git

## Preparación Local

### 1. Configurar Git (si no está configurado)
```bash
git init
git add .
git commit -m "Preparar aplicación para producción"
```

### 2. Agregar repositorio remoto
```bash
git remote add origin [URL_DE_TU_REPOSITORIO]
git push -u origin main
```

## Despliegue en el Hosting

### 1. En el servidor, clonar el repositorio
```bash
git clone [URL_DE_TU_REPOSITORIO] dorasia
cd dorasia
```

### 2. Ejecutar script de despliegue
```bash
chmod +x deploy.sh
./deploy.sh
```

### 3. Configurar .env manualmente
```env
APP_NAME=Dorasia
APP_ENV=production
APP_KEY=[Se genera automáticamente]
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_database
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

TMDB_API_KEY=tu_api_key_aqui
```

### 4. Primera importación de datos
```bash
php artisan import:korean-series
php artisan import:korean-movies
```

## Actualizaciones Futuras

```bash
git pull origin main
./deploy.sh
```

## Configuraciones de Producción

### Habilitar Auto-Login (opcional)
En `bootstrap/app.php`, descomenta:
```php
$middleware->web(append: [
    \App\Http\Middleware\StaticAuth::class,
]);
```

### Habilitar CSRF Protection
En `bootstrap/app.php`, cambia:
```php
$middleware->validateCsrfTokens(except: [
    // Agregar rutas específicas si es necesario
]);
```

## Verificación Post-Despliegue

1. ✅ Página principal carga
2. ✅ Login/logout funciona
3. ✅ Imágenes de TMDB cargan
4. ✅ Sistema de calificaciones funciona
5. ✅ Búsqueda funciona
6. ✅ Versión móvil optimizada

## Soporte

Si hay problemas, revisar:
- Logs de Laravel: `storage/logs/`
- Permisos de carpetas
- Configuración de base de datos
- API key de TMDB válida