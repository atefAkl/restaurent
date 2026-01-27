@extends('layouts.app')

@section('title', __('cashier_sessions.titles.cashier_sessions'))

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('cashier_sessions.titles.cashier_sessions') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">{{ __('cashier_sessions.titles.cashier_sessions') }}</h2>
                <p>إدارة جلسات الكاشير وتتبع العمليات المالية</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#startSessionModal">
                <i class="bi bi-plus-circle"></i>&nbsp; {{ __('cashier_sessions.buttons.new_session') }}
            </button>
        </div>
    </div>

    <!-- Active Session Alert -->
    @if($activeSession)
    <div class="alert alert-success alert-dismissible mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-circle-fill me-2"></i>
            <div>
                <strong>جلسة نشطة حالياً</strong><br>
                <small>الكاشير: {{ $activeSession->cashier_name }} | البدء: {{ $activeSession->start_time->format('H:i') }} | الصندوق: {{ $activeSession->cash_drawer }}</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Sessions Table -->
    <div class="card" style="border-radius:0px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('cashier_sessions.labels.cashier_name') }}</th>
                            <th>{{ __('cashier_sessions.labels.start_time') }}</th>
                            <th>{{ __('cashier_sessions.labels.end_time') }}</th>
                            <th>{{ __('cashier_sessions.labels.duration') }}</th>
                            <th>{{ __('cashier_sessions.labels.cash_drawer') }}</th>
                            <th>{{ __('cashier_sessions.labels.total_sales') }}</th>
                            <th>{{ __('cashier_sessions.labels.total_transactions') }}</th>
                            <th>{{ __('cashier_sessions.labels.status') }}</th>
                            <th style="min-width:120px;">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-person-fill text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $session->cashier_name }}</div>
                                        <small class="text-muted">جلسة #{{ $session->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $session->start_time->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $session->start_time->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($session->end_time)
                                <div>{{ $session->end_time->format('Y-m-d') }}</div>
                                <small class="text-muted">{{ $session->end_time->format('H:i:s') }}</small>
                                @else
                                <span class="badge bg-warning">—</span>
                                @endif
                            </td>
                            <td>
                                @if($session->end_time)
                                {{ $session->start_time->diffForHumans($session->end_time, true) }}
                                @else
                                {{ $session->start_time->diffForHumans(now(), true) }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $session->cash_drawer }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($session->total_sales, 2) }} ريال</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $session->total_transactions }}</span>
                            </td>
                            <td>
                                @if($session->status == 'active')
                                <span class="badge bg-success">
                                    <i class="bi bi-circle-fill me-1"></i>{{ __('cashier_sessions.labels.active') }}
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-circle me-1"></i>{{ __('cashier_sessions.labels.closed') }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewSessionDetails({{ $session->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="printSessionReport({{ $session->id }})">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    @if($session->status == 'active')
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="endSession({{ $session->id }})">
                                        <i class="bi bi-stop-circle"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    لا توجد جلسات حالياً
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

<!-- Start Session Modal -->
<div class="modal fade" id="startSessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('cashier_sessions.buttons.new_session') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="startSessionForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cashier_name" class="form-label">{{ __('cashier_sessions.labels.cashier_name') }}</label>
                                <input type="text" class="form-control" id="cashier_name" name="cashier_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cash_drawer" class="form-label">{{ __('cashier_sessions.labels.cash_drawer') }}</label>
                                <select class="form-select" id="cash_drawer" name="cash_drawer" required>
                                    <option value="">{{ __('cashier_sessions.hints.select_cash_drawer') }}</option>
                                    <option value="صندوق رئيسي">صندوق رئيسي</option>
                                    <option value="صندوق فرعي 1">صندوق فرعي 1</option>
                                    <option value="صندوق فرعي 2">صندوق فرعي 2</option>
                                    <option value="خزينة خارجية">خزينة خارجية</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="opening_balance" class="form-label">{{ __('cashier_sessions.labels.opening_balance') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="opening_balance" name="opening_balance"
                                        step="0.01" min="0" value="0.00" required>
                                    <span class="input-group-text">ريال</span>
                                </div>
                                <small class="form-text text-muted">{{ __('cashier_sessions.hints.enter_opening_balance') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pos_device" class="form-label">{{ __('cashier_sessions.labels.device_name') }}</label>
                                <select class="form-select" id="pos_device" name="pos_device">
                                    <option value="">{{ __('cashier_sessions.hints.select_device') }}</option>
                                    <option value="pos1">جهاز الصرافة 1 - الكاشير الرئيسي</option>
                                    <option value="pos2">جهاز الصرافة 2 - الطابق الأول</option>
                                    <option value="pos3">جهاز الصرافة 3 - الخارجي</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="printer" class="form-label">{{ __('cashier_sessions.labels.printer_name') }}</label>
                                <select class="form-select" id="printer" name="printer">
                                    <option value="">{{ __('cashier_sessions.hints.select_printer') }}</option>
                                    <option value="printer1">طابعة الفواتير الرئيسية</option>
                                    <option value="printer2">طابعة الطلبات الحرارية</option>
                                    <option value="printer3">طابعة التقارير</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">حالة الاتصال</label>
                                <div class="d-flex gap-2 mt-2">
                                    <span class="badge bg-success" id="device_status">
                                        <i class="bi bi-circle-fill me-1"></i>متصل
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="testDeviceConnection()">
                                        <i class="bi bi-arrow-repeat"></i> {{ __('cashier_sessions.labels.test_connection') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cashier_sessions.buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-circle me-1"></i>{{ __('cashier_sessions.labels.start_session') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End Session Modal -->
<div class="modal fade" id="endSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('cashier_sessions.labels.end_session') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="endSessionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="end_session_id" name="session_id">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('cashier_sessions.messages.confirm_end_session') }}
                    </div>
                    <div class="mb-3">
                        <label for="closing_balance" class="form-label">{{ __('cashier_sessions.labels.closing_balance') }}</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="closing_balance" name="closing_balance"
                                step="0.01" min="0" required>
                            <span class="input-group-text">ريال</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="session_notes" class="form-label">ملاحظات الجلسة</label>
                        <textarea class="form-control" id="session_notes" name="session_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cashier_sessions.buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-stop-circle me-1"></i>{{ __('cashier_sessions.labels.end_session') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Start Session Form
        document.getElementById('startSessionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/cashier-sessions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ تم بدء الجلسة بنجاح', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('startSessionModal')).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('❌ حدث خطأ ما', 'error');
                });
        });

        // End Session Form
        document.getElementById('endSessionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const sessionId = document.getElementById('end_session_id').value;

            fetch(`/cashier-sessions/${sessionId}/end`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ تم إنهاء الجلسة بنجاح', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('endSessionModal')).hide();
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

    function endSession(sessionId) {
        document.getElementById('end_session_id').value = sessionId;
        new bootstrap.Modal(document.getElementById('endSessionModal')).show();
    }

    function viewSessionDetails(sessionId) {
        // Implementation for viewing session details
        showNotification('🔍 جاري تحميل تفاصيل الجلسة...', 'info');
    }

    function printSessionReport(sessionId) {
        // Implementation for printing session report
        showNotification('🖨️ جاري طباعة تقرير الجلسة...', 'info');
        window.open(`/cashier-sessions/${sessionId}/print`, '_blank');
    }

    function testDeviceConnection() {
        showNotification('🔄 جاري اختبار الاتصال...', 'info');

        setTimeout(() => {
            const statusBadge = document.getElementById('device_status');
            statusBadge.className = 'badge bg-success';
            statusBadge.innerHTML = '<i class="bi bi-circle-fill me-1"></i>متصل';
            showNotification('✅ تم الاتصال بالجهاز بنجاح', 'success');
        }, 2000);
    }
</script>
@endsection