@extends('layouts.app')

@section('title', 'المنتجات')

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
                <a href="{{ route('products.index') }}" class="text-decoration-none">المنتجات</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-bold">إضافة منتج جديد</li>
        </ol>
    </nav>


    <div class="card mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="px-3 py-2">
                <h1 class="h4 fw-bold mb-0">المنتجات</h1>
                <p class="text-muted mb-0 mt-2">إدارة منتجاتك وإدارة المخزون</p>

            </div>
            <div class="btn-group">

                <a href="{{ route('products.low-stock') }}" class="btn btn-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    المنتجات منخفضة المخزون
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    إضافة منتج جديد
                </a>
            </div>
        </div>
    </div>


    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control"
                            value="{{ request('search') }}" placeholder="ابحث عن منتج...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفئة</label>
                        <select name="category_id" class="form-select">
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name_ar }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>اسم المنتج</th>
                            <th>الفئة</th>
                            <th>السعر</th>
                            <th>المخزون</th>
                            <th>الباركود</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->name_ar }}"
                                    class="rounded" style="width: 50px; height: 50px; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @else
                                <img src="{{ asset('storage/products/default.meal.icon.png') }}"
                                    alt="{{ $product->name_ar }}"
                                    class="rounded" style="width: 50px; height: 50px; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->name_ar }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->name_en }}</small>
                                </div>
                            </td>
                            <td>{{ $product->category->name_ar ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ number_format($product->price, 2) }} ريال
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-primary' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if($product->isLowStock())
                                    <br>
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        منخفض
                                    </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code>{{ $product->barcode }}</code>
                            </td>
                            <td>
                                <form action="{{ route('products.toggleStatus', [$product->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        <i class="bi {{ $product->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('products.show', ['product' => $product->id]) }}"
                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', [$product->id]) }}"
                                        class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', [$product->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">لا توجد منتجات</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh stock status every 30 seconds
    setInterval(() => {
        location.reload();
    }, 30000);
</script>
@endpush