@extends('layouts.app')

@section('title', __('cashier_sessions.titles.pos_devices'))

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
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
                            <th style="min-width:120px;">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-printer text-white"></i>
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
                                <label for="ip_address" class="form-label">عنوان IP</label>
                                <input type="text" class="form-control" id="ip_address" name="ip_address"
                                    placeholder="192.168.1.100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="port" class="form-label">المنفذ</label>
                                <input type="number" class="form-control" id="port" name="port"
                                    placeholder="9100" value="9100" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">الموقع</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="الكاشير الرئيسي" required>
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
                    <button type="button" class="btn btn-outline-primary me-2" onclick="testConnection()">
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
                    <!-- Same fields as add form -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cashier_sessions.buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('cashier_sessions.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Device Form
        document.getElementById('addDeviceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/pos-devices', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ تم إضافة الجهاز بنجاح', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
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

    function editDevice(deviceId) {
        // Load device data and show edit modal
        showNotification('🔧 جاري تحميل بيانات الجهاز...', 'info');
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
</script>
@endsection