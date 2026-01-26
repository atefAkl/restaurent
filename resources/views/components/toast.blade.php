@props(['message', 'type' => 'success', 'duration' => 5000])

@php
    $typeClasses = [
        'success' => 'bg-green-500 border-green-600 text-white',
        'error' => 'bg-red-500 border-red-600 text-white', 
        'warning' => 'bg-yellow-500 border-yellow-600 text-white',
        'info' => 'bg-blue-500 border-blue-600 text-white',
        'danger' => 'bg-red-500 border-red-600 text-white'
    ];
    
    $icons = [
        'success' => 'bi-check-circle-fill',
        'error' => 'bi-x-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill', 
        'info' => 'bi-info-circle-fill',
        'danger' => 'bi-x-circle-fill'
    ];
    
    $currentClass = $typeClasses[$type] ?? $typeClasses['success'];
    $currentIcon = $icons[$type] ?? $icons['success'];
@endphp

<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div class="toast show align-items-center {{ $currentClass }} border-0" role="alert" 
         aria-live="assertive" aria-atomic="true" 
         data-bs-delay="{{ $duration }}"
         data-toast-id="{{ uniqid() }}">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi {{ $currentIcon }} me-2"></i>
                {{ $message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="progress" style="height: 3px;">
            <div class="progress-bar bg-white bg-opacity-50" role="progressbar" 
                 style="width: 100%; transition: width {{ $duration }}ms linear;"
                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toastElement = document.querySelector('[data-toast-id]');
    if (toastElement) {
        const toastId = toastElement.getAttribute('data-toast-id');
        const duration = parseInt(toastElement.getAttribute('data-bs-delay'));
        
        // Start progress bar animation
        const progressBar = toastElement.querySelector('.progress-bar');
        if (progressBar) {
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
        }
        
        // Auto-hide toast after duration
        setTimeout(() => {
            const toastContainer = toastElement.closest('.toast-container');
            if (toastContainer) {
                toastContainer.style.transition = 'opacity 0.3s ease-out';
                toastContainer.style.opacity = '0';
                setTimeout(() => {
                    toastContainer.remove();
                }, 300);
            }
        }, duration);
        
        // Manual close functionality
        const closeBtn = toastElement.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const toastContainer = toastElement.closest('.toast-container');
                if (toastContainer) {
                    toastContainer.style.transition = 'opacity 0.3s ease-out';
                    toastContainer.style.opacity = '0';
                    setTimeout(() => {
                        toastContainer.remove();
                    }, 300);
                }
            });
        }
    }
});
</script>
