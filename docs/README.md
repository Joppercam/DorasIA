# 🎭 DORASIA - Plataforma de K-Dramas y Películas Coreanas

## 📖 Descripción General

Dorasia es una plataforma web especializada en contenido audiovisual coreano, ofreciendo una experiencia completa para los fans de K-Dramas y películas coreanas. La plataforma incluye información detallada en español, sistema de usuarios, ratings, watchlists y seguimiento de progreso de visualización.

## 🚀 Características Principales

### 📺 Contenido
- **Series Coreanas (K-Dramas)**: 643+ series con información completa
- **Películas Coreanas**: 130+ películas con traducciones al español
- **Actores y Actrices**: 3,950+ perfiles de actores con biografías
- **Géneros**: Sistema completo de categorización
- **Próximos Estrenos**: Calendario de upcoming releases

### 🌟 Funcionalidades de Usuario
- **Sistema de Ratings**: Love/Like/Dislike para series y películas
- **Watchlist Personal**: Estados (pendiente/viendo/completado)
- **Progreso de Episodios**: Tracking detallado de visualización
- **Comentarios**: Sistema de comentarios en series y actores
- **Seguimiento de Actores**: Follow/unfollow actores favoritos
- **Perfiles Públicos**: Perfiles de usuario públicos y privados

### 🔐 Autenticación
- **Registro/Login Tradicional**: Con email y contraseña
- **Google OAuth**: Integración con Google Sign-In
- **Verificación de Email**: Sistema automático
- **Perfiles Completos**: Avatares y información personal

### 🌍 Internacionalización
- **Contenido en Español**: Traducciones automáticas con OpenAI
- **Títulos Localizados**: Títulos comerciales en español
- **Biografías Traducidas**: Información de actores en español
- **Interfaz Multiidioma**: Soporte para diferentes idiomas

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Base de Datos**: SQLite (desarrollo) / MySQL (producción)
- **APIs Externas**: TMDB API, OpenAI API
- **Autenticación**: Laravel Sanctum + Google OAuth

### Frontend
- **CSS Framework**: Custom CSS con grid/flexbox
- **JavaScript**: Vanilla JS + AJAX
- **Iconos**: Emojis + SVG
- **Diseño**: Mobile-first responsive
- **Tema**: Dark theme (Netflix-style)

### Servicios
- **TMDB Service**: Importación de contenido desde The Movie Database
- **Translation Service**: Traducción automática con OpenAI GPT-4o-mini
- **Rate Limiting**: Protección contra spam y ataques
- **Cache System**: Sistema de caché para optimización

## 📂 Estructura del Proyecto

```
Dorasia/
├── app/
│   ├── Console/Commands/          # Comandos de importación y mantenimiento
│   ├── Http/Controllers/          # Controladores MVC
│   ├── Models/                    # Modelos Eloquent
│   ├── Services/                  # Servicios de negocio
│   └── Http/Middleware/           # Middleware personalizado
├── database/
│   ├── migrations/                # Migraciones de base de datos
│   └── seeders/                   # Seeders de datos iniciales
├── resources/
│   └── views/                     # Vistas Blade
├── routes/
│   └── web.php                    # Definición de rutas
├── public/                        # Assets públicos
├── docs/                          # Documentación (este directorio)
└── storage/                       # Archivos temporales y logs
```

## 🗃️ Modelos de Datos

### Modelos Principales
- **Series**: K-dramas con metadatos completos
- **Movie**: Películas con información localizada
- **Person**: Actores, directores y personal
- **Episode/Season**: Estructura de temporadas y episodios
- **User**: Sistema de usuarios y autenticación

### Modelos de Interacción
- **TitleRating**: Ratings de usuarios (love/like/dislike)
- **Watchlist**: Lista de seguimiento de usuarios
- **EpisodeProgress**: Progreso de visualización de episodios
- **Comment**: Sistema de comentarios polimórfico
- **ActorFollow**: Seguimiento de actores favoritos

### Modelos de Contenido
- **Genre**: Géneros con nombres localizados
- **Image**: Sistema de imágenes polimórfico
- **ProfessionalReview**: Reseñas profesionales
- **UpcomingSeries**: Próximos estrenos

## 🔧 Configuración y Despliegue

### Variables de Entorno Requeridas
```env
# Base de datos
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/al/database.sqlite

# APIs externas
TMDB_API_KEY=tu_clave_tmdb
OPENAI_API_KEY=tu_clave_openai

# Google OAuth
GOOGLE_CLIENT_ID=tu_google_client_id
GOOGLE_CLIENT_SECRET=tu_google_client_secret

# Configuración de sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### Comandos de Instalación
```bash
# Instalar dependencias
composer install

# Configurar base de datos
php artisan migrate
php artisan db:seed

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar servidor
php artisan serve
```

## 📋 Comandos Artisan Personalizados

### Importación de Contenido
```bash
# Importar series coreanas
php artisan import:korean-dramas --pages=5 --with-details

# Importar películas coreanas
php artisan import:more-korean-content --movies=50 --series=30

# Traducir contenido existente
php artisan translate:movies-spanish --limit=100
php artisan translate:actors-spanish --limit=50
```

### Mantenimiento
```bash
# Limpiar caché de series
php artisan clear:series-cache

# Optimizar base de datos
php artisan optimize:movie-database

