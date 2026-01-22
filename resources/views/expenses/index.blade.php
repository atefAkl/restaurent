@extends('layouts.app')

@section('title', 'المصروفات')

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
            <li class="breadcrumb-item active">المصروفات</li>
        </ol>
    </nav>

    <!-- Page Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">المصروفات</h1>
            <p class="text-muted">إدارة مصروفات المطعم والتكاليف</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i>
                إضافة مصروف جديد
            </a>
            <button type="button" class="btn btn-success" onclick="exportExpenses()">
                <i class="bi bi-download"></i>
                تصدير Excel
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">إجمالي المصروفات</h4>
                            <p class="card-text fs-3">{{ number_format($totalExpenses, 2) }} ريال</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">مصروفات اليوم</h4>
                            <p class="card-text fs-3">{{ number_format($todayExpenses, 2) }} ريال</p>
                            <small class="d-block">اليوم</small>
                        </div>
                        <i class="bi bi-calendar-day fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">عدد المصروفات</h4>
                            <p class="card-text fs-3">{{ $totalExpensesCount }}</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-receipt fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">متوسط المصروف</h4>
                            <p class="card-text fs-3">{{ number_format($avgExpense, 2) }} ريال</p>
                            <small class="d-block">متوسط قيمة المصروف</small>
                        </div>
                        <i class="bi bi-graph-down fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i>
                فلاتر المصروفات
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="expense_category" class="form-label">فئة المصروف</label>
                        <select class="form-select" id="expense_category" name="expense_category">
                            <option value="">جميع الفئات</option>
                            <option value="rent" {{ request('expense_category') == 'rent' ? 'selected' : '' }}>إيجار</option>
                            <option value="salaries" {{ request('expense_category') == 'salaries' ? 'selected' : '' }}>رواتب</option>
                            <option value="utilities" {{ request('expense_category') == 'utilities' ? 'selected' : '' }}>مرافق</option>
                            <option value="supplies" {{ request('expense_category') == 'supplies' ? 'selected' : '' }}>مواد خام</option>
                            <option value="maintenance" {{ request('expense_category') == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                            <option value="marketing" {{ request('expense_category') == 'marketing' ? 'selected' : '' }}>تسويق</option>
                            <option value="other" {{ request('expense_category') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
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
                        <label for="min_amount" class="form-label">الحد الأدنى للمبلغ</label>
                        <input type="number" class="form-control" id="min_amount" name="min_amount"
                            value="{{ request('min_amount') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                                تطبيق الفلتر
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                                مسح الفلاتر
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-receipt"></i>
                قائمة المصروفات
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>رقم المصروف</th>
                            <th>التاريخ</th>
                            <th>الفئة</th>
                            <th>الوصف</th>
                            <th>المبلغ</th>
                            <th>الموظف</th>
                            <th>الإيصال</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <span class="badge bg-primary">#{{ $expense->expense_number }}</span>
                            </td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $expense->expense_category_label }}
                                </span>
                            </td>
                            <td>{{ $expense->description }}</td>
                            <td>
                                <span class="badge bg-danger fs-6">
                                    {{ number_format($expense->amount, 2) }} ريال
                                </span>
                            </td>
                            <td>{{ $expense->user ? $expense->user->name : '-' }}</td>
                            <td>
                                @if($expense->receipt)
                                <a href="{{ asset('storage/' . $expense->receipt) }}"
                                    target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                    عرض
                                </a>
                                @else
                                <span class="text-muted">لا يوجد</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($expense->notes, 30) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                        class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المصروف؟')" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                                <p class="text-muted mt-2">لا توجد مصروفات</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus"></i>
                                    إضافة مصروف جديد
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary Row -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">ملخص المصروفات حسب الفئة</h5>
                            <div class="row">
                                @foreach($expenseSummary as $category => $total)
                                <div class="col-md-4 mb-3">
                                    <div class="p-3 border rounded">
                                        <h6>{{ $category }}</h6>
                                        <p class="mb-0 fs-5">{{ number_format($total, 2) }} ريال</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">إجمالي العرض</h5>
                            <p class="card-text fs-2 mb-0">{{ number_format($filteredTotal, 2) }} ريال</p>
                            <small>إجمالي المصروفات المعروضة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportExpenses() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('
        expenses.export ') }}';

        // Add CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        // Add filters
        const filters = ['expense_category', 'date_from', 'date_to', 'min_amount'];
        filters.forEach(filter => {
            const element = document.getElementById(filter);
            if (element && element.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = filter;
                input.value = element.value;
                form.appendChild(input);
            }
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // Auto-refresh every 30 seconds for real-time updates
    setInterval(() => {
        location.reload();
    }, 30000);
</script>
@endpush