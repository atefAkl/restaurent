@extends('layouts.app')
@section('title', 'إنشاء ثيم جديد')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-themes.index') }}">ثيمات التقارير</a></li>
                <li class="breadcrumb-item active">إنشاء ثيم جديد</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">إنشاء ثيم جديد</h5>
            
            <form method="POST" action="{{ route('report-themes.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الثيم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <input type="text" class="form-control" id="description" name="description" 
                                   value="{{ old('description') }}">
                            @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Colors Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">الألوان</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">اللون الأساسي</label>
                                    <input type="color" class="form-control form-control-color" id="primary_color" 
                                           name="styles[colors][primary]" value="#007bff">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">اللون الثانوي</label>
                                    <input type="color" class="form-control form-control-color" id="secondary_color" 
                                           name="styles[colors][secondary]" value="#6c757d">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="success_color" class="form-label">لون النجاح</label>
                                    <input type="color" class="form-control form-control-color" id="success_color" 
                                           name="styles[colors][success]" value="#28a745">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="danger_color" class="form-label">لون الخطر</label>
                                    <input type="color" class="form-control form-control-color" id="danger_color" 
                                           name="styles[colors][danger]" value="#dc3545">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="warning_color" class="form-label">لون التحذير</label>
                                    <input type="color" class="form-control form-control-color" id="warning_color" 
                                           name="styles[colors][warning]" value="#ffc107">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="info_color" class="form-label">لون المعلومات</label>
                                    <input type="color" class="form-control form-control-color" id="info_color" 
                                           name="styles[colors][info]" value="#17a2b8">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="text_color" class="form-label">لون النص</label>
                                    <input type="color" class="form-control form-control-color" id="text_color" 
                                           name="styles[colors][text]" value="#212529">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="background_color" class="form-label">لون الخلفية</label>
                                    <input type="color" class="form-control form-control-color" id="background_color" 
                                           name="styles[colors][background]" value="#ffffff">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fonts Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">الخطوط</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="font_family" class="form-label">نوع الخط</label>
                                    <select class="form-select" id="font_family" name="styles[fonts][family]">
                                        <option value="Arial, sans-serif">Arial</option>
                                        <option value="Helvetica, sans-serif">Helvetica</option>
                                        <option value="Times New Roman, serif">Times New Roman</option>
                                        <option value="Georgia, serif">Georgia</option>
                                        <option value="Courier New, monospace">Courier New</option>
                                        <option value="Verdana, sans-serif">Verdana</option>
                                        <option value="Tahoma, sans-serif">Tahoma</option>
                                        <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="font_size" class="form-label">حجم الخط</label>
                                    <select class="form-select" id="font_size" name="styles[fonts][size]">
                                        <option value="12px">12px</option>
                                        <option value="14px" selected>14px</option>
                                        <option value="16px">16px</option>
                                        <option value="18px">18px</option>
                                        <option value="20px">20px</option>
                                        <option value="22px">22px</option>
                                        <option value="24px">24px</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borders Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">الحدود</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="border_radius" class="form-label">استدارة الحواف</label>
                                    <select class="form-select" id="border_radius" name="styles[borders][radius]">
                                        <option value="0">لا شيء</option>
                                        <option value="2px">2px</option>
                                        <option value="4px" selected>4px</option>
                                        <option value="6px">6px</option>
                                        <option value="8px">8px</option>
                                        <option value="10px">10px</option>
                                        <option value="12px">12px</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="border_width" class="form-label">سمك الحدود</label>
                                    <select class="form-select" id="border_width" name="styles[borders][width]">
                                        <option value="0">لا شيء</option>
                                        <option value="1px" selected>1px</option>
                                        <option value="2px">2px</option>
                                        <option value="3px">3px</option>
                                        <option value="4px">4px</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shadows Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">الظلال</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="box_shadow" class="form-label">ظل الصندوق</label>
                            <select class="form-select" id="box_shadow" name="styles[shadows][box]">
                                <option value="none">لا شيء</option>
                                <option value="0 1px 3px rgba(0,0,0,0.12)">خفيف</option>
                                <option value="0 2px 4px rgba(0,0,0,0.1)" selected>متوسط</option>
                                <option value="0 4px 8px rgba(0,0,0,0.15)">قوي</option>
                                <option value="0 8px 16px rgba(0,0,0,0.2)">كثيف</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
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
                            <div class="form-text">سيكون هذا الثيم هو الافتراضي</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('report-themes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>حفظ الثيم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
