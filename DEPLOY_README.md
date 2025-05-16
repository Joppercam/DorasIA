# Opciones de Deployment para Dorasia

Este proyecto incluye varias opciones para hacer deployment según tu tipo de hosting:

## 1. Script Bash Completo (deploy-script.sh)

**Ideal para:** Servidores VPS con acceso SSH completo

**Características:**
- Verificación de Git
- Compilación de assets
- Sincronización con rsync
- Ejecución remota de comandos
- Verificación post-deploy

**Uso:**
```bash
# Configurar las variables en el archivo
chmod +x deploy-script.sh
./deploy-script.sh
```

## 2. Script Bash Simple (deploy-simple.sh)

**Ideal para:** Hosting compartido con SSH limitado

**Características:**
- Versión simplificada
- Solo lo esencial
- Menos verificaciones

**Uso:**
```bash
chmod +x deploy-simple.sh
./deploy-simple.sh
```

## 3. Script Python FTP (deploy-ftp.py)

**Ideal para:** Hosting sin SSH, solo FTP

**Características:**
- Subida por FTP
- Manejo de directorios
- Filtrado de archivos

**Uso:**
```bash
python3 deploy-ftp.py
```

## 4. Deployment Manual

Si prefieres hacerlo manualmente, revisa `DEPLOY_MANUAL.md`

## Configuración Inicial

Antes de usar cualquier script:

1. **Edita las variables de configuración** en el script elegido:
   - Usuario SSH/FTP
   - Servidor/Host
   - Rutas
   - Credenciales

2. **Crea un archivo .env en el servidor** con las configuraciones de producción

3. **Asegúrate de tener las herramientas necesarias**:
   - SSH: `ssh` y `rsync`
   - FTP: `python3` con librería `ftplib`

## Recomendaciones de Seguridad

1. **Nunca incluyas credenciales en los scripts**
2. **Usa SSH keys** en lugar de contraseñas cuando sea posible
3. **Mantén backups** antes de cada deployment
4. **Usa .deployignore** para excluir archivos sensibles

## Flujo Recomendado

1. Hacer cambios localmente
2. Probar en desarrollo
3. Commitear cambios en Git
4. Ejecutar script de deployment
5. Verificar en producción

## Troubleshooting

### Error de permisos
```bash
chmod -R 755 storage bootstrap/cache
```

### Error de caché
```bash
php artisan cache:clear
php artisan config:clear
```

### Error 500
- Revisa logs: `storage/logs/laravel.log`
- Verifica archivo `.env`
- Confirma permisos de directorios