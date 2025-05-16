# Resumen de Comandos de Noticias y Actores

## Comandos Principales

### 1. Importar Noticias de Actores
```bash
# Desde NewsAPI
php artisan dorasia:fetch-news --source=newsapi --limit=20 --add-images

# Desde OpenAI (IA)
php artisan dorasia:fetch-news --source=ai --limit=5

# Para un actor específico
php artisan dorasia:fetch-news --actor="Kim Soo-hyun" --limit=5
```

### 2. Importar Noticias de Películas/Series
```bash
# Desde NewsAPI
php artisan dorasia:fetch-movie-news --source=newsapi --limit=15 --add-images

# Desde TMDB
php artisan dorasia:fetch-movie-news --source=tmdb --limit=10

# Para un título específico
php artisan dorasia:fetch-movie-news --title="Vincenzo" --limit=5
```

### 3. Generar Noticias Asiáticas
```bash
# Generar 20 noticias diversas (por defecto)
php artisan news:generate-more-asian

# Generar con limpieza previa
php artisan dorasia:clean-and-generate-news --generate
```

### 4. Gestión de Imágenes de Actores
```bash
# Buscar actores sin imágenes
php artisan actors:find-missing-images

# Buscar y corregir actores sin imágenes
php artisan actors:find-missing-images --fix

# Actualizar imágenes de actores
php artisan actors:update-images --limit=10

# Actualizar todas las imágenes
php artisan actors:update-images --update-all --limit=50

# Validar noticias y actualizar imágenes de actores
php artisan news:validate-actor-images --fix --update-generic
```

## Flujo Completo (Manual)

Para ejecutar todo el flujo manualmente:

```bash
# 1. Generar noticias asiáticas
php artisan news:generate-more-asian

# 2. Importar noticias de actores
php artisan dorasia:fetch-news --source=newsapi --limit=20 --add-images

# 3. Importar noticias de películas
php artisan dorasia:fetch-movie-news --source=newsapi --limit=15 --add-images

# 4. Validar y corregir imágenes de actores
php artisan news:validate-actor-images --fix --update-generic

# 5. Actualizar imágenes faltantes
php artisan actors:update-images --limit=50
```

## Programación Cron Configurada

El sistema ejecuta automáticamente:

- **Diariamente**:
  - 05:00 - Generar noticias asiáticas adicionales
  - 06:00 - Importar noticias de actores (NewsAPI)
  - 08:00 - Validar y corregir imágenes de actores

- **Semanalmente**:
  - Domingos 06:30 - Generar noticias con IA
  - Viernes 07:30 - Actualizar desde TMDB
  - Sábados 07:00 - Importar noticias de películas
  - Lunes 09:00 - Actualizar imágenes de actores

## Notas Importantes

1. **Validación Automática**: Todos los comandos de importación de noticias ejecutan automáticamente la validación de imágenes al finalizar.

2. **Idioma**: Todas las noticias se generan en español.

3. **Imágenes**: El sistema verifica automáticamente que todos los actores en noticias tengan imágenes válidas.

4. **APIs Requeridas**:
   - NewsAPI Key en `.env`: `NEWSAPI_KEY=tu_clave`
   - OpenAI Key en `.env`: `OPENAI_API_KEY=tu_clave`
   - TMDB Key en `.env`: `TMDB_API_KEY=tu_clave`

## Solución de Problemas

Si algún comando falla:

1. Verificar que las API keys estén configuradas
2. Comprobar la conexión a internet
3. Revisar los logs: `storage/logs/laravel.log`
4. Ejecutar manualmente con `--debug` para más información

## Enlaces del Sistema

- **Noticias**: `/news`
- **Detalle de Noticia**: `/news/{slug}`
- **Noticias por Actor**: `/news/person/{slug}`