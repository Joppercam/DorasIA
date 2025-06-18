# 🤖 DORASIA - Portal de K-Dramas con IA

**DORAS[IA]** es una plataforma moderna para fanáticos de los K-Dramas en Chile, diseñada con tecnología de inteligencia artificial y enfoque en la experiencia del usuario.

## ✨ Características Principales

### 🎬 Contenido Rico
- **2,340+ K-Dramas** importados desde TMDB
- **5,860+ actores y directores** con información detallada
- **Hub de contenido exclusivo de actores** con entrevistas, biografías y noticias
- **Traducciones completas** al español chileno
- **Categorías organizadas**: Romance, Drama, Acción, Comedia, Misterio, Históricos

### 🎨 Interfaz Moderna
- **Logo AI-themed** con destaque en "IA"
- **Carruseles infinitos** con efectos hover avanzados
- **Diseño Netflix-style** completamente responsive
- **Información detallada** con reparto e plataformas de streaming

### 🎭 Hub de Contenido de Actores
- **Entrevistas transcritas** con formato pregunta/respuesta
- **Biografías detalladas** con historias personales
- **Noticias y artículos** de análisis profesional
- **Cronologías de carrera** paso a paso
- **Curiosidades y datos** únicos de cada actor
- **Videos externos** de TikTok, YouTube e Instagram (cuando disponibles)
- **Sistema honesto**: Solo contenido real, sin promesas falsas

### 🔧 Tecnología
- **Laravel 11** con PHP 8.2+
- **MySQL/MariaDB** para persistencia
- **TMDB API** para contenido actualizado
- **Localización chilena** completa

## 🚀 Deploy Rápido

```bash
# 1. Clonar el repositorio
git clone [tu-repo] dorasia
cd dorasia

# 2. Configurar entorno
cp .env.production .env
# Editar .env con tus datos de hosting

# 3. Ejecutar deploy
chmod +x deploy.sh
./deploy.sh

# 4. Importar contenido inicial
php artisan import:korean-dramas --pages=50
```

## 📋 Requisitos del Hosting

- **PHP 8.2+** con extensiones: PDO, MySQL, cURL, JSON
- **MySQL 5.7+** o **MariaDB 10.3+**
- **Memoria**: 512MB mínimo
- **Composer** instalado

## 🔮 Roadmap Futuro

### Fase 2 - Sistema de Usuarios
- [x] Registro y autenticación
- [x] Perfiles personalizados
- [x] Listas de "Favoritos" y "Por Ver"
- [x] **Contenido exclusivo de actores** para usuarios registrados

### Fase 3 - Funciones Sociales
- [x] Sistema de compartir contenido (Web Share API)
- [ ] Sistema de comentarios y reseñas
- [ ] Ratings y puntuaciones
- [ ] Seguimiento de otros usuarios
- [ ] Compartir listas

### Fase 4 - IA Avanzada
- [ ] Recomendaciones personalizadas
- [ ] Análisis de preferencias
- [ ] Notificaciones inteligentes
- [ ] Chatbot de recomendaciones

### Fase 5 - Marketplace de Productos
- [ ] **Marketplace de productos** basado en el engagement del contenido de actores
- [ ] Productos oficiales y merchandising
- [ ] Colaboraciones con marcas
- [ ] Sistema de afiliados

## 📊 Estado Actual

```
✅ Contenido: 2,340+ series, 5,860+ personas
✅ Hub de Actores: Entrevistas, biografías, noticias exclusivas
✅ Videos Externos: Soporte TikTok, YouTube, Instagram
✅ Interfaz: Netflix-style completamente funcional
✅ Responsive: Optimizado para móviles
✅ Localización: Español chileno completo
✅ Deploy: Script automatizado incluido
```

## 🛠️ Comandos Artisan Personalizados

### Importación de Contenido
```bash
# Importar por categorías
php artisan import:romance-dramas
php artisan import:recent-dramas
php artisan import:top-rated-dramas

# Traducir contenido existente
php artisan translate:existing-content

# Importación masiva
php artisan import:korean-dramas --pages=100 --with-details
```

### Contenido de Actores
```bash
# Generar contenido inicial de actores (entrevistas, biografías, noticias)
php artisan db:seed --class=ImprovedActorContentSeeder

# Agregar video externo a un actor
php artisan actors:add-video {actor_id} {video_url} --title="Título" --type=video
# Ejemplo:
php artisan actors:add-video 123 "https://youtube.com/watch?v=..." --title="Entrevista exclusiva" --type=interview

# Soporta TikTok, YouTube e Instagram
php artisan actors:add-video 456 "https://tiktok.com/@actor/video/..." --type=behind_scenes
```

### Tipos de Contenido Disponibles
- **interview**: Entrevistas transcritas (solo texto)
- **biography**: Biografías detalladas
- **news**: Noticias y novedades
- **article**: Artículos de análisis
- **timeline**: Cronologías de carrera
- **trivia**: Curiosidades y datos divertidos
- **social**: Contenido de redes sociales

**Nota importante**: El seeder genera solo contenido de texto auténtico. Para agregar videos reales, usa el comando `actors:add-video` con URLs válidas.

## 📁 Estructura del Proyecto

```
dorasia/
├── app/
│   ├── Console/Commands/     # Comandos de importación
│   ├── Http/Controllers/     # Controladores principales
│   ├── Models/              # Modelos de datos
│   └── Services/            # Servicios (TMDB, Traducción)
├── resources/views/         # Templates Blade
├── database/migrations/     # Esquema de base de datos
├── public/                  # Archivos públicos
├── deploy.sh               # Script de deploy
└── DEPLOY_GUIDE.md         # Guía detallada
```

## 🎯 Para Fanáticos Chilenos

Esta plataforma está específicamente diseñada para la comunidad chilena de K-Drama fans, incluyendo:

- **Terminología local** y expresiones chilenas
- **Horarios GMT-3** (Chile Continental)
- **Recomendaciones culturalmente relevantes**
- **Interfaz familiar** para usuarios chilenos

## 📞 Soporte

- **Documentación**: Ver `DEPLOY_GUIDE.md`
- **Logs**: Revisar `storage/logs/laravel.log`
- **API Issues**: Verificar configuración TMDB

---

🤖 **Hecho con ❤️ para la comunidad K-Drama de Chile**

*DORASIA - Donde la inteligencia artificial se encuentra con la pasión por los K-Dramas*