/**
 * Netflix-style profile transition animations
 * Handles the animations when users select profiles
 */

document.addEventListener('DOMContentLoaded', function() {
    // If on profile selector page
    const profileItems = document.querySelectorAll('.profile-item');
    if (profileItems.length > 0) {
        initializeProfileSelector(profileItems);
    }
    
    // Check for profile transitions from other pages
    const profileTransitions = document.querySelectorAll('[data-profile-transition]');
    if (profileTransitions.length > 0) {
        initializeProfileTransitions(profileTransitions);
    }
});

/**
 * Initialize profile selector animations
 */
function initializeProfileSelector(profileItems) {
    const transitionOverlay = document.querySelector('.netflix-transition');
    
    profileItems.forEach(item => {
        // Add hover effects
        item.addEventListener('mouseenter', function() {
            this.classList.add('profile-hover');
        });
        
        item.addEventListener('mouseleave', function() {
            this.classList.remove('profile-hover');
        });
        
        // Add click animation
        item.addEventListener('click', function() {
            const profileId = this.dataset.profileId;
            
            // Trigger pulse animation
            this.classList.add('profile-pulse');
            
            // Wait for pulse animation to complete
            setTimeout(() => {
                // Add selecting class
                this.classList.add('selecting');
                
                // First fade out other profiles
                profileItems.forEach(otherItem => {
                    if (otherItem !== this) {
                        otherItem.style.opacity = '0';
                        otherItem.style.transform = 'scale(0.8)';
                        otherItem.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    }
                });
                
                // Zoom and center the selected profile
                setTimeout(() => {
                    this.style.position = 'absolute';
                    this.style.top = '50%';
                    this.style.left = '50%';
                    this.style.transform = 'translate(-50%, -50%) scale(1.5)';
                    this.style.transition = 'all 0.7s ease';
                    
                    // Fade to black
                    setTimeout(() => {
                        transitionOverlay.classList.add('active');
                        
                        // Send AJAX request to set active profile
                        fetch(`/user-profiles/${profileId}/set-active-ajax`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Redirect to homepage after animation
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 500);
                            }
                        });
                    }, 700);
                }, 400);
            }, 300);
        });
    });
}

/**
 * Initialize profile transitions from other pages
 */
function initializeProfileTransitions(elements) {
    elements.forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const profileId = this.dataset.profileId;
            const transitionType = this.dataset.transitionType || 'fade';
            
            // Create transition overlay if it doesn't exist
            let transitionOverlay = document.querySelector('.netflix-transition');
            if (!transitionOverlay) {
                transitionOverlay = document.createElement('div');
                transitionOverlay.className = 'netflix-transition';
                document.body.appendChild(transitionOverlay);
                
                // Add styles
                const style = document.createElement('style');
                style.textContent = `
                    .netflix-transition {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: black;
                        z-index: 9999;
                        opacity: 0;
                        visibility: hidden;
                        transition: opacity 0.5s ease;
                    }
                    
                    .netflix-transition.active {
                        opacity: 1;
                        visibility: visible;
                    }
                `;
                document.head.appendChild(style);
            }
            
            // Fade to black
            transitionOverlay.classList.add('active');
            
            // Send AJAX request to set active profile
            fetch(`/user-profiles/${profileId}/set-active-ajax`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect after animation
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 500);
                }
            });
        });
    });
}