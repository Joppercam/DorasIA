# 🚀 Comandos para el Hosting Compartido

## 📋 Pasos exactos para dorasia.cl

### 1. Conectar al terminal del hosting
```bash
# Usar el terminal web del hosting o SSH si está disponible
```

### 2. Clonar el repositorio
```bash
cd /home/n91a0e5/
git clone [URL-de-tu-repositorio] dorasia.cl
cd dorasia.cl
```

### 3. Configurar el entorno
```bash
# Copiar configuración de producción
cp .env.production .env

# Editar con los datos de tu hosting
nano .env
# O usar: vim .env
```

### 4. Variables que debes configurar en .env:
```bash
APP_KEY=                    # Se generará automáticamente
DB_HOST=localhost          # Normalmente localhost en hosting compartido
DB_DATABASE=n91a0e5_dorasia   # Tu base de datos (ajustar según hosting)
DB_USERNAME=n91a0e5_user      # Tu usuario MySQL (ajustar según hosting)  
DB_PASSWORD=tu_password       # Tu password MySQL
TMDB_API_KEY=tu_clave_tmdb   # Tu API key de TMDB
```

### 5. Ejecutar el deploy
```bash
# Dar permisos de ejecución
chmod +x deploy.sh

# Ejecutar script de instalación
./deploy.sh
```

### 6. Importar contenido inicial
```bash
# Importar las primeras 50 páginas (recomendado para el primer deploy)
php artisan import:korean-dramas --pages=50

# O importar por categorías específicas (más rápido)
php artisan import:romance-dramas
php artisan import:recent-dramas
php artisan import:top-rated-dramas
```

### 7. Verificar que todo funciona
```bash
# Verificar contenido importado
php artisan tinker --execute="echo 'Series: ' . App\Models\Series::count();"

# Verificar permisos
ls -la storage/
ls -la bootstrap/cache/
```

## 🔧 Comandos útiles para hosting compartido

### Gestión de la aplicación:
```bash
# Limpiar caché cuando hagas cambios
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Re-optimizar después de cambios
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Importación de contenido:
```bash
# Importar más contenido (cuando tengas tiempo)
php artisan import:korean-dramas --pages=20

# Importar categorías específicas
php artisan import:comedy-dramas
php artisan import:mystery-dramas
php artisan import:historical-dramas
```

### Monitoreo:
```bash
# Ver logs de errores
tail -f storage/logs/laravel.log

# Verificar espacio en disco
du -sh .
df -h
```

## ⚠️ Consideraciones para hosting compartido

### Limitaciones típicas:
- **Tiempo de ejecución**: Máximo 30-60 segundos por request
- **Memoria**: Limitada (256-512MB)
- **CPU**: Compartida con otros usuarios

### Soluciones:
- **Importar en lotes pequeños**: `--pages=10` en lugar de `--pages=100`
- **Usar cron jobs** para importación automática nocturna
- **Optimizar caché** regularmente

### Configuración de cron job (si está disponible):
```bash
# Editar crontab
crontab -e

# Agregar esta línea para importar contenido a las 2 AM
0 2 * * * cd /home/n91a0e5/dorasia.cl && php artisan import:korean-dramas --pages=10 >/dev/null 2>&1
```

## 🎯 Document Root ya configurado

El hosting ya apunta correctamente:
- **Ruta del proyecto**: `/home/n91a0e5/dorasia.cl/`
- **Document Root**: `/home/n91a0e5/dorasia.cl/public`

No necesitas cambiar la configuración del servidor web.

## 🆘 Si algo falla

### Error de permisos:
```bash
chmod -R 755 storage bootstrap/cache
```

### Error de base de datos:
```bash
# Verificar conexión
php artisan tinker --execute="DB::connection()->getPdo();"
```

### Error de memoria:
```bash
# Importar menos contenido a la vez
php artisan import:korean-dramas --pages=5
```

### Limpiar todo y empezar de nuevo:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

---

🤖 **DORASIA** estará disponible en: **https://dorasia.cl**