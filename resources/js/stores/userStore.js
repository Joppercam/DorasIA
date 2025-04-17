import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('user', {
  state: () => ({
    favorites: [],
    watchlists: [],
    ratings: [],
    isAuthenticated: false,
    user: null,
    loading: false,
  }),
  
  actions: {
    async checkAuth() {
      try {
        this.loading = true;
        const response = await axios.get('/api/user');
        this.isAuthenticated = true;
        this.user = response.data;
      } catch (error) {
        this.isAuthenticated = false;
        this.user = null;
      } finally {
        this.loading = false;
      }
    },
    
    async fetchFavorites() {
      if (!this.isAuthenticated) return;
      
      try {
        const response = await axios.get('/api/favorites');
        this.favorites = response.data.favorites || [];
      } catch (error) {
        console.error('Error fetching favorites:', error);
      }
    },
    
    async fetchWatchlists() {
      if (!this.isAuthenticated) return;
      
      try {
        const response = await axios.get('/api/watchlists');
        this.watchlists = response.data.watchlists || [];
      } catch (error) {
        console.error('Error fetching watchlists:', error);
      }
    },
    
    async fetchRatings() {
      if (!this.isAuthenticated) return;
      
      try {
        const response = await axios.get('/api/ratings');
        this.ratings = response.data.ratings || [];
      } catch (error) {
        console.error('Error fetching ratings:', error);
      }
    },
    
    isFavorite(contentType, contentId) {
      return this.favorites.some(fav => 
        fav.content_type === contentType && 
        fav.content_id === parseInt(contentId)
      );
    },
    
    getRating(contentType, contentId) {
      const rating = this.ratings.find(r => 
        r.content_type === contentType && 
        r.content_id === parseInt(contentId)
      );
      
      return rating ? rating.rating : 0;
    }
  }
});