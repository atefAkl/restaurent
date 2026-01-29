@extends('layouts.app')
@section('title', 'تعديل قالب طباعة')
@section('content')
<div class="container">
    <h2 class="mb-4">تعديل قالب طباعة</h2>
    <form action="{{ route('print-templates.update', $printTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">اسم القالب</label>
            <input type="text" name="name" class="form-control" value="{{ $printTemplate->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">نوع القالب</label>
            <select name="type" class="form-control" required>
                <option value="order" {{ $printTemplate->type == 'order' ? 'selected' : '' }}>إيصال طلب</option>
                <option value="invoice" {{ $printTemplate->type == 'invoice' ? 'selected' : '' }}>فاتورة</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">محتوى القالب (HTML)</label>
            <textarea name="content" class="form-control" rows="10" required>{{ $printTemplate->content }}</textarea>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $printTemplate->is_active ? 'checked' : '' }}>
            <label class="form-check-label">نشط</label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="default" value="1" {{ $printTemplate->default ? 'checked' : '' }}>
            <label class="form-check-label">اجعله افتراضي</label>
        </div>
        <button type="submit" class="btn btn-success">تحديث</button>
        <a href="{{ route('print-templates.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection