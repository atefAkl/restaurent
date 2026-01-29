@extends('layouts.app')
@section('title', 'ثيمات التقارير')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item active">ثيمات التقارير</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">ثيمات التقارير</h5>
                <p class="text-muted mb-0">إدارة ثيمات وألوان التقارير</p>
            </div>
            <div>
                <a href="{{ route('report-themes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>إنشاء ثيم جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Themes Grid -->
    @if($themes->count() > 0)
    <div class="row">
        @foreach($themes as $theme)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">{{ $theme->name }}</h6>
                    <div>
                        @if($theme->is_default)
                        <span class="badge bg-warning">افتراضي</span>
                        @endif
                        @if($theme->is_active)
                        <span class="badge bg-success">نشط</span>
                        @else
                        <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($theme->description)
                    <p class="card-text text-muted">{{ $theme->description }}</p>
                    @endif

                    <!-- Color Preview -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">الألوان:</small>
                        <div class="d-flex gap-1">
                            @if(isset($theme->styles['colors']))
                            @foreach($theme->styles['colors'] as $key => $color)
                            <div class="color-preview"
                                style="width: 30px; height: 30px; background-color: {{ $color }}; border-radius: 4px; border: 1px solid #dee2e6;"
                                title="{{ $key }}"></div>
                            @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            مستخدم في {{ $theme->print_templates_count }} قالب
                        </small>
                    </div>

                    <!-- Font Preview -->
                    @if(isset($theme->styles['fonts']['family']))
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">الخط:</small>
                        <div style="font-family: {{ $theme->styles['fonts']['family'] }};">
                            مثال النص
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('report-themes.show', $theme) }}"
                            class="btn btn-sm btn-outline-primary" title="عرض">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('report-themes.preview', $theme) }}"
                            class="btn btn-sm btn-outline-info" title="معاينة" target="_blank">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="{{ route('report-themes.edit', $theme) }}"
                            class="btn btn-sm btn-outline-warning" title="تعديل">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('report-themes.duplicate', $theme) }}"
                            class="btn btn-sm btn-outline-secondary" title="نسخ">
                            <i class="bi bi-files"></i>
                        </a>
                        @if(!$theme->is_default && $theme->print_templates_count == 0)
                        <form action="{{ route('report-themes.destroy', $theme) }}"
                            method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                title="حذف"
                                onclick="return confirm('هل أنت متأكد من حذف هذا الثيم؟')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-pagination :data="$themes" />
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-palette" style="font-size: 4rem; color: #dee2e6;"></i>
        <h5 class="mt-3">لا توجد ثيمات</h5>
        <p class="text-muted">لم يتم إنشاء أي ثيمات بعد</p>
        <a href="{{ route('report-themes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>إنشاء ثيم جديد
        </a>
    </div>
    @endif
</div>

<style>
    .color-preview:hover {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
</style>
@endsection