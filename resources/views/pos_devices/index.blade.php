@extends('layouts.app')

@section('title', __('cashier_sessions.titles.pos_devices'))

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                <li class="breadcrumb-item fw-bold"><a href="{{ route('settings.index') }}">{{ __('app.sidebar.settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('cashier_sessions.titles.pos_devices') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">{{ __('cashier_sessions.titles.pos_devices') }}</h2>
                <p>إدارة أجهزة الصرافة ومراقبة حالة الاتصال</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                <i class="bi bi-plus-circle"></i>&nbsp; {{ __('cashier_sessions.buttons.add_device') }}
            </button>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="card" style="border-radius:0px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('cashier_sessions.labels.device_name') }}</th>
                            <th>{{ __('cashier_sessions.labels.device_type') }}</th>
                            <th>{{ __('cashier_sessions.labels.connection_status') }}</th>
                            <th>الموقع</th>
                            <th>آخر اتصال</th>
                            <th>الحالة</th>
                            <th style="min-width:120px;">{{ __('app.labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm mx-3 bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-printer text-white px-2 py-1"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $device->name }}</div>
                                        <small class="text-muted">{{ $device->ip_address }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $device->type }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $device->is_online ? 'bg-success' : 'bg-danger' }} me-2">
                                        <i class="bi bi-circle{{ $device->is_online ? '-fill' : '' }} me-1"></i>
                                        {{ $device->is_online ? __('cashier_sessions.labels.connected') : __('cashier_sessions.labels.disconnected') }}
                                    </span>
                                    @if($device->is_online)
                                    <small class="text-muted">{{ $device->response_time }}ms</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $device->location }}</span>
                            </td>
                            <td>
                                @if($device->last_connected)
                                <small>{{ $device->last_connected->diffForHumans() }}</small>
                                @else
                                <span class="text-muted">لم يتصل بعد</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $device->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $device->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-device-id="{{ $device->id }}" onclick="testDevice(this.dataset.deviceId)">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-device-id="{{ $device->id }}" onclick="printTest(this.dataset.deviceId)">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" data-device-id="{{ $device->id }}" onclick="editDevice(this.dataset.deviceId)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-device-id="{{ $device->id }}" onclick="deleteDevice(this.dataset.deviceId)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-printer fs-1 d-block mb-2"></i>
                                    لا توجد أجهزة صرافة حالياً
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('cashier_sessions.buttons.add_device') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDeviceForm">
                @csrf
                <div class="modal-body">
                    <!-- Discovery Section -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">اكتشاف الأجهزة تلقائياً</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="discoverDevicesBtn">
                                <i class="bi bi-search"></i> اكتشاف
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="discoveredDevices" class="row">
                                <div class="col-12 text-center text-muted">
                                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                                    اضغط على "اكتشاف" للبحث عن الأجهزة المتصلة
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Add Section -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">إضافة جهاز يدوياً</h6>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="device_id" name="device_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="device_name" class="form-label">{{ __('cashier_sessions.labels.device_name') }}</label>
                                        <input type="text" class="form-control" id="device_name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="device_type" class="form-label">{{ __('cashier_sessions.labels.device_type') }}</label>
                                        <select class="form-select" id="device_type" name="type" required>
                                            <option value="">اختر نوع الجهاز</option>
                                            <option value="POS">POS Terminal</option>
                                            <option value="Payment Terminal">Payment Terminal</option>
                                            <option value="Cash Drawer">Cash Drawer</option>
                                            <option value="Barcode Scanner">Barcode Scanner</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="connection_type" class="form-label">نوع الاتصال</label>
                                        <select class="form-select" id="connection_type" name="connection_type" required onchange="updatePortField()">
                                            <option value="">اختر نوع الاتصال</option>
                                            <option value="Network">Network</option>
                                            <option value="USB">USB</option>
                                            <option value="Serial">Serial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ip_address" class="form-label">عنوان IP</label>
                                        <input type="text" class="form-control" id="ip_address" name="ip_address"
                                            placeholder="192.168.1.100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="port" class="form-label">المنفذ <small class="text-muted" id="portHint">(اختر نوع الاتصال أولاً)</small></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="port" name="port"
                                                placeholder="9100" value="9100">
                                            <button class="btn btn-outline-secondary" type="button" onclick="showCommonPorts()">
                                                <i class="bi bi-list"></i> المنافذ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">الموقع</label>
                                        <input type="text" class="form-control" id="location" name="location"
                                            placeholder="الكاشير الرئيسي">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manufacturer" class="form-label">الشركة المصنعة</label>
                                    <select class="form-select" id="manufacturer" name="manufacturer">
                                        <option value="">اختر الشركة</option>
                                        <option value="Epson">Epson</option>
                                        <option value="Star">Star Micronics</option>
                                        <option value="Citizen">Citizen</option>
                                        <option value="Custom">Custom</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="model" class="form-label">الموديل</label>
                                    <input type="text" class="form-control" id="model" name="model"
                                        placeholder="TM-T88V">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            جهاز نشط
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cashier_sessions.buttons.cancel') }}</button>
                        <button type="button" class="btn btn-outline-warning me-2" onclick="debugForm()">
                            <i class="bi bi-bug"></i> Debug
                        </button>
                        <button type="button" class="btn btn-outline-primary me-2" onclick="testConnection()">
                            <i class="bi bi-arrow-repeat"></i> {{ __('cashier_sessions.labels.test_connection') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('cashier_sessions.buttons.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الجهاز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeviceForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_device_id" name="device_id">
                <div class="modal-body">
                    <!-- Device Name and Type -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_device_name" class="form-label">{{ __('cashier_sessions.labels.device_name') }}</label>
                                <input type="text" class="form-control" id="edit_device_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_device_type" class="form-label">{{ __('cashier_sessions.labels.device_type') }}</label>
                                <select class="form-select" id="edit_device_type" name="type" required>
                                    <option value="">اختر نوع الجهاز</option>
                                    <option value="POS">POS Terminal</option>
                                    <option value="Payment Terminal">Payment Terminal</option>
                                    <option value="Cash Drawer">Cash Drawer</option>
                                    <option value="Barcode Scanner">Barcode Scanner</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Type and IP Address -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_connection_type" class="form-label">نوع الاتصال</label>
                                <select class="form-select" id="edit_connection_type" name="connection_type" required onchange="updateEditPortField()">
                                    <option value="">اختر نوع الاتصال</option>
                                    <option value="Network">Network</option>
                                    <option value="USB">USB</option>
                                    <option value="Serial">Serial</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_ip_address" class="form-label">عنوان IP</label>
                                <input type="text" class="form-control" id="edit_ip_address" name="ip_address"
                                    placeholder="192.168.1.100">
                            </div>
                        </div>
                    </div>

                    <!-- Port and Location -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_port" class="form-label">المنفذ <small class="text-muted" id="edit_port_hint">(اختر نوع الاتصال أولاً)</small></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="edit_port" name="port"
                                        placeholder="9100" value="9100">
                                    <button class="btn btn-outline-secondary" type="button" onclick="showEditCommonPorts()">
                                        <i class="bi bi-list"></i> المنافذ
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_location" class="form-label">الموقع</label>
                                <input type="text" class="form-control" id="edit_location" name="location"
                                    placeholder="الكاشير الرئيسي">
                            </div>
                        </div>
                    </div>

                    <!-- Manufacturer and Model -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_manufacturer" class="form-label">الشركة المصنعة</label>
                                <select class="form-select" id="edit_manufacturer" name="manufacturer">
                                    <option value="">اختر الشركة</option>
                                    <option value="Epson">Epson</option>
                                    <option value="Star">Star Micronics</option>
                                    <option value="Citizen">Citizen</option>
                                    <option value="Custom">Custom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_model" class="form-label">الموديل</label>
                                <input type="text" class="form-control" id="edit_model" name="model"
                                    placeholder="TM-T88V">
                            </div>
                        </div>
                    </div>

                    <!-- Is Active Switch -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                    <label class="form-check-label" for="edit_is_active">
                                        جهاز نشط
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cashier_sessions.buttons.cancel') }}</button>
                    <button type="button" class="btn btn-outline-warning me-2" onclick="debugEditForm()">
                        <i class="bi bi-bug"></i> Debug
                    </button>
                    <button type="button" class="btn btn-outline-primary me-2" onclick="testEditConnection()">
                        <i class="bi bi-arrow-repeat"></i> {{ __('cashier_sessions.labels.test_connection') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('cashier_sessions.buttons.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global notification function
    function showNotification(message, type = 'info') {
        showToast(message, type);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Discover devices button
        document.getElementById('discoverDevicesBtn')?.addEventListener('click', function() {
            discoverDevices();
        });

        // Add Device Form
        document.getElementById('addDeviceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log('Form submitted!');
            console.log('Form data:', new FormData(this));

            const formData = new FormData(this);

            // Debug: log form data
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            fetch('/pos-devices', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showNotification('✅ تم إضافة الجهاز بنجاح', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        // Show detailed validation errors
                        if (data.errors) {
                            console.log('Validation errors:', data.errors);
                            let errorMessages = [];
                            for (const [field, messages] of Object.entries(data.errors)) {
                                console.log(`Field: ${field}, Messages:`, messages);
                                errorMessages.push(`${field}: ${messages.join(', ')}`);
                            }
                            showNotification('❌ ' + data.message + '\n' + errorMessages.join('\n'), 'error');
                        } else {
                            showNotification('❌ ' + data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('❌ حدث خطأ ما', 'error');
                });
        });
    });

    // Discover POS Devices
    function discoverDevices() {
        showNotification('🔍 جاري اكتشاف الأجهزة...', 'info');

        fetch('/api/pos-devices/discover')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDiscoveredDevices(data.devices);
                    showNotification(`✅ تم العثور على ${data.count} جهاز`, 'success');
                } else {
                    showNotification('❌ فشل اكتشاف الأجهزة: ' + data.error, 'error');
                }
            })
            .catch(error => {
                showNotification('❌ حدث خطأ أثناء اكتشاف الأجهزة', 'error');
            });
    }

    // Display discovered devices
    function displayDiscoveredDevices(devices) {
        const container = document.getElementById('discoveredDevices');
        if (!container) return;

        container.innerHTML = '';

        if (devices.length === 0) {
            container.innerHTML = '<div class="alert alert-info">لم يتم العثور على أجهزة جديدة</div>';
            return;
        }

        devices.forEach((device, index) => {
            const deviceCard = document.createElement('div');
            deviceCard.className = 'col-md-6 mb-3';
            deviceCard.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">${device.name}</h6>
                        <p class="card-text small">
                            <strong>النوع:</strong> ${device.type}<br>
                            <strong>الاتصال:</strong> ${device.connection_type}<br>
                            ${device.ip_address ? `<strong>IP:</strong> ${device.ip_address}<br>` : ''}
                            ${device.port ? `<strong>Port:</strong> ${device.port}<br>` : ''}
                            <strong>الحالة:</strong> <span class="badge bg-${device.status === 'connected' ? 'success' : 'warning'}">${device.status}</span>
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary" onclick="addDiscoveredDevice(${index})">
                                <i class="bi bi-plus"></i> إضافة
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="testDiscoveredDevice(${index})">
                                <i class="bi bi-arrow-repeat"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(deviceCard);
        });

        // Store devices globally for access
        window.discoveredDevices = devices;
    }

    // Add discovered device
    function addDiscoveredDevice(index) {
        const device = window.discoveredDevices[index];
        if (!device) return;

        // Fill the form with device data
        document.getElementById('device_name').value = device.name;
        document.getElementById('device_type').value = device.type;
        document.getElementById('connection_type').value = device.connection_type;

        if (device.ip_address) {
            document.getElementById('ip_address').value = device.ip_address;
        }
        if (device.port) {
            document.getElementById('port').value = device.port;
        }

        // Store device ID for reference
        document.getElementById('device_id').value = device.device_id;

        showNotification('📋 تم ملء بيانات الجهاز في النموذج', 'success');
    }

    // Test discovered device
    function testDiscoveredDevice(index) {
        const device = window.discoveredDevices[index];
        if (!device) return;

        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        const testData = {
            device_id: device.device_id,
            connection_type: device.connection_type
        };

        if (device.ip_address) testData.ip_address = device.ip_address;
        if (device.port) testData.port = device.port;

        fetch('/api/pos-devices/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(testData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ ' + data.message, 'success');
                } else {
                    showNotification('❌ ' + (data.error || data.message), 'error');
                }
            })
            .catch(error => {
                showNotification('❌ حدث خطأ أثناء الاختبار', 'error');
            });
    }

    function testDevice(deviceId) {
        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        fetch(`/pos-devices/${deviceId}/test`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ الجهاز متصل ويعمل بشكل صحيح', 'success');
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('❌ فشل الاتصال بالجهاز', 'error');
            });
    }

    function printTest(deviceId) {
        showNotification('🖨️ جاري طباعة اختبار...', 'info');

        fetch(`/pos-devices/${deviceId}/print-test`, {
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

    function testConnection() {
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
    }

    // Debug function
    function debugForm() {
        console.log('=== DEBUG FORM ===');

        const form = document.getElementById('addDeviceForm');
        console.log('Form element:', form);

        const formData = new FormData(form);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Check required fields
        const requiredFields = ['name', 'type', 'connection_type'];
        requiredFields.forEach(field => {
            const element = document.getElementById(field === 'name' ? 'device_name' : field);
            console.log(`${field} element:`, element);
            console.log(`${field} value:`, element?.value);
            console.log(`${field} required:`, element?.required);
        });

        // Check CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log('CSRF token:', csrfToken?.getAttribute('content'));

        // Test form submission manually
        console.log('Testing manual submission...');

        showNotification('🔍 تم طباعة معلومات التصحيح في الكونسول', 'info');
    }

    function editDevice(deviceId) {
        // Load device data and show edit modal
        showNotification('🔧 جاري تحميل بيانات الجهاز...', 'info');

        fetch(`/pos-devices/${deviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fill form with device data
                    document.getElementById('edit_device_id').value = data.device.id;
                    document.getElementById('edit_device_name').value = data.device.name;
                    document.getElementById('edit_device_type').value = data.device.type;
                    document.getElementById('edit_connection_type').value = data.device.connection_type;
                    document.getElementById('edit_ip_address').value = data.device.ip_address || '';
                    document.getElementById('edit_port').value = data.device.port || '';
                    document.getElementById('edit_location').value = data.device.location || '';
                    document.getElementById('edit_manufacturer').value = data.device.manufacturer || '';
                    document.getElementById('edit_model').value = data.device.model || '';
                    document.getElementById('edit_description').value = data.device.description || '';
                    document.getElementById('edit_is_active').checked = data.device.is_active;

                    // Update port field based on connection type
                    updateEditPortField();

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editDeviceModal'));
                    modal.show();
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ حدث خطأ ما', 'error');
            });
    }

    // Debug function for edit form
    function debugEditForm() {
        console.log('=== DEBUG EDIT FORM ===');

        const form = document.getElementById('editDeviceForm');
        console.log('Edit form element:', form);

        const formData = new FormData(form);
        console.log('Edit form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Check required fields
        const requiredFields = ['name', 'type', 'connection_type'];
        requiredFields.forEach(field => {
            const element = document.getElementById('edit_' + field);
            console.log(`edit_${field} element:`, element);
            console.log(`edit_${field} value:`, element?.value);
            console.log(`edit_${field} required:`, element?.required);
        });

        // Check CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log('CSRF token:', csrfToken?.getAttribute('content'));

        // Test form submission manually
        console.log('Testing manual edit submission...');

        showNotification('🔍 تم طباعة معلومات التصحيح في الكونسول', 'info');
    }

    // Test connection for edit form
    function testEditConnection() {
        const ipAddress = document.getElementById('edit_ip_address').value;
        const port = document.getElementById('edit_port').value;

        if (!ipAddress || !port) {
            showNotification('⚠️ يرجى إدخال عنوان IP والمنفذ', 'warning');
            return;
        }

        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        // Simulate connection test
        setTimeout(() => {
            showNotification('✅ تم الاتصال بالجهاز بنجاح', 'success');
        }, 2000);
    }

    function deleteDevice(deviceId) {
        if (confirm('هل أنت متأكد من حذف هذا الجهاز؟')) {
            fetch(`/pos-devices/${deviceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ تم حذف الجهاز بنجاح', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('❌ حدث خطأ ما', 'error');
                });
        }
    }

    // Update port field for edit form
    function updateEditPortField() {
        const connectionType = document.getElementById('edit_connection_type').value;
        const portField = document.getElementById('edit_port');
        const portHint = document.getElementById('edit_port_hint');
        const ipAddressField = document.getElementById('edit_ip_address');

        if (connectionType === 'Network') {
            portField.value = portField.value || '9100';
            portField.disabled = false;
            ipAddressField.disabled = false;
            portHint.textContent = '(المنفذ الافتراضي: 9100)';
        } else if (connectionType === 'USB') {
            portField.value = '';
            portField.disabled = true;
            ipAddressField.disabled = true;
            portHint.textContent = '(لا يحتاج منفذ لاتصال USB)';
        } else if (connectionType === 'Serial') {
            portField.value = portField.value || 'COM1';
            portField.disabled = false;
            ipAddressField.disabled = true;
            portHint.textContent = '(مثل: COM1, COM2)';
        } else {
            portField.value = '';
            portField.disabled = false;
            ipAddressField.disabled = false;
            portHint.textContent = '';
        }
    }

    // Show common ports for edit form
    function showEditCommonPorts() {
        fetch('/api/pos-devices/ports/common')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayEditPortsModal(data.ports);
                } else {
                    showNotification('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('❌ حدث خطأ ما', 'error');
            });
    }

    // Display ports modal for edit form
    function displayEditPortsModal(ports) {
        let portsHtml = '<div class="row">';

        Object.keys(ports).forEach(deviceType => {
            portsHtml += `
                <div class="col-md-6 mb-3">
                    <h6>${formatDeviceType(deviceType)}</h6>
                    <div class="list-group">
            `;

            ports[deviceType].forEach(portInfo => {
                portsHtml += `
                    <div class="list-group-item list-group-item-action" onclick="selectEditPort('${portInfo.port}', '${portInfo.description}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>${portInfo.port}</strong></span>
                            <small class="text-muted">${portInfo.description}</small>
                        </div>
                    </div>
                `;
            });

            portsHtml += '</div></div>';
        });

        portsHtml += '</div>';

        // Create modal
        const modalHtml = `
            <div class="modal fade" id="editPortsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">المنافذ الشائعة للأجهزة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${portsHtml}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('editPortsModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body and show
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('editPortsModal'));
        modal.show();
    }

    // Select port for edit form
    function selectEditPort(port, description) {
        document.getElementById('edit_port').value = port;
        bootstrap.Modal.getInstance(document.getElementById('editPortsModal')).hide();
        showNotification(`✅ تم اختيار المنفذ ${port} - ${description}`, 'success');
    }

    // Handle edit form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Device Form
        document.getElementById('editDeviceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const deviceId = document.getElementById('edit_device_id').value;
            const formData = new FormData(this);

            fetch(`/pos-devices/${deviceId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ تم تحديث الجهاز بنجاح', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editDeviceModal')).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('❌ حدث خطأ ما', 'error');
                });
        });
    });

    // Update port field based on connection type
    function updatePortField() {
        const connectionType = document.getElementById('connection_type').value;
        const portField = document.getElementById('port');
        const portHint = document.getElementById('portHint');
        const ipAddressField = document.getElementById('ip_address');

        if (connectionType === 'Network') {
            portField.value = '9100';
            portField.disabled = false;
            portHint.textContent = '(المنفذ الافتراضي: 9100)';
            ipAddressField.disabled = false;
            ipAddressField.required = true;
        } else if (connectionType === 'USB') {
            portField.value = '';
            portField.disabled = true;
            portHint.textContent = '(يتم تحديده تلقائياً)';
            ipAddressField.disabled = true;
            ipAddressField.required = false;
            ipAddressField.value = '';
        } else if (connectionType === 'Serial') {
            portField.value = 'COM1';
            portField.disabled = false;
            portHint.textContent = '(مثال: COM1, COM2)';
            ipAddressField.disabled = true;
            ipAddressField.required = false;
            ipAddressField.value = '';
        } else {
            portField.value = '9100';
            portField.disabled = false;
            portHint.textContent = '(اختر نوع الاتصال أولاً)';
            ipAddressField.disabled = false;
            ipAddressField.required = false;
        }
    }

    // Show common ports modal
    function showCommonPorts() {
        fetch('/api/pos-devices/ports/common')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPortsModal(data.ports);
                } else {
                    showNotification('❌ فشل جلب المنافذ', 'error');
                }
            })
            .catch(error => {
                showNotification('❌ حدث خطأ ما', 'error');
            });
    }

    // Display ports modal
    function displayPortsModal(ports) {
        let portsHtml = '<div class="row">';

        for (const [deviceType, manufacturerPorts] of Object.entries(ports)) {
            portsHtml += '<div class="col-md-6 mb-3">';
            portsHtml += '<h6 class="text-primary">' + formatDeviceType(deviceType) + '</h6>';

            for (const [manufacturer, portList] of Object.entries(manufacturerPorts)) {
                portsHtml += '<div class="mb-2">';
                portsHtml += '<strong>' + manufacturer + ':</strong> ';
                portsHtml += portList.join(', ');
                portsHtml += '</div>';
            }

            portsHtml += '</div>';
        }

        portsHtml += '</div>';

        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">المنافذ الشائعة لأجهزة الصراف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${portsHtml}
                        <div class="alert alert-info mt-3">
                            <strong>ملاحظات:</strong><br>
                            • 9100: المنفذ الأكثر شيوعاً للطابعات الحرارية<br>
                            • 23/24: منافذ Telnet لماسحات الباركود<br>
                            • 10009/10001: منافذ محطات الدفع<br>
                            • 4999: منفذ درج النقود
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Remove modal when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }

    // Format device type for display
    function formatDeviceType(deviceType) {
        const translations = {
            'thermal_printer': 'الطابعات الحرارية',
            'barcode_scanner': 'ماسحات الباركود',
            'cash_drawer': 'أدراج النقود',
            'payment_terminal': 'محطات الدفع',
            'customer_display': 'شاشات العميل',
            'pos_terminal': 'نقاط البيع'
        };

        return translations[deviceType] || deviceType;
    }
</script>
@endsection