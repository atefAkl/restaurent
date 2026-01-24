@extends('layouts.order')

@section('title', __('app.titles.create_order'))

@section('content')

<div class="container-fluid">
    <style>
        .text-start {
            text-align: left;
        }

        [dir=rtl] .text-start {
            text-align: right;
        }

        .text-sm {
            font-size: 0.875rem !important;
        }
    </style>
    {{-- Top Devisions --}}
    <div class="row">
        <div class="col-2 p-0">
            <h4 class="bg-secondary text-white text-center py-2">Categories</h4>
            <div>
                <div class="p-2 border-bottom text-white">
                    <button href="" class="btn btn-link btn-block text-white w-100 text-start text-decoration-none">
                        All
                    </button>
                </div>
                @foreach($categories as $category)
                <div class="border-bottom text-white">
                    <button href="" class="btn btn-link btn-block w-100 text-start text-decoration-none">
                        {{ $category->name }} {{$active_category->id == $category->id ? '(Active)' : ''}}
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-6 p-0">
            <h4 class="bg-secondary text-white text-center py-2">Products</h4>
            <div class="d-flex p-3 justify-content-start">
                @foreach($products as $product)
                <div class="p-1">
                    <div class="card " style="width: 200px; height: 130px; position:relative; overflow: hidden;">
                        @if ($product->image)
                        <img role="logo" src="{{asset('storage/'.$product->image)}}" class="p-1 card-img-top" alt="{{$product->description_ar}}">
                        @else
                        <img role="icon" src="{{asset('storage/products/default.meal.icon.png')}}" class="p-1 card-img-top" alt="{{$product->description_ar}}">
                        @endif
                        <p class="position-absolute" style="display: block; line-height: 130px; text-align: center; top: 0; color: #fff; height: 100%; background-color: #3d3e3dcc; width: 100%; font-weight: bold;">
                            {{$product->name}}
                        </p>

                        <p class="position-absolute bottom-0 end-0 p-2 text-light fw-bold">{{$product->price}}</p>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-4 p-0">
            <h4 class="bg-secondary text-white text-center py-2">Order Items</h4>
            <form action="{{route('orders.items.store')}}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

            </form>
        </div>
    </div>
    {{-- Categories --}}
</div>

<!-- Page Header with Background -->
<div class="container">
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