@extends('layouts.app')

@section('title', 'إضافة منتج جديد')

@section('content')

<div class="container-fluid">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light p-2 rounded">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i>
                    لوحة التحكم
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('clients.index') }}" class="text-decoration-none">المنتجات</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-bold">إضافة منتج جديد</li>
        </ol>
    </nav>

    <!-- Page Header with Actions -->
    <div class="p-3 mb-3 shadow-sm">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">اضافة عميل جديد</h1>
                    <p class="text-muted mb-0 mt-2">إضافة عميل جديد لقائمة العملاء</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="bi bi-arrow-right"></i>
                        العودة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" id="productForm">
        @csrf
        <div class="row">
            @error('any')
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @enderror
            <div class="col-md-12">
                <div class="card">
                   
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">اسم العميل *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">النوع *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">اختر النوع</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type }}"
                                        {{ (request()->has('type') && request('type') == $type) || old('type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">بريد الكتروني*</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                           autocomplete value="{{ old('email') }}">
                                        @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="s_number" class="form-label">الرقم التسلسلي</label>
                                        <input type="text" class="form-control" id="s_number" name="s_number"
                                            value="{{ old('s_number') ?? $serial }}" maxlength="14">
                                        @error('s_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                            </div>
                           
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            الحالة *
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i>
                                    إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i>
                                    حفظ العميل
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection