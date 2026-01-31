@extends('layouts.app')
@section('title', 'الإعدادات')

@section('content')
<style>
    .settings-group-card {
        width: 50vw;
        min-width: 500px;

        a i.bi {
            font-size: 2.5rem;
            margin-inline-end: 1rem;
        }
    }
</style>
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item active">الإعدادات</li>
            </ol>
        </nav>
    </div>

    <!-- Simple Settings Card -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="title">
                <h5 class="card-title fw-bold">الإعدادات</h5>
                <p>هنا يمكنك إدارة إعدادات النظام</p>
            </div>
            <div class="quick-actions">
                <a href="{{ route('pos-devices.index') }}" class="btn btn-primary p-2">
                    <i class="bi bi-printer me-2"></i>
                    <span>أجهزة الصرافة</span>
                </a>
            </div>
        </div>
    </div>
    <!-- Device Management Section -->
    <div class="card settings-group-card mb-3">
        <div class="card-header">
            <h6 class="fw-bold">إدارة الأجهزة</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('pos-devices.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-printer me-2"></i>
                        <span>أجهزة الصرافة</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('pos-stations.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-shop me-2"></i>
                        <span>نقاط البيع</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('printers.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-printer-fill me-2"></i>
                        <span>الطابعات</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports & Templates Section -->
    <div class="card settings-group-card mb-3">
        <div class="card-header">
            <h6 class="fw-bold">التقارير والقوالب</h6>
        </div>
        <div class="card-body">
            <div class="row">        
                <div class="col-md-4 mb-3">
                    <a href="{{ route('report-components.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-puzzle me-2"></i> <span>مكونات التقارير</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('report-templates.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-file-earmark me-2"></i> <span>قوالب التقارير</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('report-themes.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-palette me-2"></i> <span>ثيمات التقارير</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('printer-settings.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-printer me-2"></i> <span>إعدادات الطباعة</span>
                    </a>
                </div>
            </div>                   
        </div>
    </div>

    <!-- User Management Section -->
    <div class="card settings-group-card mb-3">
        <div class="card-header">
            <h6 class="fw-bold">إدارة المستخدمين</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('roles.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-shield-lock me-2"></i>
                        <span>الأدوار</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('permissions.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-key me-2"></i>
                        <span>الصلاحيات</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('users.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-person me-2"></i>
                        <span>المستخدمون</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations Section -->
    <div class="card settings-group-card mb-3">
        <div class="card-header">
            <h6 class="fw-bold">العمليات</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('cashier-sessions.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-cash-stack me-2"></i>
                        <span>جلسات الكاشير</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('rooms.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-door-open me-2"></i>
                        <span>الغرف</span>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('tables.index') }}" class="text-decoration-none d-flex align-items-center p-2 rounded settings-link">
                        <i class="bi bi-table me-2"></i>
                        <span>الطاولات</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
<style>
    .settings-link {
        color: #6c757d;
        transition: color 0.2s ease;
    }

    .settings-link:hover {
        color: #0d6efd;
    }

    .settings-link:hover i {
        color: #0d6efd;
    }
</style>
@endsection