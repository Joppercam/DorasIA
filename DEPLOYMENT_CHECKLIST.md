# Lista de Verificación para Despliegue de Dorasia

## Pre-despliegue (Local)

- [ ] Compilar assets para producción: `npm run build`
- [ ] Verificar que no hay errores en el código
- [ ] Actualizar archivo `.env.production` con credenciales reales
- [ ] Hacer backup de tu base de datos local (si quieres migrar datos)

## Subida de archivos

- [ ] Crear base de datos en el servidor
- [ ] Subir todos los archivos al servidor EXCEPTO:
  - `node_modules/`
  - `.git/`
  - `.env` (local)
  - `storage/logs/*.log`
  - `storage/framework/cache/`
  - `storage/framework/sessions/`
- [ ] Asegurarse de que `.htaccess` esté en la raíz del proyecto

## Configuración del servidor

1. [ ] Conectarse al servidor vía SSH
2. [ ] Navegar al directorio del proyecto
3. [ ] Dar permisos de ejecución al script: `chmod +x deploy.sh`
4. [ ] Ejecutar el script de despliegue: `./deploy.sh`
5. [ ] Editar el archivo `.env` con las credenciales correctas:
   - APP_URL
   - DB_DATABASE, DB_USERNAME, DB_PASSWORD
   - MAIL configuration
   - TMDB_API_KEY

## Configuración del servidor web

### Apache:
- [ ] Apuntar el DocumentRoot a `/path/to/dorasia/public`
- [ ] Verificar que mod_rewrite esté habilitado
- [ ] Asegurarse de que AllowOverride esté en All

### Nginx:
- [ ] Configurar el root a `/path/to/dorasia/public`
- [ ] Agregar las reglas de reescritura necesarias

## Post-despliegue

- [ ] Verificar que la página carga correctamente
- [ ] Probar el login/registro
- [ ] Verificar que las imágenes se cargan
- [ ] Probar la funcionalidad de búsqueda
- [ ] Verificar que los videos se reproducen
- [ ] Probar la importación de datos desde TMDB

## Mantenimiento

- [ ] Configurar backups automáticos de la base de datos
- [ ] Configurar rotación de logs
- [ ] Monitorear el espacio en disco
- [ ] Configurar SSL/HTTPS (Let's Encrypt)

## Comandos útiles

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ver información de la aplicación
php artisan about

# Verificar rutas
php artisan route:list

# Regenerar clave de aplicación (solo si es necesario)
php artisan key:generate
```

## Solución de problemas comunes

1. **Error 500**: Verificar permisos de carpetas y logs
2. **Página en blanco**: Revisar el archivo `.env` y las credenciales de BD
3. **Assets no cargan**: Ejecutar `npm run build` y verificar enlaces simbólicos
4. **Imágenes no se muestran**: Ejecutar `php artisan storage:link`
5. **Error de base de datos**: Verificar migraciones con `php artisan migrate:status`

## Seguridad

- [ ] Cambiar APP_DEBUG a false en producción
- [ ] Usar contraseñas seguras para BD y email
- [ ] Configurar firewall del servidor
- [ ] Mantener PHP y dependencias actualizadas
- [ ] Configurar HTTPS/SSL