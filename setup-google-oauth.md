# Configuración Paso a Paso - Google OAuth para DORASIA

## Paso 1: Crear proyecto en Google Cloud Console

1. Ve a: https://console.cloud.google.com/
2. Haz clic en el selector de proyecto (arriba)
3. Clic en "NUEVO PROYECTO"
4. Nombre: `DORASIA K-Dramas`
5. Crear

## Paso 2: Habilitar APIs

1. En el menú lateral, ve a "APIs y servicios" > "Biblioteca"
2. Busca "Google+ API" y habilítala
3. Busca "Google People API" y habilítala

## Paso 3: Configurar pantalla de consentimiento OAuth

1. Ve a "APIs y servicios" > "Pantalla de consentimiento de OAuth"
2. Selecciona "Externo"
3. Completa ÚNICAMENTE los campos obligatorios:
   - **Nombre de la aplicación**: DORASIA - K-Dramas
   - **Correo electrónico de asistencia del usuario**: jpablo.basualdo@gmail.com
   - **Información de contacto del desarrollador**: jpablo.basualdo@gmail.com
4. Guardar y continuar
5. En "Scopes": Saltar (no agregar nada)
6. En "Usuarios de prueba": Agregar jpablo.basualdo@gmail.com
7. Guardar

## Paso 4: Crear credenciales OAuth 2.0

1. Ve a "APIs y servicios" > "Credenciales"
2. Clic en "+ CREAR CREDENCIALES" > "ID de cliente de OAuth 2.0"
3. Tipo de aplicación: "Aplicación web"
4. Nombre: "DORASIA Web Client"
5. **Orígenes de JavaScript autorizados**:
   ```
   http://localhost:8000
   http://127.0.0.1:8000
   ```
6. **URI de redirección autorizados**:
   ```
   http://localhost:8000/auth/google/callback
   http://127.0.0.1:8000/auth/google/callback
   ```
7. CREAR

## Paso 5: Copiar credenciales

Después de crear, verás una ventana modal con:
- **ID de cliente**: (algo como: 123456789-abcdefg.apps.googleusercontent.com)
- **Secreto del cliente**: (algo como: GOCSPX-abcdefg123456)

¡Copia estos valores y los usaremos en el siguiente paso!

## Troubleshooting común:

- ❌ Si ves "Error 403: access_denied" → Agrega tu email en "Usuarios de prueba"
- ❌ Si ves "Error 400: redirect_uri_mismatch" → Verifica las URLs exactas
- ❌ Si ves "Error 401: invalid_client" → Verifica CLIENT_ID y CLIENT_SECRET