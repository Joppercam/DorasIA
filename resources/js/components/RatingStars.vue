// En resources/js/components/RatingStars.vue

<template>
    <div class="rating-stars">
      <div class="stars">
        <i 
          v-for="star in 10" 
          :key="star" 
          class="fas" 
          :class="[star <= hoveredRating ? 'fa-star' : 'fa-star',
                  star <= hoveredRating ? 'text-warning' : 'text-muted']"
          @mouseover="hoverStar(star)"
          @mouseleave="resetHover"
          @click="rateStar(star)"
        ></i>
      </div>
      <div v-if="showValue" class="rating-value">
        {{ currentRating || '-' }}/10
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import axios from 'axios';
  
  export default {
    props: {
      contentType: {
        type: String,
        required: true
      },
      contentId: {
        type: [Number, String],
        required: true
      },
      initialRating: {
        type: Number,
        default: 0
      },
      showValue: {
        type: Boolean,
        default: true
      }
    },
    
    setup(props, { emit }) {
      const currentRating = ref(props.initialRating);
      const hoveredRating = ref(0);
      
      const hoverStar = (star) => {
        hoveredRating.value = star;
      };
      
      const resetHover = () => {
        hoveredRating.value = currentRating.value;
      };
      
      const rateStar = async (star) => {
        try {
          const response = await axios.post('/ratings/rate', {
            content_type: props.contentType,
            content_id: props.contentId,
            rating: star
          });
          
          if (response.data.success) {
            currentRating.value = star;
            emit('rated', star);
          }
        } catch (error) {
          console.error('Error rating content:', error);
          
          // Si el error es 401 (no autenticado), redirigir a login
          if (error.response && error.response.status === 401) {
            window.location.href = '/login';
          }
        }
      };
      
      // Inicializar el hover con el rating actual
      hoveredRating.value = currentRating.value;
      
      return {
        currentRating,
        hoveredRating,
        hoverStar,
        resetHover,
        rateStar
      };
    }
  }
  </script>
  
  <style scoped>
  .rating-stars {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .stars {
    display: flex;
    gap: 0.25rem;
  }
  
  .stars i {
    cursor: pointer;
    font-size: 1.2rem;
  }
  
  .rating-value {
    font-size: 0.9rem;
    color: #ccc;
  }
  </style>