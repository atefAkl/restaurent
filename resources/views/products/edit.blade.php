@extends('layouts.app')

@section('title', 'تعديل المنتج')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تعديل المنتج</h1>
                <div class="btn-group">
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i>
                        عرض
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right"></i>
                        العودة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">اسم المنتج (عربي) *</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar', $product->name_ar) }}" required>
                                    @error('name_ar')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">اسم المنتج (إنجليزي)</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', $product->name_en) }}">
                                    @error('name_en')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">الفئة *</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">اختر الفئة</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_ar }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">السعر *</label>
                                            <input type="number" class="form-control" id="price" name="price"
                                                value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                            @error('price')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cost" class="form-label">التكلفة</label>
                                            <input type="number" class="form-control" id="cost" name="cost"
                                                value="{{ old('cost', $product->cost) }}" step="0.01" min="0">
                                            @error('cost')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">صورة المنتج</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror

                                    @if($product->image)
                                    <div class="mt-2">
                                        <small class="text-muted">الصورة الحالية:</small>
                                        <br>
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name_ar }}"
                                            class="rounded mt-1" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    @endif

                                    <small class="text-muted">الصيغ المسموح بها: jpg, jpeg, png, gif</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode" class="form-label">الباركود</label>
                                            <input type="text" class="form-control" id="barcode" name="barcode"
                                                value="{{ old('barcode', $product->barcode) }}">
                                            @error('barcode')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">رمز المنتج (SKU)</label>
                                            <input type="text" class="form-control" id="sku" name="sku"
                                                value="{{ old('sku', $product->sku) }}">
                                            @error('sku')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">الكمية في المخزون</label>
                                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                                                value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                                            @error('stock_quantity')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_stock_alert" class="form-label">حد التنبيه الأدنى</label>
                                            <input type="number" class="form-control" id="min_stock_alert" name="min_stock_alert"
                                                value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0">
                                            @error('min_stock_alert')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_ar" class="form-label">الوصف (عربي)</label>
                                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $product->description_ar) }}</textarea>
                                    @error('description_ar')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_en" class="form-label">الوصف (إنجليزي)</label>
                                    <textarea class="form-control" id="description_en" name="description_en" rows="3">{{ old('description_en', $product->description_en) }}</textarea>
                                    @error('description_en')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="track_inventory" name="track_inventory"
                                        {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="track_inventory">
                                        تتبع المخزون
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_seasonal" name="is_seasonal"
                                        {{ old('is_seasonal', $product->is_seasonal) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_seasonal">
                                        منتج موسمي
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                                إلغاء
                            </a>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i>
                                    حفظ التغييرات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection