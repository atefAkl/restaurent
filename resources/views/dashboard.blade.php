@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">لوحة التحكم</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                <i class="bi bi-arrow-clockwise"></i> تحديث
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            إجمالي المبيعات اليوم
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($todaySales, 2) }} ريال
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            الطلبات اليوم
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $todayOrders }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            المصروفات اليوم
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($todayExpenses, 2) }} ريال
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            منتجات منخفضة المخزون
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $lowStockProducts }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">مخطط المبيعات</h6>
                <div class="dropdown no-arrow">
                    <select class="form-select form-select-sm" id="salesPeriod" onchange="updateSalesChart()">
                        <option value="week">آخر 7 أيام</option>
                        <option value="month">آخر 30 يوم</option>
                        <option value="year">آخر 12 شهر</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">الملخص الشهري</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="monthlySummaryChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> الإيرادات: {{ number_format($monthlySales, 2) }} ريال
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> المصروفات: {{ number_format($monthlyExpenses, 2) }} ريال
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> صافي الربح: {{ number_format($monthlyProfit, 2) }} ريال
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">آخر الطلبات</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>الإجمالي</th>
                                <th>الحالة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name ?? 'عميل نقدي' }}</td>
                                <td>{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ $order->status === 'completed' ? 'مكتمل' : ($order->status === 'pending' ? 'قيد الانتظار' : $order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">لا توجد طلبات حديثة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">أكثر المنتجات مبيعاً</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                            <tr>
                                <td>{{ $product->product->name_ar ?? 'N/A' }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>{{ number_format($product->total_sales, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">لا توجد منتجات</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Shift Info -->
@if($currentShift)
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">الشفت الحالي</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>رقم الشفت:</strong> {{ $currentShift->shift_number }}
                    </div>
                    <div class="col-md-3">
                        <strong>الرصيد الافتتاحي:</strong> {{ number_format($currentShift->opening_balance, 2) }} ريال
                    </div>
                    <div class="col-md-3">
                        <strong>المبيعات:</strong> {{ number_format($currentShift->total_sales, 2) }} ريال
                    </div>
                    <div class="col-md-3">
                        <strong>بدأ في:</strong> {{ $currentShift->started_at->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'المبيعات',
                data: [],
                borderColor: 'rgb(37, 99, 235)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' ريال';
                        }
                    }
                }
            }
        }
    });

    // Monthly Summary Chart
    const monthlyCtx = document.getElementById('monthlySummaryChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'doughnut',
        data: {
            labels: ['الإيرادات', 'المصروفات', 'صافي الربح'],
            datasets: [{
                data: [{
                    {
                        $monthlySales
                    }
                }, {
                    {
                        $monthlyExpenses
                    }
                }, {
                    {
                        $monthlyProfit > 0 ? $monthlyProfit : 0
                    }
                }],
                backgroundColor: [
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(16, 185, 129, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Update Sales Chart
    function updateSalesChart() {
        const period = document.getElementById('salesPeriod').value;

        fetch(`/dashboard/sales-data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                salesChart.data.labels = data.map(item => item.date);
                salesChart.data.datasets[0].data = data.map(item => item.sales);
                salesChart.update();
            });
    }

    // Load initial data
    updateSalesChart();

    // Refresh Dashboard
    function refreshDashboard() {
        location.reload();
    }

    // Auto-refresh every 30 seconds
    setInterval(refreshDashboard, 30000);
</script>
@endsection