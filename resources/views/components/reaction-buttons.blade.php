@props(['item', 'type' => 'movie'])

@php
    $isMovie = $type === 'movie';
    $likeToggleRoute = $isMovie ? 'movies.like.toggle' : 'series.like.toggle';
    $loveToggleRoute = $isMovie ? 'movies.love.toggle' : 'series.love.toggle';
    $userId = auth()->id();
    $reactionsInfo = $item->getReactionsInfo($userId);
    $likesInfo = $reactionsInfo['likes'];
    $lovesInfo = $reactionsInfo['loves'];
    
    $isLiked = $likesInfo['is_liked'];
    $isLoved = $lovesInfo['is_loved'];
    $totalLikes = $likesInfo['total_likes'];
    $totalLoves = $lovesInfo['total_loves'];
@endphp

<div class="reaction-buttons-container" data-item-id="{{ $item->id }}" data-item-type="{{ $type }}">
    <!-- Like Button -->
    <button 
        class="reaction-button like-button {{ $isLiked ? 'active' : '' }}" 
        data-route="{{ route($likeToggleRoute, $item->id) }}"
        data-csrf="{{ csrf_token() }}"
        data-reaction-type="like"
        title="{{ $isLiked ? 'Quitar me gusta' : 'Me gusta' }}"
        @guest disabled @endguest
    >
        <div class="reaction-icon like-icon">
            <svg class="icon-outline" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 9V5C14 4.46957 13.7893 3.96086 13.4142 3.58579C13.0391 3.21071 12.5304 3 12 3C11.4696 3 10.9609 3.21071 10.5858 3.58579C10.2107 3.96086 10 4.46957 10 5V11L6 15H20L16 11V9C16 8.46957 15.7893 7.96086 15.4142 7.58579C15.0391 7.21071 14.5304 7 14 7V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 15H6V21H4V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg class="icon-filled" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 9V5C14 4.46957 13.7893 3.96086 13.4142 3.58579C13.0391 3.21071 12.5304 3 12 3C11.4696 3 10.9609 3.21071 10.5858 3.58579C10.2107 3.96086 10 4.46957 10 5V11L6 15H20L16 11V9C16 8.46957 15.7893 7.96086 15.4142 7.58579C15.0391 7.21071 14.5304 7 14 7V9Z"/>
                <path d="M4 15H6V21H4V15Z"/>
            </svg>
        </div>
        <span class="reaction-count">{{ number_format($totalLikes) }}</span>
        <span class="reaction-label">Me gusta</span>
    </button>

    <!-- Love Button -->
    <button 
        class="reaction-button love-button {{ $isLoved ? 'active' : '' }}" 
        data-route="{{ route($loveToggleRoute, $item->id) }}"
        data-csrf="{{ csrf_token() }}"
        data-reaction-type="love"
        title="{{ $isLoved ? 'Quitar me encanta' : 'Me encanta' }}"
        @guest disabled @endguest
    >
        <div class="reaction-icon love-icon">
            <svg class="icon-outline" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.57831 8.50903 2.99877 7.05 2.99877C5.59096 2.99877 4.19169 3.57831 3.16 4.61C2.1283 5.64169 1.54876 7.04096 1.54876 8.5C1.54876 9.95904 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.351 11.8792 21.7563 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.06211 22.0329 6.39459C21.7563 5.72708 21.351 5.12075 20.84 4.61V4.61Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg class="icon-filled" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.84 4.61C20.3292 4.099 19.7228 3.69364 19.0554 3.41708C18.3879 3.14052 17.6725 2.99817 16.95 2.99817C16.2275 2.99817 15.5121 3.14052 14.8446 3.41708C14.1772 3.69364 13.5708 4.099 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.57831 8.50903 2.99877 7.05 2.99877C5.59096 2.99877 4.19169 3.57831 3.16 4.61C2.1283 5.64169 1.54876 7.04096 1.54876 8.5C1.54876 9.95904 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.351 11.8792 21.7563 11.2728 22.0329 10.6054C22.3095 9.93789 22.4518 9.22248 22.4518 8.5C22.4518 7.77752 22.3095 7.06211 22.0329 6.39459C21.7563 5.72708 21.351 5.12075 20.84 4.61V4.61Z"/>
            </svg>
        </div>
        <span class="reaction-count">{{ number_format($totalLoves) }}</span>
        <span class="reaction-label">Me encanta</span>
    </button>
    
    @guest
    <div class="guest-tooltip">
        <span>Inicia sesión para reaccionar</span>
    </div>
    @endguest
</div>

<style>
.reaction-buttons-container {
    display: flex;
    gap: 12px;
    align-items: center;
    position: relative;
}

.reaction-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 120px;
    justify-content: center;
}

.reaction-button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
}

.reaction-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.reaction-button:disabled:hover {
    transform: none;
    background: rgba(255, 255, 255, 0.1);
}

.reaction-icon {
    position: relative;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-outline,
.icon-filled {
    position: absolute;
    transition: all 0.3s ease;
}

.reaction-button .icon-outline {
    opacity: 1;
    transform: scale(1);
}

.reaction-button .icon-filled {
    opacity: 0;
    transform: scale(0.8);
}

.reaction-count {
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.reaction-label {
    font-weight: 500;
    font-size: 12px;
}

/* Like Button Styles */
.like-button:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.4);
    color: #3b82f6;
}

