@extends('layouts.app')

@section('title', 'التقارير')

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
            <li class="breadcrumb-item active">التقارير</li>
        </ol>
    </nav>

    <!-- Page Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">التقارير</h1>
            <p class="text-muted">عرض وتحليل البيانات والإحصائيات</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="exportReport()">
                <i class="bi bi-download"></i>
                تصدير التقرير
            </button>
            <button type="button" class="btn btn-success" onclick="printReport()">
                <i class="bi bi-printer"></i>
                طباعة
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">إجمالي المبيعات</h4>
                            <p class="card-text fs-3">{{ number_format($totalSales, 2) }} ريال</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">عدد الطلبات</h4>
                            <p class="card-text fs-3">{{ $totalOrders }}</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">متوسط قيمة الطلب</h4>
                            <p class="card-text fs-3">{{ number_format($avgOrderValue, 2) }} ريال</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">المنتجات الأكثر مبيعاً</h4>
                            <p class="card-text fs-3">{{ $topProducts->count() }}</p>
                            <small class="d-block">هذا الشهر</small>
                        </div>
                        <i class="bi bi-trophy fs-1"></i>
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
                فلاتر التقرير
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="report_type" class="form-label">نوع التقرير</label>
                        <select class="form-select" id="report_type" name="report_type">
                            <option value="">جميع التقارير</option>
                            <option value="sales" {{ request('report_type') == 'sales' ? 'selected' : '' }}>تقرير المبيعات</option>
                            <option value="products" {{ request('report_type') == 'products' ? 'selected' : '' }}>تقرير المنتجات</option>
                            <option value="expenses" {{ request('report_type') == 'expenses' ? 'selected' : '' }}>تقرير المصروفات</option>
                            <option value="customers" {{ request('report_type') == 'customers' ? 'selected' : '' }}>تقرير العملاء</option>
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
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                                تطبيق الفلتر
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                                مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Content -->
    <div class="row">
        <!-- Sales Report -->
        @if(request('report_type') == 'sales' || !request('report_type'))
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up"></i>
                        تقرير المبيعات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>المنتجات</th>
                                    <th>الإجمالي</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->items->count() }} منتج</td>
                                    <td>{{ number_format($order->total_amount, 2) }} ريال</td>
                                    <td>
                                        <span class="badge {{ $order->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $order->status == 'completed' ? 'مكتمل' : 'قيد التنفيذ' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Products -->
            @if(request('report_type') == 'products' || !request('report_type'))
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy"></i>
                            المنتجات الأكثر مبيعاً
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($topProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $product->name_ar }}</strong>
                                    <small class="text-muted d-block">{{ $product->category->name_ar }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">{{ $product->total_sold }}</span>
                                    <small class="text-muted d-block">{{ number_format($product->total_revenue, 2) }} ريال</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Expenses Summary -->
            @if(request('report_type') == 'expenses')
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-cash-stack"></i>
                            ملخص المصروفات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4>إجمالي المصروفات</h4>
                                        <p class="fs-3 mb-0">{{ number_format($totalExpenses, 2) }} ريال</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <canvas id="expensesChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Charts Section -->
        @if(!request('report_type') || request('report_type') == 'sales')
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up"></i>
                            المبيعات الشهرية
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pie-chart"></i>
                            توزيع المبيعات حسب الفئات
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'المبيعات',
                        data: @json($monthlySales),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Cairo'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($categoryLabels),
                    datasets: [{
                        data: @json($categoryData),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'Cairo'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Expenses Chart
        const expensesCtx = document.getElementById('expensesChart');
        if (expensesCtx) {
            new Chart(expensesCtx, {
                type: 'bar',
                data: {
                    labels: @json($expenseCategories),
                    datasets: [{
                        label: 'المصروفات',
                        data: @json($expenseAmounts),
                        backgroundColor: 'rgba(255, 99, 132, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Cairo'
                                }
                            }
                        }
                    }
                }
            });
        }

        function exportReport() {
            const reportType = document.getElementById('report_type').value;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            let url = '{{ route('
            reports.export ') }}?';
            if (reportType) url += 'report_type=' + reportType + '&';
            if (dateFrom) url += 'date_from=' + dateFrom + '&';
            if (dateTo) url += 'date_to=' + dateTo;

            window.open(url, '_blank');
        }

        function printReport() {
            window.print();
        }
    </script>
    @endpush