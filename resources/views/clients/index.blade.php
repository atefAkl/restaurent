@extends('layouts.app')
@section('title', 'العملاء')
@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm" style="font-size:13px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">العملاء</li>
        </ol>
    </nav>
    <!-- عنوان رئيسي داخل بوكس -->
    <div class="card mb-3 shadow-sm" style="border-radius:10px;">
        <div class="card-body pb-2 pt-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" style="font-size:1.6rem;">العملاء</h2>
                    <div class="text-muted" style="font-size:14px;">عرض وإدارة جميع العملاء في النظام</div>
                </div>
                <a href="{{ route('clients.create') }}" class="btn btn-primary d-flex align-items-center" style="font-size:15px;gap:4px;">
                    <i class="bi bi-plus-circle"></i> عميل جديد
                </a>
            </div>
        </div>
    </div>
    <!-- Filter/Search Box -->
    <div class="card mb-3 shadow-sm" style="border-radius:10px;">
        <div class="card-body py-2 px-3">
            <form class="row g-2 align-items-center" method="GET" action="{{ route('clients.index') }}">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="رقم الطلب أو اسم العميل" value="{{ request('search') }}" style="font-size:14px;">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select" style="font-size:14px;">
                        <option value="">جميع الأنواع</option>
                        <option value="dine_in" {{ request('type') == 'dine_in' ? 'selected' : '' }}>في المطعم</option>
                        <option value="takeaway" {{ request('type') == 'takeaway' ? 'selected' : '' }}>سفري</option>
                        <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>توصيل</option>
                        <option value="catering" {{ request('type') == 'catering' ? 'selected' : '' }}>تجهيز وليمة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}" style="font-size:14px;">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> بحث</button>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card shadow-sm" style="border-radius:10px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم العميل</th>
                            <th>النوع</th>
                            <th>رقم الجوال</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->status }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('clients.show', $item) }}" class="btn btn-primary" title="عرض"><i class="bi bi-eye"></i></a>
                                    @if(Auth::user()->canManageClients() && in_array($item->status, ['pending', 'preparing', 'ready']))
                                    <a href="{{ route('clients.edit', $item) }}" class="btn btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                                    @endif
                                    <button data-id="{{ $item->id }}" onclick="printClient(this.dataset.id)" class="btn btn-success" title="طباعة"><i class="bi bi-printer"></i></button>
                                    @if(Auth::user()->canManageClients())
                                    <button data-id="{{ $item->id }}" onclick="deleteClient(this.dataset.id)" class="btn btn-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
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
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function updateClientStatus(clientId, status) {
        if (!confirm('هل أنت متأكد من تغيير حالة الطلب؟')) {
            location.reload();
            return;
        }

        fetch(`/clients/${clientId}/status`, {
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

    function printClient(clientId) {
        window.open(`/clients/${clientId}/print`, '_blank');
    }

    function deleteClient(clientId) {
        if (!confirm('هل أنت متأكد من حذف هذا الطلب؟')) {
            return;
        }

        $.ajax({
            url: `/clients/${clientId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                console.log(data)
                if (data.success) {
                    showAlert(data.message, 'success');
                    location.reload();
                } else {
                    showAlert(data.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showAlert('حدث خطأ ما', 'danger');
            }
        });
    }
</script>
@endsection