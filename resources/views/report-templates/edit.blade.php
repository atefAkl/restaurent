@extends('layouts.app')
@section('title', 'تعديل القالب')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-templates.index') }}">قوالب التقارير</a></li>
                <li class="breadcrumb-item active">تعديل {{ $template->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">تعديل القالب: {{ $template->name }}</h5>
            
            <form method="POST" action="{{ route('report-templates.update', $template) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم القالب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $template->name) }}" required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع القالب <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', $template->type) == $type ? 'selected' : '' }}>
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
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
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
                                <option value="{{ $theme->id }}" {{ old('theme_id', $template->theme_id) == $theme->id ? 'selected' : '' }}>
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
                            <textarea class="form-control" id="content" name="content" rows="4">{{ old('content', $template->content) }}</textarea>
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
                                       value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
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
                                       value="1" {{ old('is_default', $template->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    افتراضي
                                </label>
                            </div>
                            <div class="form-text">سيكون هذا القالب هو الافتراضي لنوعه</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('report-templates.show', $template) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>تحديث القالب
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template Blocks Management -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">إدارة أجزاء القالب</h6>
            <button class="btn btn-sm btn-primary" onclick="addBlock()">
                <i class="bi bi-plus-circle me-2"></i>إضافة جزء
            </button>
        </div>
        <div class="card-body">
            @if($template->templateBlocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الموقع</th>
                            <th>الحجم</th>
                            <th>الحالة</th>
                            <th>الترتيب</th>
                            <th>العناصر</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($template->templateBlocks as $block)
                        <tr>
                            <td>{{ $block->name }}</td>
                            <td><span class="badge bg-secondary">{{ $block->type }}</span></td>
                            <td>X: {{ $block->position_x }}, Y: {{ $block->position_y }}</td>
                            <td>{{ $block->width }}x{{ $block->height }}</td>
                            <td>
                                @if($block->is_visible)
                                <span class="badge bg-success">مرئي</span>
                                @else
                                <span class="badge bg-secondary">مخفي</span>
                                @endif
                            </td>
                            <td>{{ $block->order }}</td>
                            <td><span class="badge bg-primary">{{ $block->reportElements->count() }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary" onclick="editBlock({{ $block->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="manageElements({{ $block->id }})">
                                        <i class="bi bi-layers"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteBlock({{ $block->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="bi bi-layers" style="font-size: 3rem; color: #dee2e6;"></i>
                <p class="text-muted mt-2">لا توجد أجزاء في هذا القالب</p>
                <button class="btn btn-primary" onclick="addBlock()">
                    <i class="bi bi-plus-circle me-2"></i>إضافة جزء
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function addBlock() {
    // TODO: Implement add block modal
    alert('سيتم فتح نافذة لإضافة جزء جديد');
}

function editBlock(id) {
    // TODO: Implement edit block modal
    alert('سيتم فتح نافذة لتعديل الجزء رقم ' + id);
}

function manageElements(blockId) {
    // TODO: Redirect to elements management page
    alert('سيتم الانتقال إلى صفحة إدارة عناصر الجزء رقم ' + blockId);
}

function deleteBlock(id) {
    if (confirm('هل أنت متأكد من حذف هذا الجزء؟')) {
        // TODO: Implement delete block
        alert('سيتم حذف الجزء رقم ' + id);
    }
}
</script>
@endsection
