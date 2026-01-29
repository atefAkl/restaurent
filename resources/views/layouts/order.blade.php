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

    {{-- Cairo font family --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            font-family: 'Cairo', sans-serif;
        }

        body {
            font-family: 'Cairo', sans-serif;
        }

        #order_cats .active a {
            background-color: #fcfcfc;
            border-radius: 0;
            color: #00b12c !important;
        }

        [dir=rtl] .input-group .input-group-text:first-child,
        [dir=rtl] .input-group .form-control:first-child,
        [dir=rtl] .input-group .form-select:first-child {
            border-radius: 0 0.6rem 0.6rem 0 !important;
        }

        [dir=rtl] .input-group .input-group-text:last-child,
        [dir=rtl] .input-group .form-control:last-child,
        [dir=rtl] .input-group .form-select:last-child {
            border-radius: 0.6rem 0 0 0.6rem !important;
        }
    </style>
</head>

<body class="{{ request()->get('touch_mode', false) ? 'touch-mode' : '' }}">
    <div id="app">
        <!-- Navigation -->

        <div class="container-fluid">
            <div class="row">


                <!-- Main Content -->
                <main class="col-12 main-content">
                    <!-- Hidden session messages for toast system -->
                    @if(session('success'))
                    <div data-session-success="{{ session('success') }}"></div>
                    @endif

                    @if(session('error'))
                    <div data-session-error="{{ session('error') }}"></div>
                    @endif

                    @if(session('warning'))
                    <div data-session-warning="{{ session('warning') }}"></div>
                    @endif

                    @if(session('info'))
                    <div data-session-info="{{ session('info') }}"></div>
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
    <link rel="stylesheet" href="{{ asset('css/toast-styles.css') }}">
    <script src="{{ asset('js/toast-system.js') }}"></script>

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
            // Use toast system instead of alerts
            if (window.toastSystem) {
                window.toastSystem.show(message, type, 5000);
            } else {
                // Fallback to console if toast system not loaded
                console.log(`[${type.toUpperCase()}] ${message}`);
            }
        }
    </script>

    @yield('scripts')
</body>

</html>