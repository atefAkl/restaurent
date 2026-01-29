@extends('layouts.app')
@section('title', 'إنشاء قالب جديد')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-templates.index') }}">قوالب التقارير</a></li>
                <li class="breadcrumb-item active">إنشاء قالب جديد</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">إنشاء قالب جديد</h5>
            
            <form method="POST" action="{{ route('report-templates.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم القالب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع القالب <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">اختر النوع</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="theme_id" class="form-label">الثيم</label>
                            <select class="form-select" id="theme_id" name="theme_id">
                                <option value="">بدون ثيم</option>
                                @foreach($themes as $theme)
                                <option value="{{ $theme->id }}" {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                                    {{ $theme->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('theme_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="content" class="form-label">المحتوى (HTML)</label>
                            <textarea class="form-control" id="content" name="content" rows="4">{{ old('content') }}</textarea>
                            <div class="form-text">محتوى HTML احتياطي (إذا لم يتم استخدام النظام الديناميكي)</div>
                            @error('content')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    نشط
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" 
                                       value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    افتراضي
                                </label>
                            </div>
                            <div class="form-text">سيكون هذا القالب هو الافتراضي لنوعه</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>حفظ القالب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
