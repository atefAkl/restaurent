@extends('layouts.app')

@section('title', 'عرض الفئة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">عرض الفئة</h1>
                <div class="btn-group">
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i>
                        تعديل
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right"></i>
                        العودة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}"
                                alt="{{ $category->name_ar }}"
                                class="img-fluid rounded mb-3" style="max-height: 200px;">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                style="height: 200px;">
                                <i class="bi bi-folder fs-1 text-muted"></i>
                            </div>
                            @endif

                            <h4>{{ $category->name_ar }}</h4>
                            <p class="text-muted">{{ $category->name_en }}</p>

                            <div class="d-flex justify-content-center gap-2">
                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                <span class="badge bg-info">
                                    الترتيب: {{ $category->sort_order }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">معلومات الفئة</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>عدد المنتجات:</strong>
                                    <p>
                                        <span class="badge bg-primary fs-5">
                                            {{ $category->products()->count() }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>المنتجات النشطة:</strong>
                                    <p>
                                        <span class="badge bg-success fs-5">
                                            {{ $category->products()->where('is_active', true)->count() }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="card-title">الوصف</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الوصف (عربي):</strong>
                                    <p>{{ $category->description_ar ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>الوصف (إنجليزي):</strong>
                                    <p>{{ $category->description_en ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in Category -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">المنتجات في هذه الفئة</h5>
                                <a href="{{ route('products.create', ['category_id' => $category->id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus"></i>
                                    إضافة منتج
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>السعر</th>
                                            <th>المخزون</th>
                                            <th>الحالة</th>
                                            <th>إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->products()->latest()->take(10)->get() as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                        alt="{{ $product->name_ar }}"
                                                        class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                    @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                        style="width: 30px; height: 30px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $product->name_ar }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $product->name_en }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ number_format($product->price, 2) }} ريال
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('products.show', $product->id) }}"
                                                        class="btn btn-outline-primary" title="عرض">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product->id) }}"
                                                        class="btn btn-outline-warning" title="تعديل">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                لا توجد منتجات في هذه الفئة
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($category->products()->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
                                    class="btn btn-outline-primary">
                                    عرض جميع المنتجات ({{ $category->products()->count() }})
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection