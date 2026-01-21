@extends('layouts.app')

@section('title', 'المنتجات منخفضة المخزون')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    المنتجات منخفضة المخزون
                </h1>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right"></i>
                    جميع المنتجات
                </a>
            </div>

            <!-- Alert -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>تنبيه!</strong> هناك {{ $lowStockProducts->count() }} منتجات منخفضة المخزون وتحتاج إلى إعادة تعبئة.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Low Stock Products Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>الفئة</th>
                                    <th>الكمية الحالية</th>
                                    <th>حد التنبيه</th>
                                    <th>الفرق</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                <tr class="table-warning">
                                    <td>
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name_ar }}"
                                            class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
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
                                        <span class="badge bg-danger fs-6">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">
                                            {{ $product->min_stock_alert }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $product->min_stock_alert - $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-danger" role="progressbar"
                                                style="width: {{ min(($product->stock_quantity / $product->min_stock_alert) * 100, 100) }}%">
                                                {{ min(round(($product->stock_quantity / $product->min_stock_alert) * 100), 100) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showRestockModal({{ $product->id }}, '{{ $product->name_ar }}')"
                                                title="إعادة تعبئة">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-check-circle fs-1 text-success"></i>
                                        <p class="text-success mt-2">جميع المنتجات بمخزون كافٍ</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">منتجات نفدت</h5>
                                    <h2>{{ $lowStockProducts->where('stock_quantity', 0)->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">منخفضة المخزون</h5>
                                    <h2>{{ $lowStockProducts->where('stock_quantity', '>', 0)->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">إجمالي النقص</h5>
                                    <h2>{{ $lowStockProducts->sum(function($p) { return $p->min_stock_alert - $p->stock_quantity; }) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">قيمة التكلفة</h5>
                                    <h2>{{ number_format($lowStockProducts->sum(function($p) { return $p->cost * ($p->min_stock_alert - $p->stock_quantity); }), 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إعادة تعبئة المخزون</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="restockForm" method="POST" action="{{ route('inventory.transactions.store') }}">
                @csrf
                <input type="hidden" name="product_id" id="restockProductId">
                <input type="hidden" name="transaction_type" value="in">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المنتج</label>
                        <input type="text" class="form-control" id="restockProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="restockQuantity" class="form-label">الكمية المضافة *</label>
                        <input type="number" class="form-control" id="restockQuantity" name="quantity"
                            min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="restockUnitCost" class="form-label">تكلفة الوحدة</label>
                        <input type="number" class="form-control" id="restockUnitCost" name="unit_cost"
                            step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="restockNotes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="restockNotes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus"></i>
                        إضافة للمخزون
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRestockModal(productId, productName) {
        document.getElementById('restockProductId').value = productId;
        document.getElementById('restockProductName').value = productName;
        document.getElementById('restockQuantity').value = '';
        document.getElementById('restockUnitCost').value = '';
        document.getElementById('restockNotes').value = '';

        var modal = new bootstrap.Modal(document.getElementById('restockModal'));
        modal.show();
    }

    // Auto-refresh every 30 seconds
    setInterval(() => {
        location.reload();
    }, 30000);
</script>
@endpush