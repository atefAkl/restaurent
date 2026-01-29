@extends('layouts.app')
@section('title', 'إضافة قالب طباعة جديد')
@section('content')
<div class="container">
    <h2 class="mb-4">إضافة قالب طباعة جديد</h2>
    <form action="{{ route('print-templates.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">اسم القالب</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">نوع القالب</label>
            <select name="type" class="form-control" required>
                <option value="order">إيصال طلب</option>
                <option value="invoice">فاتورة</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">محتوى القالب (HTML)</label>
            <textarea name="content" class="form-control" rows="10" required></textarea>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
            <label class="form-check-label">نشط</label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="default" value="1">
            <label class="form-check-label">اجعله افتراضي</label>
        </div>
        <button type="submit" class="btn btn-success">حفظ</button>
        <a href="{{ route('print-templates.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection