# 🗃️ DATABASE SCHEMA - DORASIA

## 📊 Visión General

**Dorasia** utiliza una base de datos relacional optimizada para contenido audiovisual coreano con soporte completo para multiidioma y interacciones de usuario.

### Estadísticas Actuales
- **Tablas**: 18 principales + 7 de relaciones
- **Registros**: 650+ series, 130+ películas, 3,950+ actores
- **Motor**: SQLite (desarrollo) / MySQL (producción)
- **Encoding**: UTF-8 para soporte completo de caracteres coreanos

---

## 📋 TABLAS PRINCIPALES

### 🎭 **series**
Almacena información de K-Dramas y series coreanas

```sql
CREATE TABLE series (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,                    -- Título original
    title_es TEXT NULL,                             -- Título traducido literal
    spanish_title TEXT NULL,                        -- Título comercial en español
    original_title VARCHAR(255) NULL,               -- Título en hangul
    overview TEXT NULL,                             -- Sinopsis original
    overview_es TEXT NULL,                          -- Sinopsis traducida literal
    spanish_overview TEXT NULL,                     -- Sinopsis comercial en español
    first_air_date DATE NULL,                       -- Fecha de estreno
    last_air_date DATE NULL,                        -- Fecha final
    status VARCHAR(50) NULL,                        -- 'ended', 'returning', 'in_production'
    vote_average DECIMAL(3,1) DEFAULT 0.0,         -- Calificación TMDB (0.0-10.0)
    vote_count INT DEFAULT 0,                       -- Número de votos TMDB
    popularity DECIMAL(8,3) DEFAULT 0.0,           -- Popularidad TMDB
    poster_path VARCHAR(255) NULL,                  -- Ruta del poster (/abc123.jpg)
    backdrop_path VARCHAR(255) NULL,               -- Ruta del backdrop
    number_of_seasons INT DEFAULT 0,               -- Número de temporadas
    number_of_episodes INT DEFAULT 0,              -- Número total de episodios
    episode_run_time JSON NULL,                    -- Duración promedio [45, 50]
    original_language VARCHAR(10) DEFAULT 'ko',    -- Idioma original
    origin_country VARCHAR(255) DEFAULT 'KR',      -- País de origen
    tmdb_id INT UNIQUE NULL,                        -- ID en TMDB
    imdb_id VARCHAR(20) NULL,                       -- ID en IMDB
    homepage VARCHAR(500) NULL,                     -- Sitio web oficial
    tagline TEXT NULL,                              -- Tagline/eslogan
    is_korean_drama BOOLEAN DEFAULT TRUE,          -- Flag para K-Dramas
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para optimización
    INDEX idx_popularity (popularity DESC),
    INDEX idx_vote_average (vote_average DESC),
    INDEX idx_first_air_date (first_air_date DESC),
    INDEX idx_tmdb_id (tmdb_id),
    INDEX idx_spanish_title (spanish_title(50)),
    FULLTEXT idx_search (title, spanish_title, overview_es, spanish_overview)
);
```

#### Campos Destacados
- **Traducciones Múltiples**: `title_es` (literal) vs `spanish_title` (comercial)
- **Búsqueda Optimizada**: Índice FULLTEXT para búsquedas rápidas
- **Metadatos Completos**: Información de TMDB + datos localizados

### 🎬 **movies**
Almacena información de películas coreanas

