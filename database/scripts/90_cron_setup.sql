-- =====================================================
-- DorasIA - Script de Configuración para Cron Jobs
-- Fecha: 2025-05-16
-- Versión: 1.0.0
-- =====================================================

-- Este script crea una tabla para gestionar los cron jobs
-- y registrar su ejecución

USE dorasia_db;

-- Tabla para registrar cron jobs
CREATE TABLE IF NOT EXISTS cron_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    command VARCHAR(500) NOT NULL,
    schedule VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_run_at TIMESTAMP NULL,
    next_run_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY cron_jobs_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para logs de cron jobs
CREATE TABLE IF NOT EXISTS cron_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cron_job_id BIGINT UNSIGNED NOT NULL,
    status ENUM('success', 'failed', 'running') NOT NULL,
    output TEXT NULL,
    error TEXT NULL,
    started_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    finished_at TIMESTAMP NULL,
    duration INT NULL,
    created_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (cron_job_id) REFERENCES cron_jobs(id) ON DELETE CASCADE,
    INDEX cron_logs_cron_job_id_index (cron_job_id),
    INDEX cron_logs_status_index (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar cron jobs predefinidos
INSERT INTO cron_jobs (name, command, schedule, is_active) VALUES
('sync_tmdb_content', 'php artisan tmdb:sync', '0 2 * * *', TRUE),
('cleanup_old_sessions', 'php artisan session:gc', '0 3 * * *', TRUE),
('generate_sitemap', 'php artisan sitemap:generate', '0 1 * * *', TRUE),
('cleanup_temp_files', 'php artisan cleanup:temp', '0 4 * * *', TRUE),
('backup_database', 'mysqldump dorasia_db > backup_daily.sql', '0 5 * * *', TRUE);

-- Procedimiento para actualizar el estado de un cron job
DELIMITER $$
CREATE PROCEDURE update_cron_status(
    IN job_name VARCHAR(255),
    IN job_status VARCHAR(20),
    IN job_output TEXT,
    IN job_error TEXT
)
BEGIN
    DECLARE job_id BIGINT;
    DECLARE log_id BIGINT;
    
    -- Obtener ID del cron job
    SELECT id INTO job_id FROM cron_jobs WHERE name = job_name;
    
    IF job_id IS NOT NULL THEN
        -- Actualizar última ejecución
        UPDATE cron_jobs 
        SET last_run_at = NOW() 
        WHERE id = job_id;
        
        -- Insertar log
        INSERT INTO cron_logs (cron_job_id, status, output, error, started_at)
        VALUES (job_id, job_status, job_output, job_error, NOW());
        
        SET log_id = LAST_INSERT_ID();
        
        -- Si el job terminó, actualizar tiempo de finalización
        IF job_status != 'running' THEN
            UPDATE cron_logs 
            SET finished_at = NOW(),
                duration = TIMESTAMPDIFF(SECOND, started_at, NOW())
            WHERE id = log_id;
        END IF;
    END IF;
END$$
DELIMITER ;

-- Vista para monitorear cron jobs
CREATE VIEW cron_job_status AS
SELECT 
    cj.id,
    cj.name,
    cj.command,
    cj.schedule,
    cj.is_active,
    cj.last_run_at,
    cl.status AS last_status,
    cl.duration AS last_duration,
    cl.error AS last_error
FROM cron_jobs cj
LEFT JOIN (
    SELECT cron_job_id, status, duration, error,
           ROW_NUMBER() OVER (PARTITION BY cron_job_id ORDER BY started_at DESC) as rn
    FROM cron_logs
) cl ON cj.id = cl.cron_job_id AND cl.rn = 1
ORDER BY cj.name;

-- =====================================================
-- Scripts de Shell para Cron
-- =====================================================

-- Script wrapper para ejecutar comandos Laravel con logging
/*
#!/bin/bash
# Guardar como: /home/usuario/cron-wrapper.sh

JOB_NAME=$1
COMMAND=$2
LOG_FILE="/home/usuario/logs/cron_${JOB_NAME}_$(date +%Y%m%d).log"

# Iniciar job
mysql -u usuario -p'password' dorasia_db -e "CALL update_cron_status('$JOB_NAME', 'running', NULL, NULL)"

# Ejecutar comando
OUTPUT=$($COMMAND 2>&1)
EXIT_CODE=$?

# Guardar resultado
if [ $EXIT_CODE -eq 0 ]; then
    STATUS="success"
    ERROR=NULL
else
    STATUS="failed"
    ERROR="'$OUTPUT'"
fi

# Actualizar estado en DB
mysql -u usuario -p'password' dorasia_db -e "CALL update_cron_status('$JOB_NAME', '$STATUS', '$OUTPUT', $ERROR)"

# Log to file
echo "[$(date)] $JOB_NAME - $STATUS" >> $LOG_FILE
echo "$OUTPUT" >> $LOG_FILE
echo "---" >> $LOG_FILE
*/

-- =====================================================
-- Configuración de Crontab
-- =====================================================

/*
# Agregar estas líneas a crontab (crontab -e)

# Sincronización con TMDB (2:00 AM diario)
0 2 * * * /home/usuario/cron-wrapper.sh "sync_tmdb_content" "cd /home/usuario/public_html && php artisan tmdb:sync"

# Limpieza de sesiones (3:00 AM diario)
0 3 * * * /home/usuario/cron-wrapper.sh "cleanup_old_sessions" "cd /home/usuario/public_html && php artisan session:gc"

# Generar sitemap (1:00 AM diario)
0 1 * * * /home/usuario/cron-wrapper.sh "generate_sitemap" "cd /home/usuario/public_html && php artisan sitemap:generate"

# Backup de base de datos (5:00 AM diario)
0 5 * * * /home/usuario/cron-wrapper.sh "backup_database" "mysqldump -u usuario -p'password' dorasia_db > /home/usuario/backups/dorasia_$(date +\%Y\%m\%d).sql"

# Sincronización Git (cada 5 minutos)
*/5 * * * * cd /home/usuario/public_html && git pull origin production > /home/usuario/logs/git_sync.log 2>&1
*/

-- =====================================================
-- Índices para optimización
-- =====================================================

CREATE INDEX cron_logs_started_at_index ON cron_logs(started_at DESC);
CREATE INDEX cron_jobs_last_run_at_index ON cron_jobs(last_run_at);

-- =====================================================
-- Fin del script
-- =====================================================