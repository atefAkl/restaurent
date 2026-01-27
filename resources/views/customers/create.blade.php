@extends('layouts.app')
@section('title', 'إضافة عميل جديد')
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">إدارة العملاء</a></li>
            <li class="breadcrumb-item active">إضافة عميل جديد</li>
        </ol>
    </nav>
    <!-- عنوان -->
    <div class="card mb-3" style="border-radius:8px;">
        <div class="card-body">
            <h2 class="mb-0" style="font-size:16px; font-weight:bold;">إضافة عميل جديد</h2>
            <div class="text-muted" style="font-size:12px;">تعبئة بيانات العميل الجديد</div>
        </div>
    </div>
    <!-- نموذج الإدخال -->
    <div class="card" style="border-radius:8px;">
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label" style="font-size:13px;">اسم العميل</label>
                    <input type="text" class="form-control" id="name" name="name" required style="font-size:13px;">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label" style="font-size:13px;">رقم الجوال</label>
                    <input type="text" class="form-control" id="phone" name="phone" required style="font-size:13px;">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label" style="font-size:13px;">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" style="font-size:13px;">
                </div>
                <button type="submit" class="btn btn-success" style="font-size:13px;">حفظ العميل</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary ms-2" style="font-size:13px;">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
