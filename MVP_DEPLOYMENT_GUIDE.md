# DorasIA MVP - Guía de Deployment

## 🚀 Preparación para MVP

Esta versión MVP de DorasIA está lista para ser desplegada en tu hosting. Incluye las funcionalidades esenciales optimizadas para producción.

## 📋 Requisitos del Servidor

- PHP >= 8.1
- MySQL >= 5.7 o MariaDB >= 10.3
- Composer
- Node.js & NPM (para compilar assets)
- Extensiones PHP requeridas:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - PDO_MYSQL
  - Tokenizer
  - XML

## 🔧 Configuración

### 1. Configurar Variables de Entorno

Copia `.env.production` a `.env` y actualiza con tus datos:

```bash
cp .env.production .env
```

Actualiza estos valores:
- `APP_URL` - Tu dominio
- `DB_HOST` - Host de tu base de datos
- `DB_DATABASE` - Nombre de tu base de datos
- `DB_USERNAME` - Usuario de MySQL
- `DB_PASSWORD` - Contraseña de MySQL

### 2. Generar App Key

```bash
php artisan key:generate
```

### 3. Permisos de Directorios

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 🚀 Deployment

### Opción 1: Script Automatizado

1. Edita `deploy-mvp.sh` con tus credenciales:
   ```bash
   REMOTE_USER="tu_usuario"
   REMOTE_HOST="tu_host.com"
   REMOTE_PATH="/home/tu_usuario/public_html"
   ```

2. Ejecuta el deployment:
   ```bash
   ./deploy-mvp.sh
   ```

### Opción 2: Deployment Manual

1. Compilar assets:
   ```bash
   npm run build
   ```

2. Instalar dependencias de producción:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. Cachear configuración:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Subir archivos por FTP/SFTP (excluir):
   - `/node_modules`
   - `/.git`
   - `/.env` (usar `.env.production`)
   - `/storage/logs/*`
   - `/storage/framework/cache/*`
   - `/storage/framework/sessions/*`
   - `/storage/framework/views/*`

5. En el servidor, ejecutar:
   ```bash
   php artisan migrate --force
   ```

## 🔒 Seguridad

1. Asegúrate de que `APP_DEBUG=false` en producción
2. Configura HTTPS en tu hosting
3. Actualiza las cabeceras de seguridad en `.htaccess`
4. Verifica que los directorios sensibles no sean accesibles

## 📱 Funcionalidades del MVP

Este MVP incluye:
- ✅ Catálogo de películas y series
- ✅ Sistema de usuarios y perfiles
- ✅ Watchlist personalizada
- ✅ Ratings y comentarios
- ✅ Búsqueda y filtros
- ✅ Integración con TMDB
- ✅ Sección de noticias
- ✅ Doramas románticos

## 🐛 Troubleshooting

### Error 500
1. Verifica logs en `storage/logs/laravel.log`
2. Confirma permisos de directorios
3. Verifica configuración de `.env`

### Base de datos no conecta
1. Verifica credenciales en `.env`
2. Confirma que el servidor MySQL está activo
3. Prueba conexión con cliente MySQL

### Assets no cargan
1. Ejecuta `npm run build`
2. Verifica que `public/build` existe
3. Actualiza `APP_URL` en `.env`

## 🔄 Actualizaciones

Para actualizar el MVP:

1. Pull cambios de git:
   ```bash
   git pull origin mvp-production
   ```

2. Actualizar dependencias:
   ```bash
   composer install
   npm install
   npm run build
   ```

3. Ejecutar migraciones:
   ```bash
   php artisan migrate
   ```

4. Limpiar cachés:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

## 📧 Soporte

Para soporte técnico: juanpablo@dorasia.com

---

¡Tu MVP de DorasIA está listo para producción! 🎉