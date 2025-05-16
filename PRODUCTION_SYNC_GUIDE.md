# Guía de Sincronización Automática con Hosting

## 🚀 Configuración de la Rama Production

Esta guía te ayudará a configurar sincronización automática entre GitHub y tu hosting usando la rama `production`.

## 📋 Estructura de Ramas

- `main` - Desarrollo principal
- `production` - Rama para producción (sincronizada con hosting)
- `mvp-production` - MVP específico

## 🔧 Métodos de Sincronización

### Método 1: GitHub Actions (Recomendado)

1. **Configurar Secretos en GitHub**:
   - Ve a Settings → Secrets → Actions
   - Agrega estos secretos:
     - `HOST` - IP o dominio de tu hosting
     - `USERNAME` - Usuario SSH
     - `PASSWORD` - Contraseña SSH (o usa SSH key)
     - `PORT` - Puerto SSH (normalmente 22)

2. **El workflow se activará automáticamente** cuando:
   - Hagas push a la rama `production`
   - Manualmente desde Actions → Run workflow

### Método 2: Webhook

1. **Subir archivos al hosting**:
   ```bash
   scp webhook-deploy.php usuario@hosting:/home/usuario/public_html/
   scp sync-production.sh usuario@hosting:/home/usuario/
   chmod +x /home/usuario/sync-production.sh
   ```

2. **Configurar webhook en GitHub**:
   - Settings → Webhooks → Add webhook
   - URL: `https://tu-dominio.com/webhook-deploy.php`
   - Content type: `application/json`
   - Secret: Genera uno seguro
   - Events: Solo `push`

3. **Actualizar configuración**:
   - Edita `webhook-deploy.php` con tu secret
   - Actualiza rutas en `sync-production.sh`

### Método 3: Cron Job

1. **Crear cron job en hosting**:
   ```bash
   crontab -e
   ```

2. **Agregar línea** (ejecuta cada 5 minutos):
   ```
   */5 * * * * /home/usuario/sync-production.sh > /home/usuario/cron.log 2>&1
   ```

## 🔄 Flujo de Trabajo

### Para actualizar producción:

1. **Desde main a production**:
   ```bash
   git checkout main
   git pull origin main
   git checkout production
   git merge main
   git push origin production
   ```

2. **La sincronización ocurrirá automáticamente**

### Script helper para actualizar:

```bash
#!/bin/bash
# update-production.sh

echo "📦 Actualizando rama production..."
git checkout main
git pull origin main

echo "🔀 Mergeando a production..."
git checkout production
git merge main -m "Update from main: $(date +'%Y-%m-%d %H:%M')"

echo "📤 Pushing a GitHub..."
git push origin production

echo "✅ Listo! El hosting se actualizará automáticamente."
```

## 🔒 Seguridad

1. **Para el webhook**:
   - Usa un secret fuerte
   - Valida siempre la firma
   - Limita acceso por IP si es posible

2. **Para SSH/GitHub Actions**:
   - Usa SSH keys en lugar de contraseñas
   - Limita permisos del usuario
   - No expongas credenciales

## 🛠️ Configuración Inicial en Hosting

1. **Clonar repositorio**:
   ```bash
   cd /home/usuario/public_html
   git clone https://github.com/Joppercam/DorasIA.git .
   git checkout production
   ```

2. **Configurar Laravel**:
   ```bash
   cp .env.production .env
   # Editar .env con credenciales reales
   php artisan key:generate
   composer install --no-dev
   npm install --production
   npm run build
   php artisan migrate
   php artisan storage:link
   ```

3. **Permisos**:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

## 📊 Monitoreo

### Logs importantes:
- `/home/usuario/deployment.log` - Script de sync
- `/home/usuario/webhook.log` - Webhook
- `/home/usuario/public_html/storage/logs/laravel.log` - Aplicación

### Verificar estado:
```bash
cd /home/usuario/public_html
git status
git log --oneline -5
```

## 🚨 Troubleshooting

### El webhook no funciona:
1. Verifica el secret
2. Revisa logs del webhook
3. Prueba manualmente el script

### Errores de permisos:
```bash
chmod +x sync-production.sh
chmod -R 755 storage
chown -R www-data:www-data storage  # o usuario de tu servidor web
```

### Git pull falla:
```bash
git reset --hard origin/production
git pull origin production
```

## 🎯 Tips

1. **Siempre prueba en local primero**
2. **Usa tags para versiones estables**:
   ```bash
   git tag -a v1.0.0 -m "Primera versión estable"
   git push origin v1.0.0
   ```
3. **Mantén backups antes de actualizar**
4. **Monitorea logs después de cada deployment**

## 📞 Soporte

Si tienes problemas con la sincronización:
1. Revisa los logs
2. Verifica permisos
3. Contacta a juanpablo@dorasia.com

---

¡Tu rama `production` está lista para sincronización automática! 🎉