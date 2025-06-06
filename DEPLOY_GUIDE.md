# 🚀 DORASIA - Guía de Deploy

## 📋 Pre-requisitos del Hosting

### Requisitos Mínimos:
- **PHP 8.2+** con extensiones: PDO, MySQL, cURL, JSON, OpenSSL
- **MySQL 5.7+** o **MariaDB 10.3+**
- **Composer** instalado
- **Acceso SSH** (recomendado)

### Configuración Recomendada:
- **Memoria PHP**: 512MB mínimo
- **Tiempo de ejecución**: 300 segundos
- **Subida de archivos**: 50MB

## 🔧 Pasos de Instalación

### 1. Preparar el Hosting
```bash
# Crear base de datos MySQL
CREATE DATABASE dorasia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dorasia_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON dorasia_db.* TO 'dorasia_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Subir Archivos
- Sube todos los archivos del proyecto al hosting
- Asegúrate de que el **DocumentRoot** apunte a la carpeta `public/`

### 3. Configurar Variables de Entorno
```bash
# Copia y configura el archivo .env
cp .env.production .env

# Edita las siguientes variables:
APP_URL=https://tu-dominio.com
DB_HOST=localhost
DB_DATABASE=dorasia_db
DB_USERNAME=dorasia_user
DB_PASSWORD=tu_password_seguro
TMDB_API_KEY=tu_clave_tmdb
```

### 4. Ejecutar Deploy
```bash
# Dale permisos de ejecución
chmod +x deploy.sh

# Ejecuta el script de deploy
./deploy.sh
```

### 5. Importar Contenido Inicial
```bash
# Importa las primeras 50 páginas de contenido
php artisan import:korean-dramas --pages=50

# O importa por categorías específicas
php artisan import:romance-dramas
php artisan import:recent-dramas
```

## ⚙️ Configuración del Servidor Web

### Apache (.htaccess ya incluido)
El archivo `.htaccess` se crea automáticamente en `public/`

### Nginx
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /home/usuario/public_html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔄 Importación Automática (Opcional)

### Configurar Cron Job
```bash
# Edita el crontab
crontab -e

# Agrega esta línea para importar contenido diariamente a las 2 AM
0 2 * * * cd /home/usuario/public_html && php artisan import:korean-dramas --pages=10
```

## 📊 Estado del Contenido Actual

- ✅ **2,340+ series** de K-dramas importadas
- ✅ **5,860+ personas** (actores, directores)
- ✅ **Traducciones al español** completas
- ✅ **Categorías organizadas**: Romance, Drama, Acción, Comedia, etc.
- ✅ **Imágenes optimizadas** desde TMDB

## 🎨 Características de la Plataforma

### 🏠 Interfaz Principal
- Logo AI-themed con **DORAS[IA]** destacado
- Carruseles infinitos con 25+ series
- Hover effects con información de reparto
- Diseño responsive para móviles

### 📱 Funcionalidades
- Netflix-style home interface
- Navegación por categorías
- Sistema de búsqueda avanzado
- Información detallada de cada serie

### 🔮 Roadmap Futuro
- Sistema de usuarios y perfiles
- Listas personalizadas y favoritos
- Comentarios y reseñas
- Sistema de recomendaciones AI
- Notificaciones de nuevos episodios

## 🛠️ Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Re-optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ver estado de la base de datos
php artisan tinker
>>> App\Models\Series::count()
>>> App\Models\Person::count()

# Importar contenido específico
php artisan import:top-rated-dramas
php artisan import:recent-dramas
```

## 🔍 Troubleshooting

### Error 500
- Verificar permisos de `storage/` y `bootstrap/cache/`
- Revisar logs en `storage/logs/laravel.log`
- Verificar configuración de base de datos

### Imágenes no cargan
- Verificar conexión a TMDB API
- Comprobar clave API en `.env`

### Performance lenta
- Habilitar caché de configuración
- Optimizar base de datos
- Verificar memoria PHP

## 📞 Soporte

Para cualquier problema durante el deploy, revisa:
1. Logs del servidor web
2. `storage/logs/laravel.log`
3. Configuración de PHP y extensiones

---

🤖 **DORASIA** - Tu portal de K-Dramas powered by AI ✨