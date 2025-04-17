import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';

// Importar componentes Vue
import ContentCarousel from './components/ContentCarousel.vue';
import ContentCard from './components/ContentCard.vue';
import FavoriteButton from './components/FavoriteButton.vue';
import WatchlistButton from './components/WatchlistButton.vue';
import RatingStars from './components/RatingStars.vue';
import SearchAutocomplete from './components/SearchAutocomplete.vue';

// Crear instancia de Pinia (state management)
const pinia = createPinia();

// Inicializar la aplicación Vue
const app = createApp({});

// Registrar componentes globalmente
app.component('content-carousel', ContentCarousel);
app.component('content-card', ContentCard);
app.component('favorite-button', FavoriteButton);
app.component('watchlist-button', WatchlistButton);
app.component('rating-stars', RatingStars);
app.component('search-autocomplete', SearchAutocomplete);

// Usar Pinia
app.use(pinia);

// Montar la aplicación en elementos con el atributo data-vue
document.querySelectorAll('[data-vue]').forEach(el => {
    app.mount(el);
});

// Funcionalidad para navbar responsiva
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
});
