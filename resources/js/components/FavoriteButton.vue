<template>
  <button 
    class="btn btn-sm" 
    :class="[isFavorite ? 'btn-danger' : 'btn-outline-light']"
    @click.prevent="toggleFavorite"
    :data-bs-tooltip="tooltipPosition" 
    :title="isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos'">
    <i class="bi" :class="[isFavorite ? 'bi-heart-fill' : 'bi-heart']"></i>
  </button>
</template>

<script>
import { useUserContentStore } from '../stores/userContent';

export default {
  props: {
    contentId: {
      type: Number,
      required: true
    },
    contentType: {
      type: String,
      required: true
    },
    tooltipPosition: {
      type: String,
      default: 'bottom'
    }
  },
  
  setup(props) {
    const userContentStore = useUserContentStore();
    
    return { userContentStore };
  },
  
  data() {
    return {
      isFavorite: false,
      isLoading: false
    }
  },
  
  mounted() {
    this.checkIfFavorite();
  },
  
  methods: {
    async checkIfFavorite() {
      // Verificar en la tienda si este elemento está en favoritos
      this.isFavorite = this.userContentStore.isFavorite(this.contentId, this.contentType);
    },
    
    async toggleFavorite() {
      if (this.isLoading) return;
      
      this.isLoading = true;
      
      try {
        // Llamar a la acción en la tienda
        await this.userContentStore.toggleFavorite(this.contentId, this.contentType);
        // Actualizar estado local
        this.isFavorite = this.userContentStore.isFavorite(this.contentId, this.contentType);
      } catch (error) {
        console.error('Error al actualizar favoritos:', error);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
button {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-outline-light:hover {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>