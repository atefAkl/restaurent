@extends('layouts.app')
@section('title', 'الطلبات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الطلبات</li>
        </ol>
    </nav>
    <!-- Header & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0" style="font-size:1.6rem;">الطلبات</h2>
        <a href="{{ route('orders.create') }}" class="btn btn-primary d-flex align-items-center" style="font-size:15px;gap:4px;">
            <i class="bi bi-plus-circle"></i> طلب جديد
        </a>
    </div>
    <!-- Table Box -->
    <div class="card shadow-sm" style="border-radius:10px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:90px;">الإجراءات</th>
                        <th>الحالة</th>
                        <th>المجموع</th>
                        <th>نوع الطلب</th>
                        <th>اسم العميل</th>
                        <th>رقم الطلب</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('تأكيد الحذف؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                        <td>{{ $order->status ?? '-' }}</td>
                        <td>{{ $order->total ?? '-' }}</td>
                        <td>{{ $order->type ?? '-' }}</td>
                        <td>{{ $order->client->name ?? '-' }}</td>
                        <td>{{ $order->order_number ?? '-' }}</td>
                        <td>{{ $order->id }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection