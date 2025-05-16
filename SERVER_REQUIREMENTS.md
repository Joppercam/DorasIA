# Requisitos del Servidor para Dorasia

## Requisitos mínimos:

- **PHP**: >= 8.1
- **MySQL**: >= 5.7 o MariaDB >= 10.3
- **Node.js**: >= 16.x
- **NPM**: >= 7.x
- **Composer**: >= 2.0

## Extensiones PHP requeridas:

- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- PDO MySQL Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Configuración del servidor web:

### Apache:
- mod_rewrite habilitado
- AllowOverride All para el directorio de la aplicación

### Nginx:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Permisos de carpetas:

Las siguientes carpetas necesitan permisos de escritura (775):
- storage/
- storage/app/
- storage/app/public/
- storage/framework/
- storage/framework/cache/
- storage/framework/sessions/
- storage/framework/testing/
- storage/framework/views/
- storage/logs/
- bootstrap/cache/

## Comandos útiles del servidor:

```bash
# Verificar versión de PHP
php -v

# Verificar extensiones PHP instaladas
php -m

# Verificar versión de MySQL
mysql --version

# Verificar versión de Node.js
node -v

# Verificar versión de NPM
npm -v

# Verificar versión de Composer
composer -V
```