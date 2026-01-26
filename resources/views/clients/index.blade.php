@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 bclient-bottom">
    <h1 class="h2">الطلبات</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        {{-- @if(Auth::user()->canManageClients()) --}}
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> عميل جديد
        </a>
        {{-- @endif --}}
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('clients.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="رقم الطلب أو اسم العميل" value="{{ request('search') }}">
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
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>

                </div>
            </div>
        </form>
    </div>
</div>

<!-- Clients Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__('clients.th.client_name')}}</th>
                        <th>{{__('clients.th.type')}}</th>
                        <th>{{__('clients.th.phone')}}</th>
                        <th>{{__('clients.th.emai')}}</th>
                        <th>{{__('clients.th.status')}}</th>
                        <th>{{__('clients.th.actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                        </td>

                        <td>
                            {{ $item->type }}
                        </td>

                        <td>
                            {{ $item->phone }}
                        </td>

                        <td>
                            {{ $item->email }}
                        </td>

                        <td>
                            {{ $item->status }}
                        </td>

                        <td>

                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('clients.show', $item) }}" class="btn btn-primary" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->canManageClients() && in_array($item->status, ['pending', 'preparing', 'ready']))
                                <a href="{{ route('clients.edit', $item) }}" class="btn btn-warning" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                <button data-id="{{ $item->id }}" onclick="printClient(this.dataset.id)" class="btn btn-success" title="طباعة">
                                    <i class="bi bi-printer"></i>
                                </button>
                                @if(Auth::user()->canManageClients())
                                <button data-id="{{ $item->id }}" onclick="deleteClient(this.dataset.id)" class="btn btn-danger" title="حذف">
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

            {{ $clients->links() }}
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