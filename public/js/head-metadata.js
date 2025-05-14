/**
 * Dorasia Metadata Manager
 * Script para gestionar el cambio dinámico de metadatos de página
 */

class DorasiaMetadataManager {
    constructor() {
        this.defaultTitle = document.title;
        this.defaultDescription = this.getMetaContent('description');
        this.defaultImage = this.getMetaContent('og:image');
    }

    /**
     * Obtiene el contenido de una meta tag
     */
    getMetaContent(name) {
        const meta = document.querySelector(`meta[name="${name}"], meta[property="${name}"]`);
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Actualiza el título de la página
     */
    setTitle(title) {
        const formattedTitle = title ? `${title} | Dorasia` : 'Dorasia';
        document.title = formattedTitle;
        
        // También actualizar las meta tags de Open Graph y Twitter
        this.updateMetaTag('og:title', formattedTitle);
        this.updateMetaTag('twitter:title', formattedTitle);
    }

    /**
     * Actualiza la descripción de la página
     */
    setDescription(description) {
        const desc = description || this.defaultDescription;
        this.updateMetaTag('description', desc);
        this.updateMetaTag('og:description', desc);
        this.updateMetaTag('twitter:description', desc);
    }

    /**
     * Actualiza la imagen de la página para compartir
     */
    setImage(imageUrl) {
        const image = imageUrl || this.defaultImage;
        this.updateMetaTag('og:image', image);
        this.updateMetaTag('twitter:image', image);
    }

    /**
     * Actualiza una meta tag específica
     */
    updateMetaTag(name, content) {
        let meta = document.querySelector(`meta[name="${name}"], meta[property="${name}"]`);
        
        if (meta) {
            meta.setAttribute('content', content);
        } else {
            meta = document.createElement('meta');
            if (name.startsWith('og:')) {
                meta.setAttribute('property', name);
            } else {
                meta.setAttribute('name', name);
            }
            meta.setAttribute('content', content);
            document.head.appendChild(meta);
        }
    }

    /**
     * Reestablece los metadatos a sus valores por defecto
     */
    reset() {
        this.setTitle('');
        this.setDescription(this.defaultDescription);
        this.setImage(this.defaultImage);
    }
}

// Inicializar el gestor de metadatos
window.dorasiaMetadata = new DorasiaMetadataManager();

// Para uso desde DevTools o para debugging
console.log('Dorasia Metadata Manager initialized');