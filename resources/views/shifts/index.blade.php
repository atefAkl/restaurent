@extends('layouts.app')

@section('title', 'الورديات')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-house"></i>
                    لوحة التحكم
                </a>
            </li>
            <li class="breadcrumb-item active">الورديات</li>
        </ol>
    </nav>

    <!-- Page Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">الورديات</h1>
            <p class="text-muted">إدارة ورديات العمل والمبيعات</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('shifts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i>
                إنشاء وردية جديدة
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">الوردية الحالية</h4>
                            <p class="card-text fs-3">
                                @if($currentShift)
                                #{{ $currentShift->shift_number }}
                                @else
                                لا توجد وردية مفتوحة
                                @endif
                            </p>
                        </div>
                        <i class="bi bi-clock-history fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">إجمالي اليوم</h4>
                            <p class="card-text fs-3">{{ number_format($todayTotal, 2) }} ريال</p>
                            <small class="d-block">المبيعات النقدية + البطاقات</small>
                        </div>
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">عدد الطلبات</h4>
                            <p class="card-text fs-3">{{ $todayOrders }}</p>
                            <small class="d-block">طلبات اليوم</small>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">متوسط الطلب</h4>
                            <p class="card-text fs-3">{{ number_format($avgOrderValue, 2) }} ريال</p>
                            <small class="d-block">متوسط قيمة الطلب</small>
                        </div>
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Shift Alert -->
    @if($currentShift)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>الوردية الحالية:</strong> #{{ $currentShift->shift_number }}
                <br>
                <small>بدأت في: {{ $currentShift->started_at->format('Y-m-d H:i') }}</small>
                @if($currentShift->user)
                <br>
                <small>الموظف: {{ $currentShift->user->name }}</small>
                @endif
            </div>
            <div>
                <a href="{{ route('shifts.close', $currentShift->id) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-stop-circle"></i>
                    إغلاق الوردية
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i>
                فلاتر الورديات
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('shifts.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">الحالة</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                                تطبيق الفلتر
                            </button>
                            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                                مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Shifts Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-clock-history"></i>
                قائمة الورديات
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>رقم الوردية</th>
                            <th>الموظف</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>المدة</th>
                            <th>الرصيد الافتتاحي</th>
                            <th>المبيعات النقدية</th>
                            <th>المبيعات بالبطاقة</th>
                            <th>الرصيد الختامي</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <span class="badge bg-primary">#{{ $shift->shift_number }}</span>
                            </td>
                            <td>{{ $shift->user ? $shift->user->name : '-' }}</td>
                            <td>{{ $shift->started_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($shift->ended_at)
                                {{ $shift->ended_at->format('Y-m-d H:i') }}
                                @else
                                <span class="badge bg-warning">قيد التنفيذ</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->ended_at)
                                {{ $shift->started_at->diffInHours($shift->ended_at) }} ساعة
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ number_format($shift->opening_balance, 2) }} ريال</td>
                            <td>{{ number_format($shift->cash_sales, 2) }} ريال</td>
                            <td>{{ number_format($shift->visa_sales, 2) }} ريال</td>
                            <td>
                                @if($shift->closing_balance)
                                {{ number_format($shift->closing_balance, 2) }} ريال
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $shift->status == 'closed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $shift->status == 'closed' ? 'مغلقة' : 'مفتوحة' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('shifts.show', $shift->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($shift->status == 'open')
                                    <a href="{{ route('shifts.close', $shift->id) }}"
                                        class="btn btn-sm btn-warning" title="إغلاق">
                                        <i class="bi bi-stop-circle"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('shifts.print', $shift->id) }}"
                                        class="btn btn-sm btn-info" title="طباعة" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="bi bi-clock-history fs-1 text-muted"></i>
                                <p class="text-muted mt-2">لا توجد ورديات</p>
                                <a href="{{ route('shifts.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus"></i>
                                    إنشاء وردية جديدة
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($shifts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $shifts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection