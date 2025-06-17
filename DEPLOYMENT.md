# Instrucciones de Deployment para Dorasia

## Configuración de Producción

### 1. Archivo de Entorno (.env)
Copiar `.env.production` como `.env` en el servidor:

```bash
cp .env.production .env
```

### 2. Generar APP_KEY
```bash
php artisan key:generate
```

### 3. Configurar Permisos
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 4. Ejecutar Migraciones
```bash
php artisan migrate --force
```

### 5. Limpiar y Optimizar Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. Crear Usuario Admin
```bash
php artisan tinker
```
Luego ejecutar:
```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@dorasia.cl',
    'password' => bcrypt('tu_password_seguro'),
    'is_admin' => true,
    'email_verified_at' => now()
]);
```

## URLs de Acceso

### Usuario Regular
- **Login**: https://www.dorasia.cl/login
- **Registro**: https://www.dorasia.cl/register

### Administrador
- **Panel Admin**: https://www.dorasia.cl/admin-login

## Configuración de Sesiones

El archivo `.env.production` está configurado para usar:
- `SESSION_DOMAIN=.dorasia.cl` (incluye subdominios)
- `SESSION_DRIVER=file` (más confiable que database)
- `SESSION_SECURE_COOKIES=true` (solo HTTPS)

## Troubleshooting

### Si no funciona el login/registro:
1. Verificar que el directorio `storage/framework/sessions` existe y tiene permisos
2. Ejecutar `php artisan config:clear`
3. Verificar que APP_KEY está configurado
4. Revisar logs en `storage/logs/laravel.log`

### Si hay problemas de CSRF:
1. Verificar que las rutas AJAX están en las excepciones CSRF
2. Verificar que el dominio de cookies es correcto
3. Limpiar caché del navegador

## Comandos de Mantenimiento

```bash
# Limpiar todos los caches
php artisan optimize:clear

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar configuración
php artisan config:show session
```