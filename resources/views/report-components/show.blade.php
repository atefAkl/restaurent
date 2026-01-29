@extends('layouts.app')
@section('title', 'عرض المكون')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-components.index') }}">مكونات التقارير</a></li>
                <li class="breadcrumb-item active">عرض المكون</li>
            </ol>
        </nav>
    </div>

    <!-- Component Info -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @switch($component->type)
                            @case('header')
                            <i class="bi bi-type-h1 text-primary fs-2"></i>
                            @break
                            @case('text')
                            <i class="bi bi-text-paragraph text-info fs-2"></i>
                            @break
                            @case('image')
                            <i class="bi bi-image text-success fs-2"></i>
                            @break
                            @case('table')
                            <i class="bi bi-table text-warning fs-2"></i>
                            @break
                            @case('barcode')
                            <i class="bi bi-upc-scan text-danger fs-2"></i>
                            @break
                            @case('qr_code')
                            <i class="bi bi-qr-code text-secondary fs-2"></i>
                            @break
                            @case('line')
                            <i class="bi bi-dash-lg text-dark fs-2"></i>
                            @break
                            @case('logo')
                            <i class="bi bi-badge text-primary fs-2"></i>
                            @break
                            @default
                            <i class="bi bi-puzzle text-muted fs-2"></i>
                            @endswitch
                        </div>
                        <div>
                            <h5 class="card-title mb-1">{{ $component->name }}</h5>
                            @if($component->description)
                            <p class="text-muted mb-0">{{ $component->description }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-secondary">
                                    @switch($component->type)
                                    @case('header')
                                    ترويسة
                                    @break
                                    @case('text')
                                    نص
                                    @break
                                    @case('image')
                                    صورة
                                    @break
                                    @case('table')
                                    جدول
                                    @break
                                    @case('barcode')
                                    باركود
                                    @break
                                    @case('qr_code')
                                    QR Code
                                    @break
                                    @case('line')
                                    خط فاصل
                                    @break
                                    @case('logo')
                                    شعار
                                    @break
                                    @default
                                    {{ $component->type }}
                                    @endswitch
                                </span>
                                @if($component->is_system)
                                <span class="badge bg-info ms-2">مكون نظام</span>
                                @endif
                                @if($component->is_active)
                                <span class="badge bg-success ms-2">نشط</span>
                                @else
                                <span class="badge bg-danger ms-2">غير نشط</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        @if(!$component->is_system)
                        <a href="{{ route('report-components.edit', $component) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>تعديل
                        </a>
                        <a href="{{ route('report-components.duplicate', $component) }}" class="btn btn-secondary">
                            <i class="bi bi-files me-2"></i>نسخ
                        </a>
                        @endif
                        <a href="{{ route('report-components.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-right me-2"></i>رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Content Template -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">قالب المحتوى</h6>
                </div>
                <div class="card-body">
                    @if($component->content_template)
                    @foreach($component->content_template as $key => $value)
                    <div class="mb-2">
                        <strong>{{ $key }}:</strong>
                        <code class="ms-2">{{ $value }}</code>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">لا يوجد قالب محتوى محدد</p>
                    @endif

                    @if($component->getRequiredVariables())
                    <div class="mt-3">
                        <h6>المتغيرات المطلوبة:</h6>
                        @foreach($component->getRequiredVariables() as $variable)
                        <span class="badge bg-primary me-1">{{ $variable }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Default Properties -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">الخصائص الافتراضية</h6>
                </div>
                <div class="card-body">
                    @if($component->default_properties)
                    @foreach($component->default_properties as $key => $value)
                    <div class="mb-2">
                        <strong>{{ $key }}:</strong>
                        <code class="ms-2">{{ $value }}</code>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">لا توجد خصائص افتراضية محددة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="card-title mb-0">إحصائيات الاستخدام</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $component->template_block_components?->count() ?? 0 }}</h4>
                        <p class="text-muted mb-0">عدد القوالب المستخدمة</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-info">{{ $component->created_at->format('Y-m-d') }}</h4>
                        <p class="text-muted mb-0">تاريخ الإنشاء</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-success">{{ $component->updated_at->format('Y-m-d') }}</h4>
                        <p class="text-muted mb-0">آخر تحديث</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Usage -->
    @if($component->template_block_components && $component->template_block_components->count() > 0)
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="card-title mb-0">القوالب التي تستخدم هذا المكون</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>القالب</th>
                            <th>الجزء</th>
                            <th>اسم المثيل</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($component->template_block_components as $usage)
                        <tr>
                            <td>
                                @if($usage->templateBlock && $usage->templateBlock->printTemplate)
                                <a href="{{ route('report-templates.show', $usage->templateBlock->printTemplate) }}"
                                    class="text-decoration-none">
                                    {{ $usage->templateBlock->printTemplate->name }}
                                </a>
                                @else
                                <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($usage->templateBlock)
                                {{ $usage->templateBlock->name }}
                                @else
                                <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>{{ $usage->name }}</td>
                            <td>
                                @if($usage->is_visible)
                                <span class="badge bg-success">مرئي</span>
                                @else
                                <span class="badge bg-danger">مخفي</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Preview -->
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="card-title mb-0">معاينة المكون</h6>
        </div>
        <div class="card-body">
            <div class="preview-container" style="border: 1px solid #ddd; padding: 20px; min-height: 100px;">
                @php
                $content = $component->content_template ?? [];
                $properties = $component->default_properties ?? [];
                @endphp
                {!! $component->generateContent($content, $properties) !!}
            </div>
        </div>
    </div>
</div>
@endsection