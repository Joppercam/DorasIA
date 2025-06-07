# 🚀 Guía de Despliegue Actualizado - DORASIA

## 📋 Pasos para actualizar la aplicación en producción

### 1. 📥 Subir el script de actualización al servidor

Primero, sube el archivo `deploy-update.sh` a tu servidor usando FTP o el administrador de archivos de tu hosting.

```bash
# Si tienes acceso SSH:
scp deploy-update.sh usuario@tuservidor.com:/home/n91a0e5/
```

### 2. 🖥️ Conectarte al servidor

Conéctate a tu servidor vía SSH o usa la terminal web de tu hosting:

```bash
ssh usuario@tuservidor.com
```

### 3. 🏃 Ejecutar el script de actualización

```bash
cd /home/n91a0e5/
chmod +x deploy-update.sh
./deploy-update.sh
```

### 4. 🔍 Verificaciones importantes

Después de ejecutar el script, verifica:

1. **Menú móvil**: Abre el sitio en un móvil y verifica que aparezcan los botones ☰ y 🔍
2. **Búsqueda**: Prueba buscar series en español
3. **Base de datos**: Verifica que las series se muestren correctamente

### 5. 🆘 Si algo sale mal

Si encuentras errores durante la actualización:

```bash
# Restaurar desde el backup (el script te mostrará la ruta)
cp -r /home/n91a0e5/backups/dorasia_FECHA/* /home/n91a0e5/dorasia.cl/
```

## 📝 Actualización manual (si prefieres hacerlo paso a paso)

### Paso 1: Backup
```bash
cd /home/n91a0e5/
cp -r dorasia.cl dorasia_backup_$(date +%Y%m%d)
cp dorasia.cl/database/database.sqlite dorasia_backup_$(date +%Y%m%d)/database_backup.sqlite
```

### Paso 2: Modo mantenimiento
```bash
cd dorasia.cl
php artisan down
```

### Paso 3: Actualizar código
```bash
git pull origin main
```

### Paso 4: Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
```

### Paso 5: Ejecutar migraciones
```bash
php artisan migrate --force
```

### Paso 6: Limpiar y regenerar cachés
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 7: Corregir permisos
```bash
chmod -R 755 storage bootstrap/cache
chmod 664 database/database.sqlite
```

### Paso 8: Quitar modo mantenimiento
```bash
php artisan up
```

## 🔧 Comandos útiles post-despliegue

### Importar más contenido en español
```bash
php artisan dorasia:import-spanish --all --pages=20
```

### Corregir traducciones
```bash
php artisan dorasia:fix-translations
```

### Ver estadísticas
```bash
php artisan tinker --execute="
echo 'Series totales: ' . \App\Models\Series::count() . \"\n\";
echo 'Actores totales: ' . \App\Models\Person::count() . \"\n\";
echo 'Usuarios registrados: ' . \App\Models\User::count() . \"\n\";
"
```

### Monitorear logs
```bash
tail -f storage/logs/laravel.log
```

## ⚠️ Problemas comunes y soluciones

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Error en migraciones
```bash
# Ver el estado de las migraciones
php artisan migrate:status

# Si es necesario, rollback
php artisan migrate:rollback --step=1
```

### Permisos incorrectos
```bash
# En hosting compartido
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
chmod 664 database/database.sqlite
```

### Imágenes no se muestran
```bash
php artisan storage:link
```

## 🎯 Checklist final

- [ ] El menú móvil es visible
- [ ] La búsqueda funciona en español
- [ ] Los usuarios pueden registrarse/iniciar sesión
- [ ] Las series se muestran correctamente
- [ ] Las imágenes cargan sin problemas
- [ ] No hay errores en los logs

## 📞 Soporte

Si encuentras problemas:
1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica el backup está disponible
3. Documenta el error exacto para resolverlo

¡Éxito con el despliegue! 🎉