<template>
    <div class="search-autocomplete">
      <div class="search-input-wrapper">
        <input
          type="text"
          class="form-control"
          v-model="query"
          @input="onInput"
          @focus="showResults = true"
          @blur="onBlur"
          placeholder="Buscar películas, series, actores..."
        />
        <div class="search-icon">
          <i class="fas fa-search"></i>
        </div>
      </div>
      
      <div v-if="showResults && results.length > 0" class="search-results">
        <div v-for="(result, index) in results" :key="index" class="search-result-item" @mousedown="goToResult(result)">
          <div class="result-image">
            <img :src="result.image_url || '/img/no-image.png'" :alt="result.title" />
          </div>
          <div class="result-info">
            <div class="result-title">{{ result.title }}</div>
            <div class="result-meta">{{ result.type }} • {{ result.year }}</div>
          </div>
        </div>
        
        <div class="search-footer">
          <a @mousedown.prevent="viewAllResults" href="/search" class="view-all">
            Ver todos los resultados
          </a>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, watch } from 'vue';
  import axios from 'axios';
  import { debounce } from 'lodash';
  
  export default {
    setup() {
      const query = ref('');
      const results = ref([]);
      const showResults = ref(false);
      
      const searchAPI = debounce(async (searchQuery) => {
        if (!searchQuery || searchQuery.length < 2) {
          results.value = [];
          return;
        }
        
        try {
          const response = await axios.get('/api/search', {
            params: { query: searchQuery, limit: 5 }
          });
          
          results.value = response.data.results || [];
        } catch (error) {
          console.error('Error searching:', error);
          results.value = [];
        }
      }, 300);
      
      const onInput = () => {
        searchAPI(query.value);
      };
      
      const onBlur = () => {
        // Pequeño retraso para permitir click en los resultados
        setTimeout(() => {
          showResults.value = false;
        }, 200);
      };
      
      const goToResult = (result) => {
        if (result.url) {
          window.location.href = result.url;
        }
      };
      
      const viewAllResults = () => {
        window.location.href = `/search?query=${encodeURIComponent(query.value)}`;
      };
      
      return {
        query,
        results,
        showResults,
        onInput,
        onBlur,
        goToResult,
        viewAllResults
      };
    }
  }
  </script>
  
  <style scoped>
  .search-autocomplete {
    position: relative;
    width: 100%;
  }
  
  .search-input-wrapper {
    position: relative;
  }
  
  .search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
  }
  
  .search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: #222;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
  }
  
  .search-result-item {
    display: flex;
    padding: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
  }
  
  .search-result-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .result-image {
    width: 50px;
    height: 70px;
    overflow: hidden;
    margin-right: 10px;
    border-radius: 4px;
  }
  
  .result-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .result-info {
    flex: 1;
  }
  
  .result-title {
    font-weight: 500;
    margin-bottom: 5px;
  }
  
  .result-meta {
    font-size: 0.8rem;
    color: #aaa;
  }
  
  .search-footer {
    padding: 10px;
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .view-all {
    color: #e50914;
    text-decoration: none;
  }
  
  .view-all:hover {
    text-decoration: underline;
  }
  </style>