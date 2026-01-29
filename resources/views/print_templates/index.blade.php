@extends('layouts.app')
@section('title', 'إدارة قوالب الطباعة')
@section('content')
<div class="container">
    <h2 class="mb-4">قوالب الطباعة</h2>
    <a href="{{ route('print-templates.create') }}" class="btn btn-primary mb-3">إضافة قالب جديد</a>
    <a href="{{ url('print-templates/customize') }}" class="btn btn-success mb-3 ms-2">تخصيص سريع (مرئي)</a>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>النوع</th>
                <th>الحالة</th>
                <th>افتراضي</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr>
                <td>{{ $template->id }}</td>
                <td>{{ $template->name }}</td>
                <td>{{ $template->type }}</td>
                <td>{{ $template->is_active ? 'نشط' : 'غير نشط' }}</td>
                <td>{{ $template->default ? 'نعم' : 'لا' }}</td>
                <td>
                    <a href="{{ route('print-templates.edit', $template) }}" class="btn btn-sm btn-warning"> تعديل</a>
                    <a href="{{ route('print-templates.preview', $template) }}" class="btn btn-sm btn-info" target="_blank" style="margin-inline:2px;">معاينة</a>
                    <form action="{{ route('print-templates.destroy', $template) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('تأكيد الحذف؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection