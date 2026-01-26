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
    <!-- Custom CSS -->

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            transition: var(--transition);
        }

        /* Touch Screen Mode */
        body.touch-mode {
            font-size: 16px;
        }

        body.touch-mode .btn {
            min-height: 50px;
            font-size: 18px;
            padding: 12px 24px;
        }

        body.touch-mode .form-control {
            min-height: 50px;
            font-size: 18px;
            padding: 12px;
        }

        body.touch-mode .card {
            border-radius: 12px;
        }

        /* Breadcrumbs */
        [dir="rtl"] ol.breadcrumb li.breadcrumb-item:before {
            float: right;
            margin-inline-end: 8px;
        }

        /* Button Group */
        [dir="rtl"] .btn-group .btn {
            border-radius: 0;
        }

        [dir="rtl"] .btn-group .btn:only-child {
            border-radius: 0.25rem;
        }

        [dir="rtl"] .btn-group .btn:first-child {
            border-radius: 0 0.25rem 0.25rem 0;
        }

        [dir="rtl"] .btn-group .btn:last-child {
            border-radius: 0.25rem 0 0 0.25rem;
        }

        .card {
            border-radius: 0 !important;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .dropdown-item.active {
            background-color: var(--primary-color);
            color: white;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #e5e7eb;
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: var(--dark-color);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0;
            border-radius: 0;
            transition: var(--transition);
            border-right: 3px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--light-color);
            border-right-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-left: 0.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: var(--dark-color);
        }

        /* Buttons */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        /* Tables */
        .table {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: none;
            font-weight: 600;
            color: var(--dark-color);
        }

        .table tbody tr:hover {
            background-color: var(--light-color);
        }

        /* Forms */
        .form-control,
        .form-select {
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        /* Badges */
        .badge {
            font-weight: 500;
            border-radius: var(--border-radius);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid var(--light-color);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 76px;
                left: -250px;
                width: 250px;
                height: calc(100vh - 76px);
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-right: 0;
            }
        }

        /* RTL Support */
        [dir="rtl"] .sidebar {
            border-right: none;
            border-left: 1px solid #e5e7eb;
        }

        [dir="rtl"] .sidebar .nav-link {
            border-right: none;
            border-left: 3px solid transparent;
        }

        [dir="rtl"] .sidebar .nav-link:hover,
        [dir="rtl"] .sidebar .nav-link.active {
            border-left-color: var(--primary-color);
        }

        [dir="rtl"] .sidebar .nav-link i {
            margin-left: 0;
            margin-right: 0.5rem;
        }
    </style>
</head>

<body class="{{ request()->get('touch_mode', false) ? 'touch-mode' : '' }}">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="bi bi-shop"></i>
                    {{ config('app.name', 'Restaurant POS') }}
                </a>

                <div class="navbar-nav ms-auto">
                    <!-- Touch Mode Toggle -->
                    <button class="btn btn-outline-light btn-sm me-2" onclick="toggleTouchMode()" title="تبديل وضع شاشات اللمس">
                        <i class="bi bi-hand-index"></i>
                    </button>

                    <!-- Language Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm me-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" title="تبديل اللغة">
                            <i class="bi bi-translate"></i>
                            {{ session('locale', 'ar') === 'ar' ? 'العربية' : 'English' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start">
                            <li>
                                <a class="dropdown-item {{ session('locale', 'ar') === 'ar' ? 'active' : '' }}"
                                    href="{{ route('locale.switch', 'ar') }}">
                                    <i class="bi bi-flag"></i> العربية
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ session('locale', 'ar') === 'en' ? 'active' : '' }}"
                                    href="{{ route('locale.switch', 'en') }}">
                                    <i class="bi bi-flag"></i> English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> الإعدادات</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                @if(Auth::check())
                <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    لوحة التحكم
                                </a>
                            </li>

                            @if(Auth::user()->canManageOrders())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    الطلبات
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->canManageInventory())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-box"></i>
                                    المنتجات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="bi bi-tags"></i>
                                    الفئات
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->canViewReports())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                                    <i class="bi bi-graph-up"></i>
                                    العملاء
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->canViewReports())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                    <i class="bi bi-graph-up"></i>
                                    التقارير
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}" href="{{ route('shifts.index') }}">
                                    <i class="bi bi-clock"></i>
                                    الشيفتات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                                    <i class="bi bi-cash"></i>
                                    المصروفات
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->isKitchen())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.kitchen') }}">
                                    <i class="bi bi-egg-fried"></i>
                                    المطبخ
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </nav>
                @endif

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
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