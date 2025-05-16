import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Initialize Echo for real-time notifications
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + document.querySelector('meta[name="api-token"]')?.getAttribute('content')
        }
    }
});

// Notification bell component
window.notificationBell = function() {
    return {
        notifications: [],
        unreadCount: 0,
        showDropdown: false,
        loading: false,

        init() {
            this.fetchNotifications();
            this.listenForNotifications();
            
            // Check for new notifications every 30 seconds
            setInterval(() => {
                this.fetchNotifications();
            }, 30000);
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/api/notifications/recent');
                const data = await response.json();
                
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        },

        listenForNotifications() {
            const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
            
            if (!userId) return;

            Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    this.handleNewNotification(notification);
                });
        },

        handleNewNotification(notification) {
            // Add to the beginning of the array
            this.notifications.unshift({
                id: notification.id,
                type: notification.type,
                data: notification.data,
                read_at: null,
                created_at: new Date().toISOString(),
                message: notification.message,
                url: notification.url,
                icon: this.getIconForType(notification.type),
                color: this.getColorForType(notification.type)
            });

            // Increment unread count
            this.unreadCount++;

            // Show toast notification
            this.showToast(notification.message, notification.type);

            // Play notification sound
            this.playNotificationSound();

            // Update browser notifications if enabled
            this.showBrowserNotification(notification);
        },

        async markAsRead(notificationId) {
            try {
                await fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // Update local state
                this.notifications.forEach(n => {
                    if (!n.read_at) {
                        n.read_at = new Date().toISOString();
                    }
                });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        },

        async deleteNotification(notificationId) {
            try {
                await fetch(`/api/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // Remove from local state
                const index = this.notifications.findIndex(n => n.id === notificationId);
                if (index > -1) {
                    const notification = this.notifications[index];
                    if (!notification.read_at) {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                    this.notifications.splice(index, 1);
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },

        getIconForType(type) {
            const icons = {
                'new-follower': 'user-plus',
                'new-message': 'message-circle',
                'comment-reply': 'message-square',
                'comment-liked': 'heart',
                'mentioned': 'at-sign',
                'title-rated': 'star'
            };
            
            return icons[type] || 'bell';
        },

        getColorForType(type) {
            const colors = {
                'new-follower': 'blue',
                'new-message': 'green',
                'comment-reply': 'indigo',
                'comment-liked': 'red',
                'mentioned': 'purple',
                'title-rated': 'yellow'
            };
            
            return colors[type] || 'gray';
        },

        showToast(message, type) {
            // Dispatch custom event for toast notification
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    message: message,
                    type: 'info',
                    duration: 5000
                }
            }));
        },

        playNotificationSound() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(() => {
                // Ignore errors if autoplay is blocked
            });
        },

        showBrowserNotification(notification) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const options = {
                    body: notification.message,
                    icon: '/images/logo-icon.png',
                    badge: '/images/badge-icon.png',
                    tag: notification.id,
                    renotify: true,
                    data: { url: notification.url }
                };

                const browserNotification = new Notification('Dorasia', options);

                browserNotification.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    if (event.target.data.url) {
                        window.location.href = event.target.data.url;
                    }
                    browserNotification.close();
                };
            }
        },

        requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Justo ahora';
            if (diffMins < 60) return `hace ${diffMins} minutos`;
            if (diffHours < 24) return `hace ${diffHours} horas`;
            if (diffDays < 30) return `hace ${diffDays} días`;
            
            return date.toLocaleDateString('es');
        }
    };
};