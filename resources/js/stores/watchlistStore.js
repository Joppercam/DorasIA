import { defineStore } from 'pinia';
import axios from 'axios';

export const useWatchlistStore = defineStore('watchlist', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    initialized: false
  }),
  
  getters: {
    exists: (state) => (id) => {
      return state.items.some(item => item.content_id.toString() === id.toString());
    },
    
    count: (state) => {
      return state.items.length;
    }
  },
  
  actions: {
    async initialize() {
      if (this.initialized || !window.isAuthenticated) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/watchlist/items');
        this.items = response.data.data || [];
        this.initialized = true;
      } catch (error) {
        console.error('Error al cargar elementos de la lista:', error);
        this.error = 'No se pudo cargar tu lista de visualización';
      } finally {
        this.loading = false;
      }
    },
    
    async toggle(id, type) {
      if (!window.isAuthenticated) {
        // Redirigir a login si el usuario no está autenticado
        window.location.href = '/login';
        return;
      }
      
      if (!this.initialized) {
        await this.initialize();
      }
      
      this.loading = true;
      
      try {
        const exists = this.exists(id);
        const action = exists ? 'remove' : 'add';
        
        const response = await axios.post('/api/watchlist/toggle', {
          content_id: id,
          content_type: type,
          action: action
        });
        
        if (response.data.success) {
          if (action === 'add') {
            if (!exists) {
              this.items.push({
                content_id: id,
                content_type: type
              });
            }
          } else {
            this.items = this.items.filter(item => 
              !(item.content_id.toString() === id.toString())
            );
          }
        }
      } catch (error) {
        console.error('Error al modificar la lista:', error);
        this.error = 'No se pudo actualizar tu lista de visualización';
      } finally {
        this.loading = false;
      }
    }
  }
});