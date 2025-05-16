# Integración de Doramas Románticos en Dorasia

Este documento describe la integración especializada para doramas románticos asiáticos en la plataforma Dorasia, incluyendo las funcionalidades de categorización por subgéneros, filtrado avanzado y recomendaciones.

## Características principales

- Categorización inteligente de doramas románticos por subgéneros
- Filtrado avanzado por país de origen, subgénero y plataforma de streaming
- Sistema de recomendaciones basado en similitud de contenido
- Importación automatizada mediante la API de TMDB
- Interfaz especializada para explorar doramas románticos

## Subgéneros románticos

La plataforma categoriza automáticamente los doramas románticos en los siguientes subgéneros:

| Subgénero | Descripción | Keywords |
|-----------|-------------|----------|
| historical_romance | Romances ambientados en épocas históricas | historical, palace, dynasty, joseon, edo, qing, ming |
| romantic_comedy | Comedias con elementos románticos | comedy, funny, light-hearted, rom-com |
| melodrama | Historias de amor dramáticas/trágicas | melodrama, tearjerker, tragedy, emotional |
| supernatural_romance | Romances con elementos fantásticos | fantasy, supernatural, immortal, ghost, magical |
| medical_romance | Romances ambientados en hospitales | hospital, doctor, medical, nurse, surgery | 
| office_romance | Romances en entornos laborales | office, workplace, company, corporate, boss |
| youth_romance | Romances de adolescentes/coming of age | youth, coming of age, school, college, university, teenager |
| family_romance | Romances centrados en familias | family, marriage, couple, parenting, relationship |

## Sistema de importación

### Comando de importación

```bash
php artisan dorasia:import-romantic-dramas [opciones]
```

Opciones disponibles:
- `--country=COUNTRY`: Filtrar por país de origen (kr, jp, cn, th, all)
- `--pages=N`: Número de páginas a importar (default: 1)
- `--subgenre=SUBGENRE`: Importar solo un subgénero específico

Ejemplos:
```bash
# Importar todos los doramas románticos asiáticos (1 página)
php artisan dorasia:import-romantic-dramas

# Importar doramas románticos coreanos (2 páginas)
php artisan dorasia:import-romantic-dramas --country=kr --pages=2

# Importar solo comedias románticas japonesas
php artisan dorasia:import-romantic-dramas --country=jp --subgenre=romantic_comedy
```

### Proceso de categorización

El proceso de categorización usa un sistema de puntuación basado en:

1. Coincidencia de palabras clave en el título
2. Coincidencia de palabras clave en la sinopsis
3. Etiquetas de TMDB asociadas al contenido

Para cada doramas, se calculan puntuaciones para cada subgénero y se asigna al que obtenga la mayor puntuación.

## API y endpoints

Los siguientes endpoints están disponibles para acceder a los doramas románticos:

- **GET /romantic-dramas**: Listado principal de doramas románticos con secciones por subgénero
- **GET /romantic-dramas/subgenre/{subgenre}**: Doramas de un subgénero específico
- **GET /romantic-dramas/origin/{origin}**: Doramas de un país de origen específico
- **GET /romantic-dramas/search**: Búsqueda avanzada con filtros
- **GET /romantic-dramas/recommendations/{title}**: Recomendaciones basadas en un título

## Modelo de datos

El sistema utiliza el campo `metadata` en la tabla `titles` para almacenar información adicional:

```php
$title->metadata = [
    'romantic_subgenre' => 'historical_romance',
    'languages' => ['ko', 'en'],
    'origin_countries' => ['KR'],
    'trailer_url' => 'https://youtube.com/...',
    'content_rating' => 'TV-14',
    'genres' => ['Romance', 'History', 'Drama'],
    'networks' => ['tvN', 'Netflix'],
    'keywords' => ['palace', 'joseon', 'love', 'dynasty'],
];
```

## Integración con componentes de UI

La integración incluye componentes UI especializados:

- **NetflixHero**: Banner destacado para doramas románticos
- **NetflixCarousel**: Carrusel horizontal con categorías por subgénero
- **RomanticSubgenreBadge**: Etiqueta visual que indica el subgénero
- **RecommendationsPanel**: Panel de recomendaciones basadas en similitud

## Actualización programada

El sistema incluye tareas programadas para la actualización regular:

```php
// En App\Console\Kernel.php
protected function schedule(Schedule $schedule)
{
    // Importar doramas románticos asiáticos cada 2 días
    $schedule->command('dorasia:import-romantic-dramas --pages=2 --country=all')
             ->cron('0 1 */2 * *');
             
    // Importar doramas románticos por subgéneros cada semana
    $subgenres = ['historical_romance', 'romantic_comedy', 'melodrama', 'office_romance', 'youth_romance'];
    foreach ($subgenres as $index => $subgenre) {
        $schedule->command("dorasia:import-romantic-dramas --pages=1 --subgenre={$subgenre}")
                 ->weekly()->days([$index + 1]);
    }
}
```

## Funcionalidades sociales

Las funcionalidades sociales para doramas románticos incluyen:

- Compartir recomendaciones de doramas románticos
- Listas personalizadas por subgénero
- Comentarios y valoraciones específicas para doramas románticos

## Enriquecimiento de contenido

El sistema incluye enriquecimiento de contenido especializado para doramas románticos:

- Información sobre OST (bandas sonoras)
- Glosario de términos románticos en diferentes idiomas asiáticos
- Contexto cultural para entender mejor las dinámicas románticas
- Identificación de tropos comunes en el género romántico

## Futuros desarrollos

Próximas funcionalidades planificadas:

- Mejora del algoritmo de categorización mediante machine learning
- Integración de más fuentes de datos especializadas en doramas
- Personalización basada en preferencias de subgéneros románticos
- Calendario de estrenos de doramas románticos
- Funcionalidades sociales avanzadas para fans de doramas románticos