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
                <a href="{{ route('customers.create') }}" class="btn btn-primary d-flex align-items-center" style="font-size:15px;gap:4px;">
                    <i class="bi bi-plus-circle"></i> عميل جديد
                </a>
            </div>
        </div>
    </div>
    <!-- Filter/Search Box -->
    <div class="card mb-3 shadow-sm" style="border-radius:10px;">
        <div class="card-body py-2 px-3">
            <form class="row g-2 align-items-center" method="GET" action="">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="رقم العميل أو الاسم أو الجوال" style="font-size:14px;">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select" style="font-size:14px;">
                        <option value="">جميع الأنواع</option>
                        <option value="dine_in">داخل المطعم</option>
                        <option value="takeaway">سفري</option>
                        <option value="delivery">توصيل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="date" class="form-control" placeholder="mm/dd/yyyy" style="font-size:14px;">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> بحث</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card shadow-sm" style="border-radius:10px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:90px;">الإجراءات</th>
                        <th>الحالة</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>النوع</th>
                        <th>اسم العميل</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('تأكيد الحذف؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                        <td>{{ $customer->status ?? 1 }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->type ?? '-' }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->id }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
