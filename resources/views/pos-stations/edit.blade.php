@extends('layouts.app')
@section('title', 'تعديل نقطة البيع')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-2 rounded" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">الإعدادات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pos-stations.index') }}">نقاط البيع</a></li>
                <li class="breadcrumb-item active">تعديل نقطة البيع</li>
            </ol>
        </nav>
    </div>

    <!-- Form Box -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">تعديل نقطة البيع: {{ $posStation->name }}</h4>
            
            <form action="{{ route('pos-stations.update', $posStation) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $posStation->name }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">الكود *</label>
                            <input type="text" class="form-control" id="code" name="code" value="{{ $posStation->code }}" placeholder="POS001" required>
                            @error('code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">الموقع *</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ $posStation->location }}" placeholder="المطعم، الطابق الأول" required>
                            @error('location')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="printer_id" class="form-label">الطابعة</label>
                            <select class="form-select" id="printer_id" name="printer_id">
                                <option value="">اختر طابعة</option>
                                @foreach($printers as $printer)
                                    <option value="{{ $printer->id }}" {{ $posStation->printer_id == $printer->id ? 'selected' : '' }}>
                                        {{ $printer->name }} - {{ $printer->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('printer_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ $posStation->notes }}</textarea>
                            @error('notes')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $posStation->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                نشط
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>&nbsp; حفظ التعديلات
                        </button>
                        <a href="{{ route('pos-stations.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-right"></i>&nbsp; إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
