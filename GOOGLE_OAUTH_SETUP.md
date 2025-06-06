# Configuración de Google OAuth para DORASIA

Este documento explica cómo configurar la autenticación con Google OAuth en DORASIA.

## 1. Crear proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la Google+ API y Google OAuth API

## 2. Configurar OAuth Consent Screen

1. Ve a "APIs & Services" > "OAuth consent screen"
2. Selecciona "External" como tipo de usuario
3. Completa la información requerida:
   - **App name**: DORASIA - K-Dramas
   - **User support email**: tu-email@dominio.com
   - **Developer contact information**: tu-email@dominio.com
   - **App domain**: tu-dominio.com
   - **Authorized domains**: tu-dominio.com

## 3. Crear credenciales OAuth 2.0

1. Ve a "APIs & Services" > "Credentials"
2. Haz clic en "Create Credentials" > "OAuth 2.0 Client IDs"
3. Selecciona "Web application" como tipo
4. Configura las URLs autorizadas:

### Para desarrollo local:
- **Authorized JavaScript origins**: 
  - `http://localhost:8000`
  - `http://127.0.0.1:8000`
- **Authorized redirect URIs**: 
  - `http://localhost:8000/auth/google/callback`
  - `http://127.0.0.1:8000/auth/google/callback`

### Para producción:
- **Authorized JavaScript origins**: 
  - `https://tu-dominio.com`
- **Authorized redirect URIs**: 
  - `https://tu-dominio.com/auth/google/callback`

## 4. Configurar variables de entorno

Copia las credenciales obtenidas y actualiza tu archivo `.env`:

```env
GOOGLE_CLIENT_ID=tu-client-id-aqui
GOOGLE_CLIENT_SECRET=tu-client-secret-aqui
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## 5. Funcionalidades implementadas

✅ **Registro con Google**: Los usuarios pueden crear cuenta usando su cuenta de Google
✅ **Login con Google**: Acceso rápido con credenciales de Google
✅ **Vinculación de cuentas**: Si el email ya existe, se vincula la cuenta de Google
✅ **Avatar automático**: Se importa la imagen de perfil de Google
✅ **Perfil automático**: Se crea perfil con configuraciones por defecto
✅ **Interfaz integrada**: Botones de Google en login y registro con diseño DORASIA

## 6. Flujo de autenticación

1. Usuario hace clic en "Continuar con Google"
2. Redirección a Google OAuth
3. Usuario autoriza la aplicación
4. Google redirige a `/auth/google/callback`
5. El sistema:
   - Verifica si el usuario existe por Google ID
   - Si no existe, verifica por email
   - Si no existe cuenta, crea usuario nuevo + perfil
   - Si existe cuenta, vincula Google ID
   - Autentica al usuario automáticamente

## 7. Seguridad implementada

- ✅ Validación de estado OAuth
- ✅ Manejo de errores y excepciones
- ✅ Log de errores para debugging
- ✅ Contraseña opcional para usuarios OAuth
- ✅ Verificación automática de email
- ✅ Redirección segura post-autenticación

## 8. Testing

Para probar la funcionalidad:

1. Configura las credenciales de Google
2. Inicia el servidor: `php artisan serve`
3. Ve a `/login` o `/register`
4. Haz clic en "Continuar con Google"
5. Completa el flujo de OAuth
6. Verifica que el usuario se cree correctamente

## 9. Troubleshooting

### Error: "redirect_uri_mismatch"
- Verifica que la URL de callback esté correctamente configurada en Google Console
- Asegúrate de que coincida exactamente con `GOOGLE_REDIRECT_URI`

### Error: "invalid_client"
- Verifica que `GOOGLE_CLIENT_ID` y `GOOGLE_CLIENT_SECRET` sean correctos
- Asegúrate de que las credenciales sean para el proyecto correcto

### Error de scopes
- El sistema solicita automáticamente los scopes básicos: `openid`, `profile`, `email`
- Google proporciona automáticamente nombre, email y avatar

## 10. Próximos pasos

- [ ] Implementar desvinculación de cuenta Google
- [ ] Agregar más proveedores OAuth (Facebook, Twitter)
- [ ] Implementar refresh tokens para sesiones extendidas
- [ ] Agregar opciones de privacidad para datos de Google