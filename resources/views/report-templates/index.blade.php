@extends('layouts.app')
@section('title', 'قوالب التقارير')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item active">قوالب التقارير</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">قوالب التقارير</h5>
                <p class="text-muted mb-0">إدارة قوالب التقارير والفواتير</p>
            </div>
            <div>
                <a href="{{ route('report-templates.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>إنشاء قالب جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('report-templates.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="type" class="form-label">نوع القالب</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>طلب</option>
                            <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>فاتورة</option>
                            <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>إيصال</option>
                            <option value="kitchen_order" {{ request('type') == 'kitchen_order' ? 'selected' : '' }}>طلب مطبخ</option>
                            <option value="shift_report" {{ request('type') == 'shift_report' ? 'selected' : '' }}>تقرير شيفت</option>
                            <option value="expense_report" {{ request('type') == 'expense_report' ? 'selected' : '' }}>تقرير مصروفات</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="is_active" class="form-label">الحالة</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-2"></i>بحث
                            </button>
                            <a href="{{ route('report-templates.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="card">
        <div class="card-body">
            @if($templates->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الثيم</th>
                            <th>الحالة</th>
                            <th>الافتراضي</th>
                            <th>البلوكات</th>
                            <th>تاريخ الإنشاء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $template->name }}</strong>
                                    @if($template->description)
                                    <br><small class="text-muted">{{ $template->description }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $template->type }}
                                </span>
                            </td>
                            <td>
                                @if($template->theme)
                                <span class="badge bg-success">{{ $template->theme->name }}</span>
                                @else
                                <span class="text-muted">بدون ثيم</span>
                                @endif
                            </td>
                            <td>
                                @if($template->is_active)
                                <span class="badge bg-success">نشط</span>
                                @else
                                <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                @if($template->is_default)
                                <span class="badge bg-warning">افتراضي</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $template->templateBlocks->count() }}</span>
                            </td>
                            <td>
                                <small>{{ $template->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('report-templates.customize', $template) }}"
                                        class="btn btn-sm btn-outline-warning" title="تخصيص">
                                        <i class="bi bi-palette"></i>
                                    </a>
                                    <a href="{{ route('report-templates.show', $template) }}"
                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('report-templates.preview', $template) }}"
                                        class="btn btn-sm btn-outline-info" title="معاينة" target="_blank">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('report-templates.edit', $template) }}"
                                        class="btn btn-sm btn-outline-secondary" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('report-templates.duplicate', $template) }}"
                                        class="btn btn-sm btn-outline-dark" title="نسخ">
                                        <i class="bi bi-files"></i>
                                    </a>
                                    <form action="{{ route('report-templates.destroy', $template) }}"
                                        method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="حذف"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا القالب؟')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <x-pagination :data="$templates" />
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #dee2e6;"></i>
                <h5 class="mt-3">لا توجد قوالب</h5>
                <p class="text-muted">لم يتم إنشاء أي قوالب بعد</p>
                <a href="{{ route('report-templates.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>إنشاء قالب جديد
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection