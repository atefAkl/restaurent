@extends('layouts.app')

@section('title', 'إضافة فئة جديدة')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('categories.index') }}">الفئات</a></li>
                <li class="breadcrumb-item active">إضافة فئة جديدة</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">الفئات</h2>
                <p>إضافة فئة جديدة</p>
            </div>
            <button onclick="window.history.back()" class="btn btn-primary d-flex align-items-center" style="font-size:15px;gap:4px;">
                <i class="bi bi-plus-circle"></i> العودة
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">اسم الفئة (عربي) *</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar"
                                value="{{ old('name_ar') }}" required>
                            @error('name_ar')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_en" class="form-label">اسم الفئة (إنجليزي)</label>
                            <input type="text" class="form-control" id="name_en" name="name_en"
                                value="{{ old('name_en') }}">
                            @error('name_en')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">ترتيب العرض</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                                        value="{{ old('sort_order', 0) }}" min="0">
                                    @error('sort_order')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الفئة</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            @error('image')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">الصيغ المسموح بها: jpg, jpeg, png, gif</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">الوصف (عربي)</label>
                            <textarea class="form-control" id="description_ar" name="description_ar" rows="4">{{ old('description_ar') }}</textarea>
                            @error('description_ar')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_en" class="form-label">الوصف (إنجليزي)</label>
                            <textarea class="form-control" id="description_en" name="description_en" rows="4">{{ old('description_en') }}</textarea>
                            @error('description_en')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        حفظ الفئة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection