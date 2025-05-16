// Keyboard Shortcuts for Dorasia
class KeyboardShortcuts {
    constructor() {
        this.shortcuts = {
            's': this.focusSearch.bind(this),
            'h': this.goHome.bind(this),
            'c': this.goCatalog.bind(this),
            'n': this.goNews.bind(this),
            'p': this.goProfile.bind(this),
            'w': this.goWatchlist.bind(this),
            '?': this.showHelp.bind(this),
            'ArrowLeft': this.navigate.bind(this, 'left'),
            'ArrowRight': this.navigate.bind(this, 'right'),
            'Escape': this.closeModals.bind(this)
        };

        this.init();
    }

    init() {
        document.addEventListener('keydown', (e) => {
            // Don't trigger shortcuts when typing in input fields
            if (e.target.matches('input, textarea, select')) {
                return;
            }

            // Don't trigger shortcuts when modifiers are pressed
            if (e.ctrlKey || e.metaKey || e.altKey) {
                return;
            }

            const handler = this.shortcuts[e.key];
            if (handler) {
                e.preventDefault();
                handler();
            }
        });
    }

    focusSearch() {
        // Try to find the global search bar first
        const globalSearchInput = document.querySelector('[x-data="searchBar()"] input');
        if (globalSearchInput) {
            globalSearchInput.focus();
            return;
        }
        
        // Fallback to any search input
        const searchInput = document.querySelector('input[placeholder*="Buscar"]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    goHome() {
        window.location.href = '/';
    }

    goCatalog() {
        window.location.href = '/catalog';
    }

    goNews() {
        window.location.href = '/news';
    }

    goProfile() {
        const profileLink = document.querySelector('a[href*="/profile"]');
        if (profileLink) {
            window.location.href = profileLink.href;
        }
    }

    goWatchlist() {
        const watchlistLink = document.querySelector('a[href*="/watchlist"]');
        if (watchlistLink) {
            window.location.href = watchlistLink.href;
        }
    }

    showHelp() {
        // Create and show the keyboard shortcuts modal
        if (document.getElementById('keyboard-shortcuts-modal')) {
            document.getElementById('keyboard-shortcuts-modal').remove();
        }

        const modal = document.createElement('div');
        modal.id = 'keyboard-shortcuts-modal';
        modal.className = 'fixed inset-0 z-[60] flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/80" onclick="document.getElementById('keyboard-shortcuts-modal').remove()"></div>
            <div class="relative bg-gray-900 rounded-lg shadow-xl max-w-lg w-full p-6 z-10">
                <h2 class="text-xl font-bold mb-4">Atajos de Teclado</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Buscar</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">S</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Inicio</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">H</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Catálogo</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">C</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Noticias</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">N</kbd>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Perfil</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">P</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Mi Lista</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">W</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Ayuda</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">?</kbd>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-800">
                            <span class="text-gray-400">Cerrar modal</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs">ESC</kbd>
                        </div>
                    </div>
                </div>
                <button onclick="document.getElementById('keyboard-shortcuts-modal').remove()" 
                        class="mt-6 w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                    Cerrar
                </button>
            </div>
        `;
        document.body.appendChild(modal);
    }

    navigate(direction) {
        // Navigation for carousel items
        const activeCarousel = document.querySelector('.netflix-carousel:hover');
        if (activeCarousel) {
            const button = activeCarousel.querySelector(
                direction === 'left' ? '[data-carousel-prev]' : '[data-carousel-next]'
            );
            if (button) {
                button.click();
            }
        }
    }

    closeModals() {
        // Close any open modals
        const modals = document.querySelectorAll('[x-show]');
        modals.forEach(modal => {
            const alpineInstance = modal._x_dataStack?.[0];
            if (alpineInstance && alpineInstance.showDetails) {
                alpineInstance.showDetails = false;
            }
        });

        // Close the keyboard shortcuts modal if open
        const shortcutsModal = document.getElementById('keyboard-shortcuts-modal');
        if (shortcutsModal) {
            shortcutsModal.remove();
        }
    }
}

// Initialize keyboard shortcuts when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new KeyboardShortcuts();
});

// Export for potential use in other modules
window.KeyboardShortcuts = KeyboardShortcuts;