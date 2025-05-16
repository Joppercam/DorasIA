# Guía de Despliegue Rápido - Dorasia

## Archivos necesarios para el despliegue:

1. **`.env.production`** - Plantilla de configuración (actualizar con tus datos)
2. **`deploy.sh`** - Script automatizado de instalación
3. **`.htaccess`** - Configuración para Apache
4. **`DEPLOYMENT_CHECKLIST.md`** - Lista detallada de pasos
5. **`SERVER_REQUIREMENTS.md`** - Requisitos del servidor

## Pasos rápidos:

### 1. Preparar archivos localmente
```bash
# Compilar assets
npm run build

# Verificar que todo funciona
php artisan test
```

### 2. Actualizar configuración
Edita `.env.production` con:
- Tu URL de dominio
- Credenciales de base de datos
- API key de TMDB
- Configuración de email

### 3. Subir al servidor
Sube todos los archivos excepto:
- `node_modules/`
- `.git/`
- `.env` local
- Contenido de `storage/logs/`
- Contenido de `storage/framework/cache/`

### 4. Ejecutar en el servidor
```bash
# Dar permisos al script
chmod +x deploy.sh

# Ejecutar instalación
./deploy.sh

# Verificar permisos
chmod -R 755 storage bootstrap/cache
```

### 5. Configurar servidor web
- Apache: Apuntar DocumentRoot a `/tu-ruta/public`
- Nginx: Configurar root a `/tu-ruta/public`

### 6. Finalizar
1. Editar `.env` con credenciales finales
2. Ejecutar `php artisan key:generate`
3. Verificar que todo funciona

## ¿Problemas?
- Revisa `storage/logs/laravel.log`
- Verifica permisos de carpetas
- Consulta `DEPLOYMENT_CHECKLIST.md` para más detalles

## Importar contenido inicial
```bash
# Después del despliegue, puedes importar contenido:
php artisan news:generate-asian
php artisan news:generate-more-asian --limit=30
```

¡Tu aplicación Dorasia está lista! 🎉