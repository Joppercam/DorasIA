/**
 * Watchlist Toggle Button Component
 * 
 * This script provides a simple way to add and remove titles from the user's watchlist
 * with visual feedback and animations.
 * 
 * Usage:
 * 1. Add the class "watchlist-toggle" to any button that should toggle watchlist status
 * 2. Add data-title-id attribute with the title ID
 * 3. Optionally add data-in-watchlist="true" if the title is already in the watchlist
 * 4. Include this script in your page
 * 
 * Example:
 * <button class="watchlist-toggle" data-title-id="123" data-in-watchlist="false">
 *   <span class="add-text">+ Mi Lista</span>
 *   <span class="remove-text">✓ En Mi Lista</span>
 * </button>
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all watchlist toggle buttons
    const watchlistButtons = document.querySelectorAll('.watchlist-toggle');
    
    // Process each button
    watchlistButtons.forEach(button => {
        const titleId = button.dataset.titleId;
        const addText = button.querySelector('.add-text');
        const removeText = button.querySelector('.remove-text');
        
        // Set initial state
        updateButtonState(button);
        
        // Add click event listener
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Disable button during request
            button.disabled = true;
            
            // Apply loading animation
            button.classList.add('processing');
            
            // Send toggle request
            fetch('/watchlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title_id: titleId,
                    category: button.dataset.category || 'default'
                })
            })
            .then(response => response.json())
            .then(data => {
                // Update button state based on response
                if (data.status === 'added') {
                    button.dataset.inWatchlist = 'true';
                    showNotification('Título añadido a tu lista');
                } else if (data.status === 'removed') {
                    button.dataset.inWatchlist = 'false';
                    showNotification('Título eliminado de tu lista');
                } else if (data.status === 'exists') {
                    button.dataset.inWatchlist = 'true';
                    showNotification('Este título ya está en tu lista');
                }
                
                // Update visual state
                updateButtonState(button);
                
                // Add animation class for success feedback
                button.classList.remove('processing');
                button.classList.add('success');
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    button.classList.remove('success');
                    button.disabled = false;
                }, 1000);
            })
            .catch(error => {
                console.error('Error toggling watchlist status:', error);
                
                // Remove animation class
                button.classList.remove('processing');
                button.disabled = false;
                
                // Show error notification
                showNotification('Error al actualizar tu lista');
            });
        });
    });
    
    // Check status for all buttons at once
    const titleIds = Array.from(watchlistButtons)
        .map(button => button.dataset.titleId)
        .filter((id, index, self) => self.indexOf(id) === index); // Get unique IDs
    
    if (titleIds.length > 0) {
        // Batch check watchlist status
        titleIds.forEach(id => {
            fetch(`/watchlist/status/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Update all buttons with this title ID
                    document.querySelectorAll(`.watchlist-toggle[data-title-id="${id}"]`).forEach(button => {
                        button.dataset.inWatchlist = data.in_watchlist ? 'true' : 'false';
                        updateButtonState(button);
                    });
                })
                .catch(error => {
                    console.error(`Error checking watchlist status for title ${id}:`, error);
                });
        });
    }
    
    /**
     * Update the visual state of a watchlist toggle button
     */
    function updateButtonState(button) {
        const inWatchlist = button.dataset.inWatchlist === 'true';
        const addText = button.querySelector('.add-text');
        const removeText = button.querySelector('.remove-text');
        
        if (inWatchlist) {
            button.classList.add('in-watchlist');
            if (addText) addText.style.display = 'none';
            if (removeText) removeText.style.display = 'inline-flex';
        } else {
            button.classList.remove('in-watchlist');
            if (addText) addText.style.display = 'inline-flex';
            if (removeText) removeText.style.display = 'none';
        }
    }
    
    /**
     * Show a notification toast
     */
    function showNotification(message) {
        // Check if notification container exists, if not create it
        let notificationContainer = document.getElementById('watchlist-notifications');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'watchlist-notifications';
            notificationContainer.style.position = 'fixed';
            notificationContainer.style.bottom = '20px';
            notificationContainer.style.right = '20px';
            notificationContainer.style.zIndex = '9999';
            document.body.appendChild(notificationContainer);
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'watchlist-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <svg class="notification-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        // Style the notification
        notification.style.backgroundColor = 'rgba(26, 32, 44, 0.9)';
        notification.style.color = 'white';
        notification.style.padding = '12px 16px';
        notification.style.borderRadius = '8px';
        notification.style.marginTop = '10px';
        notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.3)';
        notification.style.borderLeft = '4px solid #e53e3e';
        notification.style.transform = 'translateY(20px)';
        notification.style.opacity = '0';
        notification.style.transition = 'all 0.3s ease-out';
        
        // Style the content
        const content = notification.querySelector('.notification-content');
        content.style.display = 'flex';
        content.style.alignItems = 'center';
        
        // Style the icon
        const icon = notification.querySelector('.notification-icon');
        icon.style.width = '20px';
        icon.style.height = '20px';
        icon.style.marginRight = '12px';
        icon.style.color = '#e53e3e';
        
        // Add to container
        notificationContainer.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.style.transform = 'translateY(0)';
            notification.style.opacity = '1';
        }, 10);
        
        // Remove after delay
        setTimeout(() => {
            notification.style.transform = 'translateY(-20px)';
            notification.style.opacity = '0';
            
            // Remove from DOM after animation completes
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
});