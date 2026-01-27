@extends('layouts.app')

@section('title', 'الفئات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">الفئات</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">الفئات</h2>
                <p>إدارة الفئات والمنتجات</p>
            </div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary d-flex align-items-center" style="font-size:15px;gap:4px;">
                <i class="bi bi-plus-circle"></i> إضافة فئة جديدة
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
                        <th>اسم الفئة</th>
                        <th>الوصف</th>
                        <th>عدد المنتجات</th>
                        <th>الحالة</th>
                        <th style="min-width:90px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>{{ $category->products_count ?? 0 }}</td>
                        <td>{{ $category->active ? 'نشط' : 'غير نشط' }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block">
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