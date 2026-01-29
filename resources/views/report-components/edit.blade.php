@extends('layouts.app')
@section('title', 'تعديل المكون')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-components.index') }}">مكونات التقارير</a></li>
                <li class="breadcrumb-item active">تعديل المكون</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">تعديل المكون: {{ $reportComponent->name }}</h5>
            
            <form method="POST" action="{{ route('report-components.update', $reportComponent) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المكون <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $reportComponent->name) }}" required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع المكون <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required onchange="updateContentTemplate()">
                                <option value="">اختر النوع</option>
                                <option value="header" {{ old('type', $reportComponent->type) == 'header' ? 'selected' : '' }}>ترويسة</option>
                                <option value="text" {{ old('type', $reportComponent->type) == 'text' ? 'selected' : '' }}>نص</option>
                                <option value="image" {{ old('type', $reportComponent->type) == 'image' ? 'selected' : '' }}>صورة</option>
                                <option value="table" {{ old('type', $reportComponent->type) == 'table' ? 'selected' : '' }}>جدول</option>
                                <option value="barcode" {{ old('type', $reportComponent->type) == 'barcode' ? 'selected' : '' }}>باركود</option>
                                <option value="qr_code" {{ old('type', $reportComponent->type) == 'qr_code' ? 'selected' : '' }}>QR Code</option>
                                <option value="line" {{ old('type', $reportComponent->type) == 'line' ? 'selected' : '' }}>خط فاصل</option>
                                <option value="logo" {{ old('type', $reportComponent->type) == 'logo' ? 'selected' : '' }}>شعار</option>
                            </select>
                            @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $reportComponent->description) }}</textarea>
                    @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content Template Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">قالب المحتوى</h6>
                        <small class="text-muted">استخدم {{variable}} للمتغيرات الديناميكية</small>
                    </div>
                    <div class="card-body">
                        <div id="contentTemplateFields">
                            <!-- Dynamic fields will be inserted here based on component type -->
                            @php
                                $contentTemplate = old('content_template', $reportComponent->content_template ?? []);
                            @endphp
                        </div>
                    </div>
                </div>

                <!-- Default Properties Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">الخصائص الافتراضية</h6>
                        <small class="text-muted">الخصائص التي ستطبق افتراضياً عند استخدام المكون</small>
                    </div>
                    <div class="card-body">
                        @php
                            $defaultProperties = old('default_properties', $reportComponent->default_properties ?? []);
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_color" class="form-label">لون النص</label>
                                    <input type="color" class="form-control form-control-color" id="prop_color" 
                                           name="default_properties[color]" value="{{ $defaultProperties['color'] ?? '#000000' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_font_size" class="form-label">حجم الخط</label>
                                    <select class="form-select" id="prop_font_size" name="default_properties[font_size]">
                                        <option value="12px" {{ ($defaultProperties['font_size'] ?? '') == '12px' ? 'selected' : '' }}>12px</option>
                                        <option value="14px" {{ ($defaultProperties['font_size'] ?? '') == '14px' ? 'selected' : '' }}>14px</option>
                                        <option value="16px" {{ ($defaultProperties['font_size'] ?? '') == '16px' ? 'selected' : '' }}>16px</option>
                                        <option value="18px" {{ ($defaultProperties['font_size'] ?? '') == '18px' ? 'selected' : '' }}>18px</option>
                                        <option value="20px" {{ ($defaultProperties['font_size'] ?? '') == '20px' ? 'selected' : '' }}>20px</option>
                                        <option value="24px" {{ ($defaultProperties['font_size'] ?? '') == '24px' ? 'selected' : '' }}>24px</option>
                                        <option value="28px" {{ ($defaultProperties['font_size'] ?? '') == '28px' ? 'selected' : '' }}>28px</option>
                                        <option value="32px" {{ ($defaultProperties['font_size'] ?? '') == '32px' ? 'selected' : '' }}>32px</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_font_weight" class="form-label">وزن الخط</label>
                                    <select class="form-select" id="prop_font_weight" name="default_properties[font_weight]">
                                        <option value="normal" {{ ($defaultProperties['font_weight'] ?? '') == 'normal' ? 'selected' : '' }}>عادي</option>
                                        <option value="bold" {{ ($defaultProperties['font_weight'] ?? '') == 'bold' ? 'selected' : '' }}>عريض</option>
                                        <option value="light" {{ ($defaultProperties['font_weight'] ?? '') == 'light' ? 'selected' : '' }}>خفيف</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_text_align" class="form-label">محاذاة النص</label>
                                    <select class="form-select" id="prop_text_align" name="default_properties[text_align]">
                                        <option value="right" {{ ($defaultProperties['text_align'] ?? '') == 'right' ? 'selected' : '' }}>يمين</option>
                                        <option value="center" {{ ($defaultProperties['text_align'] ?? '') == 'center' ? 'selected' : '' }}>وسط</option>
                                        <option value="left" {{ ($defaultProperties['text_align'] ?? '') == 'left' ? 'selected' : '' }}>يسار</option>
                                        <option value="justify" {{ ($defaultProperties['text_align'] ?? '') == 'justify' ? 'selected' : '' }}>ضبط</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_width" class="form-label">العرض</label>
                                    <input type="text" class="form-control" id="prop_width" 
                                           name="default_properties[width]" value="{{ $defaultProperties['width'] ?? '' }}" placeholder="مثال: 100px أو 100%">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_height" class="form-label">الارتفاع</label>
                                    <input type="text" class="form-control" id="prop_height" 
                                           name="default_properties[height]" value="{{ $defaultProperties['height'] ?? '' }}" placeholder="مثال: 50px أو auto">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_margin" class="form-label">الهامش الخارجي</label>
                                    <input type="text" class="form-control" id="prop_margin" 
                                           name="default_properties[margin]" value="{{ $defaultProperties['margin'] ?? '' }}" placeholder="مثال: 10px 5px">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_padding" class="form-label">الهامش الداخلي</label>
                                    <input type="text" class="form-control" id="prop_padding" 
                                           name="default_properties[padding]" value="{{ $defaultProperties['padding'] ?? '' }}" placeholder="مثال: 5px 10px">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_border" class="form-label">الحدود</label>
                                    <input type="text" class="form-control" id="prop_border" 
                                           name="default_properties[border]" value="{{ $defaultProperties['border'] ?? '' }}" placeholder="مثال: 1px solid #ccc">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_border_radius" class="form-label">استدارة الحواف</label>
                                    <input type="text" class="form-control" id="prop_border_radius" 
                                           name="default_properties[border_radius]" value="{{ $defaultProperties['border_radius'] ?? '' }}" placeholder="مثال: 5px">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_background" class="form-label">لون الخلفية</label>
                                    <input type="color" class="form-control form-control-color" id="prop_background" 
                                           name="default_properties[background]" value="{{ $defaultProperties['background'] ?? '#ffffff' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $reportComponent->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    نشط
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('report-components.show', $reportComponent) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Store the original content template data
const originalContentTemplate = @json($reportComponent->content_template ?? []);

function updateContentTemplate() {
    const type = document.getElementById('type').value;
    const container = document.getElementById('contentTemplateFields');
    
    let html = '';
    
    // Get existing values or defaults
    const getTextValue = (key, defaultValue = '') => {
        return originalContentTemplate[key] || defaultValue;
    };
    
    switch(type) {
        case 'header':
            html = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">نص الترويسة</label>
                            <input type="text" class="form-control" name="content_template[text]" 
                                   value="${getTextValue('text', '{{company_name}}')}"
                                   placeholder="مثال: {{company_name}} أو نص ثابت">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">مستوى الترويسة</label>
                            <select class="form-select" name="content_template[level]">
                                <option value="1" ${getTextValue('level') == '1' ? 'selected' : ''}>H1</option>
                                <option value="2" ${getTextValue('level') == '2' ? 'selected' : ''}>H2</option>
                                <option value="3" ${getTextValue('level') == '3' ? 'selected' : ''}>H3</option>
                                <option value="4" ${getTextValue('level') == '4' ? 'selected' : ''}>H4</option>
                                <option value="5" ${getTextValue('level') == '5' ? 'selected' : ''}>H5</option>
                                <option value="6" ${getTextValue('level') == '6' ? 'selected' : ''}>H6</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'text':
            html = `
                <div class="mb-3">
                    <label class="form-label">نص العنصر</label>
                    <input type="text" class="form-control" name="content_template[text]" 
                           value="${getTextValue('text', '{{customer_name}}')}"
                           placeholder="مثال: {{customer_name}} أو نص ثابت">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">وسم HTML</label>
                            <select class="form-select" name="content_template[tag]">
                                <option value="p" ${getTextValue('tag') == 'p' ? 'selected' : ''}>Paragraph (p)</option>
                                <option value="span" ${getTextValue('tag') == 'span' ? 'selected' : ''}>Span</option>
                                <option value="div" ${getTextValue('tag') == 'div' ? 'selected' : ''}>Div</option>
                                <option value="h1" ${getTextValue('tag') == 'h1' ? 'selected' : ''}>H1</option>
                                <option value="h2" ${getTextValue('tag') == 'h2' ? 'selected' : ''}>H2</option>
                                <option value="h3" ${getTextValue('tag') == 'h3' ? 'selected' : ''}>H3</option>
                                <option value="h4" ${getTextValue('tag') == 'h4' ? 'selected' : ''}>H4</option>
                                <option value="h5" ${getTextValue('tag') == 'h5' ? 'selected' : ''}>H5</option>
                                <option value="h6" ${getTextValue('tag') == 'h6' ? 'selected' : ''}>H6</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">CSS Class</label>
                            <input type="text" class="form-control" name="content_template[class]" 
                                   value="${getTextValue('class', '')}"
                                   placeholder="مثال: text-bold, text-center">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'image':
            html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رابط الصورة</label>
                            <input type="text" class="form-control" name="content_template[src]" 
                                   value="${getTextValue('src', '{{product_image}}')}"
                                   placeholder="مثال: {{product_image}} أو /images/logo.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نص بديل (Alt)</label>
                            <input type="text" class="form-control" name="content_template[alt]" 
                                   value="${getTextValue('alt', '{{product_name}}')}"
                                   placeholder="مثال: {{product_name}} أو شعار الشركة">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'table':
            html = `
                <div class="mb-3">
                    <label class="form-label">عناوين الأعمدة</label>
                    <input type="text" class="form-control" name="content_template[headers]" 
                           value="${getTextValue('headers', 'المنتج,الكمية,السعر,الإجمالي')}"
                           placeholder="مثال: المنتج,الكمية,السعر,الإجمالي">
                    <small class="text-muted">افصل بين العناوين بفاصلة</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">مصدر الصفوف</label>
                    <input type="text" class="form-control" name="content_template[rows]" 
                           value="${getTextValue('rows', '{{items}}')}"
                           placeholder="مثال: {{items}}">
                    <small class="text-muted">المتغير الذي يحتوي على بيانات الصفوف</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">CSS Class</label>
                            <input type="text" class="form-control" name="content_template[class]" 
                                   value="${getTextValue('class', 'table')}"
                                   placeholder="مثال: table, table-striped">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'barcode':
            html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">قيمة الباركود</label>
                            <input type="text" class="form-control" name="content_template[value]" 
                                   value="${getTextValue('value', '{{product_barcode}}')}"
                                   placeholder="مثال: {{product_barcode}} أو {{order_number}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نوع الباركود</label>
                            <select class="form-select" name="content_template[type]">
                                <option value="code128" ${getTextValue('type') == 'code128' ? 'selected' : ''}>Code 128</option>
                                <option value="code39" ${getTextValue('type') == 'code39' ? 'selected' : ''}>Code 39</option>
                                <option value="ean13" ${getTextValue('type') == 'ean13' ? 'selected' : ''}>EAN-13</option>
                                <option value="ean8" ${getTextValue('type') == 'ean8' ? 'selected' : ''}>EAN-8</option>
                                <option value="upca" ${getTextValue('type') == 'upca' ? 'selected' : ''}>UPC-A</option>
                                <option value="upce" ${getTextValue('type') == 'upce' ? 'selected' : ''}>UPC-E</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'qr_code':
            html = `
                <div class="mb-3">
                    <label class="form-label">قيمة QR Code</label>
                    <input type="text" class="form-control" name="content_template[value]" 
                           value="${getTextValue('value', '{{order_url}}')}"
                           placeholder="مثال: {{order_url}} أو {{order_number}}">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الحجم</label>
                            <input type="text" class="form-control" name="content_template[size]" 
                                   value="${getTextValue('size', '150x150')}"
                                   placeholder="مثال: 150x150">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'line':
            html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">سمك الخط</label>
                            <input type="text" class="form-control" name="content_template[thickness]" 
                                   value="${getTextValue('thickness', '1px')}"
                                   placeholder="مثال: 1px">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اللون</label>
                            <input type="color" class="form-control form-control-color" name="content_template[color]" 
                                   value="${getTextValue('color', '#cccccc')}">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'logo':
            html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رابط الشعار</label>
                            <input type="text" class="form-control" name="content_template[src]" 
                                   value="${getTextValue('src', '/images/logo.png')}"
                                   placeholder="مثال: {{company_logo}} أو /images/logo.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نص بديل (Alt)</label>
                            <input type="text" class="form-control" name="content_template[alt]" 
                                   value="${getTextValue('alt', '{{company_name}}')}"
                                   placeholder="مثال: {{company_name}}">
                        </div>
                    </div>
                </div>
            `;
            break;
            
        default:
            html = '<div class="text-muted">اختر نوع المكون لإظهار حقول المحتوى المناسبة</div>';
    }
    
    container.innerHTML = html;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateContentTemplate();
});
</script>
@endsection
