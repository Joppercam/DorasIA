# Guía de Despliegue de Dorasia

Esta guía detalla los pasos necesarios para desplegar la plataforma Dorasia en un servidor de producción.

## Requisitos del Servidor

### Sistema Operativo
- Ubuntu 20.04 LTS o superior (recomendado)
- CentOS 8+ o Debian 10+

### Software Requerido
- PHP 8.1 o superior
- MySQL 8.0+ o PostgreSQL 13+
- Redis 6.0+
- Nginx o Apache
- Node.js 16+ y npm
- Composer 2.0+
- Git
- Supervisor (para gestión de procesos)
- SSL/TLS Certificado

### Extensiones PHP Requeridas
```bash
php8.1-fpm
php8.1-mysql
php8.1-redis
php8.1-xml
php8.1-dom
php8.1-curl
php8.1-mbstring
php8.1-gd
php8.1-imagick
php8.1-zip
php8.1-bcmath
```

## Paso 1: Preparación del Servidor

### 1.1 Actualizar el Sistema
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalar Software Base
```bash
# Instalar PHP y extensiones
sudo apt install php8.1-fpm php8.1-mysql php8.1-redis php8.1-xml php8.1-dom php8.1-curl php8.1-mbstring php8.1-gd php8.1-imagick php8.1-zip php8.1-bcmath -y

# Instalar Nginx
sudo apt install nginx -y

# Instalar MySQL
sudo apt install mysql-server -y

# Instalar Redis
sudo apt install redis-server -y

# Instalar Node.js y npm
curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt install nodejs -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Supervisor
sudo apt install supervisor -y
```

## Paso 2: Configuración de Base de Datos

### 2.1 Configurar MySQL
```bash
# Acceder a MySQL
sudo mysql

# Crear base de datos y usuario
CREATE DATABASE dorasia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dorasia_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON dorasia.* TO 'dorasia_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2.2 Configurar Redis
```bash
# Editar configuración de Redis
sudo nano /etc/redis/redis.conf

# Cambiar las siguientes líneas:
supervised systemd
maxmemory 256mb
maxmemory-policy allkeys-lru

# Reiniciar Redis
sudo systemctl restart redis
sudo systemctl enable redis
```

## Paso 3: Clonar y Configurar el Proyecto

### 3.1 Clonar Repositorio
```bash
# Crear directorio para la aplicación
sudo mkdir -p /var/www/dorasia
sudo chown www-data:www-data /var/www/dorasia
cd /var/www/dorasia

# Clonar el repositorio
sudo -u www-data git clone https://github.com/tu-usuario/dorasia.git .
```

### 3.2 Configurar Laravel
```bash
# Instalar dependencias de PHP
sudo -u www-data composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js
sudo -u www-data npm install

# Compilar assets
sudo -u www-data npm run build

# Copiar archivo de configuración
sudo -u www-data cp .env.example .env

# Generar clave de aplicación
sudo -u www-data php artisan key:generate
```

### 3.3 Configurar Variables de Entorno
```bash
# Editar archivo .env
sudo -u www-data nano .env
```

Actualizar las siguientes variables:
```env
APP_NAME=Dorasia
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dorasia
DB_USERNAME=dorasia_user
DB_PASSWORD=password_seguro

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@dorasia.com
MAIL_FROM_NAME="${APP_NAME}"

PUSHER_APP_ID=tu_pusher_app_id
PUSHER_APP_KEY=tu_pusher_app_key
PUSHER_APP_SECRET=tu_pusher_app_secret
PUSHER_APP_CLUSTER=tu_pusher_cluster

TMDB_API_KEY=tu_tmdb_api_key
TMDB_API_URL=https://api.themoviedb.org/3
```

### 3.4 Configurar Permisos
```bash
# Establecer permisos correctos
sudo chown -R www-data:www-data /var/www/dorasia
sudo chmod -R 755 /var/www/dorasia
sudo chmod -R 775 /var/www/dorasia/storage
sudo chmod -R 775 /var/www/dorasia/bootstrap/cache
```

### 3.5 Ejecutar Migraciones y Seeders
```bash
# Ejecutar migraciones
sudo -u www-data php artisan migrate --force

# Ejecutar seeders
sudo -u www-data php artisan db:seed --force

# Optimizar la aplicación
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## Paso 4: Configurar Nginx

### 4.1 Crear Configuración de Nginx
```bash
sudo nano /etc/nginx/sites-available/dorasia
```

Contenido del archivo:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tu-dominio.com www.tu-dominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/dorasia/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/tu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tu-dominio.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_prefer_server_ciphers on;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    charset utf-8;

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Websocket support for Laravel Echo
    location /socket.io {
        proxy_pass http://localhost:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 4.2 Activar el Sitio
```bash
# Crear enlace simbólico
sudo ln -s /etc/nginx/sites-available/dorasia /etc/nginx/sites-enabled/

# Probar configuración
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

## Paso 5: Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Configurar renovación automática
sudo certbot renew --dry-run
```

## Paso 6: Configurar Supervisor para Queue Workers

### 6.1 Crear Configuración de Supervisor
```bash
sudo nano /etc/supervisor/conf.d/dorasia-worker.conf
```

Contenido del archivo:
```ini
[program:dorasia-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/dorasia/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/dorasia/storage/logs/worker.log
stopwaitsecs=3600
```

### 6.2 Crear Configuración para Laravel Echo Server (si se usa)
```bash
sudo nano /etc/supervisor/conf.d/dorasia-echo.conf
```

Contenido del archivo:
```ini
[program:dorasia-echo]
directory=/var/www/dorasia
process_name=%(program_name)s_%(process_num)02d
command=laravel-echo-server start
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/dorasia/storage/logs/echo.log
stopwaitsecs=3600
```

### 6.3 Actualizar Supervisor
```bash
# Recargar configuración
sudo supervisorctl reread
sudo supervisorctl update

# Iniciar procesos
sudo supervisorctl start dorasia-worker:*
sudo supervisorctl start dorasia-echo:*
```

## Paso 7: Configurar Cron Jobs

```bash
# Editar crontab para www-data
sudo crontab -u www-data -e

# Agregar la siguiente línea
* * * * * cd /var/www/dorasia && php artisan schedule:run >> /dev/null 2>&1
```

## Paso 8: Configurar Backups

### 8.1 Crear Script de Backup
```bash
sudo nano /home/ubuntu/backup-dorasia.sh
```

Contenido del script:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/ubuntu/backups"
DB_USER="dorasia_user"
DB_PASS="password_seguro"
DB_NAME="dorasia"
APP_DIR="/var/www/dorasia"

# Crear directorio de backups si no existe
mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup de archivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage/app/public

# Eliminar backups antiguos (más de 7 días)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completado: $DATE"
```

### 8.2 Hacer Script Ejecutable
```bash
chmod +x /home/ubuntu/backup-dorasia.sh
```

### 8.3 Configurar Cron para Backups
```bash
# Editar crontab
crontab -e

# Agregar línea para backup diario a las 3 AM
0 3 * * * /home/ubuntu/backup-dorasia.sh >> /home/ubuntu/backup.log 2>&1
```

## Paso 9: Monitoreo y Logs

### 9.1 Configurar Logrotate
```bash
sudo nano /etc/logrotate.d/dorasia
```

Contenido del archivo:
```
/var/www/dorasia/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        /usr/bin/supervisorctl restart dorasia-worker:* > /dev/null
    endscript
}
```

### 9.2 Instalar y Configurar New Relic (Opcional)
```bash
# Seguir las instrucciones de New Relic para PHP
# https://docs.newrelic.com/docs/agents/php-agent/installation/
```

## Paso 10: Optimización Final

### 10.1 Optimizar PHP-FPM
```bash
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

Ajustar los siguientes valores según los recursos del servidor:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

### 10.2 Optimizar MySQL
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Agregar o modificar:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_method = O_DIRECT
innodb_flush_log_at_trx_commit = 2
```

### 10.3 Reiniciar Servicios
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart mysql
sudo systemctl restart nginx
```

## Verificación Post-Despliegue

### Checklist de Verificación
- [ ] El sitio carga correctamente en HTTPS
- [ ] Pueden registrarse nuevos usuarios
- [ ] Las imágenes se cargan correctamente
- [ ] Las notificaciones en tiempo real funcionan
- [ ] Los trabajos en cola se procesan
- [ ] Los logs se están generando correctamente
- [ ] El sistema de caché está funcionando
- [ ] Los backups automáticos se ejecutan

### Comandos de Verificación
```bash
# Verificar estado de servicios
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mysql
sudo systemctl status redis

# Verificar workers de Supervisor
sudo supervisorctl status

# Verificar logs de Laravel
tail -f /var/www/dorasia/storage/logs/laravel.log

# Verificar caché
sudo -u www-data php artisan cache:manage stats
```

## Mantenimiento Regular

### Tareas Semanales
- Revisar logs de errores
- Verificar espacio en disco
- Revisar métricas de rendimiento
- Actualizar dependencias de seguridad

### Tareas Mensuales
- Optimizar tablas de base de datos
- Limpiar archivos temporales antiguos
- Revisar y rotar logs
- Actualizar certificados SSL (automático con Let's Encrypt)

### Comandos de Mantenimiento
```bash
# Limpiar caché si es necesario
sudo -u www-data php artisan cache:clear

# Optimizar base de datos
sudo mysql -u root -p -e "OPTIMIZE TABLE dorasia.titles, dorasia.users, dorasia.profiles;"

# Actualizar dependencias
sudo -u www-data composer update --no-dev
sudo -u www-data npm update
```

## Resolución de Problemas

### Error 500
1. Verificar logs: `tail -f /var/www/dorasia/storage/logs/laravel.log`
2. Verificar permisos: `sudo chown -R www-data:www-data /var/www/dorasia`
3. Limpiar caché: `sudo -u www-data php artisan cache:clear`

### Problemas de Rendimiento
1. Verificar Redis: `redis-cli ping`
2. Revisar queries lentas en MySQL
3. Optimizar caché: `sudo -u www-data php artisan cache:manage warm`
4. Revisar uso de CPU/RAM: `top` o `htop`

### Notificaciones no Funcionan
1. Verificar configuración de Pusher en `.env`
2. Revisar logs de Echo Server
3. Verificar conectividad WebSocket

## Seguridad Adicional

### Firewall (UFW)
```bash
# Configurar firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### Fail2ban
```bash
# Instalar fail2ban
sudo apt install fail2ban -y

# Configurar para proteger SSH y Nginx
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl restart fail2ban
```

## Conclusión

Esta guía cubre todos los aspectos necesarios para desplegar Dorasia en producción. Recuerda:

1. Siempre hacer backups antes de actualizaciones mayores
2. Mantener el sistema actualizado
3. Monitorear logs regularmente
4. Seguir las mejores prácticas de seguridad
5. Documentar cualquier cambio en la configuración

Para soporte adicional, consulta la documentación de Laravel y los logs del sistema.

---

Última actualización: Diciembre 2024