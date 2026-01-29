@extends('layouts.app')
@section('title', 'مكونات التقارير')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item active">مكونات التقارير</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">مكونات التقارير</h5>
                    <p class="text-muted mb-0">إدارة مكونات التقارير القابلة لإعادة الاستخدام</p>
                </div>
                <div>
                    <a href="{{ route('report-components.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>إنشاء مكون جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('report-components.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="بحث في المكونات..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="type">
                        <option value="">جميع الأنواع</option>
                        <option value="header" {{ request('type') == 'header' ? 'selected' : '' }}>ترويسة</option>
                        <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>نص</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>صورة</option>
                        <option value="table" {{ request('type') == 'table' ? 'selected' : '' }}>جدول</option>
                        <option value="barcode" {{ request('type') == 'barcode' ? 'selected' : '' }}>باركود</option>
                        <option value="qr_code" {{ request('type') == 'qr_code' ? 'selected' : '' }}>QR Code</option>
                        <option value="line" {{ request('type') == 'line' ? 'selected' : '' }}>خط فاصل</option>
                        <option value="logo" {{ request('type') == 'logo' ? 'selected' : '' }}>شعار</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="is_system">
                        <option value="">الكل</option>
                        <option value="1" {{ request('is_system') == '1' ? 'selected' : '' }}>مكونات النظام</option>
                        <option value="0" {{ request('is_system') == '0' ? 'selected' : '' }}>مكونات مخصصة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-2"></i>بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Components List -->
    <div class="card">
        <div class="card-body">
            @if($components->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>المكون</th>
                            <th>النوع</th>
                            <th>الاستخدام</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($components as $component)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @switch($component->type)
                                        @case('header')
                                        <i class="bi bi-type-h1 text-primary fs-4"></i>
                                        @break
                                        @case('text')
                                        <i class="bi bi-text-paragraph text-info fs-4"></i>
                                        @break
                                        @case('image')
                                        <i class="bi bi-image text-success fs-4"></i>
                                        @break
                                        @case('table')
                                        <i class="bi bi-table text-warning fs-4"></i>
                                        @break
                                        @case('barcode')
                                        <i class="bi bi-upc-scan text-danger fs-4"></i>
                                        @break
                                        @case('qr_code')
                                        <i class="bi bi-qr-code text-secondary fs-4"></i>
                                        @break
                                        @case('line')
                                        <i class="bi bi-dash-lg text-dark fs-4"></i>
                                        @break
                                        @case('logo')
                                        <i class="bi bi-badge text-primary fs-4"></i>
                                        @break
                                        @default
                                        <i class="bi bi-puzzle text-muted fs-4"></i>
                                        @endswitch
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $component->name }}</div>
                                        @if($component->description)
                                        <small class="text-muted">{{ $component->description }}</small>
                                        @endif
                                        @if($component->is_system)
                                        <span class="badge bg-info ms-2">نظام</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $component->template_block_components_count }} قالب</span>
                            </td>
                            <td>
                                @if($component->is_active)
                                <span class="badge bg-success">نشط</span>
                                @else
                                <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('report-components.show', $component) }}"
                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$component->is_system)
                                    <a href="{{ route('report-components.edit', $component) }}"
                                        class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('report-components.duplicate', $component) }}"
                                        class="btn btn-sm btn-outline-secondary" title="نسخ">
                                        <i class="bi bi-files"></i>
                                    </a>
                                    <form action="{{ route('report-components.destroy', $component) }}"
                                        method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="حذف"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المكون؟')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="مكون نظام">
                                        <i class="bi bi-lock"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <x-pagination :data="$components" />
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-puzzle text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">لا توجد مكونات</h5>
                <p class="text-muted">لم يتم العثور على مكونات تطابق البحث</p>
                <a href="{{ route('report-components.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>إنشاء مكون جديد
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection