# Documentación Completa del Proyecto Dorasia

## Descripción General

Dorasia es una plataforma de streaming especializada en contenido asiático (dramas coreanos, series japonesas, películas asiáticas) construida con Laravel, Alpine.js y Tailwind CSS. El proyecto incluye funcionalidades avanzadas como perfiles sociales, sistema de recomendaciones, notificaciones en tiempo real y un sistema de caché optimizado.

## Características Principales

### 1. Sistema de Catálogo
- Gestión de títulos, temporadas y episodios
- Integración con la API de TMDB para obtener metadatos
- Sistema de géneros y categorías
- Soporte para múltiples idiomas
- Gestión de imágenes (posters y backdrops)

### 2. Sistema de Usuarios y Perfiles
- Autenticación con Laravel Breeze
- Perfiles de usuario personalizables
- Sistema de seguimiento entre usuarios
- Preferencias de géneros
- Configuración de privacidad
- Avatar personalizable

### 3. Funciones Sociales
- Sistema de followers/following
- Mensajería privada entre usuarios
- Feed de actividad de usuarios seguidos
- Comentarios en títulos
- Sistema de calificaciones
- Listas de visualización (watchlist)
- Historial de visualización con progreso

### 4. Sistema de Notificaciones
- Notificaciones en tiempo real con Laravel Echo
- Tipos de notificaciones:
  - Nuevos seguidores
  - Mensajes nuevos
  - Likes en comentarios
  - Respuestas a comentarios
- Notificaciones push y en base de datos
- Gestión de notificaciones leídas/no leídas

### 5. Sistema de Caché
- Caché multinivel con Redis
- Estrategias de invalidación automática
- Precarga de datos frecuentes
- Comando CLI para gestión de caché

### 6. Sistema de Noticias
- Publicación de noticias del mundo del entretenimiento asiático
- Asociación con personas (actores/directores)
- Sistema de imágenes para noticias
- Generación automática de slugs

## Arquitectura Técnica

### Stack Tecnológico
- **Backend**: Laravel 10.x
- **Frontend**: Alpine.js, Blade Templates
- **CSS**: Tailwind CSS
- **Base de Datos**: MySQL/PostgreSQL
- **Caché**: Redis
- **Tiempo Real**: Laravel Echo con Pusher/WebSockets
- **Gestión de Activos**: Vite

### Estructura de Directorios

```
dorasia-new/
├── app/
│   ├── Console/Commands/    # Comandos Artisan personalizados
│   ├── Http/Controllers/    # Controladores de la aplicación
│   ├── Models/             # Modelos Eloquent
│   ├── Notifications/      # Clases de notificaciones
│   ├── Providers/          # Service Providers
│   ├── Services/           # Servicios de la aplicación
│   └── Traits/             # Traits reutilizables
├── config/                 # Archivos de configuración
├── database/
│   ├── factories/          # Factories para testing
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/            # Seeders de datos
├── public/                 # Archivos públicos
├── resources/
│   ├── css/               # Archivos CSS
│   ├── js/                # Archivos JavaScript
│   └── views/             # Vistas Blade
├── routes/                # Definición de rutas
└── storage/               # Almacenamiento de archivos

```

## Modelos y Relaciones

### Usuario (User)
- `hasOne` Profile
- `hasMany` Comments
- `hasMany` Notifications

### Perfil (Profile)
- `belongsTo` User
- `belongsToMany` Profile (followers/following)
- `hasMany` Messages
- `hasMany` Ratings
- `hasMany` WatchHistories
- `hasMany` Watchlists

### Título (Title)
- `belongsToMany` Genre
- `belongsToMany` Person
- `hasMany` Season
- `hasMany` Comment
- `hasMany` Rating
- `hasMany` WatchHistory
- `hasMany` Watchlist

### Temporada (Season)
- `belongsTo` Title
- `hasMany` Episode

### Episodio (Episode)
- `belongsTo` Season
- `hasMany` WatchHistory

### Género (Genre)
- `belongsToMany` Title

### Persona (Person)
- `belongsToMany` Title
- `belongsToMany` News

### Noticia (News)
- `belongsToMany` Person

### Comentario (Comment)
- `belongsTo` User
- `belongsTo` Title
- `hasMany` likes (polimórfico)
- `hasMany` replies (auto-referencial)

### Calificación (Rating)
- `belongsTo` Profile
- `belongsTo` Title

### Historial de Visualización (WatchHistory)
- `belongsTo` Profile
- `belongsTo` Title
- `belongsTo` Episode (opcional)

### Lista de Visualización (Watchlist)
- `belongsTo` Profile
- `belongsTo` Title

### Mensaje (Message)
- `belongsTo` Profile (sender)
- `belongsTo` Profile (receiver)
- `belongsTo` Conversation

### Notificación (Notification)
- `morphTo` notifiable (User)

## API Endpoints

### Catálogo
- `GET /api/titles` - Listar títulos con filtros
- `GET /api/titles/{id}` - Obtener detalles de un título
- `GET /api/genres` - Listar géneros
- `GET /api/categories` - Listar categorías

