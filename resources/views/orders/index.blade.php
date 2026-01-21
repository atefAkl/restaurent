@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">الطلبات</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @if(Auth::user()->canManageOrders())
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> طلب جديد
        </a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('orders.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="رقم الطلب أو اسم العميل" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>قيد التحضير</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>جاهز</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">جميع الأنواع</option>
                        <option value="dine_in" {{ request('type') == 'dine_in' ? 'selected' : '' }}>في المطعم</option>
                        <option value="takeaway" {{ request('type') == 'takeaway' ? 'selected' : '' }}>توصيل</option>
                        <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>توصيل</option>
                        <option value="catering" {{ request('type') == 'catering' ? 'selected' : '' }}>تجهيز وليمة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> بحث
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>التاريخ</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>الموظف</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $order->customer_name ?? 'عميل نقدي' }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $order->type === 'dine_in' ? 'في المطعم' : 
                                   ($order->type === 'takeaway' ? 'توصيل' : 
                                   ($order->type === 'delivery' ? 'توصيل' : 'تجهيز وليمة')) }}
                            </span>
                        </td>
                        <td><strong>{{ number_format($order->total_amount, 2) }} ريال</strong></td>
                        <td>
                            <select class="form-select form-select-sm" onchange="updateOrderStatus({{ $order->id }}, this.value)"
                                @if(!Auth::user()->canManageOrders()) disabled @endif>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>قيد التحضير</option>
                                <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>جاهز</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </td>
                        <td>{{ $order->user->name }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->canManageOrders() && in_array($order->status, ['pending', 'preparing', 'ready']))
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                <button onclick="printOrder({{ $order->id }})" class="btn btn-success" title="طباعة">
                                    <i class="bi bi-printer"></i>
                                </button>
                                @if(Auth::user()->canManageOrders() && !in_array($order->status, ['completed', 'cancelled']))
                                <button onclick="deleteOrder({{ $order->id }})" class="btn btn-danger" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">لا توجد طلبات</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                عرض {{ $orders->firstItem() }} إلى {{ $orders->lastItem() }} من {{ $orders->total() }} طلب
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateOrderStatus(orderId, status) {
        if (!confirm('هل أنت متأكد من تغيير حالة الطلب؟')) {
            location.reload();
            return;
        }

        fetch(`/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                    location.reload();
                }
            })
            .catch(error => {
                showAlert('حدث خطأ ما', 'danger');
                location.reload();
            });
    }

    function printOrder(orderId) {
        window.open(`/orders/${orderId}/print`, '_blank');
    }

    function deleteOrder(orderId) {
        if (!confirm('هل أنت متأكد من حذف هذا الطلب؟')) {
            return;
        }

        fetch(`/orders/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    location.reload();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('حدث خطأ ما', 'danger');
            });
    }
</script>
@endsection