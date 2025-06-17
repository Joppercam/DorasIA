# Fix de Sesiones y Registro para Dorasia

## Cambios Realizados:

### 1. **Habilitación de CSRF Protection**
- Modificado `bootstrap/app.php` para habilitar CSRF con excepciones específicas
- Solo se excluyen rutas API y callback de Google OAuth

### 2. **Configuración de Sesiones**
- Cambiado driver de sesiones de 'file' a 'database' en `.env`
- Agregadas configuraciones de seguridad para cookies

### 3. **Para Producción**
Actualiza tu archivo `.env.production`:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_ENCRYPT=false
SESSION_COOKIE=dorasia_session
SESSION_SECURE_COOKIE=true  # Cambiar a true para HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 4. **Comandos a ejecutar en producción:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Verificación de funcionamiento:
1. Las sesiones ahora se guardan en base de datos
2. CSRF está activo para formularios
3. Las cookies de sesión son seguras

## Importante:
- En producción, asegúrate de que `SESSION_SECURE_COOKIE=true` cuando uses HTTPS
- La tabla de sesiones ya existe en la base de datos