// resources/js/components/ContentCarousel.vue
<template>
  <div class="content-carousel mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="m-0">{{ title }}</h2>
      <a v-if="viewAllLink" :href="viewAllLink" class="btn btn-sm btn-outline-primary">
        Ver todos
      </a>
    </div>
    
    <div class="position-relative">
      <!-- Botones de navegación -->
      <button 
        v-show="showPrevButton && items.length > itemsPerView"
        @click="prevSlide" 
        class="carousel-control carousel-control-prev" 
        type="button">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      
      <!-- Contenedor de slides -->
      <div class="carousel-container" ref="container">
        <div class="carousel-track" :style="trackStyle" ref="track">
          <div 
            v-for="(item, index) in items" 
            :key="index" 
            class="carousel-item"
            :style="{ width: itemWidth + 'px' }">
            
            <div class="content-card h-100">
              <div class="position-relative">
                <img 
                  :src="item.poster_path ? item.poster_path : '/images/placeholder-poster.jpg'" 
                  class="card-img-top" 
                  :alt="item.title"
                  loading="lazy">
                <div class="content-type-badge">{{ item.type }}</div>
                <div v-if="item.vote_average" class="rating-badge">
                  <i class="bi bi-star-fill"></i> {{ parseFloat(item.vote_average).toFixed(1) }}
                </div>
                
                <!-- Overlay con acciones -->
                <div class="card-actions">
                  <div class="action-buttons" v-if="isAuthenticated">
                    <button 
                      @click.stop="toggleFavorite(item)" 
                      class="btn btn-sm action-btn"
                      :class="{'btn-danger': isFavorite(item.id), 'btn-outline-light': !isFavorite(item.id)}"
                      :title="isFavorite(item.id) ? 'Quitar de favoritos' : 'Añadir a favoritos'">
                      <i class="bi" :class="[isFavorite(item.id) ? 'bi-heart-fill' : 'bi-heart']"></i>
                    </button>
                    
                    <button 
                      @click.stop="toggleWatchlist(item)" 
                      class="btn btn-sm action-btn"
                      :class="{'btn-primary': isInWatchlist(item.id), 'btn-outline-light': !isInWatchlist(item.id)}"
                      :title="isInWatchlist(item.id) ? 'Quitar de Mi Lista' : 'Añadir a Mi Lista'">
                      <i class="bi" :class="[isInWatchlist(item.id) ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus']"></i>
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="card-body p-2">
                <h6 class="card-title text-truncate">{{ item.title }}</h6>
                <p class="card-text small text-muted">
                  {{ item.country }} • {{ item.year }}
                </p>
                <a :href="item.link" class="stretched-link"></a>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      
      <button 
        v-show="showNextButton && items.length > itemsPerView"
        @click="nextSlide" 
        class="carousel-control carousel-control-next" 
        type="button">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</template>

<script>
import { useUserContentStore } from '../stores/userContent';

export default {
  props: {
    title: {
      type: String,
      required: true
    },
    items: {
      type: Array,
      required: true
    },
    viewAllLink: {
      type: String,
      default: null
    },
    itemsPerView: {
      type: Number,
      default: 6
    }
  },
  
  setup() {
    const userContentStore = useUserContentStore();
    
    return { 
      userContentStore,
      isFavorite: (id) => userContentStore.isFavorite(id),
      isInWatchlist: (id) => userContentStore.isInWatchlist(id)
    };
  },
  
  data() {
    return {
      currentIndex: 0,
      itemWidth: 0,
      trackWidth: 0,
      containerWidth: 0,
      itemsToScroll: 1,
      touchStartX: 0,
      touchEndX: 0,
      animating: false,
      isAuthenticated: window.isAuthenticated || false // Tomar el estado de autenticación de window
    }
  },
  
  computed: {
    trackStyle() {
      return {
        transform: `translateX(-${this.currentIndex * this.itemWidth}px)`,
        width: `${this.trackWidth}px`,
        transition: this.animating ? 'transform 0.5s ease' : 'none'
      }
    },
    
    totalSlides() {
      return Math.max(0, this.items.length - this.itemsPerView);
    },
    
    showPrevButton() {
      return this.currentIndex > 0;
    },
    
    showNextButton() {
      return this.currentIndex < this.totalSlides;
    }
  },
  
  mounted() {
    this.calculateDimensions();
    this.setupTouchEvents();
    
    // Observar cambios en el tamaño
    this.resizeObserver = new ResizeObserver(this.handleResize);
    this.resizeObserver.observe(this.$el);
    
    window.addEventListener('resize', this.calculateDimensions);
    
    // Inicializar store
    if (this.isAuthenticated) {
      this.userContentStore.fetchUserContent();
    }
  },
  
  beforeUnmount() {
    window.removeEventListener('resize', this.calculateDimensions);
    
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
    
    // Eliminar eventos táctiles
    const track = this.$refs.track;
    if (track) {
      track.removeEventListener('touchstart', this.handleTouchStart);
      track.removeEventListener('touchend', this.handleTouchEnd);
    }
  },
  
  methods: {
    calculateDimensions() {
      const containerWidth = this.$el.offsetWidth;
      this.containerWidth = containerWidth;
      
      // Ajustar items por vista según el ancho de la pantalla
      if (containerWidth < 576) {
        this.itemsPerView = 2;
        this.itemsToScroll = 2;
      } else if (containerWidth < 768) {
        this.itemsPerView = 3;
        this.itemsToScroll = 3;
      } else if (containerWidth < 992) {
        this.itemsPerView = 4;
        this.itemsToScroll = 4;
      } else {
        this.itemsPerView = 6;
        this.itemsToScroll = 6;
      }
      
      // Calcular el ancho de cada item
      this.itemWidth = containerWidth / this.itemsPerView;
      
      // Calcular el ancho total del track
      this.trackWidth = this.itemWidth * this.items.length;
      
      // Ajustar el índice actual si es necesario
      this.currentIndex = Math.min(this.currentIndex, this.totalSlides);
    },
    
    nextSlide() {
      if (this.animating) return;
      
      this.animating = true;
      const nextIndex = this.currentIndex + this.itemsToScroll;
      this.currentIndex = Math.min(nextIndex, this.totalSlides);
      
      setTimeout(() => {
        this.animating = false;
      }, 500);
    },
    
    prevSlide() {
      if (this.animating) return;
      
      this.animating = true;
      const prevIndex = this.currentIndex - this.itemsToScroll;
      this.currentIndex = Math.max(prevIndex, 0);
      
      setTimeout(() => {
        this.animating = false;
      }, 500);
    },
    
    handleResize() {
      this.calculateDimensions();
    },
    
    setupTouchEvents() {
      const track = this.$refs.track;
      if (track) {
        track.addEventListener('touchstart', this.handleTouchStart, { passive: true });
        track.addEventListener('touchend', this.handleTouchEnd, { passive: true });
      }
    },
    
    handleTouchStart(e) {
      this.touchStartX = e.changedTouches[0].screenX;
    },
    
    handleTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].screenX;
      this.handleSwipe();
    },
    
    handleSwipe() {
      const SWIPE_THRESHOLD = 50;
      if (this.touchStartX - this.touchEndX > SWIPE_THRESHOLD) {
        // Deslizado hacia la izquierda
        this.nextSlide();
      } else if (this.touchEndX - this.touchStartX > SWIPE_THRESHOLD) {
        // Deslizado hacia la derecha
        this.prevSlide();
      }
    },
    
    toggleFavorite(item) {
      if (!this.isAuthenticated) {
        window.location.href = '/login';
        return;
      }
      
      this.userContentStore.toggleFavorite(item.id, this.getContentType(item));
    },
    
    toggleWatchlist(item) {
      if (!this.isAuthenticated) {
        window.location.href = '/login';
        return;
      }
      
      this.userContentStore.toggleWatchlist(item.id, this.getContentType(item));
    },
    
    getContentType(item) {
      return item.type === 'Película' ? 'movie' : 'tv-show';
    }
  }
}
</script>

<style scoped>
.carousel-container {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.carousel-track {
  display: flex;
  touch-action: pan-y;
}

.carousel-item {
  flex: 0 0 auto;
  padding: 0 10px;
}

.carousel-control {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 40px;
  height: 40px;
  background-color: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1;
  border: none;
  transition: background-color 0.3s ease;
}

.carousel-control:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

.carousel-control-prev {
  left: -20px;
}

.carousel-control-next {
  right: -20px;
}

.content-card {
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  border: none;
}

.content-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.content-card .card-img-top {
  height: 300px;
  object-fit: cover;
}

.content-type-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
}

.rating-badge {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background-color: rgba(255, 193, 7, 0.9);
  color: black;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.card-actions {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.content-card:hover .card-actions {
  opacity: 1;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.action-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .content-card .card-img-top {
    height: 200px;
  }
  
  /* En móvil, mostrar controles solo al tocar */
  .carousel-control {
    opacity: 0.5;
  }
  
  /* Hacer más grande el área táctil en móviles */
  .carousel-control {
    width: 36px;
    height: 36px;
  }
}
</style>