/**
 * Script para mejorar la experiencia en las páginas de catálogo
 * Provee funcionalidades como cambio de vista, filtros dinámicos, 
 * animaciones, scroll infinito y ordenación
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('catalogPage', () => ({
        // Estados básicos
        loading: false,
        currentPage: 1,
        hasMorePages: true,
        viewType: 'grid', // grid o list
        sortOrder: 'latest', // latest, oldest, rating, title
        filterVisible: false,
        expandedHero: false,
        titles: [],
        filteredTitles: [],
        
        // Filtros
        filters: {
            years: [],
            ratings: [],
            duration: [0, 300], // minutos
            selectedGenres: [],
            selectedTypes: []
        },
        
        // Funciones de inicialización
        init() {
            // Cargar preferencia de vista guardada o usar grid por defecto
            this.viewType = localStorage.getItem('dorasiaCatalogView') || 'grid';
            
            // Obtener filtros de la URL si existen
            const urlParams = new URLSearchParams(window.location.search);
            this.sortOrder = urlParams.get('sort') || 'latest';
            
            // Aplicar clase de transición a la página
            document.body.classList.add('catalog-page');
            
            // Configurar scroll infinito
            this.setupInfiniteScroll();
            
            // Inicializar la animación del hero banner
            this.initHeroBanner();
            
            // Activar lazy loading de imágenes
            this.setupLazyLoading();
        },
        
        // Para cambiar entre vista de cuadrícula y lista
        toggleView(viewType) {
            // Agregamos clase para la animación al cambiar de vista
            document.querySelector('.catalog-container').classList.add('view-transition');
            
            setTimeout(() => {
                this.viewType = viewType;
                localStorage.setItem('dorasiaCatalogView', viewType);
                
                document.querySelector('.catalog-container').classList.remove('view-transition');
            }, 150);
        },
        
        // Expandir/colapsar el hero banner
        toggleHeroBanner() {
            this.expandedHero = !this.expandedHero;
            
            const heroBanner = document.querySelector('.hero-banner');
            if (this.expandedHero) {
                heroBanner.classList.remove('hero-banner--collapsed');
                heroBanner.classList.add('hero-banner--expanded');
            } else {
                heroBanner.classList.remove('hero-banner--expanded');
                heroBanner.classList.add('hero-banner--collapsed');
            }
        },
        
        // Inicializar animación del hero banner
        initHeroBanner() {
            // El hero comienza colapsado
            const heroBanner = document.querySelector('.hero-banner');
            if (heroBanner) {
                heroBanner.classList.add('hero-banner--collapsed');
                
                // Opcional: expandir automáticamente el hero después de un tiempo para atraer la atención
                setTimeout(() => {
                    // Luego volver a colapsar
                    setTimeout(() => {
                        if (!this.expandedHero) {
                            heroBanner.classList.remove('hero-banner--expanded');
                            heroBanner.classList.add('hero-banner--collapsed');
                        }
                    }, 3000);
                    
                    // Expandir brevemente para mostrar la funcionalidad
                    if (!this.expandedHero) {
                        heroBanner.classList.remove('hero-banner--collapsed');
                        heroBanner.classList.add('hero-banner--expanded');
                    }
                }, 1500);
            }
        },
        
        // Configurar carga infinita
        setupInfiniteScroll() {
            const options = {
                root: null, // usar el viewport
                rootMargin: '0px',
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.loading && this.hasMorePages) {
                        this.loadMoreTitles();
                    }
                });
            }, options);
            
            const loadMoreTrigger = document.querySelector('.infinite-scroll__trigger');
            if (loadMoreTrigger) {
                observer.observe(loadMoreTrigger);
            }
        },
        
        // Cargar más títulos (simulación de carga infinita)
        loadMoreTitles() {
            this.loading = true;
            
            // Incrementamos la página
            this.currentPage++;
            
            // Simulamos la llamada AJAX para una paginación real
            const url = new URL(window.location.href);
            url.searchParams.set('page', this.currentPage);
            url.searchParams.set('ajax', '1'); // Para detectar que es una solicitud AJAX
            
            fetch(url.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.titles.length > 0) {
                        // Agregar nuevos títulos con animación
                        setTimeout(() => {
                            this.appendTitles(data.titles);
                            this.loading = false;
                        }, 800); // Simular tiempo de carga
                    } else {
                        this.hasMorePages = false;
                        this.loading = false;
                    }
                })
                .catch(error => {
                    console.error('Error cargando más títulos:', error);
                    this.loading = false;
                });
        },
        
        // Agregar títulos nuevos al DOM
        appendTitles(newTitles) {
            const container = document.querySelector('.catalog-grid');
            
            if (!container) return;
            
            // Crear elementos para nuevos títulos
            newTitles.forEach(title => {
                // Aquí se generaría el HTML para cada título
                // Como ejemplo, solo añadimos una tarjeta vacía
                const titleElement = document.createElement('div');
                titleElement.classList.add('dorasia-card', 'title-card', 'opacity-0');
                titleElement.innerHTML = `
                    <!-- El contenido de cada tarjeta vendría aquí -->
                    <div class="animate-pulse bg-gray-800 rounded h-64 w-full"></div>
                `;
                
                container.appendChild(titleElement);
                
                // Animar la entrada
                setTimeout(() => {
                    titleElement.classList.remove('opacity-0');
                    titleElement.classList.add('transition-opacity', 'duration-500', 'opacity-100');
                }, 50);
            });
        },
        
        // Aplicar un filtro
        applyFilter(filterType, value) {
            const url = new URL(window.location.href);
            
            // Restablecer a la página 1 al cambiar filtros
            url.searchParams.delete('page');
            
            // Actualizar el filtro en la URL
            if (value === null || value === '') {
                url.searchParams.delete(filterType);
            } else {
                url.searchParams.set(filterType, value);
            }
            
            // Navegar a la URL con el nuevo filtro
            window.location.href = url.toString();
        },
        
        // Cambiar orden de los resultados
        changeSort(sortOrder) {
            this.sortOrder = sortOrder;
            this.applyFilter('sort', sortOrder);
        },
        
        // Configurar lazy loading de imágenes
        setupLazyLoading() {
            // Solo si el navegador soporta IntersectionObserver
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('.lazy-image');
                
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy-image');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                lazyImages.forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback para navegadores que no soportan IntersectionObserver
                const lazyImages = document.querySelectorAll('.lazy-image');
                
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-image');
                });
            }
        },
        
        // Filtrar géneros relacionados
        filterByRelatedGenre(genreId) {
            this.applyFilter('genre', genreId);
        }
    }));
});

// Script para manejar animaciones de desplazamiento
document.addEventListener('DOMContentLoaded', function() {
    // Animación para elementos al hacer scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('visible');
            }
        });
    };
    
    // Iniciar animaciones al cargar
    animateOnScroll();
    
    // Continuar animaciones al hacer scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Detectar si hay soporte para la API de Share
    const shareButtons = document.querySelectorAll('.share-button');
    
    if (navigator.share) {
        shareButtons.forEach(button => {
            button.style.display = 'flex';
            
            button.addEventListener('click', async () => {
                const url = button.dataset.url;
                const title = button.dataset.title;
                
                try {
                    await navigator.share({
                        title: title,
                        url: url
                    });
                } catch (error) {
                    console.warn('Error compartiendo:', error);
                }
            });
        });
    }
});

// Función para copiar al portapapeles
function copyToClipboard(text) {
    // Fallback para navegadores antiguos
    if (!navigator.clipboard) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('Enlace copiado al portapapeles');
        } catch (err) {
            console.error('Error al copiar texto:', err);
        }
        
        document.body.removeChild(textArea);
        return;
    }
    
    // Método moderno
    navigator.clipboard.writeText(text)
        .then(() => {
            showToast('Enlace copiado al portapapeles');
        })
        .catch(err => {
            console.error('Error al copiar texto:', err);
        });
}

// Mostrar un toast de notificación
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-md shadow-lg text-sm z-50';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 10);
    
    // Animar salida y eliminar
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translate(-50%, 20px)';
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 2000);
}