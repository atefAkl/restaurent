@extends('layouts.app')
@section('title', 'عرض القالب')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-templates.index') }}">قوالب التقارير</a></li>
                <li class="breadcrumb-item active">{{ $template->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Template Info -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title">{{ $template->name }}</h5>
                    @if($template->description)
                    <p class="text-muted">{{ $template->description }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('report-templates.customize', $template) }}" class="btn btn-warning">
                            <i class="bi bi-palette me-2"></i>تخصيص
                        </a>
                        <a href="{{ route('report-templates.edit', $template) }}" class="btn btn-info">
                            <i class="bi bi-pencil me-2"></i>تعديل
                        </a>
                        <a href="{{ route('report-templates.preview', $template) }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-eye me-2"></i>معاينة
                        </a>
                        <a href="{{ route('report-templates.duplicate', $template) }}" class="btn btn-secondary">
                            <i class="bi bi-files me-2"></i>نسخ
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>النوع:</strong>
                    <span class="badge bg-info">{{ $template->type }}</span>
                </div>
                <div class="col-md-3">
                    <strong>الحالة:</strong>
                    @if($template->is_active)
                    <span class="badge bg-success">نشط</span>
                    @else
                    <span class="badge bg-secondary">غير نشط</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>الافتراضي:</strong>
                    @if($template->is_default)
                    <span class="badge bg-warning">نعم</span>
                    @else
                    <span class="text-muted">لا</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>الثيم:</strong>
                    @if($template->theme)
                    <span class="badge bg-success">{{ $template->theme->name }}</span>
                    @else
                    <span class="text-muted">بدون ثيم</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Template Blocks -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">أجزاء القالب</h6>
                </div>
                <div class="card-body">
                    @if($template->templateBlocks->count() > 0)
                    @foreach($template->templateBlocks as $block)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $block->name }}</strong>
                                <span class="badge bg-secondary ms-2">{{ $block->type }}</span>
                            </div>
                            <div>
                                @if($block->is_visible)
                                <span class="badge bg-success">مرئي</span>
                                @else
                                <span class="badge bg-secondary">مخفي</span>
                                @endif
                                <span class="badge bg-primary">ترتيب: {{ $block->order }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">الموقع: X={{ $block->position_x }}, Y={{ $block->position_y }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">الحجم: {{ $block->width }}x{{ $block->height }}</small>
                                </div>
                            </div>

                            @if($block->reportElements->count() > 0)
                            <div class="mt-3">
                                <small class="text-muted d-block mb-2">العناصر:</small>
                                <div class="row">
                                    @foreach($block->reportElements as $element)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-{{ $element->type == 'text' ? 'type' : ($element->type == 'logo' ? 'image' : 'square') }} me-1"></i>
                                                {{ $element->name }}
                                            </span>
                                            <span class="badge bg-light text-dark">{{ $element->type }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="mt-3">
                                <small class="text-muted">لا توجد عناصر في هذا الجزء</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-layers" style="font-size: 3rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-2">لا توجد أجزاء في هذا القالب</p>
                        <a href="{{ route('report-templates.edit', $template) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-2"></i>إضافة جزء
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Template Settings -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">إعدادات القالب</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>المعرف:</strong></td>
                            <td>#{{ $template->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>الاسم:</strong></td>
                            <td>{{ $template->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>النوع:</strong></td>
                            <td>{{ $template->type }}</td>
                        </tr>
                        <tr>
                            <td><strong>نشط:</strong></td>
                            <td>{{ $template->is_active ? 'نعم' : 'لا' }}</td>
                        </tr>
                        <tr>
                            <td><strong>افتراضي:</strong></td>
                            <td>{{ $template->is_default ? 'نعم' : 'لا' }}</td>
                        </tr>
                        <tr>
                            <td><strong>عدد الأجزاء:</strong></td>
                            <td>{{ $template->templateBlocks->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $template->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>آخر تعديل:</strong></td>
                            <td>{{ $template->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Theme Info -->
            @if($template->theme)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">معلومات الثيم</h6>
                </div>
                <div class="card-body">
                    <p><strong>اسم الثيم:</strong> {{ $template->theme->name }}</p>
                    @if($template->theme->description)
                    <p><strong>الوصف:</strong> {{ $template->theme->description }}</p>
                    @endif

                    @if(isset($template->theme->styles['colors']))
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">الألوان:</small>
                        <div class="d-flex gap-1 flex-wrap">
                            @foreach($template->theme->styles['colors'] as $key => $color)
                            <div class="color-preview"
                                style="width: 25px; height: 25px; background-color: {{ $color }}; border-radius: 3px; border: 1px solid #dee2e6;"
                                title="{{ $key }}"></div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .color-preview:hover {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
</style>
@endsection