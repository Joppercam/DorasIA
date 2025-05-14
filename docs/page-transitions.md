# Transiciones de Página en Dorasia

Este documento explica cómo se implementan y utilizan las transiciones de página en la aplicación Dorasia, proporcionando una experiencia de navegación fluida similar a Netflix.

## Características Principales

- **Transición de fundido (fade)**: Utilizada para cambios generales de página
- **Transición de deslizamiento (slide-up)**: Para ver detalles de títulos
- **Transición de zoom**: Para entrar al reproductor de video
- **Detección automática de tipo de transición** basada en patrones de URL
- **Soporte para preferencias de usuario** (guardar tipo preferido o desactivar)
- **Respeto a la preferencia `prefers-reduced-motion`** para accesibilidad
- **Compatible con dispositivos móviles y de escritorio**

## Cómo Funciona

El sistema de transiciones está construido con Alpine.js y CSS, integrado en el layout principal de la aplicación. 

1. Cuando un usuario hace clic en un enlace interno:
   - El sistema intercepta el clic
   - Aplica una animación de salida en la página actual
   - Navega a la nueva página
   - La nueva página aplica una animación de entrada

2. Al entrar a la nueva página, el sistema:
   - Restablece el scroll al inicio
   - Aplica la animación de entrada adecuada al tipo de página

## Uso Básico

Para utilizar las transiciones en una vista:

```blade
<x-app-layout>
    <x-slot name="title">Título de la Página</x-slot>
    <x-slot name="pageClass">nombre-clase-opcional</x-slot>
    <x-slot name="transitionType">fade</x-slot> <!-- fade | slide-up | zoom -->
    
    <!-- Contenido de la página -->
</x-app-layout>
```

### Tipos de Transición Disponibles

- **fade**: Un simple fundido para navegación general
- **slide-up**: Deslizamiento hacia arriba para detalles
- **zoom**: Efecto de zoom para contenido en pantalla completa

### Uso en Enlaces

Para definir un tipo específico de transición en un enlace:

```html
<a href="{{ route('titles.show', $title->slug) }}" data-transition="slide-up">
    Ver detalles
</a>
```

O dejar que el sistema lo detecte automáticamente basado en el patrón de URL.

## Personalización por Usuario

Los usuarios pueden personalizar su experiencia con el panel de configuración que aparece en la esquina inferior derecha (para usuarios autenticados):

- Activar/desactivar todas las transiciones
- Seleccionar el tipo de transición por defecto

## Consideraciones Técnicas

### Archivos Principales

- `/resources/js/page-transitions.js`: Lógica principal de transiciones
- `/resources/css/page-transitions.css`: Estilos y animaciones CSS
- `/resources/views/layouts/app.blade.php`: Integración en el layout

### Mejoras de Rendimiento

Para mantener un rendimiento óptimo:

1. Solo se utilizan propiedades CSS optimizadas (`opacity` y `transform`)
2. Las animaciones tienen una duración breve (300-500ms)
3. Se evita animar propiedades que causan reflow (como `height` o `width`)

### Accesibilidad

El sistema respeta la preferencia del usuario `prefers-reduced-motion`:

- Si está activada, las transiciones se desactivan automáticamente
- El panel de control permite desactivar manualmente las transiciones

## Ejemplos de Uso

### Página de Inicio (fade)

```blade
<x-app-layout>
    <x-slot name="title">Inicio</x-slot>
    <x-slot name="pageClass">home-page</x-slot>
    <x-slot name="transitionType">fade</x-slot>
    
    <!-- Contenido de la página de inicio -->
</x-app-layout>
```

### Página de Detalles de Título (slide-up)

```blade
<x-app-layout>
    <x-slot name="title">{{ $title->title }}</x-slot>
    <x-slot name="pageClass">titles-detail-page</x-slot>
    <x-slot name="transitionType">slide-up</x-slot>
    
    <!-- Contenido de la página de detalles -->
</x-app-layout>
```

### Página de Reproductor (zoom)

```blade
<x-app-layout>
    <x-slot name="title">Reproduciendo: {{ $title->title }}</x-slot>
    <x-slot name="pageClass">video-player-page</x-slot>
    <x-slot name="transitionType">zoom</x-slot>
    
    <!-- Contenido del reproductor -->
</x-app-layout>
```

## Solución de Problemas

### Las transiciones no funcionan

1. Verifica que no esté activada la preferencia `prefers-reduced-motion` en el sistema
2. Comprueba que las transiciones no estén desactivadas manualmente en el panel
3. Asegúrate de estar utilizando enlaces internos

### Problemas de rendimiento

Si notas problemas de rendimiento en dispositivos de gama baja:

1. Utiliza la opción de desactivar transiciones en el panel
2. Considera reducir el tiempo de las animaciones en `/resources/css/page-transitions.css`

## Extensión y Personalización

Para añadir nuevos tipos de transiciones:

1. Define las animaciones en CSS en `/resources/css/page-transitions.css`
2. Actualiza el método `getTransitionTypeFromLink` en `/resources/js/page-transitions.js`
3. Añade la nueva opción a la configuración del usuario en `app.blade.php`