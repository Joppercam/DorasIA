# Dorasia - Plataforma de Streaming de Contenido Coreano

Este es un proyecto completamente nuevo y limpio de Dorasia, una plataforma de streaming enfocada en contenido coreano.

## Características

- Sistema de autenticación de usuarios completo
- Login con redes sociales (Google, Facebook, Twitter)
- Gestión de múltiples perfiles por usuario
- Interfaz adaptada para mostrar contenido coreano
- Sistema de recomendaciones personalizado
- Comentarios en tiempo real
- Compartir listas de favoritos entre usuarios
- Integración con TMDB API para importar dramas y películas asiáticas
- Búsqueda y filtro avanzado de doramas románticos asiáticos
- Categorización inteligente por subgéneros románticos

## Requisitos

- PHP 8.2+
- Composer
- Node.js y NPM
- Base de datos SQLite (desarrollo) o MySQL/PostgreSQL (producción)

## Instalación

1. Clona este repositorio
2. Instala las dependencias de PHP:
   ```
   composer install
   ```
3. Instala las dependencias de JavaScript:
   ```
   npm install
   ```
4. Compila los assets:
   ```
   npm run build
   ```
5. Configura tu archivo `.env` (puedes usar `.env.example` como plantilla)
6. Genera la clave de la aplicación:
   ```
   php artisan key:generate
   ```
7. Configura las credenciales para inicio de sesión con redes sociales:
   ```
   # Para Google
   GOOGLE_CLIENT_ID=tu-client-id
   GOOGLE_CLIENT_SECRET=tu-client-secret
   
   # Para Facebook
   FACEBOOK_CLIENT_ID=tu-client-id
   FACEBOOK_CLIENT_SECRET=tu-client-secret
   
   # Para Twitter
   TWITTER_CLIENT_ID=tu-client-id
   TWITTER_CLIENT_SECRET=tu-client-secret
   ```
8. Ejecuta las migraciones:
   ```
   php artisan migrate
   ```
9. Para cargar un catálogo completo de muestra, puedes usar:
   ```
   ./init-sample-catalog.sh
   ```
   
   Este script configura:
   - Una base de datos limpia con la estructura necesaria
   - Categorías para K-Drama, J-Drama, C-Drama y Películas
   - Géneros comunes para contenido asiático
   - Títulos de ejemplo para cada categoría
   - Imágenes de muestra para posters y backdrops
   - Un usuario de prueba (test@example.com / password)

## Uso

1. Inicia el servidor de desarrollo:
   ```
   php artisan serve
   ```
2. Accede a `http://localhost:8000` en tu navegador

## Estructura de perfiles

Dorasia utiliza un sistema de perfiles similar a Netflix:

1. Cada usuario puede crear múltiples perfiles
2. Los perfiles se utilizan para personalizar la experiencia
3. Un perfil se mantiene activo durante la sesión
4. Las recomendaciones y el historial se guardan por perfil

Para probar esta funcionalidad:

1. Regístrate como usuario
2. Crea uno o más perfiles
3. Cambia entre perfiles para ver cómo cambia la experiencia

## Sistema de importación de contenido

Dorasia utiliza la API de TMDB (The Movie Database) para importar dramas y películas asiáticas:

1. Obtén una API key y Access Token de TMDB (ver `/docs/api-instructions.md` para instrucciones detalladas)
2. Configura tus credenciales en el archivo `.env`:
   ```
   TMDB_API_KEY=tu_api_key_aqui
   TMDB_ACCESS_TOKEN=tu_access_token_aqui
   ```

3. Ejecuta los comandos de importación:

```bash
# Importar doramas coreanos (5 páginas por defecto)
php artisan import:korean-dramas

# Importar doramas japoneses
php artisan import:japanese-dramas

# Importar doramas chinos
php artisan import:chinese-dramas

# Importar películas asiáticas (KR=Corea, JP=Japón, CN=China)
php artisan import:asian-movies --country=KR

# Importar doramas románticos asiáticos
php artisan dorasia:import-romantic-dramas

# Importar doramas románticos por subgénero específico
php artisan dorasia:import-romantic-dramas --subgenre=historical_romance
```

Opciones disponibles:
- `--pages=N`: Número de páginas a importar (20 títulos por página)
- `--update`: Actualizar títulos existentes con nueva información
- `--force`: Forzar actualización completa incluyendo temporadas, episodios y reparto
- `--country=KR|JP|CN|TH|all`: Filtrar por país de origen (solo para comandos que lo soportan)
- `--subgenre=SUBGENRE`: Filtrar por subgénero romántico (historical_romance, romantic_comedy, melodrama, etc.)

Para actualizar información biográfica y fotos del reparto:
```bash
php artisan update:person-details --limit=50 --missing-only
```

Para más detalles, consulta `/docs/api-instructions.md`.

## Desarrollo

### Comandos útiles

- Ejecutar pruebas: `php artisan test`
- Actualizar base de datos: `php artisan migrate:refresh`
- Compilar assets en desarrollo: `npm run dev`
- Probar migraciones: `./test-migrations.sh`

## Créditos

Desarrollado para Dorasia por Juan Pablo Basualdo.
