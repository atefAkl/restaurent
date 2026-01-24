<!DOCTYPE html>
<html lang="{{ session('locale', 'ar') }}" dir="{{ $dir ?? 'rtl' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Restaurant POS') }}</title>

    <link rel="shortcut icon" href="{{ asset('R.png') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    
</head>

<body class="{{ request()->get('touch_mode', false) ? 'touch-mode' : '' }}">
    <div id="app">
        <!-- Navigation -->
       
        <div class="container-fluid">
            <div class="row">
                

                <!-- Main Content -->
                <main class="col-12 main-content">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                        <i class="bi bi-check-circle"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        // Touch Mode Toggle
        function toggleTouchMode() {
            const body = document.body;
            if (body.classList.contains('touch-mode')) {
                body.classList.remove('touch-mode');
                localStorage.removeItem('touchMode');
            } else {
                body.classList.add('touch-mode');
                localStorage.setItem('touchMode', 'true');
            }
        }

        // Load touch mode preference
        if (localStorage.getItem('touchMode') === 'true') {
            document.body.classList.add('touch-mode');
        }

        // Common AJAX functions
        function showLoading() {
            // Show loading spinner
        }

        function hideLoading() {
            // Hide loading spinner
        }

        function showAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show fade-in" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            const alertContainer = document.querySelector('.main-content');
            alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>

</html>