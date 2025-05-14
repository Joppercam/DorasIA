/**
 * Page Transitions for Dorasia
 * 
 * Este módulo maneja las transiciones entre páginas, proporcionando
 * animaciones fluidas cuando los usuarios navegan por la aplicación.
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('pageTransition', () => ({
        isTransitioning: false,
        transitionType: 'fade', // fade, slide-up, zoom
        prefersReducedMotion: false,
        
        init() {
            // Verificar si el usuario prefiere reducir el movimiento
            this.checkReducedMotionPreference();
            
            // Escuchar cambios en la preferencia de movimiento reducido
            window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', () => {
                this.checkReducedMotionPreference();
            });
            
            // Interceptar clics en enlaces para manejar transiciones
            this.setupLinkInterception();
            
            // Exponer método global para cambiar directamente el tipo de transición
            window.setTransitionType = (type) => {
                if (['fade', 'slide-up', 'zoom'].includes(type)) {
                    this.transitionType = type;
                    localStorage.setItem('dorasiaTransitionType', type);
                }
            };
            
            // Recuperar tipo de transición guardada
            const savedTransitionType = localStorage.getItem('dorasiaTransitionType');
            if (savedTransitionType) {
                this.transitionType = savedTransitionType;
            }
            
            // Recuperar preferencia de transiciones
            const transitionsEnabled = localStorage.getItem('dorasiaTransitionsEnabled');
            if (transitionsEnabled === 'false') {
                this.disableTransitions();
            }
        },
        
        checkReducedMotionPreference() {
            this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            if (this.prefersReducedMotion) {
                document.documentElement.classList.add('no-transitions');
            } else {
                document.documentElement.classList.remove('no-transitions');
            }
        },
        
        setupLinkInterception() {
            // Solo interceptar enlaces internos que no sean descargas ni abran en una nueva pestaña
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                
                if (!link) return;
                if (link.hasAttribute('download')) return;
                if (link.target === '_blank') return;
                if (link.getAttribute('href').startsWith('#')) return;
                if (link.getAttribute('href').includes('://') && !link.getAttribute('href').includes(window.location.hostname)) return;
                if (this.prefersReducedMotion) return;
                if (document.documentElement.classList.contains('no-transitions')) return;
                
                // Para formularios y otras acciones que no son enlaces directos
                if (link.getAttribute('href').startsWith('javascript:')) return;
                
                e.preventDefault();
                
                this.navigateTo(link.href, this.getTransitionTypeFromLink(link));
            });
        },
        
        getTransitionTypeFromLink(link) {
            // Permitir transiciones específicas por enlace usando data attributes
            if (link.dataset.transition && ['fade', 'slide-up', 'zoom'].includes(link.dataset.transition)) {
                return link.dataset.transition;
            }
            
            // Detectar automáticamente el tipo de transición basado en patrones de URL
            const href = link.getAttribute('href');
            
            // Enlaces a detalles de títulos - usar slide-up
            if (href.includes('/titles/')) {
                return 'slide-up';
            }
            
            // Enlaces a reproductor de video - usar zoom
            if (href.includes('/watch/') || href.includes('/player/')) {
                return 'zoom';
            }
            
            // Por defecto usar la transición global configurada
            return this.transitionType;
        },
        
        async navigateTo(url, transitionType = null) {
            if (this.isTransitioning) return;
            
            const currentTransitionType = transitionType || this.transitionType;
            this.isTransitioning = true;
            
            // Aplicar clase de salida según el tipo de transición
            document.body.classList.add(`transition-active`);
            document.body.classList.add(`transition-${currentTransitionType}-out`);
            
            // Esperar a que la animación de salida termine
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Navegar a la nueva página
            window.location.href = url;
        },
        
        enableTransitions() {
            document.documentElement.classList.remove('no-transitions');
            localStorage.setItem('dorasiaTransitionsEnabled', 'true');
        },
        
        disableTransitions() {
            document.documentElement.classList.add('no-transitions');
            localStorage.setItem('dorasiaTransitionsEnabled', 'false');
        },
        
        toggleTransitions() {
            if (document.documentElement.classList.contains('no-transitions')) {
                this.enableTransitions();
                return true;
            } else {
                this.disableTransitions();
                return false;
            }
        },
        
        isTransitionsEnabled() {
            return !document.documentElement.classList.contains('no-transitions');
        }
    }));
});

/**
 * Configuración de transición de entrada cuando la página carga
 */
document.addEventListener('DOMContentLoaded', () => {
    // Recuperar preferencia de usuario
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const transitionsDisabled = localStorage.getItem('dorasiaTransitionsEnabled') === 'false';
    
    if (!prefersReducedMotion && !transitionsDisabled) {
        // Obtener el tipo de transición del cuerpo, si existe
        const transitionType = document.body.dataset.transitionType || 'fade';
        
        // Aplicar la clase de transición de entrada
        document.body.classList.add(`transition-${transitionType}-in`);
        
        // Eliminar la clase después de que termine la animación
        setTimeout(() => {
            document.body.classList.remove(`transition-${transitionType}-in`);
            document.body.classList.remove('transition-active');
        }, 500);
    }
});