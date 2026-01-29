@extends('layouts.app')
@section('title', 'تخصيص مكونات القالب')
@section('content')
<div class="container">
    <h2 class="mb-4">تخصيص مكونات القالب</h2>
    <form method="POST" action="{{ route('print-templates.update', $printTemplate) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" value="{{ $printTemplate->name }}">
        <input type="hidden" name="type" value="{{ $printTemplate->type }}">
        <input type="hidden" name="is_active" value="{{ $printTemplate->is_active }}">
        <input type="hidden" name="default" value="{{ $printTemplate->default }}">
        <div class="mb-3">
            <label class="form-label">المكونات الظاهرة في القالب</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_customer" value="1" {{ strpos($printTemplate->content, '{customer_name}') !== false ? 'checked' : '' }}>
                <label class="form-check-label">بيانات العميل</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_items" value="1" {{ strpos($printTemplate->content, '{items_table}') !== false ? 'checked' : '' }}>
                <label class="form-check-label">جدول المنتجات</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_totals" value="1" {{ strpos($printTemplate->content, '{total_amount}') !== false ? 'checked' : '' }}>
                <label class="form-check-label">الإجماليات</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_payment" value="1" {{ strpos($printTemplate->content, '{payment_method}') !== false ? 'checked' : '' }}>
                <label class="form-check-label">طريقة الدفع</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="show_notes" value="1" {{ strpos($printTemplate->content, '{notes}') !== false ? 'checked' : '' }}>
                <label class="form-check-label">ملاحظات الطلب</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">تحديث القالب</button>
        <a href="{{ route('print-templates.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
    <div class="alert alert-info mt-4">
        <strong>ملاحظة:</strong> عند تفعيل أو إلغاء أي مكون سيتم إضافة أو إزالة المتغير الخاص به تلقائياً من محتوى القالب.
    </div>
</div>
@endsection