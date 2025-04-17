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
        v-show="showPrevButton"
        @click="prevSlide" 
        class="carousel-control carousel-control-prev" 
        type="button">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      
      <!-- Contenedor de slides -->
      <div class="carousel-container">
        <div class="carousel-track" :style="trackStyle" ref="track">
          <div 
            v-for="(item, index) in items" 
            :key="index" 
            class="carousel-item"
            :style="{ width: itemWidth + 'px' }">
            
            <div class="content-card h-100">
              <div class="position-relative">
                <img 
                  :src="item.poster_path ? '/storage/' + item.poster_path : '/images/placeholder-poster.jpg'" 
                  class="card-img-top" 
                  :alt="item.title">
                <div class="content-type-badge">{{ item.type }}</div>
                <div v-if="item.vote_average" class="rating-badge">
                  <i class="bi bi-star-fill"></i> {{ Number(item.vote_average).toFixed(1) }}
                </div>
              </div>
              <div class="card-body p-2">
                <h6 class="card-title text-truncate">{{ item.title }}</h6>
                <p class="card-text small text-muted">
                  {{ item.country_of_origin }} • {{ formatYear(item.release_date || item.first_air_date) }}
                </p>
                <a :href="item.link" class="stretched-link"></a>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      
      <button 
        v-show="showNextButton"
        @click="nextSlide" 
        class="carousel-control carousel-control-next" 
        type="button">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</template>

<script>
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
  
  data() {
    return {
      currentIndex: 0,
      itemWidth: 0,
      trackWidth: 0,
      containerWidth: 0,
      itemsToScroll: 1,
      resizeObserver: null
    }
  },
  
  computed: {
    trackStyle() {
      return {
        transform: `translateX(-${this.currentIndex * this.itemWidth}px)`,
        width: `${this.trackWidth}px`
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
    
    // Observar cambios en el tamaño de la ventana
    this.resizeObserver = new ResizeObserver(this.handleResize);
    this.resizeObserver.observe(this.$el);
    
    window.addEventListener('resize', this.calculateDimensions);
  },
  
  beforeUnmount() {
    window.removeEventListener('resize', this.calculateDimensions);
    
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
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
      const nextIndex = this.currentIndex + this.itemsToScroll;
      this.currentIndex = Math.min(nextIndex, this.totalSlides);
    },
    
    prevSlide() {
      const prevIndex = this.currentIndex - this.itemsToScroll;
      this.currentIndex = Math.max(prevIndex, 0);
    },
    
    handleResize() {
      this.calculateDimensions();
    },
    
    formatYear(dateString) {
      if (!dateString) return 'N/A';
      return new Date(dateString).getFullYear();
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
  transition: transform 0.5s ease;
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

@media (max-width: 768px) {
  .content-card .card-img-top {
    height: 200px;
  }
}
</style>