.like-button.active {
    background: rgba(59, 130, 246, 0.25);
    border-color: rgba(59, 130, 246, 0.5);
    color: #3b82f6;
}

.like-button.active .icon-outline {
    opacity: 0;
    transform: scale(1.2);
}

.like-button.active .icon-filled {
    opacity: 1;
    transform: scale(1);
}

.like-button.active:hover {
    background: rgba(59, 130, 246, 0.35);
    border-color: rgba(59, 130, 246, 0.7);
}

/* Love Button Styles */
.love-button:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #ef4444;
}

.love-button.active {
    background: rgba(239, 68, 68, 0.25);
    border-color: rgba(239, 68, 68, 0.5);
    color: #ef4444;
}

.love-button.active .icon-outline {
    opacity: 0;
    transform: scale(1.2);
}

.love-button.active .icon-filled {
    opacity: 1;
    transform: scale(1);
}

.love-button.active:hover {
    background: rgba(239, 68, 68, 0.35);
    border-color: rgba(239, 68, 68, 0.7);
}

/* Animation for reaction action */
@keyframes reactionPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

.reaction-button.animating .reaction-icon {
    animation: reactionPulse 0.6s ease;
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

.reaction-buttons-container:hover .guest-tooltip {
    opacity: 1;
    visibility: visible;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .reaction-buttons-container {
        gap: 8px;
        flex-direction: column;
        width: 100%;
    }
    
    .reaction-button {
        padding: 8px 12px;
        font-size: 11px;
        gap: 6px;
        min-width: 100px;
        width: 100%;
    }
    
    .reaction-icon {
        width: 18px;
        height: 18px;
    }
    
    .icon-outline,
    .icon-filled {
        width: 18px;
        height: 18px;
    }
    
    .reaction-label {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .reaction-buttons-container {
        flex-direction: row;
        justify-content: space-between;
    }
    
    .reaction-button {
        flex: 1;
        min-width: auto;
    }
    
    .reaction-label {
        display: none;
    }
}

/* Loading state */
.reaction-button.loading {
    pointer-events: none;
    opacity: 0.7;
}

.reaction-button.loading::after {
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

/* Success feedback */
@keyframes successPulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.reaction-button.success {
    animation: successPulse 0.8s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reactionButtons = document.querySelectorAll('.reaction-button');
    
    reactionButtons.forEach(button => {
        if (button.disabled) return;
        
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const container = this.closest('.reaction-buttons-container');
            const route = this.dataset.route;
            const csrf = this.dataset.csrf;
            const reactionType = this.dataset.reactionType;
            
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
                    // Update UI based on reaction type
                    const isActive = reactionType === 'like' ? data.is_liked : data.is_loved;
                    const reactionsInfo = data.reactions;
                    
                    // Toggle active state
                    this.classList.toggle('active', isActive);
                    
                    // Update counts for both buttons
                    const likeButton = container.querySelector('.like-button');
                    const loveButton = container.querySelector('.love-button');
                    
                    if (likeButton) {
                        const likeCount = likeButton.querySelector('.reaction-count');
                        likeCount.textContent = new Intl.NumberFormat().format(reactionsInfo.likes.total_likes);
                        likeButton.classList.toggle('active', reactionsInfo.likes.is_liked);
                        likeButton.title = reactionsInfo.likes.is_liked ? 'Quitar me gusta' : 'Me gusta';
                    }
                    
                    if (loveButton) {
                        const loveCount = loveButton.querySelector('.reaction-count');
                        loveCount.textContent = new Intl.NumberFormat().format(reactionsInfo.loves.total_loves);
                        loveButton.classList.toggle('active', reactionsInfo.loves.is_loved);
                        loveButton.title = reactionsInfo.loves.is_loved ? 'Quitar me encanta' : 'Me encanta';
                    }
                    
                    // Add success animation
                    this.classList.add('success');
                    setTimeout(() => {
                        this.classList.remove('success');
                    }, 800);
                    
                    // Add reaction animation
                    this.classList.add('animating');
                    setTimeout(() => {
                        this.classList.remove('animating');
                    }, 600);
                    
                } else {
                    throw new Error(data.message || 'Error al procesar la reacción');
                }
                
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message
                showErrorToast('Error al procesar la reacción. Intenta nuevamente.');
            } finally {
                // Remove loading state
                this.classList.remove('loading');
            }
        });
    });
    
    function showErrorToast(message) {
        const existingToast = document.querySelector('.error-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const errorMsg = document.createElement('div');
        errorMsg.className = 'error-toast';
        errorMsg.textContent = message;
        errorMsg.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            z-index: 10000;
            animation: slideInFromRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            font-weight: 500;
            max-width: 300px;
        `;
        
        document.body.appendChild(errorMsg);
        
        setTimeout(() => {
            errorMsg.style.animation = 'slideOutToRight 0.3s ease forwards';
            setTimeout(() => {
                errorMsg.remove();
            }, 300);
        }, 3000);
    }
});

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
@keyframes slideInFromRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutToRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
`;
document.head.appendChild(style);
</script>