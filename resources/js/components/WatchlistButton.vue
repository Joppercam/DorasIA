<template>
  <button 
    class="btn btn-sm" 
    :class="[inWatchlist ? 'btn-primary' : 'btn-outline-light']"
    @click.prevent="toggleWatchlist"
    :data-bs-tooltip="tooltipPosition" 
    :title="inWatchlist ? 'Quitar de Mi Lista' : 'Agregar a Mi Lista'">
    <i class="bi" :class="[inWatchlist ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus']"></i>
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
      inWatchlist: false,
      isLoading: false
    }
  },
  
  mounted() {
    this.checkIfInWatchlist();
  },
  
  methods: {
    async checkIfInWatchlist() {
      // Verificar en la tienda si este elemento está en la lista de ver más tarde
      this.inWatchlist = this.userContentStore.isInWatchlist(this.contentId, this.contentType);
    },
    
    async toggleWatchlist() {
      if (this.isLoading) return;
      
      this.isLoading = true;
      
      try {
        // Llamar a la acción en la tienda
        await this.userContentStore.toggleWatchlist(this.contentId, this.contentType);
        // Actualizar estado local
        this.inWatchlist = this.userContentStore.isInWatchlist(this.contentId, this.contentType);
      } catch (error) {
        console.error('Error al actualizar lista:', error);
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