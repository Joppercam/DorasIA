{{-- Trailer Modal Component --}}
<div id="trailerModal" class="trailer-modal" style="display: none;">
    <div class="modal-overlay" onclick="closeTrailer()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="trailerTitle" class="modal-title">Trailer</h3>
            <button class="modal-close" onclick="closeTrailer()" aria-label="Cerrar trailer">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="video-container">
                <iframe id="trailerIframe" 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

<style>
.trailer-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
}

.modal-content {
    position: relative;
    width: 90%;
    max-width: 1000px;
    max-height: 90vh;
    background: #1a1a1a;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8);
    animation: slideUp 0.3s ease-out;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: rgba(0, 0, 0, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-title:before {
    content: "🎬";
    font-size: 1.2rem;
}

.modal-close {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

.modal-body {
    padding: 0;
}

.video-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    background: #000;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes slideDown {
    from {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
}

/* Mobile responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        max-width: none;
        margin: 1rem;
        border-radius: 15px;
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-title {
        font-size: 1.2rem;
    }
    
    .modal-close {
        width: 35px;
        height: 35px;
    }
    
    .video-container {
        padding-bottom: 56.25%;
    }
}

@media (max-width: 480px) {
    .modal-content {
        width: 98%;
        margin: 0.5rem;
        border-radius: 12px;
    }
    
    .modal-header {
        padding: 0.8rem 1rem;
    }
    
    .modal-title {
        font-size: 1rem;
    }
}

/* Loading state */
.video-container.loading:before {
    content: "⏳ Cargando trailer...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 1.1rem;
    z-index: 1;
}
</style>

<script>
// Trailer Modal Functions
function playTrailer(youtubeId, title = 'Trailer') {
    if (!youtubeId) {
        console.error('No YouTube ID provided');
        return;
    }
    
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    const titleElement = document.getElementById('trailerTitle');
    const videoContainer = iframe.parentElement;
    
    // Set title
    titleElement.textContent = title;
    
    // Show loading state
    videoContainer.classList.add('loading');
    
    // Construct YouTube URL
    const embedUrl = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&rel=0&modestbranding=1&playsinline=1`;
    
    // Set iframe source
    iframe.src = embedUrl;
    
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Remove loading state after a short delay
    setTimeout(() => {
        videoContainer.classList.remove('loading');
    }, 1000);
    
    // Track event (optional)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'trailer_play', {
            'video_title': title,
            'youtube_id': youtubeId
        });
    }
}

function closeTrailer() {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    
    // Add closing animation
    modal.style.animation = 'fadeOut 0.3s ease-out';
    
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.animation = 'fadeIn 0.3s ease-out';
        iframe.src = ''; // Stop video
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('trailerModal');
        if (modal.style.display === 'flex') {
            closeTrailer();
        }
    }
});

// Prevent modal from closing when clicking on content
document.addEventListener('DOMContentLoaded', function() {
    const modalContent = document.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>