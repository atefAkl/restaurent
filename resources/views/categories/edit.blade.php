@extends('layouts.app')

@section('title', 'تعديل الفئة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تعديل الفئة</h1>
                <div class="btn-group">
                    <a href="{{ route('categories.show', $category->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i>
                        عرض
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right"></i>
                        العودة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">اسم الفئة (عربي) *</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar', $category->name_ar) }}" required>
                                    @error('name_ar')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">اسم الفئة (إنجليزي)</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', $category->name_en) }}">
                                    @error('name_en')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">ترتيب العرض</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                value="{{ old('sort_order', $category->sort_order) }}" min="0">
                                            @error('sort_order')
                                            <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                                {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                نشط
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">صورة الفئة</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror

                                    @if($category->image)
                                    <div class="mt-2">
                                        <small class="text-muted">الصورة الحالية:</small>
                                        <br>
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name_ar }}"
                                                class="rounded mt-1" style="width: 100px; height: 100px; object-fit: cover;"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="bg-light rounded mt-1 align-items-center justify-content-center"
                                                style="width: 100px; height: 100px; display: none;">
                                                <i class="bi bi-folder text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <small class="text-muted">الصيغ المسموح بها: jpg, jpeg, png, gif</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_ar" class="form-label">الوصف (عربي)</label>
                                    <textarea class="form-control" id="description_ar" name="description_ar" rows="4">{{ old('description_ar', $category->description_ar) }}</textarea>
                                    @error('description_ar')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_en" class="form-label">الوصف (إنجليزي)</label>
                                    <textarea class="form-control" id="description_en" name="description_en" rows="4">{{ old('description_en', $category->description_en) }}</textarea>
                                    @error('description_en')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
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