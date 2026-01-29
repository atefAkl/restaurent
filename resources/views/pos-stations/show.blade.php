@extends('layouts.app')
@section('title', 'عرض تفاصيل نقطة البيع')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pos-stations.index') }}">نقاط البيع</a></li>
                <li class="breadcrumb-item active">تفاصيل نقطة البيع</li>
            </ol>
        </nav>
    </div>

    <!-- Details Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">تفاصيل نقطة البيع: {{ $posStation->name }}</h4>
                <div>
                    <a href="{{ route('pos-stations.edit', $posStation) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i>&nbsp; تعديل
                    </a>
                    <a href="{{ route('pos-stations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right"></i>&nbsp; عودة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>الكود:</strong></td>
                            <td><span class="badge bg-secondary">{{ $posStation->code }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>الاسم:</strong></td>
                            <td>{{ $posStation->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>الموقع:</strong></td>
                            <td>{{ $posStation->location }}</td>
                        </tr>
                        <tr>
                            <td><strong>الحالة:</strong></td>
                            <td>
                                @if($posStation->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>الطابعة:</strong></td>
                            <td>{{ $posStation->printer?->name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $posStation->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>آخر تحديث:</strong></td>
                            <td>{{ $posStation->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($posStation->notes)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>ملاحظات:</h6>
                    <p class="text-muted">{{ $posStation->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
