# Configuración de Google OAuth para Dorasia

## El Problema
Estás recibiendo el error "Missing required parameter: client_id" porque las credenciales de Google OAuth no están configuradas en tu archivo `.env`.

## Solución Paso a Paso

### 1. Crear Credenciales en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. En el menú lateral, busca "APIs y servicios" > "Credenciales"
4. Haz clic en "Crear credenciales" > "ID de cliente OAuth"
5. Selecciona "Aplicación web" como tipo de aplicación
6. Configura:
   - **Nombre**: Dorasia (o el nombre que prefieras)
   - **URIs de redirección autorizadas**: 
     - Para desarrollo local: `http://localhost:8000/auth/google/callback`
     - Para producción: `https://tudominio.com/auth/google/callback`

### 2. Copiar las Credenciales

Una vez creado, Google te mostrará:
- **Client ID**: Un string largo que termina en `.apps.googleusercontent.com`
- **Client Secret**: Una cadena secreta

### 3. Actualizar el Archivo .env

Abre tu archivo `.env` y actualiza estas líneas:

```env
GOOGLE_CLIENT_ID=aqui_pega_tu_client_id
GOOGLE_CLIENT_SECRET=aqui_pega_tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 4. Limpiar Caché de Configuración

Después de actualizar el `.env`, ejecuta:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. Verificar Configuración

Visita `/check-oauth-config.php` en tu navegador para verificar que todo esté correctamente configurado.

## URLs Importantes

- Script de verificación: `/check-oauth-config.php`
- Test de navegación: `/test-auth-navigation.php`
- Login con Google: `/auth/google`

## Notas Adicionales

- Las URIs de redirección en Google Console DEBEN coincidir exactamente con las de tu aplicación
- Para desarrollo local, usa `http://localhost:8000/auth/google/callback`
- Para producción, usa HTTPS: `https://tudominio.com/auth/google/callback`
- Puedes agregar múltiples URIs de redirección en Google Console

## Troubleshooting

Si sigues teniendo problemas:

1. Verifica que las credenciales estén correctamente copiadas (sin espacios extra)
2. Asegúrate de que la URI de redirección coincida exactamente
3. Limpia la caché después de cambiar el `.env`
4. Reinicia tu servidor de desarrollo
5. Revisa los logs en `storage/logs/laravel.log`

## Seguridad

- NUNCA compartas tu `Client Secret`
- No subas el archivo `.env` a control de versiones
- En producción, usa variables de entorno del servidor en lugar del archivo `.env`