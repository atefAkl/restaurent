@extends('layouts.app')
@section('title', 'الطلبات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">الطلبات</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="p-3">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">الطلبات</h2>
                <p>إدارة وعرض جميع الطلبات</p>
            </div>
            <a href="{{ route('orders.create') }}" class="btn btn-primary d-flex align-items-center" style="margin-inline-end: 1rem;">
                <i class="bi bi-plus-circle"></i>&nbsp; طلب جديد
            </a>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>رقم الطلب</th>
                        <th>اسم العميل</th>
                        <th>نوع الطلب</th>
                        <th>المجموع</th>
                        <th>الحالة</th>
                        <th style="min-width:90px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_type }}</td>
                        <td>{{ $order->total_amount }}</td>
                        <td>{{ $order->status }}</td>
                        <td>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('تأكيد الحذف؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>

    {{-- Application Sellers --}}
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">البائعين</h5>
            <ul>
                @foreach($sellers as $seller)
                    <li>{{ $seller->name }} ({{ $seller->email }})</li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection