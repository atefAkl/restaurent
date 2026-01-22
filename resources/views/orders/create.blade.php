@extends('layouts.app')

@section('title', 'إنشاء طلب جديد')

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
                <a href="{{ route('orders.index') }}" class="text-decoration-none">الطلبات</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-bold">إنشاء طلب جديد</li>
        </ol>
    </nav>

    <!-- Page Header with Background -->
    <div class="bg-light py-4 mb-4 shadow-sm">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">إنشاء طلب جديد</h1>
                    <p class="text-muted mb-0 mt-2">إضافة طلب جديد للعميل</p>
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


    <!-- Order Form -->
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf
        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person"></i>
                            معلومات العميل
                        </h5>
                        <button type="button" class="btn btn-light py-1" onclick="addOrderItem()">
                            <i class="bi bi-person-plus"></i>
                            عميل افتراضي
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-bold text-dark">اسم العميل</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label fw-bold text-dark">رقم الهاتف</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                value="{{ old('customer_phone') }}">
                            @error('customer_phone')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_address" class="form-label fw-bold text-dark">العنوان</label>
                            <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea>
                            @error('customer_address')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="order_type" class="form-label fw-bold text-dark">نوع الطلب</label>
                            <select class="form-select" id="order_type" name="order_type" required>
                                <option value="">اختر نوع الطلب</option>
                                <option value="dine_in" {{ old('order_type') == 'dine_in' ? 'selected' : '' }}>جلسة في المطعم</option>
                                <option value="takeaway" {{ old('order_type') == 'takeaway' ? 'selected' : '' }}>توصيل</option>
                                <option value="delivery" {{ old('order_type') == 'delivery' ? 'selected' : '' }}>خدمة توصيل</option>
                            </select>
                            @error('order_type')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-cart-plus"></i>
                            عناصر الطلب
                        </h5>
                        <button type="button" class="btn btn-light py-1" onclick="addOrderItem()">
                            <i class="bi bi-plus"></i>
                            إضافة منتج
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Order Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="orderItemsTable">
                                <thead>
                                    <tr>
                                        <th class="bg-light">المنتج</th>
                                        <th class="bg-light">السعر</th>
                                        <th class="bg-light">الكمية</th>
                                        <th class="bg-light">الإجمالي</th>
                                        <th class="bg-light">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="itemRow" style="">
                                        <td>
                                            <select class="form-select product-select" name="products[]" onchange="updateItemPrice(this)">
                                                <option value="">اختر منتج</option>
                                                @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-name="{{ $product->name_ar }}"
                                                    data-barcode="{{ $product->barcode ?? '' }}"
                                                    data-sku="{{ $product->sku ?? '' }}">
                                                    {{ $product->name_ar }}
                                                    @if($product->barcode)
                                                    ({{ $product->barcode }})
                                                    @endif
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-price" name="prices[]"
                                                readonly step="0.01" min="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-quantity" name="quantities[]"
                                                min="1" value="1" onchange="updateItemTotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-total" name="totals[]"
                                                readonly step="0.01" min="0">
                                        </td>
                                        <td>
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="barcode_{{ time() }}"
                                                    placeholder="باركود أو SKU"
                                                    onkeyup="searchByBarcode(this)">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="document.getElementById('barcode_{{ time() }}').focus()">
                                                    <i class="bi bi-upc-scan"></i>
                                                </button>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeOrderItem(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Order Summary -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold text-dark">ملاحظات الطلب</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold text-dark">ملخص الطلب</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">المجموع الفرعي:</span>
                                            <span id="subtotal" class="fw-bold text-primary">0.00 ريال</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">الضريبة (15%):</span>
                                            <span id="tax" class="fw-bold text-primary">0.00 ريال</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">التوصيل:</span>
                                            <input type="number" class="form-control form-control-sm d-inline-block"
                                                id="delivery_fee" name="delivery_fee"
                                                value="{{ old('delivery_fee', 0) }}" step="0.01" min="0"
                                                style="width: 100px;" onchange="updateOrderTotal()">
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-dark fs-5">الإجمالي:</strong>
                                            <strong id="total" class="text-success fs-4">0.00 ريال</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                        إلغاء
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-warning" onclick="saveAsDraft()">
                            <i class="bi bi-save"></i>
                            حفظ كمسودة
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check"></i>
                            إنشاء الطلب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemCount = 0;

    // Products data from PHP
    const products = JSON.parse(document.getElementById('products-json').textContent);

    function addOrderItem() {
        itemCount++;
        const row = document.getElementById('itemRow').cloneNode(true);
        row.id = 'item_' + itemCount;
        row.style.display = '';

        // Clear values
        row.querySelectorAll('select, input').forEach(input => {
            if (input.type !== 'number') {
                input.value = '';
            } else if (input.name !== 'quantities[]') {
                input.value = '0';
            } else {
                input.value = '1';
            }
        });

        document.getElementById('orderItemsTable').getElementsByTagName('tbody')[0].appendChild(row);
    }

    function removeOrderItem(button) {
        button.closest('tr').remove();
        updateOrderTotal();
    }

    function updateItemPrice(select) {
        const row = select.closest('tr');
        const selectedProduct = products.find(p => p.id == select.value);

        if (selectedProduct) {
            row.querySelector('.item-price').value = selectedProduct.price;
            updateItemTotal(row.querySelector('.item-quantity'));
        }
    }

    function updateItemTotal(quantityInput) {
        const row = quantityInput.closest('tr');
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = price * quantity;
        row.querySelector('.item-total').value = total.toFixed(2);
        updateOrderTotal();
    }

    function updateOrderTotal() {
        const totals = Array.from(document.querySelectorAll('.item-total'))
            .map(input => parseFloat(input.value) || 0);
        const subtotal = totals.reduce((sum, total) => sum + total, 0);
        const tax = subtotal * 0.15;
        const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
        const total = subtotal + tax + deliveryFee;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' ريال';
        document.getElementById('tax').textContent = tax.toFixed(2) + ' ريال';
        document.getElementById('total').textContent = total.toFixed(2) + ' ريال';
    }

    function searchByBarcode(input) {
        const barcode = input.value.trim();
        if (barcode.length < 3) return;

        // Search for product by barcode or SKU
        const product = products.find(p => p.barcode === barcode || p.sku === barcode);

        if (product) {
            const row = input.closest('tr');
            const select = row.querySelector('.product-select');
            select.value = product.id;
            updateItemPrice(select);
            row.querySelector('.item-quantity').focus();
            input.value = '';
        }
    }

    function saveAsDraft() {
        const form = document.getElementById('orderForm');
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'is_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        form.submit();
    }

    // Add first item on load
    document.addEventListener('DOMContentLoaded', function() {
        addOrderItem();
    });
</script>
@endpush

<!-- Hidden products data for JavaScript -->
<div id="products-json" style="display: none;">
    {{ $products->map(function($p) {
        return [
            'id' => $p->id,
            'name_ar' => $p->name_ar,
            'price' => $p->price,
            'barcode' => $p->barcode ?? '',
            'sku' => $p->sku ?? ''
        ];
    }) }}
</div>