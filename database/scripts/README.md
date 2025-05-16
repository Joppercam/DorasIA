# Scripts de Base de Datos - DorasIA

Este directorio contiene todos los scripts SQL para gestionar la base de datos de DorasIA.

## 📁 Estructura de Archivos

- `00_initial_schema.sql` - Esquema inicial completo de la base de datos
- `01_updates_*.sql` - Actualizaciones incrementales
- `02_data_*.sql` - Scripts de datos (seeders)
- `99_maintenance_*.sql` - Scripts de mantenimiento

## 🚀 Uso

### Instalación Inicial

1. **Crear la base de datos en tu servidor:**
   ```sql
   CREATE DATABASE dorasia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Ejecutar el script inicial:**
   ```bash
   mysql -u tu_usuario -p dorasia_db < database/scripts/00_initial_schema.sql
   ```

### Actualizaciones

Los scripts de actualización deben ejecutarse en orden:
```bash
mysql -u tu_usuario -p dorasia_db < database/scripts/01_update_add_features.sql
```

### Con Laravel Migrations

Alternativamente, puedes usar las migraciones de Laravel:
```bash
php artisan migrate
```

## 📋 Convenciones de Nombres

- `00-09` - Scripts de estructura principal
- `10-19` - Scripts de datos iniciales
- `20-79` - Scripts de actualización
- `80-89` - Scripts de mantenimiento
- `90-99` - Scripts especiales o temporales

## 🔧 Scripts Útiles

### Backup
```bash
mysqldump -u usuario -p dorasia_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar
```bash
mysql -u usuario -p dorasia_db < backup.sql
```

### Verificar estructura
```sql
SHOW TABLES;
DESCRIBE nombre_tabla;
```

## ⚠️ Notas Importantes

1. **Siempre haz backup** antes de ejecutar scripts en producción
2. **Verifica** la compatibilidad con tu versión de MySQL/MariaDB
3. **Revisa** los scripts antes de ejecutarlos
4. **Documenta** cualquier cambio manual en la base de datos

## 📝 Historial de Cambios

### v1.0.0 (2025-05-16)
- Esquema inicial completo
- Tablas de usuarios, perfiles, contenido
- Sistema de watchlist y ratings
- Módulo de noticias

---

¿Necesitas agregar un nuevo script? Sigue la convención de nombres y actualiza este README.