import './bootstrap';

// Dorasia custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dorasia app loaded');
    
    // Card hover effects
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        let hoverTimeout;
        
        card.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                this.classList.add('hovering');
            }, window.innerWidth <= 768 ? 300 : 500);
        });
        
        card.addEventListener('mouseleave', function() {
            clearTimeout(hoverTimeout);
            this.classList.remove('hovering');
        });
    });
    
    // Carousel navigation
    window.slideCarousel = function(button, direction) {
        const container = button.parentElement;
        const carousel = container.querySelector('.carousel');
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // For mobile: cards are 85vw, so calculate based on viewport
            const cardWidth = window.innerWidth * 0.85 + 16; // 85vw + gap
            const currentIndex = parseInt(carousel.dataset.current || '0');
            const maxCards = carousel.children.length;
            
            let newIndex = currentIndex + direction;
            newIndex = Math.max(0, Math.min(newIndex, maxCards - 1));
            
            carousel.style.transform = `translateX(-${newIndex * cardWidth}px)`;
            carousel.dataset.current = newIndex;
        } else {
            // Desktop behavior
            const cardWidth = 350 + 8;
            const currentIndex = parseInt(carousel.dataset.current || '0');
            const maxCards = carousel.children.length;
            const visibleCards = Math.floor(container.offsetWidth / cardWidth);
            const maxIndex = Math.max(0, maxCards - visibleCards);
            
            let newIndex = currentIndex + direction;
            newIndex = Math.max(0, Math.min(newIndex, maxIndex));
            
            carousel.style.transform = `translateX(-${newIndex * cardWidth}px)`;
            carousel.dataset.current = newIndex;
        }
    };
});