# Actualizar contenido
php artisan update:current-series
```

## 🛡️ Seguridad

### Medidas Implementadas
- **Rate Limiting**: Límites en APIs y autenticación
- **CSRF Protection**: Protección contra ataques CSRF
- **SQL Injection**: Prevención con Eloquent ORM
- **XSS Protection**: Escapado automático en Blade
- **Secure Headers**: Headers de seguridad personalizados

### Middleware de Seguridad
- **RateLimitMiddleware**: Control de velocidad de requests
- **SecurityHeadersMiddleware**: Headers de seguridad HTTP
- **ChileanLocalization**: Localización regional

## 📊 Performance y Optimización

### Estrategias de Cache
- **Query Cache**: Cache de consultas frecuentes
- **View Cache**: Cache de vistas compiladas
- **Session Cache**: Optimización de sesiones
- **API Cache**: Cache de respuestas de APIs externas

### Optimizaciones de Base de Datos
- **Índices Estratégicos**: Índices en campos de búsqueda
- **Eager Loading**: Carga eficiente de relaciones
- **Pagination**: Paginación en listados grandes
- **Query Optimization**: Consultas optimizadas

## 🔗 APIs y Integraciones

### APIs Externas
- **TMDB API**: Importación de metadatos de películas/series
- **OpenAI API**: Traducción automática de contenido
- **Google OAuth**: Autenticación con Google

### APIs Internas
- **Search API**: Búsqueda de contenido `/api/search`
- **Autocomplete API**: Autocompletado de actores `/api/actors/autocomplete`
- **Upcoming API**: Próximos estrenos `/api/upcoming/*`

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

### Características Mobile
- **Touch-friendly**: Botones y elementos táctiles optimizados
- **Swipe Support**: Navegación por gestos
- **Progressive Enhancement**: Funcionalidad básica sin JavaScript
- **Mobile-first**: Diseño optimizado para móviles primero

## 🎨 Diseño y UI/UX

### Paleta de Colores
- **Primario**: #141414 (Netflix Dark)
- **Secundario**: #00d4ff (Cyan)
- **Acento**: #ff006e (Pink/Purple gradient)
- **Texto**: #ffffff (White)
- **Texto Secundario**: #cccccc (Light Gray)

### Componentes UI
- **Cards**: Diseño tipo Netflix para contenido
- **Modals**: Overlays para acciones rápidas
- **Tooltips**: Información contextual
- **Loading States**: Estados de carga animados
- **Error States**: Manejo elegante de errores

## 🧪 Testing y QA

### Tipos de Testing
- **Unit Tests**: Tests de modelos y servicios
- **Feature Tests**: Tests de funcionalidades completas
- **Browser Tests**: Tests de interfaz de usuario
- **API Tests**: Tests de endpoints

### Herramientas de QA
- **Laravel Testing**: Framework de testing integrado
- **PHPUnit**: Unit testing
- **Laravel Dusk**: Browser testing
- **Code Coverage**: Análisis de cobertura

## 📈 Métricas y Analytics

### Métricas de Usuario
- **Registros**: Nuevos usuarios por día/semana/mes
- **Actividad**: Sessions, pageviews, tiempo en sitio
- **Engagement**: Ratings, comentarios, follows
- **Retención**: Usuarios activos recurrentes

### Métricas de Contenido
- **Contenido Popular**: Series/películas más vistas
- **Tendencias**: Géneros y actores trending
- **Calificaciones**: Distribución de ratings
- **Búsquedas**: Términos más buscados

## 🚀 Roadmap y Futuras Mejoras

### Próximas Características
- **Sistema de Notificaciones**: Push notifications para nuevos episodios
- **Recomendaciones IA**: Sistema de recomendaciones basado en ML
- **Social Features**: Amigos, compartir listas, chat
- **Mobile App**: Aplicación móvil nativa
- **Streaming Integration**: Links a plataformas de streaming

### Optimizaciones Técnicas
- **GraphQL API**: API más eficiente
- **Real-time Updates**: WebSockets para actualizaciones en tiempo real
- **CDN Integration**: Distribución global de contenido
- **Microservices**: Arquitectura de microservicios
- **Kubernetes**: Orquestación de contenedores

## 🤝 Contribución

### Guidelines para Desarrollo
1. **Code Style**: Seguir PSR-12 y Laravel conventions
2. **Git Flow**: Feature branches con pull requests
3. **Testing**: Cobertura mínima del 80%
4. **Documentation**: Documentar nuevas características
5. **Security**: Revisar implicaciones de seguridad

### Proceso de Development
1. Fork del repositorio
2. Crear feature branch
3. Implementar cambios con tests
4. Actualizar documentación
5. Submit pull request
6. Code review y merge

## 📞 Soporte y Contacto

### Documentación Técnica
- **API Documentation**: `/docs/api.md`
- **Database Schema**: `/docs/database.md`
- **Deployment Guide**: `/docs/deployment.md`
- **Troubleshooting**: `/docs/troubleshooting.md`

### Recursos Adicionales
- **Laravel Documentation**: https://laravel.com/docs
- **TMDB API**: https://developers.themoviedb.org
- **OpenAI API**: https://platform.openai.com/docs

---

**Versión**: 1.0.0  
**Última Actualización**: Junio 2025  
**Desarrollado con**: ❤️ para la comunidad K-Drama