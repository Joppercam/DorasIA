// En resources/js/stores/userContent.js

import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserContentStore = defineStore('userContent', {
  state: () => ({
    favorites: [],
    watchlist: [],
    ratings: [],
    isLoading: false,
    error: null
  }),
  
  getters: {
    isFavorite: (state) => (contentId, contentType) => {
      return state.favorites.some(
        item => item.content_id === contentId && item.content_type === contentType
      );
    },
    
    isInWatchlist: (state) => (contentId, contentType) => {
      return state.watchlist.some(
        item => item.content_id === contentId && item.content_type === contentType
      );
    }
  },
  
  actions: {
    async fetchUserContent() {
      if (!window.isAuthenticated) return;
      
      this.isLoading = true;
      this.error = null;
      
      try {
        // Obtener favoritos
        const favoritesResponse = await axios.get('/api/favorites');
        this.favorites = favoritesResponse.data.data;
        
        // Obtener lista de ver más tarde
        const watchlistResponse = await axios.get('/api/watchlist');
        this.watchlist = watchlistResponse.data.data;
      } catch (error) {
        console.error('Error al cargar contenido del usuario:', error);
        this.error = 'No se pudo cargar el contenido del usuario';
      } finally {
        this.isLoading = false;
      }
    },
    
    async toggleFavorite(contentId, contentType) {
      if (!window.isAuthenticated) {
        // Redirigir a login si no está autenticado
        window.location.href = '/login';
        return;
      }
      
      try {
        const isFavorite = this.isFavorite(contentId, contentType);
        const action = isFavorite ? 'remove' : 'add';
        
        const response = await axios.post('/api/favorites/toggle', {
          content_id: contentId,
          content_type: contentType,
          action: action
        });
        
        if (response.data.success) {
          if (action === 'add') {
            // Agregar a favoritos si no existe
            if (!isFavorite) {
              this.favorites.push({
                content_id: contentId,
                content_type: contentType,
                created_at: new Date().toISOString()
              });
            }
          } else {
            // Eliminar de favoritos
            this.favorites = this.favorites.filter(
              item => !(item.content_id === contentId && item.content_type === contentType)
            );
          }
        }
        
        return response.data;
      } catch (error) {
        console.error('Error al actualizar favoritos:', error);
        throw error;
      }
    },
    
    async toggleWatchlist(contentId, contentType) {
      if (!window.isAuthenticated) {
        // Redirigir a login si no está autenticado
        window.location.href = '/login';
        return;
      }
      
      try {
        const inWatchlist = this.isInWatchlist(contentId, contentType);
        const action = inWatchlist ? 'remove' : 'add';
        
        const response = await axios.post('/api/watchlist/toggle', {
          content_id: contentId,
          content_type: contentType,
          action: action
        });
        
        if (response.data.success) {
          if (action === 'add') {
            // Agregar a la lista si no existe
            if (!inWatchlist) {
              this.watchlist.push({
                content_id: contentId,
                content_type: contentType,
                created_at: new Date().toISOString()
              });
            }
          } else {
            // Eliminar de la lista
            this.watchlist = this.watchlist.filter(
              item => !(item.content_id === contentId && item.content_type === contentType)
            );
          }
        }
        
        return response.data;
      } catch (error) {
        console.error('Error al actualizar lista:', error);
        throw error;
      }
    }
  }
});