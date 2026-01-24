@extends('layouts.app')

@section('title', 'الفئات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">الفئات</h1>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    إضافة فئة جديدة
                </a>
            </div>

            <!-- Categories Grid -->
            <div class="row">
                @forelse($categories as $category)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title">{{ $category->name_ar }}</h5>
                                    <p class="text-muted small">{{ $category->name_en }}</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('categories.show', $category->id) }}">
                                                <i class="bi bi-eye"></i> عرض
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('categories.edit', $category->id) }}">
                                                <i class="bi bi-pencil"></i> تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('products.index', ['category_id' => $category->id]) }}">
                                                <i class="bi bi-box"></i> المنتجات
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('categories.toggleStatus', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi {{ $category->is_active ? 'bi-x' : 'bi-check' }}"></i>
                                                    {{ $category->is_active ? 'تعطيل' : 'تفعيل' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">
                                                    <i class="bi bi-trash"></i> حذف
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="category-image-wrapper mb-3 text-center">
                                @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                    alt="{{ $category->name_ar }}"
                                    class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="height: 150px; {{ $category->image ? 'display:none;' : '' }}">
                                    <i class="bi bi-folder fs-1 text-muted"></i>
                                </div>
                            </div>

                            <p class="card-text">{{ Str::limit($category->description_ar, 100) }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-box"></i>
                                    {{ $category->products()->count() }} منتج
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                        <p class="text-muted mt-3">لا توجد فئات</p>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i>
                            إضافة فئة جديدة
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection