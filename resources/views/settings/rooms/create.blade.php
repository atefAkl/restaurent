@extends('layouts.app')
@section('title', 'إضافة غرفة جديدة')
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">إدارة الغرف</a></li>
            <li class="breadcrumb-item active">إضافة غرفة جديدة</li>
        </ol>
    </nav>

    <!-- عنوان وإجراءات سريعة -->
    <div class="card mb-3" style="border-radius:8px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0" style="font-size:16px; font-weight:bold;">إضافة غرفة جديدة</h2>
                <div class="text-muted" style="font-size:12px;">يرجى إدخال بيانات الغرفة بدقة</div>
            </div>
            <div>
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">رجوع</a>
            </div>
        </div>
    </div>

    <!-- نموذج الإدخال -->
    <div class="card" style="border-radius:8px;">
        <div class="card-body">
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:12px;">اسم الغرفة</span>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: غرفة 1" style="font-size:14px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:12px;">كود الغرفة</span>
                            <input type="text" name="number" class="form-control" required placeholder="مثال: R01AR" style="font-size:14px;">
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" style="font-size:14px;">حفظ</button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary" style="font-size:12px;">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
