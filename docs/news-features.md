# Funcionalidad de Noticias en Dorasia

Este documento describe la funcionalidad de noticias implementada en Dorasia, que permite mostrar noticias sobre actores, películas y series asiáticas obtenidas de diversas fuentes.

## Modelos y Relaciones

- `News`: Modelo para noticias con los campos título, slug, contenido, imagen, fuente, fecha de publicación, etc.
- `Person`: Modelo de actores que tiene una relación many-to-many con News.

La relación entre `News` y `Person` se implementa a través de la tabla pivot `news_person`, que incluye un campo adicional `primary_subject` para indicar si el actor es el tema principal de la noticia.

## Comandos de Artisan

### Obtención de Noticias de Actores

```bash
php artisan dorasia:fetch-news [opciones]
```

**Opciones:**
- `--source=newsapi|ai`: La fuente de las noticias (NewsAPI.org o generado por IA)
- `--limit=10`: Número máximo de noticias a obtener
- `--days=7`: Buscar noticias de los últimos X días (solo para NewsAPI)
- `--actor="Nombre o ID"`: Buscar noticias para un actor específico
- `--add-images`: Intentar descargar imágenes para las noticias

### Obtención de Noticias de Películas y Series

```bash
php artisan dorasia:fetch-movie-news [opciones]
```

**Opciones:**
- `--source=newsapi|tmdb|ai`: La fuente de las noticias (NewsAPI, TMDB, o generado por IA)
- `--limit=10`: Número máximo de noticias a obtener
- `--days=7`: Buscar noticias de los últimos X días (solo para NewsAPI)
- `--title="Nombre o ID"`: Buscar noticias para una película/serie específica
- `--genre="Género"`: Buscar noticias para un género específico
- `--add-images`: Intentar descargar imágenes para las noticias

## Configuración

Para utilizar estas características, es necesario configurar las siguientes variables en el archivo `.env`:

```
# Para obtener noticias reales de NewsAPI.org
NEWSAPI_KEY=tu_clave_api_aquí

# Para generar noticias con OpenAI
OPENAI_API_KEY=tu_clave_api_aquí
OPENAI_ORGANIZATION=tu_id_de_organización_aquí # Opcional

# Ya configurado para la importación de películas/series
TMDB_API_KEY=tu_clave_api_aquí
TMDB_ACCESS_TOKEN=tu_token_de_acceso_aquí
```

## Script para Importar Noticias

Se incluye un script shell `import-news.sh` para facilitar la ejecución de los comandos:

```bash
./import-news.sh --actor-news newsapi --limit 15 --images
./import-news.sh --movie-news tmdb --limit 10
```

Ejecuta `./import-news.sh --help` para ver todas las opciones disponibles.

## Programación de Tareas

Los comandos están programados en el Schedule de Laravel para ejecutarse automáticamente:

- **Noticias de actores (NewsAPI)**: Diariamente a las 6:00 AM
- **Noticias de actores (IA)**: Semanalmente los domingos a las 6:30 AM
- **Noticias de películas/series (NewsAPI)**: Semanalmente los sábados a las 7:00 AM
- **Noticias de películas/series (TMDB)**: Semanalmente los viernes a las 7:30 AM

Para activar estos jobs programados, configura un cron job en el servidor:

```
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## Rutas y Controladores

### Rutas Públicas

- `/news`: Página principal de noticias
- `/news/{slug}`: Página de detalle de una noticia
- `/news/person/{slug}`: Noticias relacionadas con un actor específico

### Rutas de Administración

- `/admin/news`: Panel de administración de noticias
- `/admin/news/create`: Crear una nueva noticia
- `/admin/news/{id}/edit`: Editar una noticia existente

## Vistas

- `resources/views/news/index.blade.php`: Listado de noticias
- `resources/views/news/show.blade.php`: Detalle de una noticia
- `resources/views/people/show.blade.php`: Perfil de actor con sección de noticias
- `resources/views/admin/news/*`: Vistas para la administración de noticias