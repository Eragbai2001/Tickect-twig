/**
 * Toast Notification System
 * Simple toast notifications that auto-dismiss
 */

const ToastSystem = {
    container: null,

    /**
     * Initialize the toast container
     */
    init() {
        if (this.container) return;
        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        `;
        document.body.appendChild(this.container);
    },

    /**
     * Show a toast notification
     */
    show(message, type = 'default', duration = 3500) {
        this.init();

        const toast = document.createElement('div');
        const bgColor = {
            'success': '#10B981',
            'error': '#EF4444',
            'default': '#6366F1'
        }[type] || '#6366F1';

        const textColor = '#FFFFFF';

        toast.style.cssText = `
            background-color: ${bgColor};
            color: ${textColor};
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease-out;
            min-width: 250px;
            max-width: 400px;
        `;

        toast.textContent = message;
        this.container.appendChild(toast);

        // Add animation styles if not exists
        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        // Auto remove after duration
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    },

    /**
     * Show success toast
     */
    success(message) {
        this.show(message, 'success');
    },

    /**
     * Show error toast
     */
    error(message) {
        this.show(message, 'error');
    },

    /**
     * Show info toast
     */
    info(message) {
        this.show(message, 'default');
    }
};

// Make it available globally
window.ToastSystem = ToastSystem;
