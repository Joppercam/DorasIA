@props(['item', 'type' => 'movie'])

@php
    $isMovie = $type === 'movie';
    $toggleRoute = $isMovie ? 'movies.like.toggle' : 'series.like.toggle';
    $userId = auth()->id();
    $likesInfo = $item->getLikesInfo($userId);
    $isLiked = $likesInfo['is_liked'];
    $totalLikes = $likesInfo['total_likes'];
@endphp

<div class="like-button-container" data-item-id="{{ $item->id }}" data-item-type="{{ $type }}">
    <button 
        class="like-button {{ $isLiked ? 'liked' : '' }}" 
        data-route="{{ route($toggleRoute, $item->id) }}"
        data-csrf="{{ csrf_token() }}"
        title="{{ $isLiked ? 'Quitar me gusta' : 'Me gusta' }}"
        @guest disabled @endguest
    >
        <div class="like-icon">
            <svg class="heart-outline" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.57831 8.50903 2.99877 7.05 2.99877C5.59096 2.99877 4.19169 3.57831 3.16 4.61C2.1283 5.64169 1.54876 7.04096 1.54876 8.5C1.54876 9.95904 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.351 11.8792 21.7563 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.06211 22.0329 6.39459C21.7563 5.72708 21.351 5.12075 20.84 4.61V4.61Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg class="heart-filled" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.57831 8.50903 2.99877 7.05 2.99877C5.59096 2.99877 4.19169 3.57831 3.16 4.61C2.1283 5.64169 1.54876 7.04096 1.54876 8.5C1.54876 9.95904 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.351 11.8792 21.7563 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.06211 22.0329 6.39459C21.7563 5.72708 21.351 5.12075 20.84 4.61V4.61Z"/>
            </svg>
        </div>
        <span class="like-count">{{ number_format($totalLikes) }}</span>
    </button>
    
    @guest
    <div class="guest-tooltip">
        <span>Inicia sesión para dar me gusta</span>
    </div>
    @endguest
</div>

<style>
.like-button-container {
    position: relative;
    display: inline-block;
}

.like-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.like-button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

.like-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.like-button:disabled:hover {
    transform: none;
    background: rgba(255, 255, 255, 0.1);
}

.like-icon {
    position: relative;
    width: 24px;
    height: 24px;
}

.heart-outline,
.heart-filled {
    position: absolute;
    top: 0;
    left: 0;
    transition: all 0.3s ease;
}

.like-button .heart-outline {
    opacity: 1;
    transform: scale(1);
}

.like-button .heart-filled {
    opacity: 0;
    transform: scale(0.8);
    color: #e53e3e;
}

.like-button.liked .heart-outline {
    opacity: 0;
    transform: scale(1.2);
}

.like-button.liked .heart-filled {
    opacity: 1;
    transform: scale(1);
}

.like-button.liked {
    background: rgba(229, 62, 62, 0.2);
    border-color: rgba(229, 62, 62, 0.4);
    color: #e53e3e;
}

.like-button.liked:hover {
    background: rgba(229, 62, 62, 0.3);
    border-color: rgba(229, 62, 62, 0.6);
}

.like-count {
    font-weight: 600;
    min-width: 20px;
    text-align: left;
}

/* Animation for like action */
@keyframes heartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

.like-button.animating .like-icon {
    animation: heartPulse 0.6s ease;
}

/* Guest tooltip */
.guest-tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    margin-bottom: 8px;
}

.guest-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: rgba(0, 0, 0, 0.9);
}

.like-button-container:hover .guest-tooltip {
    opacity: 1;
    visibility: visible;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .like-button {
        padding: 6px 12px;
        font-size: 12px;
        gap: 6px;
    }
    
    .like-icon {
        width: 20px;
        height: 20px;
    }
    
    .heart-outline,
    .heart-filled {
        width: 20px;
        height: 20px;
    }
}

/* Loading state */
.like-button.loading {
    pointer-events: none;
    opacity: 0.7;
}

.like-button.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        if (button.disabled) return;
        
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const container = this.closest('.like-button-container');
            const route = this.dataset.route;
            const csrf = this.dataset.csrf;
            
            // Add loading state
            this.classList.add('loading');
            
            try {
                const response = await fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI
                    const isLiked = data.is_liked;
                    const totalLikes = data.total_likes;
                    
                    // Toggle liked state
                    this.classList.toggle('liked', isLiked);
                    
                    // Update count
                    const countElement = this.querySelector('.like-count');
                    countElement.textContent = new Intl.NumberFormat().format(totalLikes);
                    
                    // Update title
                    this.title = isLiked ? 'Quitar me gusta' : 'Me gusta';
                    
                    // Add animation
                    this.classList.add('animating');
                    setTimeout(() => {
                        this.classList.remove('animating');
                    }, 600);
                    
                } else {
                    throw new Error(data.message || 'Error al procesar la acción');
                }
                
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message (you can customize this)
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-toast';
                errorMsg.textContent = 'Error al procesar la acción. Intenta nuevamente.';
                errorMsg.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #e53e3e;
                    color: white;
                    padding: 12px 16px;
                    border-radius: 6px;
                    z-index: 10000;
                    animation: slideIn 0.3s ease;
                `;
                
                document.body.appendChild(errorMsg);
                
                setTimeout(() => {
                    errorMsg.remove();
                }, 3000);
            } finally {
                // Remove loading state
                this.classList.remove('loading');
            }
        });
    });
});

// Add CSS for error toast
const style = document.createElement('style');
style.textContent = `
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
`;
document.head.appendChild(style);
</script>