class ToastNotification {
    constructor() {
        this.container = this.createContainer();
    }
    
    createContainer() {
        const existingContainer = document.getElementById('toast-container');
        if (existingContainer) {
            return existingContainer;
        }
        
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
    }
    
    show(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: '<i class="fas fa-check-circle mr-2"></i>',
            error: '<i class="fas fa-exclamation-circle mr-2"></i>',
            warning: '<i class="fas fa-exclamation-triangle mr-2"></i>',
            info: '<i class="fas fa-info-circle mr-2"></i>'
        };
        
        toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full flex items-center`;
        toast.innerHTML = `${icons[type]}<span>${message}</span>`;
        
        this.container.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto eliminar
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// Inicializar globalmente
window.toast = new ToastNotification();

// Helpers para uso común
window.showSuccess = (message) => window.toast.show(message, 'success');
window.showError = (message) => window.toast.show(message, 'error');
window.showWarning = (message) => window.toast.show(message, 'warning');
window.showInfo = (message) => window.toast.show(message, 'info');