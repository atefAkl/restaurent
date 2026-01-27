@extends('layouts.app')
@section('title', 'إدارة الغرف')
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">إدارة الغرف</li>
        </ol>
    </nav>

    <!-- عنوان وإجراءات سريعة -->
    <div class="card mb-3" style="border-radius:8px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0" style="font-size:16px; font-weight:bold;">إدارة الغرف</h2>
                <div class="text-muted" style="font-size:12px;">عرض جميع الغرف وإدارتها</div>
            </div>
            <div>
                <a href="{{ route('rooms.create') }}" class="btn btn-primary btn-sm" style="font-size:12px;">إضافة غرفة جديدة</a>
            </div>
        </div>
    </div>

    <!-- جدول البيانات -->
    <div class="card" style="border-radius:8px;">
        <div class="card-body p-2">
            <table class="table table-bordered table-hover mb-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th style="font-size:12px;">#</th>
                        <th style="font-size:12px;">اسم الغرفة</th>
                        <th style="font-size:12px;">رقم الغرفة</th>
                        <th style="font-size:12px;">الحالة</th>
                        <th style="font-size:12px;">عدد الطاولات</th>
                        <th style="font-size:12px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>{{ $room->id }}</td>
                        <td>{{ $room->name }}</td>
                        <td>{{ $room->number }}</td>
                        <td>{{ $room->status ?? '-' }}</td>
                        <td>{{ $room->tables->count() }}</td>
                        <td>
                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-warning" style="font-size:11px;">تعديل</a>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="font-size:11px;" onclick="return confirm('تأكيد الحذف؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