```sql
CREATE TABLE movies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,                    -- Título original
    title_es TEXT NULL,                             -- Título traducido literal
    spanish_title TEXT NULL,                        -- Título comercial español
    original_title VARCHAR(255) NULL,               -- Título en hangul
    overview TEXT NULL,                             -- Sinopsis original
    overview_es TEXT NULL,                          -- Sinopsis traducida
    spanish_overview TEXT NULL,                     -- Sinopsis comercial
    release_date DATE NULL,                         -- Fecha de estreno
    runtime INT NULL,                               -- Duración en minutos
    vote_average DECIMAL(3,1) DEFAULT 0.0,         -- Calificación TMDB
    vote_count INT DEFAULT 0,                       -- Número de votos
    popularity DECIMAL(8,3) DEFAULT 0.0,           -- Popularidad TMDB
    poster_path VARCHAR(255) NULL,                  -- Poster image
    backdrop_path VARCHAR(255) NULL,               -- Backdrop image
    status VARCHAR(50) DEFAULT 'released',         -- Estado de lanzamiento
    original_language VARCHAR(10) DEFAULT 'ko',    -- Idioma original
    tmdb_id INT UNIQUE NULL,                        -- ID TMDB
    imdb_id VARCHAR(20) NULL,                       -- ID IMDB
    budget DECIMAL(15,2) DEFAULT 0,                -- Presupuesto
    revenue DECIMAL(15,2) DEFAULT 0,               -- Recaudación
    tagline TEXT NULL,                              -- Tagline
    adult BOOLEAN DEFAULT FALSE,                   -- Contenido adulto
    production_companies JSON NULL,                -- Productoras
    production_countries JSON NULL,                -- Países productores
    spoken_languages JSON NULL,                    -- Idiomas hablados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_popularity (popularity DESC),
    INDEX idx_vote_average (vote_average DESC),
    INDEX idx_release_date (release_date DESC),
    INDEX idx_runtime (runtime),
    FULLTEXT idx_movie_search (title, spanish_title, overview_es, spanish_overview)
);
```

### 👤 **people** (Actores/Personal)
Información de actores, directores y personal de producción

```sql
CREATE TABLE people (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                     -- Nombre original
    name_es VARCHAR(255) NULL,                      -- Nombre en español (si aplica)
    known_for_department VARCHAR(100) NULL,        -- 'Acting', 'Directing', etc.
    biography TEXT NULL,                            -- Biografía original
    biography_es TEXT NULL,                         -- Biografía en español
    birthday DATE NULL,                             -- Fecha de nacimiento
    deathday DATE NULL,                             -- Fecha de fallecimiento
    place_of_birth VARCHAR(255) NULL,              -- Lugar de nacimiento original
    place_of_birth_es VARCHAR(255) NULL,           -- Lugar en español
    profile_path VARCHAR(255) NULL,                -- Foto de perfil
    tmdb_id INT UNIQUE NULL,                        -- ID TMDB
    imdb_id VARCHAR(20) NULL,                       -- ID IMDB
    popularity DECIMAL(8,3) DEFAULT 0.0,           -- Popularidad TMDB
    adult BOOLEAN DEFAULT FALSE,                   -- Contenido adulto
    gender TINYINT DEFAULT 0,                      -- 0=unknown, 1=female, 2=male
    homepage VARCHAR(500) NULL,                     -- Sitio web personal
    also_known_as JSON NULL,                       -- Nombres alternativos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_popularity (popularity DESC),
    INDEX idx_known_for (known_for_department),
    INDEX idx_gender (gender),
    FULLTEXT idx_people_search (name, name_es, biography_es)
);
```

### 📺 **seasons** & **episodes**
Estructura jerárquica de temporadas y episodios

```sql
CREATE TABLE seasons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    series_id BIGINT UNSIGNED NOT NULL,            -- FK a series
    season_number INT NOT NULL,                     -- Número de temporada
    name VARCHAR(255) NULL,                         -- Nombre de temporada
    overview TEXT NULL,                             -- Descripción de temporada
    air_date DATE NULL,                             -- Fecha de emisión
    episode_count INT DEFAULT 0,                   -- Número de episodios
    poster_path VARCHAR(255) NULL,                 -- Poster de temporada
    vote_average DECIMAL(3,1) DEFAULT 0.0,        -- Calificación promedio
    tmdb_id INT NULL,                              -- ID TMDB de temporada
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    UNIQUE KEY unique_season (series_id, season_number),
    INDEX idx_series_season (series_id, season_number)
);

CREATE TABLE episodes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    series_id BIGINT UNSIGNED NOT NULL,            -- FK a series
    season_id BIGINT UNSIGNED NOT NULL,            -- FK a seasons
    episode_number INT NOT NULL,                   -- Número de episodio
    season_number INT NOT NULL,                    -- Número de temporada
    name VARCHAR(255) NULL,                        -- Título del episodio
    overview TEXT NULL,                            -- Sinopsis del episodio
    air_date DATE NULL,                            -- Fecha de emisión
    runtime INT NULL,                              -- Duración en minutos
    still_path VARCHAR(255) NULL,                 -- Imagen del episodio
    vote_average DECIMAL(3,1) DEFAULT 0.0,        -- Calificación
    vote_count INT DEFAULT 0,                     -- Número de votos
    tmdb_id INT NULL,                             -- ID TMDB
    guest_stars JSON NULL,                        -- Estrellas invitadas
    crew JSON NULL,                               -- Crew del episodio
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_episode (series_id, season_number, episode_number),
    INDEX idx_series_episode (series_id, season_number, episode_number),
    INDEX idx_air_date (air_date)
);
```

