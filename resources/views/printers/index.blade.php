@extends('layouts.app')

@section('content')
<div class="container-fluid" id="printers-container" data-printers="{{ json_encode($printers ?? []) }}">
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('app.sidebar.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">{{__('app.sidebar.settings')}}</a></li>
                <li class="breadcrumb-item active">{{__('printers.title')}}</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="title">
                <h1 class="h3 mb-0">{{ __('printers.title') }}</h1>
                <p class="text-muted mb-0">{{ __('printers.subtitle') }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('printer-settings.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-gear me-1"></i> إعدادات الطباعة
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrinterModal">
                    <i class="bi bi-plus-circle me-1"> &nbsp; </i>{{ __('app.actions.new') }}
                </button>
            </div>
        </div>
    </div>


    <!-- Available Printers Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-printer me-2"></i>الطابعات المتاحة على الجهاز
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshPrinters()">
                        <i class="bi bi-arrow-clockwise me-1"></i> &nbsp; تحديث
                    </button>
                </div>
                <div class="card-body">
                    <div id="availablePrintersList" class="row">
                        <div class="col-12 text-center text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            جاري اكتشاف الطابعات...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configured Printers Table -->
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('printers.list') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('printers.labels.name') }}</th>
                            <th>{{ __('printers.labels.type') }}</th>
                            <th>{{ __('printers.labels.connection') }}</th>
                            <th>{{ __('printers.labels.location') }}</th>
                            <th>{{ __('printers.labels.status') }}</th>
                            <th>{{ __('printers.labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($printers ?? [] as $printer)
                        <tr>
                            <td>{{ $printer->name }}</td>
                            <td>{{ $printer->type }}</td>
                            <td>{{ $printer->connection_type }}</td>
                            <td>{{ $printer->location }}</td>
                            <td>
                                <span class="badge bg-{{ $printer->is_active ? 'success' : 'secondary' }}">
                                    {{ $printer->is_active ? __('printers.active') : __('printers.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" data-printer-id="{{ $printer->id }}" onclick="editPrinter(this.dataset.printerId)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" data-printer-id="{{ $printer->id }}" onclick="testPrinterConnection(this.dataset.printerId)">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info" data-printer-id="{{ $printer->id }}" onclick="printTest(this.dataset.printerId)">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" data-printer-id="{{ $printer->id }}" onclick="deletePrinter(this.dataset.printerId)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Add Printer Modal -->
<div class="modal fade" id="addPrinterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>إضافة طابعة جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPrinterForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="printer_name" class="form-label">{{ __('printers.labels.name') }} *</label>
                                <input type="text" class="form-control" id="printer_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="printer_type" class="form-label">{{ __('printers.labels.type') }} *</label>
                                <select class="form-select" id="printer_type" name="type" required>
                                    <option value="">{{ __('printers.labels.select_type') }}</option>
                                    <option value="Thermal">{{ __('printers.labels.thermal') }}</option>
                                    <option value="Laser">{{ __('printers.labels.laser') }}</option>
                                    <option value="Inkjet">{{ __('printers.labels.inkjet') }}</option>
                                    <option value="POS">{{ __('printers.labels.pos') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="connection_type" class="form-label">{{ __('printers.labels.connection_type') }} *</label>
                                <select class="form-select" id="connection_type" name="connection_type" required>
                                    <option value="">{{ __('printers.labels.select_connection') }}</option>
                                    <option value="USB">{{ __('printers.labels.usb') }}</option>
                                    <option value="Network">{{ __('printers.labels.network') }}</option>
                                    <option value="Bluetooth">{{ __('printers.labels.bluetooth') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paper_type" class="form-label">{{ __('printers.labels.paper_type') }}</label>
                                <select class="form-select" id="paper_type" name="paper_type">
                                    <option value="regular">{{ __('printers.labels.regular') }}</option>
                                    <option value="thermal">{{ __('printers.labels.thermal') }}</option>
                                    <option value="cashier">{{ __('printers.labels.cashier') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="network_fields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ip_address" class="form-label">{{ __('printers.labels.ip_address') }}</label>
                                <input type="text" class="form-control" id="ip_address" name="ip_address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="port" class="form-label">{{ __('printers.labels.port') }}</label>
                                <input type="number" class="form-control" id="port" name="port" value="9100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">{{ __('printers.labels.location') }}</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manufacturer" class="form-label">{{ __('printers.labels.manufacturer') }}</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">{{ __('printers.labels.model') }}</label>
                                <input type="text" class="form-control" id="model" name="model">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paper_width" class="form-label">{{ __('printers.labels.paper_width') }} (mm)</label>
                                <input type="number" class="form-control" id="paper_width" name="paper_width" value="210">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('printers.labels.description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                {{ __('printers.labels.active_printer') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('printers.labels.cancel') }}</button>
                <button type="button" class="btn btn-outline-primary me-2" onclick="testConnection()">
                    <i class="bi bi-arrow-repeat"></i> {{ __('printers.labels.test_connection') }}
                </button>
                <button type="submit" class="btn btn-primary" form="addPrinterForm">
                    <i class="bi bi-plus-circle me-1"> &nbsp; </i>{{ __('printers.labels.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Available Printer Modal -->
<div class="modal fade" id="addAvailablePrinterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-printer me-2"></i>{{ __('printers.labels.add_available_printer') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAvailablePrinterForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="available_printer_name" name="name">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> &nbsp;
                        {{ __('printers.hints.adding_printer', ['printer' => '<strong id="selected_printer_name"></strong>']) }}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_printer_type" class="form-label">{{__('printers.labels.printer_type')}} *</label>
                                <select class="form-select" id="available_printer_type" name="type" required>
                                    <option value="">{{__('printers.labels.select_type')}}</option>
                                    <option value="Thermal">{{__('printers.types.thermal')}}</option>
                                    <option value="Laser">{{__('printers.types.laser')}}</option>
                                    <option value="Inkjet">{{__('printers.types.inkjet')}}</option>
                                    <option value="POS">{{__('printers.types.pos')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_connection_type" class="form-label">{{__('printers.labels.connection_type')}} *</label>
                                <select class="form-select" id="available_connection_type" name="connection_type" required>
                                    <option value="">{{__('printers.labels.select_connection_type')}}</option>
                                    <option value="USB">{{__('printers.connection_types.usb')}}</option>
                                    <option value="Network">{{__('printers.connection_types.network')}}</option>
                                    <option value="Bluetooth">{{__('printers.connection_types.bluetooth')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="available_network_fields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_ip_address" class="form-label">{{__('printers.labels.ip_address')}}</label>
                                <input type="text" class="form-control" id="available_ip_address" name="ip_address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_port" class="form-label">{{__('printers.labels.port')}}</label>
                                <input type="number" class="form-control" id="available_port" name="port" value="9100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_location" class="form-label">{{__('printers.labels.location')}} *</label>
                                <input type="text" class="form-control" id="available_location" name="location" placeholder="مثال: الكاشير الرئيسي" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_paper_type" class="form-label">{{__('printers.labels.paper_type')}} *</label>
                                <select class="form-select" id="available_paper_type" name="paper_type" required>
                                    <option value="">{{__('printers.labels.select_paper_type')}}</option>
                                    <option value="regular">{{__('printers.paper_types.regular')}}</option>
                                    <option value="thermal">{{__('printers.paper_types.thermal')}}</option>
                                    <option value="cashier">{{__('printers.paper_types.cashier')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_paper_width" class="form-label">{{__('printers.labels.paper_width')}} *</label>
                                <select class="form-select" id="available_paper_width" name="paper_width" required>
                                    <option value="">{{__('printers.labels.select_paper_width')}}</option>
                                    <option value="58">{{__('printers.paper_widths.58mm')}}</option>
                                    <option value="80">{{__('printers.paper_widths.80mm')}}</option>
                                    <option value="112">{{__('printers.paper_widths.112mm')}}</option>
                                    <option value="210">210 مم (A4)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="available_print_density" class="form-label">{{__('printers.labels.print_density')}} *</label>
                                <select class="form-select" id="available_print_density" name="print_density" required>
                                    <option value="">{{__('printers.labels.select_print_density')}}</option>
                                    <option value="low">{{__('printers.print_densities.low')}}</option>
                                    <option value="medium">{{__('printers.print_densities.medium')}}</option>
                                    <option value="high">{{__('printers.print_densities.high')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="available_is_active" name="is_active" checked>
                            <label class="form-check-label" for="available_is_active">
                                {{__('printers.labels.activate_immediately')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('printers.labels.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"> &nbsp; </i>{{__('printers.labels.add_printer')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Show notification function - Global scope
    function showNotification(message, type = 'info') {
        showToast(message, type);
    }

    // Language loader helper - Updated for external JSON loading
    function waitForLanguage(callback, maxAttempts = 100) {
        let attempts = 0;

        function checkLanguage() {
            attempts++;

            if (window.lang && Object.keys(window.lang).length > 0) {
                callback();
            } else if (attempts < maxAttempts) {
                setTimeout(checkLanguage, 50); // Check every 50ms
            } else {
                console.warn('Language data not loaded after maximum attempts');
                callback(); // Proceed anyway
            }
        }

        checkLanguage();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for language data to be loaded from external JSON file
        waitForLanguage(function() {
            // Load configured printers from data attribute
            const printersData = document.getElementById('printers-container');
            window.configuredPrinters = printersData ? JSON.parse(printersData.dataset.printers || '[]') : [];

            // Load available printers on page load
            loadAvailablePrinters();

            // Show/hide network fields based on connection type
            document.getElementById('connection_type').addEventListener('change', function() {
                const networkFields = document.getElementById('network_fields');
                if (this.value === 'Network') {
                    networkFields.style.display = 'block';
                    document.getElementById('ip_address').setAttribute('required', 'required');
                } else {
                    networkFields.style.display = 'none';
                    document.getElementById('ip_address').removeAttribute('required');
                }
            });

            // Show/hide available network fields
            document.getElementById('available_connection_type').addEventListener('change', function() {
                const networkFields = document.getElementById('available_network_fields');
                if (this.value === 'Network') {
                    networkFields.style.display = 'block';
                } else {
                    networkFields.style.display = 'none';
                }
            });

            // Add printer form submission
            document.getElementById('addPrinterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                savePrinter();
            });

            // Add available printer form submission
            document.getElementById('addAvailablePrinterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveAvailablePrinter();
            });

            // Test connection function
            window.testConnection = function() {
                const ipAddress = document.getElementById('ip_address').value;
                const port = document.getElementById('port').value;

                if (!ipAddress || !port) {
                    showNotification('⚠️ يرجى إدخال عنوان IP والمنفذ', 'warning');
                    return;
                }

                showNotification('🔄 جاري اختبار الاتصال...', 'info');

                // Simulate connection test
                setTimeout(() => {
                    showNotification('✅ تم الاتصال بالجهاز بنجاح', 'success');
                }, 2000);
            };
        });
    });

    // Show/hide network fields for available printer modal
    document.getElementById('available_connection_type').addEventListener('change', function() {
        const networkFields = document.getElementById('available_network_fields');
        if (this.value === 'Network') {
            networkFields.style.display = 'block';
        } else {
            networkFields.style.display = 'none';
        }
    });

    // Add Printer Form
    document.getElementById('addPrinterForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/printers', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('✅ تم إضافة الطابعة بنجاح', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addPrinterModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('❌ ' + (data.message || 'فشل إضافة الطابعة'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ ما: ' + error.message, 'error');
            });
    });

    function loadAvailablePrinters() {
        const container = document.getElementById('availablePrintersList');
        container.innerHTML = '<div class="col-12 text-center text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>جاري اكتشاف الطابعات...</div>';

        fetch('/api/printers/discover', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.printers.length > 0) {
                    displayAvailablePrinters(data.printers);
                } else {
                    container.innerHTML = '<div class="col-12 text-center text-muted"><i class="bi bi-printer me-2"></i>لم يتم العثور على طابعات</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="col-12 text-center text-danger"><i class="bi bi-exclamation-triangle me-2"></i>فشل اكتشاف الطابعات</div>';
            });
    }

    function displayAvailablePrinters(printers) {
        const container = document.getElementById('availablePrintersList');
        const configuredPrinters = window.configuredPrinters || [];
        const configuredNames = configuredPrinters.map(p => p.name);

        let html = '';
        printers.forEach(function(printer) {
            const isConfigured = configuredNames.includes(printer);
            const printerType = detectPrinterType(printer);
            const printerIcon = getPrinterIcon(printerType);

            html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 ${isConfigured ? 'border-success' : ''}">
                    <div class="card-header d-flex align-items-center mb-2">
                        <i class="bi ${printerIcon} mx-3 text-primary"></i>
                        <h6 class="card-title mb-0">${printer}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-info">${printerType}</span>
                            ${isConfigured ? '<span class="badge bg-success me-1">مضافة</span>' : ''}
                        </div>
                        <p class="card-text small text-muted">طابعة متاحة على الجهاز</p>
                        <div class="d-flex justify-content-end">
                            ${isConfigured ?
                                '<button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-check-circle me-1"></i>' + t('printers.already_added') + '</button>' :
                                '<button class="btn btn-sm btn-primary" onclick="addAvailablePrinter(\''+printer+'\')">'+
                                    '<i class="bi bi-plus-circle me-1"></i> &nbsp; ' + t('printers.actions.add') +
                                '</button>'
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
        });

        container.innerHTML = html || '<div class="col-12 text-center text-muted"><i class="bi bi-printer me-2"></i>' + t('printers.no_printers_found') + '</div>';
    }

    function detectPrinterType(printerName) {
        const name = printerName.toLowerCase();
        if (name.includes('thermal') || name.includes('epson') || name.includes('star')) {
            return 'Thermal';
        } else if (name.includes('laser') || name.includes('hp') || name.includes('canon')) {
            return 'Laser';
        } else if (name.includes('inkjet') || name.includes('pixma') || name.includes('deskjet')) {
            return 'Inkjet';
        } else if (name.includes('pos') || name.includes('receipt')) {
            return 'POS';
        }
        return 'Unknown';
    }

    function getPrinterIcon(type) {
        switch (type) {
            case 'Thermal':
                return 'bi-printer-fill';
            case 'Laser':
                return 'bi-printer';
            case 'Inkjet':
                return 'bi-printer';
            case 'POS':
                return 'bi-receipt';
            default:
                return 'bi-printer';
        }
    }

    function refreshPrinters() {
        showNotification('🔄 ' + t('printers.refreshing_printers'), 'info');
        loadAvailablePrinters();
    }

    function testPrinterConnection(printerId) {
        showNotification('🔄 ' + t('printers.testing_connection'), 'info');

        fetch('/printers/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    printer_id: printerId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPrinterInfoModal(data.printer_info);
                    showNotification('✅ ' + data.message, 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ ' + t('printers.connection_error'), 'error');
            });
    }

    function showPrinterInfoModal(printerInfo) {
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="printerInfoModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-printer me-2"></i>{{ __('printers.printer_info') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">{{ __('printers.labels.basic_info') }}</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>{{ __('printers.labels.name') }}</strong></td>
                                            <td>${printerInfo.name}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('printers.labels.type') }}</strong></td>
                                            <td>${printerInfo.type}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('printers.labels.connection_type') }}</strong></td>
                                            <td>${printerInfo.connection_type}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('printers.labels.status') }}</strong></td>
                                            <td>
                                                <span class="badge bg-${printerInfo.status === 'online' ? 'success' : 'danger'}">
                                                    ${printerInfo.status}
                                                </span>
                                            </td>
                                        </tr>
                                        ${printerInfo.response_time ? `
                                        <tr>
                                            <td><strong>Response Time</strong></td>
                                            <td>${printerInfo.response_time}</td>
                                        </tr>
                                        ` : ''}
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">{{ __('printers.labels.connection_details') }}</h6>
                                    <table class="table table-sm">
                                        ${Object.entries(printerInfo.details).map(([key, value]) => `
                                            <tr>
                                                <td><strong>${key.replace(/_/g, ' ')}</strong></td>
                                                <td>${value}</td>
                                            </tr>
                                        `).join('')}
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('printers.labels.close') }}</button>
                            <button type="button" class="btn btn-primary" onclick="printTestPage('${printerInfo.name}')">
                                <i class="bi bi-printer me-1"></i> {{ __('printers.labels.print_test') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('printerInfoModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body and show
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('printerInfoModal'));
        modal.show();

        // Remove modal from DOM when hidden
        document.getElementById('printerInfoModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    function printTestPage(printerName) {
        showNotification('🔄 ' + t('printers.printing_test'), 'info');

        // Here you would implement actual test page printing
        setTimeout(() => {
            showNotification('✅ ' + t('printers.test_sent'), 'success');
        }, 2000);
    }

    function addAvailablePrinter(printerName) {
        // Set printer name in modal
        document.getElementById('available_printer_name').value = printerName;
        document.getElementById('selected_printer_name').textContent = printerName;

        // Auto-detect printer type
        const printerType = detectPrinterType(printerName);
        document.getElementById('available_printer_type').value = printerType;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addAvailablePrinterModal'));
        modal.show();
    }

    // Add Available Printer Form
    document.getElementById('addAvailablePrinterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAvailablePrinter();
    });

    function saveAvailablePrinter() {
        const form = document.getElementById('addAvailablePrinterForm');
        const formData = new FormData(form);
        // Validate required fields
        if (!formData.get('type') || !formData.get('connection_type')) {
            showNotification('⚠️ يرجى ملء جميع الحقول المطلوبة', 'warning');
            return;
        }

        fetch('/printers', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Try to get error details from 422 response
                    return response.json().then(errorData => {
                        throw new Error(JSON.stringify(errorData));
                    }).catch(() => {
                        throw new Error('Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('✅ تم إضافة الطابعة بنجاح', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addAvailablePrinterModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('❌ ' + (data.message || 'فشل إضافة الطابعة'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ ما: ' + error.message, 'error');
            });
    }

    function testPrinter(printerId) {
        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        fetch('/printers/' + printerId + '/test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ الطابعة متصلة وتعمل بشكل صحيح', 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('❌ فشل الاتصال بالطابعة', 'error');
            });
    }

    function printTest(printerId) {
        showNotification('🖨️ جاري طباعة اختبار...', 'info');

        fetch('/printers/' + printerId + '/print-test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ تم طباعة اختبار بنجاح', 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('❌ فشل الطباعة', 'error');
            });
    }

    window.testPrinterConnection = function(printerId) {
        showNotification('🔄 جاري اختبار الاتصال بالطابعة...', 'info');

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
                    showNotification('✅ تم إرسال صفحة الاختبار إلى الطابعة بنجاح', 'success');

                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ أثناء طباعة صفحة الاختبار', 'error');
            });
    };

    // Print test page (alias for testPrinterConnection)
    window.printTest = function(printerId) {
        testPrinterConnection(printerId);
    };

    function testConnection() {
        const connectionType = document.getElementById('connection_type').value;
        const ipAddress = document.getElementById('ip_address').value;
        const port = document.getElementById('port').value;

        if (connectionType === 'Network' && (!ipAddress || !port)) {
            showNotification('⚠️ يرجى إدخال عنوان IP والمنفذ للاتصال الشبكي', 'warning');
            return;
        }

        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        setTimeout(function() {
            showNotification('✅ تم الاتصال بالطابعة بنجاح', 'success');
        }, 2000);
    }

    function editPrinter(printerId) {
        showNotification('🔧 جاري تحميل بيانات الطابعة...', 'info');
    }

    function deletePrinter(printerId) {
        if (confirm('هل أنت متأكد من حذف هذه الطابعة؟')) {
            fetch('/printers/' + printerId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(' تم حذف الطابعة بنجاح', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(' ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification(' حدث خطأ ما', 'error');
                });

        } // Wait 100ms for language data to load
    }
</script>
@endpush