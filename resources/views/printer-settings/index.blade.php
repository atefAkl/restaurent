@extends('layouts.app')

@section('title', 'إعدادات الطباعة')

@section('content')
<div class="container-fluid mt-5">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('printers.index') }}">الطابعات</a></li>
                <li class="breadcrumb-item active">اعدادات الطباعة</li>
            </ol>
        </nav>
    </div>
    <div class="card mb-3">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="p-3">
                <h1 class="h3 mb-0">إعدادات الطباعة</h1>
                <p class="text-muted mb-0">تكوين إعدادات الطابعات والطباعة التلقائية</p>
            </div>
            <div class="btn-group p-3">
                <button type="button" class="btn btn-outline-primary" onclick="testReceipt()">
                    <i class="bi bi-printer"></i>
                    طباعة إيصال تجريبي
                </button>
                <button type="submit" class="btn btn-primary" onclick="saveSettings(event)">
                    <i class="bi bi-save"></i>
                    حفظ الإعدادات
                </button>
            </div>
        </div>
    </div>

    <!-- Printer Selection -->

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="col-lg-6">
                        <i class="bi bi-printer me-2"></i>
                        اختيار الطابعة الافتراضية
                        </h5>
                    </div>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label for="default_printer" class="form-label">الطابعة الافتراضية</label>
                        <select class="form-select" id="default_printer" name="default_printer_id">
                            <option value="">اختر طابعة افتراضية</option>
                            @foreach($printers as $printer)
                            <option value="{{ $printer->id }}" {{ $defaultPrinter && $defaultPrinter->id == $printer->id ? 'selected' : '' }}>
                                {{ $printer->name }} ({{ $printer->type }} - {{ $printer->connection_type }})
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">سيتم استخدام هذه الطابعة بشكل افتراضي لجميع عمليات الطباعة</div>
                    </div>

                    @if($defaultPrinter)
                    <div class="alert alert-info">
                        <h6 class="alert-heading">الطابعة الحالية:</h6>
                        <p class="mb-1">
                            <strong>{{ $defaultPrinter->name }}</strong><br>
                            <small class="text-muted">
                                النوع: {{ $defaultPrinter->type }} |
                                الاتصال: {{ $defaultPrinter->connection_type }} |
                                الموقع: {{ $defaultPrinter->location }}
                            </small>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Print Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        إعدادات الطباعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="auto_print_receipt" name="auto_print_receipt">
                            <label class="form-check-label" for="auto_print_receipt">
                                طباعة الإيصال تلقائياً
                            </label>
                        </div>
                        <div class="form-text">سيتم طباعة الإيصال تلقائياً بعد كل عملية بيع</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="paper_width" class="form-label">عرض الورق</label>
                                <select class="form-select" id="paper_width" name="paper_width">
                                    <option value="58">58 مم</option>
                                    <option value="80" selected>80 مم</option>
                                    <option value="112">112 مم</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="print_density" class="form-label">كثافة الطباعة</label>
                                <select class="form-select" id="print_density" name="print_density">
                                    <option value="low">منخفضة</option>
                                    <option value="medium" selected>متوسطة</option>
                                    <option value="high">عالية</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="print_speed" class="form-label">سرعة الطباعة</label>
                                <select class="form-select" id="print_speed" name="print_speed">
                                    <option value="slow">بطيئة</option>
                                    <option value="medium" selected>متوسطة</option>
                                    <option value="fast">سريعة</option>
                                </select>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Receipt Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-text me-2"></i>
                        محتوى الإيصال
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="receipt_header" class="form-label">رأس الإيصال</label>
                                <textarea class="form-control" id="receipt_header" name="receipt_header" rows="3" placeholder="">مطعم الأكل السريع | شارع الرئيسي - القاهرة | تلفون: 0123456789</textarea>
                                <div class="form-text">سيظهر في أعلى كل إيصال</div>
                            </div>
                        </div>
                        <div class="col md-6">
                            <div class="mb-3">
                                <label for="receipt_footer" class="form-label">ذيل الإيصال</label>
                                <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2" placeholder="">شكراً لزيارتكم | نتمنى لكم يوماً سعيداً</textarea>
                                <div class="form-text">سيظهر في أسفل كل إيصال</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="print_logo" name="print_logo" checked>
                                <label class="form-check-label" for="print_logo">
                                    طباعة الشعار
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="print_qr_code" name="print_qr_code" checked>
                                <label class="form-check-label" for="print_qr_code">
                                    طباعة رمز QR
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="col-lg-6 h-100">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>
                        معاينة الإيصال
                    </h5>
                </div>
                <div class="card-body">
                    <div class="receipt-preview bg-light p-3" style="font-family: monospace; font-size: 12px; white-space: pre;">
                        RESTAURANT POS
                        ================================
                        إيصال تجريبي
                        ================================

                        الصنف الكمية السعر
                        --------------------------------
                        شاي 2 10.00
                        قهوة 1 15.00
                        ساندوتش 3 45.00
                        --------------------------------
                        الإجمالي: 70.00
                        ضريبة: 7.00
                        المجموع: 77.00

                        ================================
                        QR CODE
                        [رمز QR للدفع الإلكتروني]
                        ================================

                        شكراً لزيارتكم
                        نتمنى لكم يوماً سعيداً

                        التاريخ: 2026-01-28 16:08:00
                        الكاشير: تجريبي



                    </div>
                    <div class="form-text mt-2">
                        <small class="text-muted">هذه معاينة لكيفية ظهور الإيصال المطبوع</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Printers -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-printer me-2"></i>
                        الطابعات المتاحة
                    </h5>
                </div>
                <div class="card-body">
                    @if($printers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>الاتصال</th>
                                    <th>الموقع</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($printers as $printer)
                                <tr>
                                    <td>{{ $printer->name }}</td>
                                    <td>{{ $printer->type }}</td>
                                    <td>{{ $printer->connection_type }}</td>
                                    <td>{{ $printer->location }}</td>
                                    <td>
                                        <span class="badge bg-{{ $printer->is_active ? 'success' : 'secondary' }}">
                                            {{ $printer->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                        @if($printer->is_default)
                                        <span class="badge bg-primary ms-1">افتراضي</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" data-printer-id="{{ $printer->id }}" onclick="testPrinter(this.dataset.printerId)">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <a href="{{ route('printers.edit', $printer->id) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        لا توجد طابعات متاحة. <a href="{{ route('printers.create') }}" class="alert-link">إضافة طابعة جديدة</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load current settings
    document.addEventListener('DOMContentLoaded', function() {
        loadSettings();
    });

    // Show notification function
    function showNotification(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        // Add to page
        const container = document.getElementById('toast-container') || createToastContainer();
        container.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove after hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Create toast container if not exists
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    }

    // Load printer settings
    function loadSettings() {
        fetch('/printer-settings/get')
            .then(response => response.json())
            .then(data => {
                // Update form fields
                document.getElementById('default_printer').value = data.default_printer_id || '';
                document.getElementById('auto_print_receipt').checked = data.auto_print_receipt || false;
                document.getElementById('receipt_header').value = data.receipt_header || '';
                document.getElementById('receipt_footer').value = data.receipt_footer || '';
                document.getElementById('print_logo').checked = data.print_logo !== false;
                document.getElementById('print_qr_code').checked = data.print_qr_code !== false;
                document.getElementById('paper_width').value = data.paper_width || '80';
                document.getElementById('print_density').value = data.print_density || 'medium';
                document.getElementById('copies').value = data.copies || 1;
            })
            .catch(error => {
                console.error('Error loading settings:', error);
                showNotification('❌ فشل تحميل الإعدادات', 'error');
            });
    }

    // Save settings
    function saveSettings(event) {
        event.preventDefault(); // Prevent form submission

        showNotification('🔄 جاري حفظ الإعدادات...', 'info');

        const formData = new FormData();
        formData.append('default_printer_id', document.getElementById('default_printer').value);
        formData.append('auto_print_receipt', document.getElementById('auto_print_receipt').checked);
        formData.append('receipt_header', document.getElementById('receipt_header').value);
        formData.append('receipt_footer', document.getElementById('receipt_footer').value);
        formData.append('print_logo', document.getElementById('print_logo').checked);
        formData.append('print_qr_code', document.getElementById('print_qr_code').checked);
        formData.append('paper_width', document.getElementById('paper_width').value);
        formData.append('print_density', document.getElementById('print_density').value);
        formData.append('copies', document.getElementById('copies').value);

        console.log('Saving settings:', Object.fromEntries(formData));

        fetch('/printer-settings/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showNotification('✅ ' + data.message, 'success');
                    // Reload page after successful save to show updated settings
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('❌ ' + (data.message || 'فشل حفظ الإعدادات'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ أثناء حفظ الإعدادات: ' + error.message, 'error');
            });
    }

    // Test receipt printing
    function testReceipt() {
        showNotification('🔄 جاري طباعة إيصال تجريبي...', 'info');

        fetch('/printer-settings/test-receipt', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
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
                showNotification('❌ حدث خطأ أثناء طباعة الإيصال التجريبي', 'error');
            });
    }

    // Test specific printer
    function testPrinter(printerId) {
        showNotification('🔄 جاري اختبار الطابعة...', 'info');

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
                    showNotification('✅ تم اختبار الطابعة بنجاح', 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ أثناء اختبار الطابعة', 'error');
            });
    }
</script>
@endpush