### 🏷️ **genres** 
Géneros con soporte multiidioma

```sql
CREATE TABLE genres (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,                    -- Nombre original (inglés)
    display_name VARCHAR(100) NULL,               -- Nombre en español
    name_es VARCHAR(100) NULL,                    -- Traducción alternativa
    tmdb_id INT UNIQUE NULL,                      -- ID en TMDB
    description TEXT NULL,                        -- Descripción del género
    is_korean_specific BOOLEAN DEFAULT FALSE,    -- Géneros específicos K-Drama
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_name (name),
    INDEX idx_display_name (display_name)
);
```

---

## 👥 SISTEMA DE USUARIOS

### 🔐 **users**
Información de usuarios registrados

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Nombre completo
    email VARCHAR(255) UNIQUE NOT NULL,           -- Email único
    email_verified_at TIMESTAMP NULL,             -- Verificación de email
    password VARCHAR(255) NOT NULL,               -- Hash de contraseña
    remember_token VARCHAR(100) NULL,             -- Token de "recordarme"
    google_id VARCHAR(255) NULL,                  -- ID de Google OAuth
    avatar VARCHAR(500) NULL,                     -- URL del avatar
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_google_id (google_id)
);
```

### ⭐ **title_ratings** (Calificaciones)
Sistema de ratings para series y películas

```sql
CREATE TABLE title_ratings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,             -- FK a users
    ratable_type VARCHAR(255) NOT NULL,           -- 'App\Models\Series' o 'App\Models\Movie'
    ratable_id BIGINT UNSIGNED NOT NULL,          -- ID de la serie/película
    rating_type ENUM('love', 'like', 'dislike') NOT NULL, -- Tipo de rating
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_rating (user_id, ratable_type, ratable_id),
    INDEX idx_ratable (ratable_type, ratable_id),
    INDEX idx_rating_type (rating_type)
);
```

### 📝 **watchlist**
Lista de seguimiento de usuarios

```sql
CREATE TABLE watchlist (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,             -- FK a users
    watchlistable_type VARCHAR(255) NOT NULL,     -- Tipo de contenido
    watchlistable_id BIGINT UNSIGNED NOT NULL,    -- ID del contenido
    status ENUM('pending', 'watching', 'completed', 'dropped') DEFAULT 'pending',
    priority TINYINT DEFAULT 5,                   -- Prioridad 1-10
    notes TEXT NULL,                              -- Notas personales
    started_at TIMESTAMP NULL,                   -- Cuándo empezó a ver
    completed_at TIMESTAMP NULL,                 -- Cuándo terminó
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_watchlist (user_id, watchlistable_type, watchlistable_id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_watchlistable (watchlistable_type, watchlistable_id)
);
```

### 📊 **episode_progress**
Progreso de visualización de episodios

```sql
CREATE TABLE episode_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,             -- FK a users
    episode_id BIGINT UNSIGNED NOT NULL,          -- FK a episodes
    series_id BIGINT UNSIGNED NOT NULL,           -- FK a series (desnormalizado para queries)
    progress_percentage TINYINT DEFAULT 0,        -- Porcentaje visto (0-100)
    watch_time_seconds INT DEFAULT 0,             -- Tiempo visto en segundos
    is_completed BOOLEAN DEFAULT FALSE,           -- Si terminó el episodio
    last_watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Última vez que vio
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE,
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_episode (user_id, episode_id),
    INDEX idx_user_series (user_id, series_id),
    INDEX idx_last_watched (last_watched_at)
);
```

---

## 🔗 TABLAS DE RELACIONES

### 🎭 **series_genre** & **movie_genre**
Relaciones many-to-many entre contenido y géneros

```sql
CREATE TABLE series_genre (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    series_id BIGINT UNSIGNED NOT NULL,
    genre_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE,
    UNIQUE KEY unique_series_genre (series_id, genre_id)
);

CREATE TABLE movie_genre (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    genre_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE,
    UNIQUE KEY unique_movie_genre (movie_id, genre_id)
);
```

### 👥 **series_person** & **movie_person**
Relaciones entre contenido y personas (cast/crew)

```sql
CREATE TABLE series_person (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    series_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    role ENUM('actor', 'director', 'writer', 'producer', 'other') DEFAULT 'actor',
    character VARCHAR(255) NULL,                   -- Personaje interpretado
    department VARCHAR(100) NULL,                 -- Departamento (Acting, Directing, etc.)
    job VARCHAR(100) NULL,                        -- Trabajo específico
    order_index TINYINT DEFAULT 0,               -- Orden en créditos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    UNIQUE KEY unique_series_person_role (series_id, person_id, role),
    INDEX idx_series_role (series_id, role),
    INDEX idx_person_role (person_id, role)
);

CREATE TABLE movie_person (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    character VARCHAR(255) NULL,
    department VARCHAR(100) NULL,
    job VARCHAR(100) NULL,
    order_index TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    INDEX idx_movie_person (movie_id, person_id),
    INDEX idx_person_movies (person_id)
);
```

---

## 💬 SISTEMA DE COMENTARIOS

### 📝 **comments**
Sistema de comentarios polimórfico

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,             -- FK a users
    commentable_type VARCHAR(255) NOT NULL,       -- Tipo de modelo comentado
    commentable_id BIGINT UNSIGNED NOT NULL,      -- ID del modelo comentado
    content TEXT NOT NULL,                        -- Contenido del comentario
    rating TINYINT NULL,                          -- Rating opcional (1-5)
    is_approved BOOLEAN DEFAULT TRUE,             -- Moderación
    parent_id BIGINT UNSIGNED NULL,              -- Para comentarios anidados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_user_comments (user_id),
    INDEX idx_approved (is_approved),
    FULLTEXT idx_content (content)
);
```

---

## 📅 CONTENIDO FUTURO

### 🔮 **upcoming_series**
Próximos estrenos de K-Dramas

```sql
CREATE TABLE upcoming_series (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,                  -- Título anunciado
    spanish_title VARCHAR(255) NULL,             -- Título en español
    air_date DATE NULL,                           -- Fecha de estreno estimada
    status ENUM('announced', 'filming', 'post_production', 'completed') DEFAULT 'announced',
    network VARCHAR(100) NULL,                   -- Canal/plataforma
    episode_count INT NULL,                      -- Episodios estimados
    overview TEXT NULL,                          -- Sinopsis
    poster_path VARCHAR(255) NULL,              -- Poster promocional
    tmdb_id INT UNIQUE NULL,                     -- ID TMDB si existe
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_air_date (air_date),
    INDEX idx_status (status),
    INDEX idx_network (network)
);
```

---

## 🔧 OPTIMIZACIONES Y PERFORMANCE

### Índices Estratégicos
```sql
-- Búsquedas frecuentes
CREATE INDEX idx_series_popularity_rating ON series (popularity DESC, vote_average DESC);
CREATE INDEX idx_movies_recent ON movies (release_date DESC, popularity DESC);
CREATE INDEX idx_people_acting ON people (known_for_department, popularity DESC);

-- Queries de usuario
CREATE INDEX idx_watchlist_user_status ON watchlist (user_id, status, updated_at DESC);
CREATE INDEX idx_episode_progress_user ON episode_progress (user_id, last_watched_at DESC);
CREATE INDEX idx_ratings_user_type ON title_ratings (user_id, rating_type, created_at DESC);

-- Queries de contenido
CREATE INDEX idx_series_korean ON series (is_korean_drama, vote_average DESC);
CREATE INDEX idx_episodes_series_order ON episodes (series_id, season_number, episode_number);
```

### Particionado (Para Futuro)
```sql
-- Particionar comments por fecha para mejor performance
ALTER TABLE comments PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

---

## 🔍 QUERIES COMUNES OPTIMIZADAS

### Series Populares con Géneros
```sql
SELECT s.id, s.spanish_title, s.vote_average, 
       GROUP_CONCAT(g.display_name) as genres
FROM series s
LEFT JOIN series_genre sg ON s.id = sg.series_id
LEFT JOIN genres g ON sg.genre_id = g.id
WHERE s.is_korean_drama = TRUE
GROUP BY s.id
ORDER BY s.popularity DESC, s.vote_average DESC
LIMIT 20;
```

### Progreso de Usuario en Series
```sql
SELECT s.spanish_title, 
       COUNT(e.id) as total_episodes,
       COUNT(ep.id) as watched_episodes,
       ROUND((COUNT(ep.id) / COUNT(e.id)) * 100, 1) as completion_percentage
FROM series s
JOIN episodes e ON s.id = e.series_id
LEFT JOIN episode_progress ep ON e.id = ep.episode_id AND ep.user_id = ?
WHERE s.id IN (
    SELECT watchlistable_id FROM watchlist 
    WHERE user_id = ? AND watchlistable_type = 'App\\Models\\Series'
)
GROUP BY s.id, s.spanish_title;
```

### Top Actores por Popularidad
```sql
SELECT p.name, p.popularity, 
       COUNT(DISTINCT sp.series_id) as series_count,
       COUNT(DISTINCT mp.movie_id) as movies_count
FROM people p
LEFT JOIN series_person sp ON p.id = sp.person_id AND sp.role = 'actor'
LEFT JOIN movie_person mp ON p.id = mp.person_id
WHERE p.known_for_department = 'Acting'
GROUP BY p.id
ORDER BY p.popularity DESC
LIMIT 50;
```

---

## 📊 ESTADÍSTICAS DE BASE DE DATOS

### Tamaños Estimados (Datos Actuales)
```
series: ~50 MB (643 registros)
movies: ~15 MB (130 registros)  
people: ~200 MB (3,950 registros)
episodes: ~300 MB (~15,000 episodios estimados)
comments: ~10 MB (crecimiento variable)
users: ~1 MB (datos de usuario)
```

### Crecimiento Proyectado
- **Series**: +50 por mes
- **Películas**: +20 por mes
- **Actores**: +100 por mes
- **Usuarios**: Variable según adopción
- **Comentarios**: 10x crecimiento con más usuarios

---

## 🚀 MIGRACIONES FUTURAS RECOMENDADAS

### Performance
```sql
-- Agregar columnas calculadas para performance
ALTER TABLE series ADD COLUMN cache_rating_average DECIMAL(3,1) GENERATED ALWAYS AS (
    IFNULL((SELECT AVG(CASE rating_type 
                           WHEN 'love' THEN 5 
                           WHEN 'like' THEN 3.5 
                           WHEN 'dislike' THEN 1 
                           END) 
            FROM title_ratings 
            WHERE ratable_id = series.id AND ratable_type = 'App\\Models\\Series'), 0)
) STORED;
```

### Nuevas Características
```sql
-- Tags personalizados de usuarios
CREATE TABLE user_tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    taggable_type VARCHAR(255) NOT NULL,
    taggable_id BIGINT UNSIGNED NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_tag (user_id, taggable_type, taggable_id, tag_name)
);

-- Sistema de notificaciones
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_notifiable (notifiable_type, notifiable_id),
    INDEX idx_read_at (read_at)
);
```

---

**Documentación de Base de Datos - Versión 1.0**  
**Última Actualización**: Junio 2025  
**Total de Tablas**: 25 tablas principales + relaciones  
**Encoding**: UTF-8 (soporte completo coreano)