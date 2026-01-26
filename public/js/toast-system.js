class ToastSystem {
    constructor() {
        this.container = null;
        this.toasts = [];
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.querySelector('.toast-system-container')) {
            this.container = document.createElement('div');
            this.container.className = 'toast-system-container position-fixed top-0 p-3';
            this.container.style.zIndex = '9999';
            this.container.style.pointerEvents = 'none';

            // Check document direction for RTL/LTR positioning
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl' ||
                document.body.getAttribute('dir') === 'rtl' ||
                getComputedStyle(document.documentElement).direction === 'rtl';

            if (isRTL) {
                this.container.style.left = '20px';
                this.container.style.right = 'auto';
            } else {
                this.container.style.left = 'auto';
                this.container.style.right = '20px';
            }

            document.body.appendChild(this.container);
        } else {
            this.container = document.querySelector('.toast-system-container');
        }
    }

    show(message, type = 'success', duration = 5000) {
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

        const typeClasses = {
            'success': 'bg-green-500 border-green-600 text-white',
            'error': 'bg-red-500 border-red-600 text-white',
            'warning': 'bg-yellow-500 border-yellow-600 text-white',
            'info': 'bg-blue-500 border-blue-600 text-white',
            'danger': 'bg-red-500 border-red-600 text-white'
        };

        const icons = {
            'success': 'bi-check-circle-fill',
            'error': 'bi-x-circle-fill',
            'warning': 'bi-exclamation-triangle-fill',
            'info': 'bi-info-circle-fill',
            'danger': 'bi-x-circle-fill'
        };

        const currentClass = typeClasses[type] || typeClasses['success'];
        const currentIcon = icons[type] || icons['success'];

        const toastHtml = `
            <div id="${toastId}" class="toast show align-items-center ${currentClass} border-0 mb-2 shadow-lg" 
                 role="alert" aria-live="assertive" aria-atomic="true"
                 style="min-width: 300px; max-width: 400px; pointer-events: auto; backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center">
                    <div class="toast-body d-flex align-items-center flex-grow-1">
                        <i class="bi ${currentIcon} me-2 fs-5"></i>
                        <div class="flex-grow-1">${message}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3 me-2" 
                            data-toast-close="${toastId}" aria-label="Close"
                            style="flex-shrink: 0;"></button>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-white bg-opacity-75" role="progressbar" 
                         style="width: 100%; transition: width ${duration}ms linear;"
                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        `;

        // Add toast to container
        this.container.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const progressBar = toastElement.querySelector('.progress-bar');
        const closeBtn = toastElement.querySelector('[data-toast-close]');

        // Start progress bar animation
        setTimeout(() => {
            if (progressBar) {
                progressBar.style.width = '0%';
            }
        }, 100);

        // Auto-hide toast after duration
        const autoHideTimer = setTimeout(() => {
            this.hide(toastId);
        }, duration);

        // Manual close functionality
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                clearTimeout(autoHideTimer);
                this.hide(toastId);
            });
        }

        // Store toast info
        this.toasts.push({
            id: toastId,
            timer: autoHideTimer,
            element: toastElement
        });

        return toastId;
    }

    hide(toastId) {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            // Check document direction for animation
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl' ||
                document.body.getAttribute('dir') === 'rtl' ||
                getComputedStyle(document.documentElement).direction === 'rtl';

            toastElement.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
            toastElement.style.opacity = '0';

            if (isRTL) {
                toastElement.style.transform = 'translateX(100%)';
            } else {
                toastElement.style.transform = 'translateX(-100%)';
            }

            setTimeout(() => {
                toastElement.remove();
                // Remove from toasts array
                this.toasts = this.toasts.filter(toast => toast.id !== toastId);
            }, 400);
        }
    }

    // Show success toast
    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    // Show error toast
    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    // Show warning toast
    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    // Show info toast
    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }

    // Clear all toasts
    clear() {
        this.toasts.forEach(toast => {
            clearTimeout(toast.timer);
            this.hide(toast.id);
        });
    }
}

// Initialize toast system
window.toastSystem = new ToastSystem();

// Global functions for backward compatibility
window.showToast = function (message, type = 'success', duration = 5000) {
    return window.toastSystem.show(message, type, duration);
};

window.showSuccessToast = function (message, duration = 5000) {
    return window.toastSystem.success(message, duration);
};

window.showErrorToast = function (message, duration = 5000) {
    return window.toastSystem.error(message, duration);
};

window.showWarningToast = function (message, duration = 5000) {
    return window.toastSystem.warning(message, duration);
};

window.showInfoToast = function (message, duration = 5000) {
    return window.toastSystem.info(message, duration);
};

// Auto-show session messages on page load
document.addEventListener('DOMContentLoaded', function () {
    // Check for session messages and convert them to toasts
    const sessionMessages = [
        { type: 'success', selector: '[data-session-success]' },
        { type: 'error', selector: '[data-session-error]' },
        { type: 'warning', selector: '[data-session-warning]' },
        { type: 'info', selector: '[data-session-info]' }
    ];

    sessionMessages.forEach(({ type, selector }) => {
        const element = document.querySelector(selector);
        if (element) {
            const message = element.getAttribute(`data-session-${type}`);
            if (message) {
                window.toastSystem.show(message, type, 5000);
                element.remove(); // Remove the hidden element
            }
        }
    });
});