### Perfiles Sociales
- `POST /api/profiles/{profile}/follow` - Seguir a un perfil
- `DELETE /api/profiles/{profile}/follow` - Dejar de seguir
- `GET /api/profiles/{profile}/followers` - Obtener seguidores
- `GET /api/profiles/{profile}/following` - Obtener seguidos
- `GET /api/profiles/feed` - Obtener feed de actividad
- `GET /api/profiles/suggestions` - Sugerencias de perfiles

### Mensajería
- `POST /api/messages/send` - Enviar mensaje
- `GET /api/messages/conversations` - Listar conversaciones
- `GET /api/messages/conversation/{userId}` - Obtener conversación
- `PUT /api/messages/read/{conversationId}` - Marcar como leído

### Interacciones
- `POST /api/titles/{title}/rate` - Calificar título
- `POST /api/titles/{title}/watchlist` - Añadir a watchlist
- `POST /api/titles/{title}/watch-history` - Registrar visualización
- `POST /api/comments` - Crear comentario
- `POST /api/comments/{comment}/like` - Dar like a comentario
- `DELETE /api/comments/{comment}/like` - Quitar like

### Notificaciones
- `GET /api/notifications` - Obtener notificaciones
- `GET /api/notifications/recent` - Notificaciones recientes
- `PUT /api/notifications/{id}/read` - Marcar como leída
- `PUT /api/notifications/read-all` - Marcar todas como leídas
- `DELETE /api/notifications/{id}` - Eliminar notificación

## Funcionalidades Avanzadas

### Sistema de Caché

El sistema de caché utiliza Redis para almacenar datos frecuentemente accedidos:

```php
// Ejemplo de uso del CacheService
$title = CacheService::rememberTitle($titleId, function() use ($titleId) {
    return Title::with(['genres', 'seasons', 'people'])
        ->findOrFail($titleId);
}, CacheService::DURATION_MEDIUM);
```

### Notificaciones en Tiempo Real

Implementación con Laravel Echo y Pusher:

```javascript
// Configuración en el frontend
Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        // Manejar notificación en tiempo real
        showToast(notification.message);
        updateNotificationBell();
    });
```

### Sistema de Recomendaciones

Algoritmo basado en:
- Géneros preferidos del usuario
- Historial de visualización
- Calificaciones otorgadas
- Actividad de usuarios seguidos

## Configuración del Entorno

### Variables de Entorno Requeridas

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dorasia
DB_USERNAME=root
DB_PASSWORD=

# Redis/Caché
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Notificaciones en tiempo real
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

# API de TMDB
TMDB_API_KEY=
TMDB_API_URL=https://api.themoviedb.org/3

# Almacenamiento
FILESYSTEM_DISK=public
```

## Comandos Artisan Personalizados

- `php artisan cache:manage clear` - Limpiar caché
- `php artisan cache:manage warm` - Precalentar caché
- `php artisan cache:manage stats` - Ver estadísticas de caché
- `php artisan tmdb:import` - Importar datos desde TMDB
- `php artisan news:cleanup` - Limpiar noticias antiguas

## Seguridad

### Medidas Implementadas
- Autenticación con Laravel Breeze
- Autorización basada en políticas
- Protección CSRF
- Validación de entrada en todos los formularios
- Sanitización de datos de usuario
- Rate limiting en APIs
- Encriptación de contraseñas con bcrypt

### Mejores Prácticas
- Uso de prepared statements
- Escapado de salida en vistas
- Validación de archivos subidos
- Restricción de tipos MIME permitidos
- Límites de tamaño de archivo

## Testing

### Estrategia de Testing
- Tests unitarios para servicios y modelos
- Tests de integración para APIs
- Tests de navegador con Laravel Dusk
- Tests de carga con Apache JMeter

### Ejecutar Tests

```bash
# Tests unitarios y de integración
php artisan test

# Tests de navegador
php artisan dusk

# Tests con cobertura
php artisan test --coverage
```

## Optimización de Rendimiento

### Estrategias Implementadas
- Caché agresivo con Redis
- Lazy loading de imágenes
- Paginación en listados largos
- Optimización de consultas con eager loading
- Compresión de assets con Vite
- CDN para archivos estáticos
- Queue workers para tareas pesadas

### Monitoreo
- Laravel Telescope para debugging
- Logs estructurados con contexto
- Métricas de rendimiento en cache
- Alertas en errores críticos

## Mantenimiento

### Tareas Regulares
- Limpieza de logs antiguos
- Optimización de tablas de base de datos
- Actualización de índices
- Renovación de certificados SSL
- Backup de base de datos
- Monitoreo de espacio en disco

### Actualizaciones
- Mantener Laravel actualizado
- Actualizar dependencias regularmente
- Revisar vulnerabilidades con `npm audit`
- Actualizar políticas de seguridad

## Contribución

### Flujo de Trabajo
1. Fork del repositorio
2. Crear rama para feature/bugfix
3. Implementar cambios con tests
4. Ejecutar linters y tests
5. Crear pull request
6. Revisión de código
7. Merge a rama principal

### Estándares de Código
- PSR-12 para PHP
- ESLint para JavaScript
- Prettier para formateo
- Commits atómicos con mensajes descriptivos
- Documentación de nuevas funcionalidades

## Licencia

Este proyecto está licenciado bajo [especificar licencia].

## Contacto

Para preguntas o soporte, contactar a [información de contacto].

---

Última actualización: Diciembre 2024