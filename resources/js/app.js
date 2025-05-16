import './bootstrap';
<<<<<<< HEAD
import './page-transitions';
import './catalog-enhanced';
import './profile-transitions';
import './toast';
import './keyboard-shortcuts';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
=======
import { createApp } from 'vue';
import { createPinia } from 'pinia';

// Importar componentes Vue
import ContentCarousel from './components/ContentCarousel.vue';
import ContentCard from './components/ContentCard.vue';
import FavoriteButton from './components/FavoriteButton.vue';
import WatchlistButton from './components/WatchlistButton.vue';
import RatingStars from './components/RatingStars.vue';
import SearchAutocomplete from './components/SearchAutocomplete.vue';

// Importar stores
import { useWatchlistStore } from './stores/watchlistStore';
import { useUserContentStore } from './stores/userContent';

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

// Inicializar las stores si el usuario está autenticado
window.addEventListener('DOMContentLoaded', () => {
    const isAuthenticated = document.body.classList.contains('user-authenticated');
    window.isAuthenticated = isAuthenticated;
    
    if (isAuthenticated) {
        // Inicializar watchlist store
        const watchlistStore = useWatchlistStore();
        watchlistStore.initialize();
        
        // Agregar store a window para acceso desde Alpine.js
        window.stores = {
            watchlist: watchlistStore
        };
        
        // Para Alpine.js - Proporcionar la tienda watchlist
        document.addEventListener('alpine:init', () => {
            Alpine.store('watchlist', {
                exists(id) {
                    return watchlistStore.exists(id);
                },
                toggle(id, type) {
                    return watchlistStore.toggle(id, type);
                }
            });
        });
    }
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
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
