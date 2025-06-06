# 🗄️ Configuración SQLite para DORASIA

## ✅ Ventajas de SQLite en hosting compartido

- **Sin configuración**: No necesitas crear bases de datos MySQL
- **Sin passwords**: No hay credenciales que configurar
- **Archivo único**: Todo en `database/database.sqlite`
- **Sin foreign keys**: Evita problemas de restricciones
- **Perfecto para hosting compartido**

## 📋 Pasos actualizados para el deploy

### 1. Conectar y clonar (igual que antes)
```bash
cd /home/n91a0e5/
git clone https://github.com/Joppercam/DorasIA.git dorasia.cl
cd dorasia.cl
```

### 2. Configurar SQLite (.env ya está listo)
```bash
# El .env.production ya viene configurado para SQLite
cp .env.production .env

# Solo necesitas agregar tu API key de TMDB
nano .env
# Editar: TMDB_API_KEY=tu_clave_aqui
```

### 3. Ejecutar deploy (funciona automáticamente)
```bash
# Usando PHP 8.4 como configuraste antes
alias php84="/opt/cpanel/ea-php84/root/usr/bin/php"

# El script ahora crea automáticamente la base SQLite
php84 /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader
php84 artisan key:generate --force

# Crear base de datos SQLite
touch database/database.sqlite
chmod 664 database/database.sqlite

# Ejecutar migraciones (ahora funcionarán sin problemas)
php84 artisan migrate --force
```

### 4. Optimizar y cachear
```bash
php84 artisan config:cache
php84 artisan route:cache
php84 artisan view:cache
```

### 5. Importar contenido
```bash
# Ahora las importaciones deberían funcionar perfectamente
php84 artisan import:korean-dramas --pages=30
```

## 🔧 Comandos útiles para SQLite

### Verificar la base de datos:
```bash
# Ver tamaño del archivo
ls -lh database/database.sqlite

# Verificar contenido
php84 artisan tinker --execute="echo 'Títulos: ' . App\Models\Title::count();"
```

### Hacer backup:
```bash
# SQLite es un solo archivo, muy fácil de respaldar
cp database/database.sqlite backup_$(date +%Y%m%d).sqlite
```

### Resetear si es necesario:
```bash
# Eliminar base y volver a migrar
rm database/database.sqlite
touch database/database.sqlite
chmod 664 database/database.sqlite
php84 artisan migrate --force
```

## ⚡ Ventajas adicionales

1. **Performance**: SQLite es muy rápido para sitios web
2. **Confiabilidad**: Sin desconexiones de red a MySQL
3. **Simplicidad**: Un solo archivo para toda la data
4. **Portabilidad**: Fácil de mover entre servidores

## 📊 Qué esperar

Con SQLite configurado:
- ✅ Migraciones funcionarán sin errores
- ✅ Foreign keys manejadas automáticamente
- ✅ Importación de contenido sin problemas
- ✅ Mejor performance en hosting compartido

---

🤖 **DORASIA con SQLite - Simple y poderoso** ✨