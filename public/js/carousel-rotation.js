/**
 * Sistema de Rotación Automática de Carruseles - Dorasia
 * Rota el contenido de los carruseles automáticamente para mostrar variedad
 */

class CarouselRotationManager {
    constructor() {
        this.rotationIntervals = new Map();
        this.carouselData = new Map();
        this.isVisible = true;
        this.lastRotationTime = new Map();
        
        // Configuración de rotación por tipo de carrusel
        this.rotationConfig = {
            'popular': { 
                interval: 10 * 60 * 1000, // 10 minutos
                autoStart: true,
                showIndicator: true
            },
            'top_rated': { 
                interval: 15 * 60 * 1000, // 15 minutos
                autoStart: true,
                showIndicator: true
            },
            'recent': { 
                interval: 8 * 60 * 1000, // 8 minutos
                autoStart: true,
                showIndicator: true
            },
            'romance': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'drama': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'comedy': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'action': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'mystery': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'historical': { 
                interval: 20 * 60 * 1000, // 20 minutos
                autoStart: true,
                showIndicator: false
            },
            'personalized_actor_content': {
                interval: 5 * 60 * 1000, // 5 minutos - rotación rápida para contenido personalizado
                autoStart: true,
                showIndicator: true
            },
            'featured_actor_content': {
                interval: 15 * 60 * 1000, // 15 minutos
                autoStart: true,
                showIndicator: true
            },
            'recent_actor_content': {
                interval: 8 * 60 * 1000, // 8 minutos
                autoStart: true,
                showIndicator: true
            },
            'actors_with_content': {
                interval: 25 * 60 * 1000, // 25 minutos
                autoStart: true,
                showIndicator: false
            }
        };

        this.init();
    }

    init() {
        this.setupVisibilityDetection();
        this.detectCarousels();
        this.addRotationControls();
        this.startAutoRotation();
        
        console.log('🔄 Carousel Rotation Manager initialized');
    }

    setupVisibilityDetection() {
        // Pausar rotación cuando la página no está visible
        document.addEventListener('visibilitychange', () => {
            this.isVisible = !document.hidden;
            if (this.isVisible) {
                this.resumeAllRotations();
            } else {
                this.pauseAllRotations();
            }
        });

        // Pausar cuando la ventana pierde el foco
        window.addEventListener('blur', () => {
            this.isVisible = false;
            this.pauseAllRotations();
        });

        window.addEventListener('focus', () => {
            this.isVisible = true;
            this.resumeAllRotations();
        });
    }

    detectCarousels() {
        const carouselSelectors = [
            '.netflix-carousel[data-type]',
            '.series-carousel[data-type]',
            '.movies-carousel[data-type]',
            '.mobile-row[data-type]',
            '.actor-content-section[data-type]',
            '.actors-with-content-section[data-type]',
            '[data-carousel-type]'
        ];

        carouselSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(carousel => {
                const type = carousel.dataset.type || carousel.dataset.carouselType;
                if (type && this.rotationConfig[type]) {
                    this.registerCarousel(carousel, type);
                }
            });
        });
    }

    registerCarousel(carouselElement, type) {
        const carouselId = `carousel-${type}-${Date.now()}`;
        carouselElement.setAttribute('data-rotation-id', carouselId);
        
        const config = this.rotationConfig[type];
        this.carouselData.set(carouselId, {
            element: carouselElement,
            type: type,
            config: config,
            lastSeed: this.generateSeed(),
            isRotating: false
        });

        // Agregar indicador de rotación si está habilitado
        if (config.showIndicator) {
            this.addRotationIndicator(carouselElement, carouselId);
        }

        console.log(`📺 Registered carousel: ${type} (ID: ${carouselId})`);
    }

    addRotationIndicator(carouselElement, carouselId) {
        const header = carouselElement.querySelector('.section-header, .carousel-header, h2, h3');
        if (!header) return;

        const indicator = document.createElement('div');
        indicator.className = 'rotation-indicator';
        indicator.innerHTML = `
            <div class="rotation-status">
                <div class="rotation-icon">🔄</div>
                <span class="rotation-text">Contenido rotativo</span>
                <div class="rotation-timer"></div>
            </div>
        `;

        header.appendChild(indicator);

        // Agregar estilos si no existen
        if (!document.getElementById('rotation-styles')) {
            this.addRotationStyles();
        }
    }

    addRotationStyles() {
        const styles = document.createElement('style');
        styles.id = 'rotation-styles';
        styles.textContent = `
            .rotation-indicator {
                display: inline-flex;
                align-items: center;
                margin-left: 1rem;
                opacity: 0.7;
                transition: opacity 0.3s ease;
            }

            .rotation-indicator:hover {
                opacity: 1;
            }

            .rotation-status {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background: rgba(255, 255, 255, 0.1);
                padding: 0.3rem 0.8rem;
                border-radius: 15px;
                font-size: 0.8rem;
                color: #ccc;
                backdrop-filter: blur(10px);
            }

            .rotation-icon {
                font-size: 0.9rem;
                animation: rotate 2s linear infinite;
            }

            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .rotation-timer {
                font-size: 0.7rem;
                color: #999;
                min-width: 3rem;
                text-align: center;
            }

            .rotation-status.rotating {
                background: rgba(0, 212, 255, 0.2);
                border: 1px solid rgba(0, 212, 255, 0.3);
                color: #00d4ff;
            }

            .rotation-status.rotating .rotation-icon {
                animation-duration: 0.5s;
            }

            .carousel-refresh-btn {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                cursor: pointer;
                font-size: 0.8rem;
                margin-left: 1rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .carousel-refresh-btn:hover {
                background: rgba(0, 212, 255, 0.2);
                border-color: #00d4ff;
                color: #00d4ff;
            }

            .carousel-refresh-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(styles);
    }

    addRotationControls() {
        this.carouselData.forEach((data, carouselId) => {
            const header = data.element.querySelector('.section-header, .carousel-header, h2, h3');
            if (!header || header.querySelector('.carousel-refresh-btn')) return;

            const refreshBtn = document.createElement('button');
            refreshBtn.className = 'carousel-refresh-btn';
            refreshBtn.innerHTML = '🔄 Actualizar';
            refreshBtn.addEventListener('click', () => this.manualRotate(carouselId));

            header.appendChild(refreshBtn);
        });
    }

    startAutoRotation() {
        this.carouselData.forEach((data, carouselId) => {
            if (data.config.autoStart) {
                this.startCarouselRotation(carouselId);
            }
        });
    }

    startCarouselRotation(carouselId) {
        const data = this.carouselData.get(carouselId);
        if (!data || data.isRotating) return;

        data.isRotating = true;
        this.lastRotationTime.set(carouselId, Date.now());

        const intervalId = setInterval(() => {
            if (this.isVisible) {
                this.rotateCarousel(carouselId);
            }
        }, data.config.interval);

        this.rotationIntervals.set(carouselId, intervalId);

        // Actualizar indicador visual
        this.updateRotationIndicator(carouselId, true);

        console.log(`▶️ Started rotation for ${data.type} carousel (every ${data.config.interval / 1000}s)`);
    }

    stopCarouselRotation(carouselId) {
        const intervalId = this.rotationIntervals.get(carouselId);
        if (intervalId) {
            clearInterval(intervalId);
            this.rotationIntervals.delete(carouselId);
        }

        const data = this.carouselData.get(carouselId);
        if (data) {
            data.isRotating = false;
        }

        this.updateRotationIndicator(carouselId, false);
    }

    async rotateCarousel(carouselId) {
        const data = this.carouselData.get(carouselId);
        if (!data) return;

        try {
            this.showRotationLoading(carouselId, true);
            
            const newSeed = this.generateSeed();
            const response = await fetch(`/api/carousel/rotate?type=${data.type}&seed=${newSeed}&count=25`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success && result.series) {
                await this.updateCarouselContent(carouselId, result.series);
                data.lastSeed = newSeed;
                this.lastRotationTime.set(carouselId, Date.now());
                
                console.log(`🔄 Rotated ${data.type} carousel with ${result.series.length} new items`);
            }

        } catch (error) {
            console.error(`❌ Failed to rotate ${data.type} carousel:`, error);
            this.showRotationError(carouselId);
        } finally {
            this.showRotationLoading(carouselId, false);
        }
    }

    async updateCarouselContent(carouselId, newSeries) {
        const data = this.carouselData.get(carouselId);
        const carousel = data.element;
        
        // Buscar el contenedor de elementos del carrusel
        const itemsContainer = carousel.querySelector(
            '.netflix-carousel-items, .carousel-items, .series-grid, .movies-grid, .swiper-wrapper'
        );
        
        if (!itemsContainer) {
            console.warn('No items container found for carousel update');
            return;
        }

        // Crear nuevos elementos HTML
        const newItemsHTML = newSeries.map(series => this.createSeriesItemHTML(series)).join('');
        
        // Animación de salida
        itemsContainer.style.opacity = '0.5';
        itemsContainer.style.transform = 'translateY(-10px)';
        
        // Esperar un poco para la animación
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Actualizar contenido
        itemsContainer.innerHTML = newItemsHTML;
        
        // Animación de entrada
        itemsContainer.style.transition = 'all 0.5s ease';
        itemsContainer.style.opacity = '1';
        itemsContainer.style.transform = 'translateY(0)';
        
        // Reinicializar eventos si es necesario
        this.reinitializeCarouselEvents(carousel);
    }

    createSeriesItemHTML(series) {
        const posterUrl = series.poster_path 
            ? `https://image.tmdb.org/t/p/w500${series.poster_path}`
            : '/images/no-poster-series.svg';
        
        const rating = series.vote_average ? Number(series.vote_average).toFixed(1) : 'N/A';
        const year = series.first_air_date ? new Date(series.first_air_date).getFullYear() : '';

        return `
            <div class="netflix-item" data-series-id="${series.id}">
                <a href="/series/${series.id}" class="netflix-item-link">
                    <div class="netflix-item-image">
                        <img src="${posterUrl}" 
                             alt="${series.title || series.display_title || 'K-Drama'}"
                             loading="lazy"
                             onerror="this.src='/images/no-poster-series.svg'">
                        <div class="netflix-item-overlay">
                            <div class="netflix-item-info">
                                <h4 class="netflix-item-title">${series.title || series.display_title || 'Sin título'}</h4>
                                <div class="netflix-item-meta">
                                    <span class="netflix-item-rating">⭐ ${rating}</span>
                                    ${year ? `<span class="netflix-item-year">${year}</span>` : ''}
                                </div>
                            </div>
                            <div class="netflix-item-actions">
                                <button class="netflix-play-btn" onclick="window.location.href='/series/${series.id}'">
                                    ▶️ Ver
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    reinitializeCarouselEvents(carousel) {
        // Reinicializar eventos de carrusel (scroll, swipe, etc.)
        const items = carousel.querySelectorAll('.netflix-item');
        items.forEach(item => {
            // Agregar eventos de hover
            item.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });
            
            item.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });
    }

    manualRotate(carouselId) {
        const data = this.carouselData.get(carouselId);
        if (!data) return;

        // Deshabilitar el botón temporalmente
        const refreshBtn = data.element.querySelector('.carousel-refresh-btn');
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '🔄 Actualizando...';
        }

        this.rotateCarousel(carouselId).finally(() => {
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '🔄 Actualizar';
            }
        });
    }

    generateSeed() {
        // Generar semilla basada en tiempo con variación aleatoria
        const now = Date.now();
        const random = Math.floor(Math.random() * 1000);
        return `${now}-${random}`;
    }

    updateRotationIndicator(carouselId, isRotating) {
        const data = this.carouselData.get(carouselId);
        if (!data) return;

        const indicator = data.element.querySelector('.rotation-status');
        if (!indicator) return;

        if (isRotating) {
            indicator.classList.add('rotating');
            indicator.querySelector('.rotation-text').textContent = 'Rotación activa';
        } else {
            indicator.classList.remove('rotating');
            indicator.querySelector('.rotation-text').textContent = 'Rotación pausada';
        }
    }

    showRotationLoading(carouselId, isLoading) {
        const data = this.carouselData.get(carouselId);
        if (!data) return;

        const status = data.element.querySelector('.rotation-status');
        if (!status) return;

        if (isLoading) {
            status.classList.add('rotating');
            status.querySelector('.rotation-text').textContent = 'Actualizando...';
        } else {
            status.classList.remove('rotating');
            status.querySelector('.rotation-text').textContent = 'Contenido rotativo';
        }
    }

    showRotationError(carouselId) {
        const data = this.carouselData.get(carouselId);
        if (!data) return;

        const status = data.element.querySelector('.rotation-status');
        if (status) {
            status.style.background = 'rgba(255, 107, 107, 0.2)';
            status.style.borderColor = '#ff6b6b';
            status.style.color = '#ff6b6b';
            status.querySelector('.rotation-text').textContent = 'Error al actualizar';
            
            setTimeout(() => {
                status.style.background = '';
                status.style.borderColor = '';
                status.style.color = '';
                status.querySelector('.rotation-text').textContent = 'Contenido rotativo';
            }, 3000);
        }
    }

    pauseAllRotations() {
        this.rotationIntervals.forEach((intervalId, carouselId) => {
            clearInterval(intervalId);
            this.updateRotationIndicator(carouselId, false);
        });
        console.log('⏸️ All carousel rotations paused');
    }

    resumeAllRotations() {
        this.carouselData.forEach((data, carouselId) => {
            if (data.config.autoStart && !this.rotationIntervals.has(carouselId)) {
                this.startCarouselRotation(carouselId);
            }
        });
        console.log('▶️ All carousel rotations resumed');
    }

    destroy() {
        this.pauseAllRotations();
        this.rotationIntervals.clear();
        this.carouselData.clear();
        console.log('🗑️ Carousel Rotation Manager destroyed');
    }
}

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname === '/' || window.location.pathname === '/home') {
        // Pequeño delay para asegurar que todos los elementos estén cargados
        setTimeout(() => {
            window.carouselRotationManager = new CarouselRotationManager();
            
            // Mostrar notificación de inicio
            if (typeof showToast === 'function') {
                showToast('🔄 Sistema de rotación de carruseles activado', 'info', 3000);
            } else {
                console.log('🔄 Sistema de rotación de carruseles activado');
            }
        }, 1000);
    }
});

// Limpiar al cambiar de página
window.addEventListener('beforeunload', () => {
    if (window.carouselRotationManager) {
        window.carouselRotationManager.destroy();
    }
});