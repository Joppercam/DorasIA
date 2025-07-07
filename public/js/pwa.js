// Dorasia PWA Manager v1.0
// Service Worker + Install Prompt + Push Notifications

class DorasyaPWA {
    constructor() {
        this.swRegistration = null;
        this.installPrompt = null;
        this.isSubscribed = false;
        
        this.init();
    }
    
    async init() {
        console.log('🎬 Dorasia PWA: Inicializando...');
        
        // Registrar Service Worker
        await this.registerServiceWorker();
        
        // Configurar install prompt
        this.setupInstallPrompt();
        
        // Configurar push notifications
        await this.setupPushNotifications();
        
        // Event listeners
        this.setupEventListeners();
        
        console.log('✅ Dorasia PWA: Listo');
    }
    
    // === SERVICE WORKER ===
    
    async registerServiceWorker() {
        // Temporarily disable Service Worker due to fetch errors
        console.warn('⚠️ Service Worker desactivado temporalmente');
        
        // Unregister existing service workers and clear caches
        if ('serviceWorker' in navigator) {
            try {
                const registrations = await navigator.serviceWorker.getRegistrations();
                for (let registration of registrations) {
                    await registration.unregister();
                    console.log('🗑️ Service Worker unregistered:', registration.scope);
                }
                
                // Clear all caches
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    for (let cacheName of cacheNames) {
                        await caches.delete(cacheName);
                        console.log('🗑️ Cache deleted:', cacheName);
                    }
                }
            } catch (error) {
                console.error('Error unregistering service workers:', error);
            }
        }
        return;
        
        if (!('serviceWorker' in navigator)) {
            console.warn('⚠️ Service Worker no soportado');
            return;
        }
        
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            
            this.swRegistration = registration;
            console.log('✅ Service Worker registrado:', registration.scope);
            
