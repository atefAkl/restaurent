@extends('layouts.app')
@section('title', 'إنشاء مكون جديد')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report-components.index') }}">مكونات التقارير</a></li>
                <li class="breadcrumb-item active">إنشاء مكون جديد</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">إنشاء مكون جديد</h5>

            <form method="POST" action="{{ route('report-components.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المكون <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" required>
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
                                <option value="header" {{ old('type') == 'header' ? 'selected' : '' }}>ترويسة</option>
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>نص</option>
                                <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>صورة</option>
                                <option value="table" {{ old('type') == 'table' ? 'selected' : '' }}>جدول</option>
                                <option value="barcode" {{ old('type') == 'barcode' ? 'selected' : '' }}>باركود</option>
                                <option value="qr_code" {{ old('type') == 'qr_code' ? 'selected' : '' }}>QR Code</option>
                                <option value="line" {{ old('type') == 'line' ? 'selected' : '' }}>خط فاصل</option>
                                <option value="logo" {{ old('type') == 'logo' ? 'selected' : '' }}>شعار</option>
                            </select>
                            @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content Template Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">قالب المحتوى</h6>
                        <small class="text-muted">استخدم &amp;#123;&amp;#123;variable&amp;#125;&amp;#125; للمتغيرات الديناميكية</small>
                    </div>
                    <div class="card-body">
                        <div id="contentTemplateFields">
                            <!-- Dynamic fields will be inserted here based on component type -->
                            <div class="text-muted">
                                اختر نوع المكون لإظهار حقول المحتوى المناسبة
                            </div>
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_color" class="form-label">لون النص</label>
                                    <input type="color" class="form-control form-control-color" id="prop_color"
                                        name="default_properties[color]" value="#000000">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_font_size" class="form-label">حجم الخط</label>
                                    <select class="form-select" id="prop_font_size" name="default_properties[font_size]">
                                        <option value="12px">12px</option>
                                        <option value="14px" selected>14px</option>
                                        <option value="16px">16px</option>
                                        <option value="18px">18px</option>
                                        <option value="20px">20px</option>
                                        <option value="24px">24px</option>
                                        <option value="28px">28px</option>
                                        <option value="32px">32px</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_font_weight" class="form-label">وزن الخط</label>
                                    <select class="form-select" id="prop_font_weight" name="default_properties[font_weight]">
                                        <option value="normal">عادي</option>
                                        <option value="bold">عريض</option>
                                        <option value="light">خفيف</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_text_align" class="form-label">محاذاة النص</label>
                                    <select class="form-select" id="prop_text_align" name="default_properties[text_align]">
                                        <option value="right">يمين</option>
                                        <option value="center">وسط</option>
                                        <option value="left">يسار</option>
                                        <option value="justify">ضبط</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_width" class="form-label">العرض</label>
                                    <input type="text" class="form-control" id="prop_width"
                                        name="default_properties[width]" placeholder="مثال: 100px أو 100%">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_height" class="form-label">الارتفاع</label>
                                    <input type="text" class="form-control" id="prop_height"
                                        name="default_properties[height]" placeholder="مثال: 50px أو auto">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_margin" class="form-label">الهامش الخارجي</label>
                                    <input type="text" class="form-control" id="prop_margin"
                                        name="default_properties[margin]" placeholder="مثال: 10px 5px">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prop_padding" class="form-label">الهامش الداخلي</label>
                                    <input type="text" class="form-control" id="prop_padding"
                                        name="default_properties[padding]" placeholder="مثال: 5px 10px">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_border" class="form-label">الحدود</label>
                                    <input type="text" class="form-control" id="prop_border"
                                        name="default_properties[border]" placeholder="مثال: 1px solid #ccc">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_border_radius" class="form-label">استدارة الحواف</label>
                                    <input type="text" class="form-control" id="prop_border_radius"
                                        name="default_properties[border_radius]" placeholder="مثال: 5px">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prop_background" class="form-label">لون الخلفية</label>
                                    <input type="color" class="form-control form-control-color" id="prop_background"
                                        name="default_properties[background]" value="#ffffff">
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
                                    value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    نشط
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('report-components.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>حفظ المكون
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateContentTemplate() {
        const type = document.getElementById('type').value;
        const container = document.getElementById('contentTemplateFields');

        let html = '';

        switch (type) {
            case 'header':
                html = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">نص الترويسة</label>
                            <input type="text" class="form-control" name="content_template[text]" 
                                   placeholder="مثال: &amp;#123;&amp;#123;company_name&amp;#125;&amp;#125; أو نص ثابت">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">مستوى الترويسة</label>
                            <select class="form-select" name="content_template[level]">
                                <option value="1">H1</option>
                                <option value="2">H2</option>
                                <option value="3">H3</option>
                                <option value="4">H4</option>
                                <option value="5">H5</option>
                                <option value="6">H6</option>
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
                                   placeholder="مثال: &amp;#123;&amp;#123;customer_name&amp;#125;&amp;#125; أو نص ثابت">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">وسم HTML</label>
                            <select class="form-select" name="content_template[tag]">
                                <option value="p">Paragraph (p)</option>
                                <option value="span">Span</option>
                                <option value="div">Div</option>
                                <option value="h1">H1</option>
                                <option value="h2">H2</option>
                                <option value="h3">H3</option>
                                <option value="h4">H4</option>
                                <option value="h5">H5</option>
                                <option value="h6">H6</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">CSS Class</label>
                            <input type="text" class="form-control" name="content_template[class]" 
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
                                   placeholder="مثال: &amp;#123;&amp;#123;product_image&amp;#125;&amp;#125; أو /images/logo.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نص بديل (Alt)</label>
                            <input type="text" class="form-control" name="content_template[alt]" 
                                   placeholder="مثال: &amp;#123;&amp;#123;product_name&amp;#125;&amp;#125; أو شعار الشركة">
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
                           placeholder="مثال: المنتج,الكمية,السعر,الإجمالي">
                    <small class="text-muted">افصل بين العناوين بفاصلة</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">مصدر الصفوف</label>
                    <input type="text" class="form-control" name="content_template[rows]" 
                           placeholder="مثال: &amp;#123;&amp;#123;items&amp;#125;&amp;#125;" value="&amp;#123;&amp;#123;items&amp;#125;&amp;#125;">
                    <small class="text-muted">المتغير الذي يحتوي على بيانات الصفوف</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">CSS Class</label>
                            <input type="text" class="form-control" name="content_template[class]" 
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
                                   placeholder="مثال: &amp;#123;&amp;#123;product_barcode&amp;#125;&amp;#125; أو &amp;#123;&amp;#123;order_number&amp;#125;&amp;#125;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نوع الباركود</label>
                            <select class="form-select" name="content_template[type]">
                                <option value="code128">Code 128</option>
                                <option value="code39">Code 39</option>
                                <option value="ean13">EAN-13</option>
                                <option value="ean8">EAN-8</option>
                                <option value="upca">UPC-A</option>
                                <option value="upce">UPC-E</option>
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
                           placeholder="مثال: &amp;#123;&amp;#123;order_url&amp;#125;&amp;#125; أو &amp;#123;&amp;#123;order_number&amp;#125;&amp;#125;">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الحجم</label>
                            <input type="text" class="form-control" name="content_template[size]" 
                                   placeholder="مثال: 150x150" value="150x150">
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
                                   placeholder="مثال: 1px" value="1px">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اللون</label>
                            <input type="color" class="form-control form-control-color" name="content_template[color]" 
                                   value="#cccccc">
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
                                   placeholder="مثال: &amp;#123;&amp;#123;company_logo&amp;#125;&amp;#125; أو /images/logo.png" value="/images/logo.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نص بديل (Alt)</label>
                            <input type="text" class="form-control" name="content_template[alt]" 
                                   placeholder="مثال: &amp;#123;&amp;#123;company_name&amp;#125;&amp;#125;" value="&amp;#123;&amp;#123;company_name&amp;#125;&amp;#125;">
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