-- =====================================================
-- DorasIA - Script de Base de Datos Inicial
-- Fecha: 2025-05-16
-- Versión: 1.0.0
-- =====================================================

-- Eliminar la base de datos si existe (CUIDADO en producción)
-- DROP DATABASE IF EXISTS dorasia_db;

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS dorasia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dorasia_db;

-- =====================================================
-- Tablas de Usuario y Autenticación
-- =====================================================

-- Tabla de usuarios
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX users_email_index (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de perfiles de usuario
CREATE TABLE profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    bio TEXT NULL,
    location VARCHAR(255) NULL,
    is_child BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_public BOOLEAN DEFAULT TRUE,
    allow_messages BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX profiles_user_id_index (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tokens de restablecimiento de contraseña
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de sesiones
CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    PRIMARY KEY (id),
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tablas de Contenido
-- =====================================================

-- Tabla de géneros
CREATE TABLE genres (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    tmdb_id INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX genres_slug_index (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de países
CREATE TABLE countries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(2) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    language VARCHAR(5) DEFAULT 'es',
    country VARCHAR(2) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX categories_slug_index (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla principal de títulos (películas y series)
CREATE TABLE titles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255) NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    synopsis TEXT NULL,
    type ENUM('movie', 'series') NOT NULL,
    year INT NULL,
    duration INT NULL,
    poster_path VARCHAR(255) NULL,
    backdrop_path VARCHAR(255) NULL,
    trailer_url VARCHAR(255) NULL,
    homepage VARCHAR(255) NULL,
    imdb_id VARCHAR(20) NULL,
    tmdb_id INT NULL,
    status VARCHAR(50) NULL,
    release_date DATE NULL,
    rating DECIMAL(3,1) DEFAULT 0,
    vote_count INT DEFAULT 0,
    popularity DECIMAL(8,2) DEFAULT 0,
    adult BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    country VARCHAR(2) NULL,
    streaming_platform VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX titles_slug_index (slug),
    INDEX titles_type_index (type),
    INDEX titles_year_index (year),
    INDEX titles_tmdb_id_index (tmdb_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de temporadas (para series)
CREATE TABLE seasons (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title_id BIGINT UNSIGNED NOT NULL,
    season_number INT NOT NULL,
    name VARCHAR(255) NULL,
    overview TEXT NULL,
    poster_path VARCHAR(255) NULL,
    air_date DATE NULL,
    tmdb_id INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    INDEX seasons_title_id_index (title_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de episodios
CREATE TABLE episodes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    season_id BIGINT UNSIGNED NOT NULL,
    episode_number INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    overview TEXT NULL,
    air_date DATE NULL,
    duration INT NULL,
    still_path VARCHAR(255) NULL,
    tmdb_id INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
    INDEX episodes_season_id_index (season_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de personas (actores, directores, etc.)
CREATE TABLE people (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NULL,
    biography TEXT NULL,
    birthday DATE NULL,
    deathday DATE NULL,
    place_of_birth VARCHAR(255) NULL,
    profile_path VARCHAR(255) NULL,
    imdb_id VARCHAR(20) NULL,
    tmdb_id INT NULL,
    popularity DECIMAL(8,2) DEFAULT 0,
    gender INT NULL,
    known_for_department VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX people_slug_index (slug),
    INDEX people_name_index (name),
    INDEX people_tmdb_id_index (tmdb_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tablas de Relaciones
-- =====================================================

-- Relación título-género
CREATE TABLE title_genre (
    title_id BIGINT UNSIGNED NOT NULL,
    genre_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (title_id, genre_id),
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación título-persona (cast y crew)
CREATE TABLE title_person (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    type ENUM('cast', 'crew') NOT NULL,
    character_name VARCHAR(255) NULL,
    job VARCHAR(255) NULL,
    department VARCHAR(255) NULL,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    INDEX title_person_title_id_index (title_id),
    INDEX title_person_person_id_index (person_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tablas de Interacción de Usuario
-- =====================================================

-- Tabla de watchlist
CREATE TABLE watchlists (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    profile_id BIGINT UNSIGNED NOT NULL,
    title_id BIGINT UNSIGNED NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    priority INT DEFAULT 0,
    is_favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY watchlists_profile_title_unique (profile_id, title_id),
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    INDEX watchlists_profile_id_index (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de calificaciones
CREATE TABLE ratings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    profile_id BIGINT UNSIGNED NOT NULL,
    title_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(2,1) NOT NULL,
    review TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY ratings_profile_title_unique (profile_id, title_id),
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    INDEX ratings_profile_id_index (profile_id),
    INDEX ratings_title_id_index (title_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comentarios
CREATE TABLE comments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    profile_id BIGINT UNSIGNED NOT NULL,
    commentable_type VARCHAR(255) NOT NULL,
    commentable_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    content TEXT NOT NULL,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX comments_profile_id_index (profile_id),
    INDEX comments_commentable_index (commentable_type, commentable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de visualización
CREATE TABLE watch_histories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    profile_id BIGINT UNSIGNED NOT NULL,
    title_id BIGINT UNSIGNED NOT NULL,
    season_id BIGINT UNSIGNED NULL,
    episode_id BIGINT UNSIGNED NULL,
    watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT DEFAULT 0,
    duration INT DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE,
    INDEX watch_histories_profile_id_index (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tablas de Noticias y Social
-- =====================================================

-- Tabla de noticias
CREATE TABLE news (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    image VARCHAR(255) NULL,
    category VARCHAR(50) NULL,
    source VARCHAR(255) NULL,
    source_url VARCHAR(255) NULL,
    published_at TIMESTAMP NULL,
    views_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX news_slug_index (slug),
    INDEX news_published_at_index (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación noticias-personas
CREATE TABLE news_person (
    news_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (news_id, person_id),
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE notifications (
    id CHAR(36) NOT NULL,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX notifications_notifiable_index (notifiable_type, notifiable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Datos Iniciales
-- =====================================================

-- Insertar géneros básicos
INSERT INTO genres (name, slug, tmdb_id) VALUES
('Drama', 'drama', 18),
('Romance', 'romance', 10749),
('Comedia', 'comedia', 35),
('Acción', 'accion', 28),
('Aventura', 'aventura', 12),
('Ciencia Ficción', 'ciencia-ficcion', 878),
('Fantasía', 'fantasia', 14),
('Terror', 'terror', 27),
('Thriller', 'thriller', 53),
('Misterio', 'misterio', 9648),
('Documental', 'documental', 99),
('Animación', 'animacion', 16),
('Musical', 'musical', 10402),
('Western', 'western', 37),
('Bélica', 'belica', 10752),
('Histórica', 'historica', 36),
('Familiar', 'familiar', 10751);

-- Insertar categorías iniciales
INSERT INTO categories (name, slug, description, language, country) VALUES
('K-Dramas', 'k-dramas', 'Series de televisión coreanas', 'ko', 'KR'),
('J-Dramas', 'j-dramas', 'Series de televisión japonesas', 'ja', 'JP'),
('C-Dramas', 'c-dramas', 'Series de televisión chinas', 'zh', 'CN'),
('Thai-Dramas', 'thai-dramas', 'Series de televisión tailandesas', 'th', 'TH'),
('Películas Asiáticas', 'peliculas-asiaticas', 'Películas de Asia', 'es', NULL),
('Doramas Románticos', 'doramas-romanticos', 'Doramas con temática romántica', 'es', NULL),
('Nuevos Lanzamientos', 'nuevos-lanzamientos', 'Últimos estrenos', 'es', NULL),
('Populares', 'populares', 'Los más vistos', 'es', NULL);

-- =====================================================
-- Índices Adicionales para Optimización
-- =====================================================

CREATE INDEX titles_popularity_index ON titles(popularity DESC);
CREATE INDEX titles_release_date_index ON titles(release_date DESC);
CREATE INDEX news_category_index ON news(category);
CREATE INDEX ratings_score_index ON ratings(score DESC);

-- =====================================================
-- Vistas útiles (opcional)
-- =====================================================

-- Vista de títulos populares
CREATE VIEW popular_titles AS
SELECT 
    t.*,
    AVG(r.score) as average_rating,
    COUNT(DISTINCT r.id) as rating_count,
    COUNT(DISTINCT w.id) as watchlist_count
FROM titles t
LEFT JOIN ratings r ON t.id = r.title_id
LEFT JOIN watchlists w ON t.id = w.title_id
GROUP BY t.id
ORDER BY t.popularity DESC;

-- =====================================================
-- Configuración de Charset y Collation
-- =====================================================

ALTER DATABASE dorasia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =====================================================
-- Fin del script
-- =====================================================