            // Manejar actualizaciones
            registration.addEventListener('updatefound', () => {
                console.log('🔄 Nueva versión disponible');
                this.handleServiceWorkerUpdate(registration);
            });
            
        } catch (error) {
            console.error('❌ Error registrando Service Worker:', error);
        }
    }
    
    handleServiceWorkerUpdate(registration) {
        const newWorker = registration.installing;
        
        newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                // Nueva versión disponible
                this.showUpdateNotification();
            }
        });
    }
    
    showUpdateNotification() {
        if (confirm('🎬 Nueva versión de Dorasia disponible. ¿Actualizar ahora?')) {
            window.location.reload();
        }
    }
    
    // === INSTALL PROMPT ===
    
    setupInstallPrompt() {
        // Capturar evento beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('📱 Install prompt disponible');
            e.preventDefault();
            this.installPrompt = e;
            this.showInstallButton();
        });
        
        // Detectar si ya está instalado
        window.addEventListener('appinstalled', () => {
            console.log('✅ Dorasia instalada como PWA');
            this.hideInstallButton();
            this.trackInstall();
        });
    }
    
    showInstallButton() {
        // Crear botón de instalación si no existe
        if (!document.getElementById('pwa-install-btn')) {
            const installBtn = document.createElement('button');
            installBtn.id = 'pwa-install-btn';
            installBtn.innerHTML = '📱 Instalar App';
            installBtn.className = 'pwa-install-button';
            installBtn.onclick = () => this.promptInstall();
            
            // Agregar estilos
            this.addInstallButtonStyles();
            
            // Agregar al DOM
            document.body.appendChild(installBtn);
            
            // Mostrar con animación
            setTimeout(() => installBtn.classList.add('show'), 100);
        }
    }
    
    hideInstallButton() {
        const btn = document.getElementById('pwa-install-btn');
        if (btn) {
            btn.remove();
        }
    }
    
    async promptInstall() {
        if (!this.installPrompt) return;
        
        const result = await this.installPrompt.prompt();
        console.log('📱 Install prompt result:', result.outcome);
        
        if (result.outcome === 'accepted') {
            this.trackInstall();
        }
        
        this.installPrompt = null;
        this.hideInstallButton();
    }
    
    addInstallButtonStyles() {
        if (document.getElementById('pwa-install-styles')) return;
        
        const styles = document.createElement('style');
        styles.id = 'pwa-install-styles';
        styles.textContent = `
            .pwa-install-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 10000;
                background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 25px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(0, 212, 255, 0.3);
                transform: translateY(100px);
                opacity: 0;
                transition: all 0.3s ease;
                font-size: 14px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .pwa-install-button.show {
                transform: translateY(0);
                opacity: 1;
            }
            
            .pwa-install-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 25px rgba(0, 212, 255, 0.4);
            }
            
            @media (max-width: 768px) {
                .pwa-install-button {
                    bottom: 15px;
                    right: 15px;
                    padding: 10px 16px;
                    font-size: 13px;
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // === PUSH NOTIFICATIONS ===
    
    async setupPushNotifications() {
        if (!('Notification' in window) || !('PushManager' in window)) {
            console.warn('⚠️ Push notifications no soportadas');
            return;
        }
        
        // Verificar estado actual
        await this.checkSubscriptionStatus();
        
        // Mostrar botón de notificaciones si es apropiado
        this.showNotificationPrompt();
    }
    
    async checkSubscriptionStatus() {
        if (!this.swRegistration) return;
        
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            this.isSubscribed = !!subscription;
            console.log('🔔 Push subscription status:', this.isSubscribed);
        } catch (error) {
            console.error('❌ Error checking subscription:', error);
        }
    }
    
    showNotificationPrompt() {
        // Solo mostrar si no está suscrito y no ha rechazado antes
        if (this.isSubscribed || Notification.permission === 'denied') return;
        
        // Mostrar después de 30 segundos de navegación
        setTimeout(() => {
            if (Notification.permission === 'default') {
                this.createNotificationPrompt();
            }
        }, 30000);
    }
    
    createNotificationPrompt() {
        // Crear modal de notificaciones
        const modal = document.createElement('div');
        modal.id = 'notification-prompt-modal';
        modal.innerHTML = `
            <div class="notification-prompt-overlay">
                <div class="notification-prompt-content">
                    <div class="notification-icon">🔔</div>
                    <h3>¿Recibir notificaciones?</h3>
                    <p>Te avisaremos sobre nuevas series, episodios y contenido que te interese.</p>
                    <div class="notification-buttons">
                        <button class="btn-allow" onclick="window.dorasiaPWA.requestNotificationPermission()">
                            ✅ Permitir
                        </button>
                        <button class="btn-deny" onclick="window.dorasiaPWA.dismissNotificationPrompt()">
                            ❌ Ahora no
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        this.addNotificationPromptStyles();
        document.body.appendChild(modal);
        
        // Mostrar con animación
        setTimeout(() => modal.classList.add('show'), 100);
    }
    
    addNotificationPromptStyles() {
        if (document.getElementById('notification-prompt-styles')) return;
        
        const styles = document.createElement('style');
        styles.id = 'notification-prompt-styles';
        styles.textContent = `
            #notification-prompt-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10001;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            #notification-prompt-modal.show {
                opacity: 1;
                visibility: visible;
            }
            
            .notification-prompt-overlay {
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .notification-prompt-content {
                background: #1a1a2e;
                border: 1px solid rgba(0, 212, 255, 0.3);
                border-radius: 15px;
                padding: 2rem;
                max-width: 400px;
                text-align: center;
                color: white;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .notification-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            
            .notification-prompt-content h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: #00d4ff;
            }
            
            .notification-prompt-content p {
                color: #ccc;
                line-height: 1.5;
                margin-bottom: 2rem;
            }
            
            .notification-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
            }
            
            .notification-buttons button {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .btn-allow {
                background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
                color: white;
            }
            
            .btn-allow:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
            }
            
            .btn-deny {
                background: rgba(255, 255, 255, 0.1);
                color: #ccc;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .btn-deny:hover {
                background: rgba(255, 255, 255, 0.2);
            }
        `;
        document.head.appendChild(styles);
    }
    
    async requestNotificationPermission() {
        try {
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('✅ Notificaciones permitidas');
                await this.subscribeToNotifications();
                this.showSuccessMessage('¡Notificaciones activadas! 🎉');
            } else {
                console.log('❌ Notificaciones denegadas');
            }
            
            this.dismissNotificationPrompt();
            
        } catch (error) {
            console.error('❌ Error requesting permission:', error);
            this.dismissNotificationPrompt();
        }
    }
    
    dismissNotificationPrompt() {
        const modal = document.getElementById('notification-prompt-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => modal.remove(), 300);
        }
    }
    
    async subscribeToNotifications() {
        if (!this.swRegistration) return;
        
        try {
            // VAPID key - en producción, usar tu propia key
            const vapidKey = 'BFXl7P8J6ZMBjZeQCttFLT6LUKCXKmK8CnXxZ2x6qRyF3gXwYX3PH7X9pRq5qNGp9Q7eR5K8L9pXqD6F2Y8sE4w';
            
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(vapidKey)
            });
            
            console.log('✅ Suscrito a push notifications');
            
            // Enviar suscripción al servidor
            await this.sendSubscriptionToServer(subscription);
            
            this.isSubscribed = true;
            
        } catch (error) {
            console.error('❌ Error subscribing to notifications:', error);
        }
    }
    
    async sendSubscriptionToServer(subscription) {
        // En producción, enviar al backend para guardar la suscripción
        try {
            const response = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    subscription: subscription,
                    user_id: this.getCurrentUserId()
                })
            });
            
            if (response.ok) {
                console.log('✅ Suscripción enviada al servidor');
            }
        } catch (error) {
            console.log('ℹ️ Guardando suscripción localmente (servidor no disponible)');
            localStorage.setItem('dorasia_push_subscription', JSON.stringify(subscription));
        }
    }
    
    // === UTILITIES ===
    
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    
    getCurrentUserId() {
        // Intentar obtener user ID desde meta tag o localStorage
        const metaUser = document.querySelector('meta[name="user-id"]');
        if (metaUser) return metaUser.content;
        
        const localUser = localStorage.getItem('dorasia_user_id');
        if (localUser) return localUser;
        
        return null;
    }
    
    showSuccessMessage(message) {
        // Toast notification
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            z-index: 10002;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(0, 212, 255, 0.3);
            transform: translateX(400px);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    setupEventListeners() {
        // Cachear series cuando el usuario las visite
        if (window.location.pathname.startsWith('/series/')) {
            this.cacheCurrentSeries();
        }
        
        // Online/offline status
        window.addEventListener('online', () => {
            console.log('🌐 Conexión restaurada');
            this.syncOfflineData();
        });
        
        window.addEventListener('offline', () => {
            console.log('📵 Sin conexión - Modo offline activado');
        });
    }
    
    cacheCurrentSeries() {
        if (this.swRegistration && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CACHE_SERIES',
                url: window.location.href
            });
        }
    }
    
    async syncOfflineData() {
        // Sincronizar datos que se guardaron offline
        const offlineData = localStorage.getItem('dorasia_offline_actions');
        if (offlineData) {
            try {
                const actions = JSON.parse(offlineData);
                for (const action of actions) {
                    await this.executeOfflineAction(action);
                }
                localStorage.removeItem('dorasia_offline_actions');
                console.log('✅ Datos offline sincronizados');
            } catch (error) {
                console.error('❌ Error syncing offline data:', error);
            }
        }
    }
    
    async executeOfflineAction(action) {
        // Ejecutar acciones que se hicieron offline
        try {
            await fetch(action.url, {
                method: action.method,
                headers: action.headers,
                body: action.body
            });
        } catch (error) {
            console.error('❌ Error executing offline action:', error);
        }
    }
    
    trackInstall() {
        // Analytics - instalación de PWA
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_install', {
                event_category: 'PWA',
                event_label: 'Dorasia App Installed'
            });
        }
        console.log('📊 PWA install tracked');
    }
}

// === INICIALIZACIÓN ===

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.dorasiaPWA = new DorasyaPWA();
    });
} else {
    window.dorasiaPWA = new DorasyaPWA();
}

// Exponer funciones globales para compatibilidad
window.requestNotificationPermission = () => window.dorasiaPWA?.requestNotificationPermission();
window.dismissNotificationPrompt = () => window.dorasiaPWA?.dismissNotificationPrompt();

console.log('🎬 Dorasia PWA Script v1.0 cargado');