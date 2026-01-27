@extends('layouts.app')
@section('title', 'إدارة الطاولات')
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">إدارة الطاولات</li>
        </ol>
    </nav>

    <!-- عنوان وإجراءات سريعة -->
    <div class="card mb-3" style="border-radius:8px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0" style="font-size:16px; font-weight:bold;">إدارة الطاولات</h2>
                <div class="text-muted" style="font-size:12px;">عرض جميع الطاولات وإدارتها</div>
            </div>
            <div>
                <a href="{{ route('tables.create') }}" class="btn btn-primary btn-sm" style="font-size:12px;">إضافة طاولة جديدة</a>
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
                        <th style="font-size:12px;">اسم الطاولة</th>
                        <th style="font-size:12px;">رقم الطاولة</th>
                        <th style="font-size:12px;">الغرفة</th>
                        <th style="font-size:12px;">الحالة</th>
                        <th style="font-size:12px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                    <tr>
                        <td>{{ $table->id }}</td>
                        <td>{{ $table->name }}</td>
                        <td>{{ $table->number }}</td>
                        <td>{{ $table->room ? $table->room->name : '-' }}</td>
                        <td>{{ $table->status ?? '-' }}</td>
                        <td>
                            <a href="{{ route('tables.edit', $table) }}" class="btn btn-sm btn-warning" style="font-size:11px;">تعديل</a>
                            <form action="{{ route('tables.destroy', $table) }}" method="POST" style="display:inline-block">
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
