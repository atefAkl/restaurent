@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">المنتجات</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">المنتجات</h2>
                <p>إدارة منتجاتك وعرضها في النظام</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle"></i>&nbsp; جديد
            </a>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card" style="border-radius:0px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم المنتج</th>
                        <th>الفئة</th>
                        <th>الكمية</th>
                        <th>الحالة</th>
                        <th>السعر</th>
                        <th style="min-width:90px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->active ? 'نشط' : 'غير نشط' }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block">
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
    </div>
</div>
@endsection