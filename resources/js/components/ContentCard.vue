<template>
  <div class="content-card h-100">
    <div class="position-relative">
      <img :src="item.poster_path" class="card-img-top" :alt="item.title">
      <div class="content-type-badge">{{ item.type }}</div>
      <div v-if="item.vote_average" class="rating-badge">
        <i class="bi bi-star-fill"></i> {{ parseFloat(item.vote_average).toFixed(1) }}
      </div>
      
      <!-- Superposición con acciones -->
      <div class="card-actions">
        <div class="action-buttons" v-if="isAuthenticated">
          <favorite-button 
            :content-id="item.id" 
            :content-type="contentType"
            tooltip-position="top"
          ></favorite-button>
          
          <watchlist-button 
            :content-id="item.id" 
            :content-type="contentType"
            tooltip-position="top"
          ></watchlist-button>
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
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  
  computed: {
    isAuthenticated() {
      // Esto debería verificar si el usuario está autenticado
      // Puedes obtenerlo desde una tienda Pinia o desde window
      return window.isAuthenticated || false;
    },
    
    contentType() {
      // Determina el tipo de contenido basado en el tipo mostrado
      return this.item.type === 'Película' ? 'movie' : 'tv-show';
    }
  }
}
</script>

<style scoped>
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

.content-card:hover .card-actions {
  opacity: 1;
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

.action-buttons {
  display: flex;
  gap: 10px;
}

@media (max-width: 768px) {
  .content-card .card-img-top {
    height: 200px;
  }
  
  /* En móvil, mostrar acciones siempre para facilitar la interacción táctil */
  .card-actions {
    opacity: 1;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 50%);
    align-items: flex-end;
    padding-bottom: 15px;
  }
}
</style>