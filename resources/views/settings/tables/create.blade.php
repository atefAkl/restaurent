@extends('layouts.app')
@section('title', 'إضافة طاولة جديدة')
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tables.index') }}">إدارة الطاولات</a></li>
            <li class="breadcrumb-item active">إضافة طاولة جديدة</li>
        </ol>
    </nav>

    <!-- عنوان وإجراءات سريعة -->
    <div class="card mb-3" style="border-radius:8px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0" style="font-size:16px; font-weight:bold;">إضافة طاولة جديدة</h2>
                <div class="text-muted" style="font-size:12px;">يرجى إدخال بيانات الطاولة بدقة</div>
            </div>
            <div>
                <a href="{{ route('tables.index') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">رجوع</a>
            </div>
        </div>
    </div>

    <!-- نموذج الإدخال -->
    <div class="card" style="border-radius:8px;">
        <div class="card-body">
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:12px;">اسم الطاولة</span>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: طاولة 1" style="font-size:14px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:12px;">رقم الطاولة</span>
                            <input type="text" name="number" class="form-control" required placeholder="مثال: T01" style="font-size:14px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:12px;">الغرفة</span>
                            <select name="room_id" class="form-select" style="font-size:13px;">
                                <option value="">بدون غرفة</option>
                                @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" style="font-size:14px;">حفظ</button>
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary" style="font-size:12px;">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
