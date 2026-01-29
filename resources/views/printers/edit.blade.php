@extends('layouts.app')

@section('title', 'تعديل الطابعة')

@section('content')
<div class="container-fluid mt-5">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('printers.index') }}">الطابعات</a></li>
                <li class="breadcrumb-item active">اعدادات الطابعة</li>
            </ol>
        </nav>
    </div>
    <!-- Page Header -->
    <div class="card mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="title p-3">
                <h1 class="h3 mb-0">اعدادات الطابعة</h1>
                <p class="text-muted mb-0">تعديل إعدادات الطابعة والخصائص الفيزيائية</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('printers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right"></i>
                    العودة للطابعات
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('printers.update', $printer->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Basic Information -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            المعلومات الأساسية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="name" class="form-label">اسم الطابعة</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $printer->name }}" required>
                                    @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-1">
                                    <label for="type" class="form-label">نوع الطابعة</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="thermal" {{ $printer->type === 'thermal' ? 'selected' : '' }}>طابعة حرارية</option>
                                        <option value="pos" {{ $printer->type === 'pos' ? 'selected' : '' }}>طابعة نقطة بيع</option>
                                        <option value="laser" {{ $printer->type === 'laser' ? 'selected' : '' }}>طابعة ليزر</option>
                                        <option value="inkjet" {{ $printer->type === 'inkjet' ? 'selected' : '' }}>طابعة حبر</option>
                                        <option value="matrix" {{ $printer->type === 'matrix' ? 'selected' : '' }}>طابعة مصفوفة</option>
                                    </select>
                                    @error('type')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-1">
                                    <label for="location" class="form-label">الموقع</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ $printer->location }}" required>
                                    @error('location')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">الوصف</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $printer->description }}</textarea>
                                    @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $printer->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                نشط
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" {{ $printer->is_default ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_default">
                                                افتراضي
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Connection Settings -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-wifi me-2"></i>
                            إعدادات الاتصال
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="connection_type" class="form-label">نوع الاتصال</label>
                                    <select class="form-select" id="connection_type" name="connection_type" required>
                                        <option value="Network" {{ $printer->connection_type === 'Network' ? 'selected' : '' }}>شبكة</option>
                                        <option value="USB" {{ $printer->connection_type === 'USB' ? 'selected' : '' }}>USB</option>
                                        <option value="Bluetooth" {{ $printer->connection_type === 'Bluetooth' ? 'selected' : '' }}>بلوتوث</option>
                                    </select>
                                    @error('connection_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Network Settings -->
                            <div class="col-md-4">
                                <div id="network-settings" class="connection-settings" {{ $printer->connection_type !== 'Network' ? 'style="display:none"' : '' }}>
                                    <div class="mb-3">
                                        <label for="ip_address" class="form-label">عنوان IP</label>
                                        <input type="text" class="form-control" id="ip_address" name="ip_address" value="{{ $printer->ip_address }}" placeholder="192.168.1.100">
                                        @error('ip_address')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="network-settings" class="connection-settings" {{ $printer->connection_type !== 'Network' ? 'style="display:none"' : '' }}>
                                    <div class="mb-3">
                                        <label for="port" class="form-label">المنفذ</label>
                                        <input type="number" class="form-control" id="port" name="port" value="{{ $printer->port ?? 9100 }}" placeholder="9100">
                                        @error('port')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- USB Settings -->
                                <div id="usb-settings" class="connection-settings" {{ $printer->connection_type !== 'USB' ? 'style="display:none"' : '' }}>
                                    <div class="mb-3">
                                        <label for="usb_name" class="form-label">اسم الطابعة في النظام</label>
                                        <input type="text" class="form-control" id="usb_name" name="usb_name" value="{{ $printer->settings['usb_name'] ?? '' }}" placeholder="EPSON TM-T88IV">
                                        <div class="form-text">اسم الطابعة كما يظهر في قائمة الطابعات في Windows</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Bluetooth Settings -->
                                <div id="bluetooth-settings" class="connection-settings" {{ $printer->connection_type !== 'Bluetooth' ? 'style="display:none"' : '' }}>
                                    <div class="mb-3">
                                        <label for="bluetooth_address" class="form-label">عنوان البلوتوث</label>
                                        <input type="text" class="form-control" id="bluetooth_address" name="bluetooth_address" value="{{ $printer->settings['bluetooth_address'] ?? '' }}" placeholder="00:11:22:33:44:55">
                                        <div class="form-text">عنوان MAC للطابعة البلوتوثية</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Physical Printer Settings -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>
                            إعدادات الطابعة الفيزيائية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="manufacturer" class="form-label">الشركة المصنعة</label>
                                <select class="form-select" id="manufacturer" name="manufacturer">
                                    <option value="">اختر الشركة المصنعة</option>
                                    <option value="Epson" {{ ($printer->manufacturer ?? '') === 'Epson' ? 'selected' : '' }}>Epson</option>
                                    <option value="Canon" {{ ($printer->manufacturer ?? '') === 'Canon' ? 'selected' : '' }}>Canon</option>
                                    <option value="HP" {{ ($printer->manufacturer ?? '') === 'HP' ? 'selected' : '' }}>HP</option>
                                    <option value="Brother" {{ ($printer->manufacturer ?? '') === 'Brother' ? 'selected' : '' }}>Brother</option>
                                    <option value="Star" {{ ($printer->manufacturer ?? '') === 'Star' ? 'selected' : '' }}>Star</option>
                                    <option value="Citizen" {{ ($printer->manufacturer ?? '') === 'Citizen' ? 'selected' : '' }}>Citizen</option>
                                    <option value="Other" {{ ($printer->manufacturer ?? '') === 'Other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="model" class="form-label">الموديل</label>
                                <input type="text" class="form-control" id="model" name="model" value="{{ $printer->model }}" placeholder="TM-T88IV">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="paper_type" class="form-label">نوع الورق</label>
                                <select class="form-select" id="paper_type" name="paper_type">
                                    <option value="thermal" {{ $printer->paper_type === 'thermal' ? 'selected' : '' }}>ورق حراري</option>
                                    <option value="regular" {{ $printer->paper_type === 'regular' ? 'selected' : '' }}>ورق عادي</option>
                                    <option value="cashier" {{ $printer->paper_type === 'cashier' ? 'selected' : '' }}>ورق كاشير</option>
                                    <option value="label" {{ $printer->paper_type === 'label' ? 'selected' : '' }}>ملصقات</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="paper_width" class="form-label">عرض الورق (مم)</label>
                                <select class="form-select" id="paper_width" name="paper_width">
                                    <option value="58" {{ $printer->paper_width === '58' ? 'selected' : '' }}>58 مم</option>
                                    <option value="80" {{ $printer->paper_width === '80' ? 'selected' : '' }}>80 مم</option>
                                    <option value="112" {{ $printer->paper_width === '112' ? 'selected' : '' }}>112 مم</option>
                                    <option value="210" {{ $printer->paper_width === '210' ? 'selected' : '' }}>A4 (210 مم)</option>
                                    <option value="297" {{ $printer->paper_width === '297' ? 'selected' : '' }}>A3 (297 مم)</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="print_density" class="form-label">كثافة الطباعة</label>
                                <select class="form-select" id="print_density" name="print_density">
                                    <option value="low" {{ $printer->print_density === 'low' ? 'selected' : '' }}>منخفضة</option>
                                    <option value="medium" {{ $printer->print_density === 'medium' ? 'selected' : '' }}>متوسطة</option>
                                    <option value="high" {{ $printer->print_density === 'high' ? 'selected' : '' }}>عالية</option>
                                </select>
                            </div>
                        </div>

                        <!-- Color Settings -->
                        <div class="mb-3">
                            <label class="form-label">إعدادات الألوان</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="radio" id="color_mode" name="printer_settings[color_mode]" value="color_mode" {{ ($printer->printer_settings['color_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="color_mode">
                                            طباعة ملونة
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="radio" id="grayscale" name="printer_settings[color_mode]" value="grayscale" {{ ($printer->printer_settings['grayscale'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="grayscale">
                                            أبيض وأسود
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="radio" id="monochrome" name="printer_settings[color_mode]" value="monochrome" {{ ($printer->printer_settings['monochrome'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="monochrome">
                                            Monochrome
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orientation Settings -->
                        <div class="mb-3">
                            <label for="orientation" class="form-label">اتجاه الطباعة</label>
                            <select class="form-select" id="orientation" name="printer_settings[orientation]">
                                <option value="portrait" {{ ($printer->printer_settings['orientation'] ?? 'portrait') === 'portrait' ? 'selected' : '' }}>عمودي (Portrait)</option>
                                <option value="landscape" {{ ($printer->printer_settings['orientation'] ?? 'portrait') === 'landscape' ? 'selected' : '' }}>أفقي (Landscape)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test & Status -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-activity me-2"></i>
                            الحالة والاختبار
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Current Status -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6>الحالة الحالية</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-{{ $printer->is_active ? 'success' : 'secondary' }} px-3 py-2 mx-2">
                                                {{ $printer->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                            <span class="badge bg-{{ $printer->is_online ? 'info' : 'warning' }} px-3 py-2">
                                                {{ $printer->is_online ? 'متصل' : 'منفصل' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            @if($printer->is_default)
                                            <span class="badge bg-primary">افتراضي</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4 " style="border-inline-start: 2px solid rgba(6, 110, 207, 1);">
                                <div class="text-muted small">
                                    <div>عدد الطباعات: {{ $printer->total_prints ?? 0 }}</div>
                                    <div>آخر استخدام: {{ $printer->last_used ? $printer->last_used->diffForHumans() : 'لم يُستخدم' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Buttons -->
                        <div class="mb-4">
                            <h6>اختبار الطابعة</h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" id="test-connection-btn" data-printer-id="{{ $printer->id }}" onclick="testConnection(this.dataset.printerId)">
                                    <i class="bi bi-wifi me-2"></i>
                                    اختبار الاتصال
                                </button>
                                <button type="button" class="btn btn-success" id="print-test-page-btn" data-printer-id="{{ $printer->id }}" onclick="printTestPage(this.dataset.printerId)">
                                    <i class="bi bi-printer me-2"></i>
                                    طباعة صفحة اختبار
                                </button>
                            </div>
                        </div>

                        <!-- Connection Info -->
                        <div class="mb-4">
                            <h6>معلومات الاتصال</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="small">
                                    <div><strong>النوع:</strong> {{ $printer->connection_type }}</div>
                                    @if($printer->connection_type === 'Network')
                                    <div><strong>IP:</strong> {{ $printer->ip_address }}</div>
                                    <div><strong>المنفذ:</strong> {{ $printer->port ?? 9100 }}</div>
                                    @endif
                                    <div><strong>الموقع:</strong> {{ $printer->location }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Buttons -->
        <div class="card mb-5">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('printers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                        إلغاء
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-info" onclick="testAllSettings()">
                            <i class="bi bi-check-all"></i>
                            اختبار كل الإعدادات
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Connection type change handler
    document.getElementById('connection_type').addEventListener('change', function() {
        const connectionType = this.value;

        // Hide all connection settings
        document.querySelectorAll('.connection-settings').forEach(el => {
            el.style.display = 'none';
        });

        // Show relevant settings
        if (connectionType === 'Network') {
            document.getElementById('network-settings').style.display = 'block';
        } else if (connectionType === 'USB') {
            document.getElementById('usb-settings').style.display = 'block';
        } else if (connectionType === 'Bluetooth') {
            document.getElementById('bluetooth-settings').style.display = 'block';
        }
    });

    // Test connection
    function testConnection(printerId) {
        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        fetch(`/printers/test-connection`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    printer_id: printerId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ ' + data.message, 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ أثناء اختبار الاتصال', 'error');
            });
    }

    // Print test page
    function printTestPage(printerId) {
        showNotification('🔄 جاري طباعة صفحة الاختبار...', 'info');

        fetch(`/printers/${printerId}/print-test`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ تم إرسال صفحة الاختبار بنجاح', 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ أثناء طباعة الصفحة', 'error');
            });
    }

    // Test all settings
    function testAllSettings() {
        const printerId = document.getElementById('test-connection-btn').dataset.printerId;

        // Test connection first
        testConnection(printerId);

        // Then print test page after a delay
        setTimeout(() => {
            printTestPage(printerId);
        }, 2000);
    }
</script>
@endpush