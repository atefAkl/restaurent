@extends('layouts.order')

@section('title', 'إنشاء طلب جديد - POS')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">الطلبات</a></li>
                    <li class="breadcrumb-item active">إنشاء طلب جديد</li>
                </ol>
            </nav>
            <h2 class="mb-0">إنشاء طلب جديد - POS</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <form id="posForm" method="POST" action="{{ route('orders.store') }}">
        @csrf
        <div class="row">
            <!-- Right Side: Categories & Products (Parts 1-2) -->
            <div class="col-lg-8">
                <!-- Part 1: Categories Navigation -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-grid-3x3"></i> الفئات</h5>
                    </div>
                    <div class="card-body">
                        <div id="categoriesContainer" class="d-flex flex-wrap gap-2">
                            <!-- Categories will be loaded dynamically -->
                            <div class="text-center w-100 py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Part 2: Products List -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-basket"></i> المنتجات</h5>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer" class="row g-3">
                            <!-- Products will be loaded based on selected category -->
                            <div class="col-12 text-center text-muted py-5">
                                <i class="bi bi-arrow-up-circle fs-1 d-block mb-2"></i>
                                اختر فئة من الأعلى لعرض المنتجات
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Part 3: Order Items Details -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> تفاصيل الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th style="width: 150px;">الكمية</th>
                                        <th style="width: 120px;">السعر</th>
                                        <th style="width: 120px;">الإجمالي</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsTable">
                                    <tr id="emptyOrderRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                            لا توجد منتجات في الطلب بعد
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Side: Customer Type, Summary, Payment (Parts 4-7) -->
            <div class="col-lg-4">
                <!-- Part 4: Customer Type -->
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> نوع العميل</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">نوع الطلب</label>
                            <select class="form-select" name="type" id="orderType" required>
                                <option value="">-- اختر نوع الطلب --</option>
                                <option value="dine_in">صالة</option>
                                <option value="takeaway">سفري</option>
                                <option value="delivery">توصيل</option>
                                <option value="subscription">اشتراك</option>
                            </select>
                        </div>

                        <!-- Room Number (for Dine-in) -->
                        <div class="mb-3 d-none" id="roomNumberGroup">
                            <label class="form-label">رقم الطاولة/الغرفة</label>
                            <input type="text" class="form-control" name="room_number" id="roomNumber">
                        </div>

                        <!-- Customer Phone (for Takeaway/Delivery) -->
                        <div class="mb-3 d-none" id="customerPhoneGroup">
                            <label class="form-label">رقم الهاتف</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="customer_phone" id="customerPhone" placeholder="05xxxxxxxx">
                                <button class="btn btn-outline-secondary" type="button" id="searchCustomerBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Customer Name (auto-filled or manual) -->
                        <div class="mb-3 d-none" id="customerNameGroup">
                            <label class="form-label">اسم العميل</label>
                            <input type="text" class="form-control" name="customer_name" id="customerName">
                            <input type="hidden" name="customer_id" id="customerId">
                        </div>

                        <!-- Delivery Address -->
                        <div class="mb-3 d-none" id="customerAddressGroup">
                            <label class="form-label">عنوان التوصيل</label>
                            <textarea class="form-control" name="customer_address" id="customerAddress" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Part 5: Order Summary -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> ملخص الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>المجموع الفرعي:</span>
                            <strong id="subtotalDisplay">0.00 ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>الضريبة (15%):</span>
                            <strong id="taxDisplay">0.00 ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الخصم:</span>
                            <strong id="discountDisplay" class="text-danger">0.00 ريال</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 fw-bold">الإجمالي:</span>
                            <strong class="fs-4 text-primary" id="totalDisplay">0.00 ريال</strong>
                        </div>

                        <!-- Discount Input -->
                        <div class="mb-3">
                            <label class="form-label">خصم إضافي (ريال)</label>
                            <input type="number" class="form-control" name="discount_amount" id="discountAmount" 
                                   value="0" min="0" step="0.01">
                        </div>

                        <!-- Hidden fields for calculations -->
                        <input type="hidden" name="subtotal" id="subtotalInput">
                        <input type="hidden" name="tax_amount" id="taxInput">
                        <input type="hidden" name="total_amount" id="totalInput">

                        <!-- Notes -->
                        <div class="mb-0">
                            <label class="form-label">ملاحظات</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Part 6: Payment Method -->
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> طريقة الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select class="form-select" name="payment_method" id="paymentMethod" required>
                                <option value="">-- اختر طريقة الدفع --</option>
                                <option value="cash">نقدي</option>
                                <option value="card">بطاقة</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="on_account">آجل</option>
                                <option value="subscription">اشتراك</option>
                            </select>
                        </div>

                        <!-- Part 7: Bank/Machine Info (conditional) -->
                        <div class="mb-3 d-none" id="paymentReferenceGroup">
                            <label class="form-label" id="paymentReferenceLabel">رقم الآلة/الحساب</label>
                            <input type="text" class="form-control" name="payment_reference" id="paymentReference">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المبلغ المدفوع</label>
                            <input type="number" class="form-control" name="paid_amount" id="paidAmount" 
                                   value="0" min="0" step="0.01">
                        </div>

                        <div class="alert alert-info mb-0">
                            <small>
                                <strong>المتبقي:</strong> 
                                <span id="remainingDisplay">0.00 ريال</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 mb-2" id="saveOrderBtn">
                            <i class="bi bi-check-circle"></i> حفظ الطلب
                        </button>
                        <button type="button" class="btn btn-primary w-100 mb-2" id="saveAndPrintBtn">
                            <i class="bi bi-printer"></i> حفظ وطباعة
                        </button>
                        <button type="button" class="btn btn-outline-danger w-100" id="cancelOrderBtn">
                            <i class="bi bi-x-circle"></i> إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Hidden template for order items -->
<template id="orderItemTemplate">
    <tr class="order-item-row" data-product-id="">
        <td>
            <strong class="product-name"></strong>
            <input type="hidden" class="product-id-input" name="products[]">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <button class="btn btn-outline-secondary decrease-qty" type="button">-</button>
                <input type="number" class="form-control text-center quantity-input" name="quantities[]" value="1" min="1">
                <button class="btn btn-outline-secondary increase-qty" type="button">+</button>
            </div>
        </td>
        <td>
            <span class="unit-price"></span>
            <input type="hidden" class="unit-price-input" name="prices[]">
        </td>
        <td>
            <strong class="item-total"></strong>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global state
    const orderItems = new Map(); // productId -> {name, price, quantity}
    
    // DOM Elements
    const categoriesContainer = document.getElementById('categoriesContainer');
    const productsContainer = document.getElementById('productsContainer');
    const orderItemsTable = document.getElementById('orderItemsTable');
    const emptyOrderRow = document.getElementById('emptyOrderRow');
    const orderTypeSelect = document.getElementById('orderType');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    
    // Part 1: Load Categories
    loadCategories();
    
    function loadCategories() {
        fetch('/api/categories')
            .then(response => response.json())
            .then(categories => {
                categoriesContainer.innerHTML = '';
                categories.forEach(category => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary category-btn';
                    btn.dataset.categoryId = category.id;
                    btn.innerHTML = `
                        <i class="bi bi-tag"></i>
                        ${category.name_ar}
                    `;
                    btn.addEventListener('click', () => loadProducts(category.id, btn));
                    categoriesContainer.appendChild(btn);
                });
            })
            .catch(error => {
                console.error('Error loading categories:', error);
                categoriesContainer.innerHTML = '<div class="alert alert-danger">خطأ في تحميل الفئات</div>';
            });
    }
    
    // Part 2: Load Products by Category
    function loadProducts(categoryId, activeBtn) {
        // Update active category button
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        activeBtn.classList.add('active');
        
        productsContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border"></div></div>';
        
        fetch(`/api/categories/${categoryId}/products`)
            .then(response => response.json())
            .then(products => {
                productsContainer.innerHTML = '';
                if (products.length === 0) {
                    productsContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">لا توجد منتجات في هذه الفئة</div>';
                    return;
                }
                
                products.forEach(product => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 col-lg-3';
                    col.innerHTML = `
                        <div class="card product-card h-100 shadow-sm" style="cursor: pointer;" data-product-id="${product.id}">
                            <img src="${product.image_url || '/images/no-image.png'}" class="card-img-top" alt="${product.name_ar}" 
                                 style="height: 150px; object-fit: cover;" 
                                 onerror="this.src='/images/no-image.png'">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">${product.name_ar}</h6>
                                <p class="card-text text-primary fw-bold mb-0">${parseFloat(product.price).toFixed(2)} ريال</p>
                            </div>
                        </div>
                    `;
                    
                    col.querySelector('.product-card').addEventListener('click', () => {
                        addProductToOrder(product);
                    });
                    
                    productsContainer.appendChild(col);
                });
            })
            .catch(error => {
                console.error('Error loading products:', error);
                productsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">خطأ في تحميل المنتجات</div></div>';
            });
    }
    
    // Part 3: Add Product to Order
    function addProductToOrder(product) {
        if (orderItems.has(product.id)) {
            // Increase quantity if already exists
            const item = orderItems.get(product.id);
            item.quantity++;
            updateOrderItemRow(product.id);
        } else {
            // Add new item
            orderItems.set(product.id, {
                name: product.name_ar,
                price: parseFloat(product.price),
                quantity: 1
            });
            renderOrderItem(product.id);
        }
        
        emptyOrderRow.classList.add('d-none');
        updateOrderSummary();
    }
    
    function renderOrderItem(productId) {
        const template = document.getElementById('orderItemTemplate');
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.order-item-row');
        const item = orderItems.get(productId);
        
        row.dataset.productId = productId;
        row.querySelector('.product-name').textContent = item.name;
        row.querySelector('.product-id-input').value = productId;
        row.querySelector('.quantity-input').value = item.quantity;
        row.querySelector('.unit-price').textContent = item.price.toFixed(2) + ' ريال';
        row.querySelector('.unit-price-input').value = item.price;
        row.querySelector('.item-total').textContent = (item.price * item.quantity).toFixed(2) + ' ريال';
        
        // Event listeners
        row.querySelector('.decrease-qty').addEventListener('click', () => decreaseQuantity(productId));
        row.querySelector('.increase-qty').addEventListener('click', () => increaseQuantity(productId));
        row.querySelector('.quantity-input').addEventListener('change', (e) => updateQuantity(productId, parseInt(e.target.value)));
        row.querySelector('.remove-item').addEventListener('click', () => removeItem(productId));
        
        orderItemsTable.appendChild(row);
    }
    
    function updateOrderItemRow(productId) {
        const row = document.querySelector(`.order-item-row[data-product-id="${productId}"]`);
        const item = orderItems.get(productId);
        
        row.querySelector('.quantity-input').value = item.quantity;
        row.querySelector('.item-total').textContent = (item.price * item.quantity).toFixed(2) + ' ريال';
    }
    
    function increaseQuantity(productId) {
        const item = orderItems.get(productId);
        item.quantity++;
        updateOrderItemRow(productId);
        updateOrderSummary();
    }
    
    function decreaseQuantity(productId) {
        const item = orderItems.get(productId);
        if (item.quantity > 1) {
            item.quantity--;
            updateOrderItemRow(productId);
            updateOrderSummary();
        }
    }
    
    function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) newQuantity = 1;
        const item = orderItems.get(productId);
        item.quantity = newQuantity;
        updateOrderItemRow(productId);
        updateOrderSummary();
    }
    
    function removeItem(productId) {
        orderItems.delete(productId);
        document.querySelector(`.order-item-row[data-product-id="${productId}"]`).remove();
        
        if (orderItems.size === 0) {
            emptyOrderRow.classList.remove('d-none');
        }
        
        updateOrderSummary();
    }
    
    // Part 5: Update Order Summary
    function updateOrderSummary() {
        let subtotal = 0;
        orderItems.forEach(item => {
            subtotal += item.price * item.quantity;
        });
        
        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        const taxableAmount = subtotal - discount;
        const tax = taxableAmount * 0.15; // 15% VAT
        const total = taxableAmount + tax;
        
        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ريال';
        document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ريال';
        document.getElementById('discountDisplay').textContent = discount.toFixed(2) + ' ريال';
        document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ريال';
        
        document.getElementById('subtotalInput').value = subtotal.toFixed(2);
        document.getElementById('taxInput').value = tax.toFixed(2);
        document.getElementById('totalInput').value = total.toFixed(2);
        
        updateRemainingAmount();
    }
    
    function updateRemainingAmount() {
        const total = parseFloat(document.getElementById('totalInput').value) || 0;
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        const remaining = total - paid;
        
        document.getElementById('remainingDisplay').textContent = remaining.toFixed(2) + ' ريال';
    }
    
    // Part 4: Order Type Change Events
    orderTypeSelect.addEventListener('change', function() {
        const type = this.value;
        
        // Hide all conditional fields
        document.getElementById('roomNumberGroup').classList.add('d-none');
        document.getElementById('customerPhoneGroup').classList.add('d-none');
        document.getElementById('customerNameGroup').classList.add('d-none');
        document.getElementById('customerAddressGroup').classList.add('d-none');
        
        if (type === 'dine_in') {
            document.getElementById('roomNumberGroup').classList.remove('d-none');
        } else if (type === 'takeaway' || type === 'delivery') {
            document.getElementById('customerPhoneGroup').classList.remove('d-none');
            document.getElementById('customerNameGroup').classList.remove('d-none');
            
            if (type === 'delivery') {
                document.getElementById('customerAddressGroup').classList.remove('d-none');
            }
        }
    });
    
    // Search Customer by Phone
    document.getElementById('searchCustomerBtn').addEventListener('click', function() {
        const phone = document.getElementById('customerPhone').value;
        if (!phone) return;
        
        fetch(`/api/customers/search?phone=${phone}`)
            .then(response => response.json())
            .then(customer => {
                if (customer) {
                    document.getElementById('customerId').value = customer.id;
                    document.getElementById('customerName').value = customer.name;
                    document.getElementById('customerAddress').value = customer.address || '';
                } else {
                    document.getElementById('customerId').value = '';
                    document.getElementById('customerName').value = '';
                    alert('لم يتم العثور على العميل. يمكنك إدخال الاسم يدوياً.');
                }
            })
            .catch(error => {
                console.error('Error searching customer:', error);
            });
    });
    
    // Part 6-7: Payment Method Change Events
    paymentMethodSelect.addEventListener('change', function() {
        const method = this.value;
        const referenceGroup = document.getElementById('paymentReferenceGroup');
        const referenceLabel = document.getElementById('paymentReferenceLabel');
        
        if (method === 'card') {
            referenceGroup.classList.remove('d-none');
            referenceLabel.textContent = 'رقم الآلة';
        } else if (method === 'bank_transfer') {
            referenceGroup.classList.remove('d-none');
            referenceLabel.textContent = 'رقم الحساب البنكي';
        } else {
            referenceGroup.classList.add('d-none');
        }
    });
    
    // Event listeners for calculations
    document.getElementById('discountAmount').addEventListener('input', updateOrderSummary);
    document.getElementById('paidAmount').addEventListener('input', updateRemainingAmount);
    
    // Save and Print Button
    document.getElementById('saveAndPrintBtn').addEventListener('click', function() {
        // Add a hidden field to indicate we want to print
        const printInput = document.createElement('input');
        printInput.type = 'hidden';
        printInput.name = 'print_receipt';
        printInput.value = '1';
        document.getElementById('posForm').appendChild(printInput);
        
        document.getElementById('posForm').submit();
    });
    
    // Cancel Button
    document.getElementById('cancelOrderBtn').addEventListener('click', function() {
        if (confirm('هل أنت متأكد من إلغاء الطلب؟')) {
            window.location.href = '{{ route("orders.index") }}';
        }
    });
    
    // Form Validation
    document.getElementById('posForm').addEventListener('submit', function(e) {
        if (orderItems.size === 0) {
            e.preventDefault();
            alert('يجب إضافة منتج واحد على الأقل للطلب');
            return false;
        }
    });
});
</script>

<style>
.category-btn {
    min-width: 120px;
    margin: 5px;
}

.category-btn.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
}

.order-item-row {
    transition: background-color 0.2s;
}

.order-item-row:hover {
    background-color: #f8f9fa;
}
</style>
@endpush
