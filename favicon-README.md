# Implementación de Favicon y Metadatos Dinámicos en Dorasia

Este documento explica los cambios implementados para mejorar la experiencia visual y el SEO de la plataforma Dorasia mediante la incorporación de favicons y metadatos dinámicos en las páginas.

## Cambios Realizados

### 1. Sistema de Favicon

Se ha implementado un sistema completo de favicon que incluye:

- **SVG Vectorial**: Un archivo SVG base con el logo de Dorasia (una "D" estilizada) usando los colores corporativos (rojo #E51013 y negro #141414).
- **Múltiples Tamaños**: Soporte para diferentes tamaños de favicon adaptados a distintas plataformas y dispositivos.
- **Web Manifest**: Un archivo site.webmanifest para mejorar la experiencia en dispositivos móviles y permitir la instalación como PWA.
- **Script de Generación**: Herramienta para generar automáticamente todos los tamaños de favicon necesarios.

### 2. Metadatos Dinámicos

Se han actualizado las plantillas para soportar metadatos dinámicos:

- **Títulos Específicos**: Cada página ahora muestra "Dorasia | [Nombre de la página]" en la pestaña del navegador.
- **Descripciones SEO**: Se incluyen metadescripciones específicas para cada tipo de contenido.
- **Meta Tags para Redes Sociales**: Implementación completa de OpenGraph y Twitter Cards para mejorar la compartibilidad.
- **Componente MetaTags**: Se creó un componente reutilizable para facilitar la asignación de metadatos desde los controladores.

### 3. Cambios Técnicos

- **ViewServiceProvider**: Nuevo proveedor de servicios que registra valores por defecto para los metadatos.
- **JavaScript Utility**: Script para gestionar metadatos dinámicos en caso de cambios de página mediante AJAX/SPA.
- **Optimización de Controladores**: Se actualizó el TitleController para generar metadatos específicos basados en el contenido.

## Estructura de Archivos

```
public/
  ├── favicon/
  │   ├── favicon.svg                # Favicon vectorial base
  │   ├── favicon-16x16.png          # Favicon pequeño (generado)
  │   ├── favicon-32x32.png          # Favicon mediano (generado)
  │   ├── favicon-180x180.png        # Apple touch icon (generado)
  │   ├── favicon-192x192.png        # Android icon (generado)
  │   ├── favicon-512x512.png        # PWA icon (generado)
  │   ├── site.webmanifest           # Configuración para PWA
  │   └── generate-favicons.php      # Script para generar favicons
  ├── favicon.ico                    # Favicon tradicional
  └── js/
      └── head-metadata.js           # Utilidad para gestionar metadatos dinámicos
```

## Uso en Controladores

Para asignar metadatos específicos en un controlador:

```php
return view('mi-vista', [
    'metaTitle' => 'Título Específico',
    'metaDescription' => 'Descripción específica para esta página (máx. 160 caracteres).',
    'metaImage' => asset('ruta/a/imagen.jpg'),
]);
```

O usando el componente en las vistas:

```blade
<x-meta-tags 
    title="Título Específico" 
    description="Descripción específica." 
    image="{{ asset('ruta/a/imagen.jpg') }}" 
/>
```

## Consideraciones Futuras

- Agregar más tamaños de favicon para mejorar la compatibilidad con más dispositivos.
- Implementar un sistema de microdata con Schema.org para mejorar aún más el SEO.
- Crear un panel de administración para gestionar los metadatos por defecto.

## Conclusión

Estos cambios mejorarán significativamente la experiencia de usuario en Dorasia al proporcionar indicadores visuales claros en las pestañas del navegador y optimizar cómo se muestra el contenido cuando se comparte en redes sociales.