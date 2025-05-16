-- =====================================================
-- DorasIA - Configuración Mínima para Producción
-- Fecha: 2025-05-16
-- Versión: 1.0.0
-- =====================================================

-- Este script contiene solo las tablas esenciales
-- para un MVP en producción

USE dorasia_db;

-- =====================================================
-- Configuración de Performance
-- =====================================================

-- Optimizaciones para MySQL/MariaDB
SET sql_mode = 'NO_ENGINE_SUBSTITUTION';
SET innodb_buffer_pool_size = '256M';
SET max_connections = 100;

-- =====================================================
-- Usuario Admin Inicial
-- =====================================================

-- Crear usuario admin (cambiar contraseña)
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Admin', 'admin@dorasia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Crear perfil para admin
INSERT INTO profiles (user_id, name, is_active, created_at, updated_at) VALUES
(1, 'Administrador', TRUE, NOW(), NOW());

-- =====================================================
-- Datos de Ejemplo Mínimos
-- =====================================================

-- Algunos títulos de ejemplo
INSERT INTO titles (title, original_title, slug, synopsis, type, year, poster_path, backdrop_path, tmdb_id, rating, popularity, created_at, updated_at) VALUES
('Vincenzo', 'Vincenzo', 'vincenzo', 'Un consigliere de la mafia coreano-italiano busca venganza.', 'series', 2021, '/posters/vincenzo.jpg', '/backdrops/vincenzo.jpg', 96777, 8.8, 95.5, NOW(), NOW()),
('El Reino', 'Kingdom', 'el-reino', 'Un príncipe lucha contra una plaga zombie en la era Joseon.', 'series', 2019, '/posters/kingdom.jpg', '/backdrops/kingdom.jpg', 70593, 8.3, 88.2, NOW(), NOW()),
('Alquimia de Almas', 'Alchemy of Souls', 'alquimia-de-almas', 'Una poderosa maga en el cuerpo de una mujer ciega.', 'series', 2022, '/posters/alchemy.jpg', '/backdrops/alchemy.jpg', 135157, 8.7, 92.1, NOW(), NOW());

-- =====================================================
-- Configuración de Índices para Performance
-- =====================================================

-- Índices compuestos para queries comunes
CREATE INDEX idx_titles_type_popularity ON titles(type, popularity DESC);
CREATE INDEX idx_titles_year_rating ON titles(year DESC, rating DESC);
CREATE INDEX idx_profiles_user_active ON profiles(user_id, is_active);

-- =====================================================
-- Triggers para Mantenimiento Automático
-- =====================================================

-- Trigger para actualizar timestamp
DELIMITER $$
CREATE TRIGGER update_titles_timestamp 
BEFORE UPDATE ON titles 
FOR EACH ROW 
BEGIN
    SET NEW.updated_at = NOW();
END$$
DELIMITER ;

-- =====================================================
-- Procedimientos Almacenados Útiles
-- =====================================================

-- Procedimiento para obtener estadísticas
DELIMITER $$
CREATE PROCEDURE get_site_stats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM titles WHERE type = 'movie') as total_movies,
        (SELECT COUNT(*) FROM titles WHERE type = 'series') as total_series,
        (SELECT COUNT(*) FROM ratings) as total_ratings,
        (SELECT COUNT(*) FROM comments) as total_comments;
END$$
DELIMITER ;

-- =====================================================
-- Configuración de Seguridad
-- =====================================================

-- Crear usuario de aplicación con permisos limitados
-- CREATE USER 'dorasia_app'@'localhost' IDENTIFIED BY 'contraseña_segura';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON dorasia_db.* TO 'dorasia_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Configuración de Backup Automático
-- =====================================================

-- Event para limpieza automática de logs antiguos
DELIMITER $$
CREATE EVENT IF NOT EXISTS cleanup_old_logs
ON SCHEDULE EVERY 1 WEEK
DO
BEGIN
    DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY));
    DELETE FROM cron_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
END$$
DELIMITER ;

-- =====================================================
-- Verificación Final
-- =====================================================

-- Verificar tablas creadas
SELECT table_name, table_rows 
FROM information_schema.tables 
WHERE table_schema = 'dorasia_db' 
ORDER BY table_name;

-- =====================================================
-- Fin del script
-- =====================================================