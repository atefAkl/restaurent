@extends('layouts.app')
@section('title', 'نقاط البيع')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item active">نقاط البيع</li>
            </ol>
        </nav>
    </div>

    <!-- Header & Add Button -->
    <div class="card mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="p-3">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">نقاط البيع</h2>
                <p>إدارة وعرض جميع نقاط البيع</p>
            </div>
            <a href="{{ route('pos-stations.create') }}" class="btn btn-primary d-flex align-items-center" style="margin-inline-end: 1rem;">
                <i class="bi bi-plus-circle"></i>&nbsp; نقطة بيع جديدة
            </a>
        </div>
    </div>

    <!-- Table Box -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>الموقع</th>
                        <th>الطابعة</th>
                        <th>الأجهزة</th>
                        <th>الحالة</th>
                        <th style="min-width:90px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posStations as $posStation)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="badge bg-secondary">{{ $posStation->code }}</span></td>
                        <td>{{ $posStation->name }}</td>
                        <td>{{ $posStation->location }}</td>
                        <td>{{ $posStation->printer?->name ?? 'غير محدد' }}</td>
                        <td>
                            @if($posStation->posDevices->count() > 0)
                            <span class="badge bg-info">{{ $posStation->posDevices->count() }} جهاز</span>
                            <div class="small text-muted">
                                @foreach($posStation->posDevices->take(2) as $device)
                                {{ $device->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                @if($posStation->posDevices->count() > 2)
                                +{{ $posStation->posDevices->count() - 2 }}
                                @endif
                            </div>
                            @else
                            <span class="text-muted">لا توجد أجهزة</span>
                            @endif
                        </td>
                        <td>
                            @if($posStation->is_active)
                            <span class="badge bg-success">نشط</span>
                            @else
                            <span class="badge bg-danger">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pos-stations.edit', $posStation) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('pos-stations.show', $posStation) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('pos-stations.destroy', $posStation) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('تأكيد الحذف؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $posStations->links() }}
    </div>
</div>
@endsection