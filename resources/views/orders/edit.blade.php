@extends('layouts.app')

@section('title', 'تحديث الطلب #' . $order->order_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">الطلبات</a></li>
                    <li class="breadcrumb-item active">تحديث الطلب #{{ $order->order_number }}</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="bi bi-pencil-square"></i> تحديث الطلب #{{ $order->order_number }}
            </h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <form id="updateOrderForm" method="POST" action="{{ route('orders.update', $order) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Right Side: Order Items -->
            <div class="col-lg-8">
                <!-- Current Order Items -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> منتجات الطلب</h5>
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
                                    @forelse($order->orderItems as $item)
                                    <tr class="order-item-row" data-product-id="{{ $item->product_id }}" data-item-id="{{ $item->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name_ar }}" 
                                                     class="rounded me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <strong>{{ $item->product->name_ar }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary decrease-qty">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       value="{{ $item->quantity }}" min="1" 
                                                       data-price="{{ $item->price }}">
                                                <button type="button" class="btn btn-outline-secondary increase-qty">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="item-price">{{ number_format($item->price, 2) }} ريال</td>
                                        <td class="item-total">{{ number_format($item->price * $item->quantity, 2) }} ريال</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="emptyOrderRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                            لا توجد منتجات في الطلب
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> ملاحظات</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="أضف أي ملاحظات للطلب...">{{ $order->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Left Side: Customer Info & Summary -->
            <div class="col-lg-4">
                <!-- Customer Information -->
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> معلومات العميل</h5>
                    </div>
                    <div class="card-body">
                        <!-- Order Type -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">نوع الطلب <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="orderType" required>
                                <option value="">-- اختر نوع الطلب --</option>
                                <option value="dine_in" {{ $order->type == 'dine_in' ? 'selected' : '' }}>صالة</option>
                                <option value="takeaway" {{ $order->type == 'takeaway' ? 'selected' : '' }}>سفري</option>
                                <option value="delivery" {{ $order->type == 'delivery' ? 'selected' : '' }}>توصيل</option>
                                <option value="catering" {{ $order->type == 'catering' ? 'selected' : '' }}>تجهيز طلبات</option>
                            </select>
                        </div>

                        <!-- Room Number (for Dine-in) -->
                        <div class="mb-3 {{ $order->type != 'dine_in' ? 'd-none' : '' }}" id="roomNumberGroup">
                            <label class="form-label">رقم الطاولة/الغرفة</label>
                            <input type="text" class="form-control" name="room_number" id="roomNumber" 
                                   value="{{ $order->room_number }}">
                        </div>

                        <!-- Customer Search with Autocomplete -->
                        <div class="mb-3 {{ in_array($order->type, ['takeaway', 'delivery', 'catering']) ? '' : 'd-none' }}" id="customerSearchGroup">
                            <label class="form-label">البحث عن العميل</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control" 
                                       id="customerSearch" 
                                       placeholder="ابحث بالاسم أو رقم الهاتف..."
                                       autocomplete="off">
                                <div class="position-absolute w-100" style="z-index: 1000;">
                                    <ul id="customerSearchResults" class="list-group d-none" style="max-height: 300px; overflow-y: auto;"></ul>
                                </div>
                            </div>
                            <small class="text-muted">ابدأ بالكتابة للبحث عن العميل</small>
                        </div>

                        <!-- Customer Phone -->
                        <div class="mb-3 {{ in_array($order->type, ['takeaway', 'delivery', 'catering']) ? '' : 'd-none' }}" id="customerPhoneGroup">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" name="customer_phone" id="customerPhone" 
                                   value="{{ $order->customer_phone }}" 
                                   placeholder="05xxxxxxxx">
                        </div>

                        <!-- Customer Name -->
                        <div class="mb-3 {{ in_array($order->type, ['takeaway', 'delivery', 'catering']) ? '' : 'd-none' }}" id="customerNameGroup">
                            <label class="form-label">اسم العميل</label>
                            <input type="text" class="form-control" name="customer_name" id="customerName" 
                                   value="{{ $order->customer_name }}"
                                   placeholder="أدخل اسم العميل">
                            <input type="hidden" name="customer_id" id="customerId" value="{{ $order->customer_id }}">
                        </div>

                        <!-- Delivery Address -->
                        <div class="mb-3 {{ $order->type == 'delivery' ? '' : 'd-none' }}" id="customerAddressGroup">
                            <label class="form-label">عنوان التوصيل</label>
                            <textarea class="form-control" name="customer_address" id="customerAddress" 
                                      rows="2" placeholder="أدخل عنوان التوصيل">{{ $order->customer_address }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> ملخص الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>المجموع الفرعي:</span>
                            <strong id="subtotalDisplay">{{ number_format($order->subtotal, 2) }} ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>الضريبة (15%):</span>
                            <strong id="taxDisplay">{{ number_format($order->tax_amount, 2) }} ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الخصم:</span>
                            <strong id="discountDisplay" class="text-danger">{{ number_format($order->discount_amount, 2) }} ريال</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 fw-bold">الإجمالي:</span>
                            <strong class="fs-4 text-primary" id="totalDisplay">{{ number_format($order->total_amount, 2) }} ريال</strong>
                        </div>

                        <!-- Discount Input -->
                        <div class="mb-3">
                            <label class="form-label">خصم إضافي (ريال)</label>
                            <input type="number" class="form-control" name="discount_amount" id="discountAmount" 
                                   value="{{ $order->discount_amount }}" min="0" step="0.01">
                        </div>

                        <!-- Hidden fields for calculations -->
                        <input type="hidden" name="subtotal" id="subtotalInput" value="{{ $order->subtotal }}">
                        <input type="hidden" name="tax_amount" id="taxInput" value="{{ $order->tax_amount }}">
                        <input type="hidden" name="total_amount" id="totalInput" value="{{ $order->total_amount }}">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> طريقة الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select class="form-select" name="payment_method" id="paymentMethod" required>
                                <option value="">-- اختر طريقة الدفع --</option>
                                <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="card" {{ $order->payment_method == 'card' ? 'selected' : '' }}>بطاقة</option>
                                <option value="bank_transfer" {{ $order->payment_method == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="mixed" {{ $order->payment_method == 'mixed' ? 'selected' : '' }}>مختلط</option>
                            </select>
                        </div>

                        <!-- Payment Reference -->
                        <div class="mb-3 {{ in_array($order->payment_method, ['card', 'bank_transfer']) ? '' : 'd-none' }}" id="paymentReferenceGroup">
                            <label class="form-label" id="paymentReferenceLabel">
                                {{ $order->payment_method == 'card' ? 'رقم الآلة' : 'رقم الحساب البنكي' }}
                            </label>
                            <input type="text" class="form-control" name="payment_reference" id="paymentReference" 
                                   value="{{ $order->payment_reference }}">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle"></i> حفظ التحديثات
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderTypeSelect = document.getElementById('orderType');
    const customerSearchInput = document.getElementById('customerSearch');
    const customerSearchResults = document.getElementById('customerSearchResults');
    const customerPhoneInput = document.getElementById('customerPhone');
    const customerNameInput = document.getElementById('customerName');
    const customerIdInput = document.getElementById('customerId');
    const discountInput = document.getElementById('discountAmount');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    
    let searchTimeout;
    let orderItems = new Map();

    // Initialize order items from existing data
    document.querySelectorAll('.order-item-row').forEach(row => {
        const productId = row.dataset.productId;
        const quantity = parseInt(row.querySelector('.quantity-input').value);
        const price = parseFloat(row.querySelector('.quantity-input').dataset.price);
        
        orderItems.set(productId, {
            productId: productId,
            quantity: quantity,
            price: price
        });
    });

    // Order Type Change Handler
    orderTypeSelect.addEventListener('change', function() {
        const type = this.value;
        
        // Hide all conditional fields
        document.getElementById('roomNumberGroup').classList.add('d-none');
        document.getElementById('customerSearchGroup').classList.add('d-none');
        document.getElementById('customerPhoneGroup').classList.add('d-none');
        document.getElementById('customerNameGroup').classList.add('d-none');
        document.getElementById('customerAddressGroup').classList.add('d-none');
        
        if (type === 'dine_in') {
            document.getElementById('roomNumberGroup').classList.remove('d-none');
        } else if (['takeaway', 'delivery', 'catering'].includes(type)) {
            document.getElementById('customerSearchGroup').classList.remove('d-none');
            document.getElementById('customerPhoneGroup').classList.remove('d-none');
            document.getElementById('customerNameGroup').classList.remove('d-none');
            
            if (type === 'delivery') {
                document.getElementById('customerAddressGroup').classList.remove('d-none');
            }
        }
    });

    // Advanced Customer Search with Autocomplete
    customerSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Hide results if search term is too short
        if (searchTerm.length < 2) {
            customerSearchResults.classList.add('d-none');
            customerSearchResults.innerHTML = '';
            return;
        }
        
        // Set a new timeout for search
        searchTimeout = setTimeout(() => {
            searchCustomers(searchTerm);
        }, 300); // Wait 300ms after user stops typing
    });

    // Search Customers Function
    function searchCustomers(searchTerm) {
        // Show loading state
        customerSearchResults.innerHTML = '<li class="list-group-item"><div class="spinner-border spinner-border-sm me-2" role="status"></div> جاري البحث...</li>';
        customerSearchResults.classList.remove('d-none');
        
        // Make AJAX request
        fetch(`{{ route('clients.searchByNameOrPhone') }}?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.clients && data.clients.length > 0) {
                    displaySearchResults(data.clients);
                } else {
                    customerSearchResults.innerHTML = '<li class="list-group-item text-muted text-center">لم يتم العثور على نتائج</li>';
                }
            })
            .catch(error => {
                console.error('Error searching customers:', error);
                customerSearchResults.innerHTML = '<li class="list-group-item text-danger text-center">حدث خطأ في البحث</li>';
            });
    }

    // Display Search Results
    function displaySearchResults(clients) {
        customerSearchResults.innerHTML = '';
        
        clients.forEach(client => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action customer-result-item';
            li.style.cursor = 'pointer';
            li.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block">${client.name}</strong>
                        <small class="text-muted"><i class="bi bi-telephone"></i> ${client.phone}</small>
                    </div>
                    <i class="bi bi-arrow-left-circle text-primary"></i>
                </div>
            `;
            
            // Click handler for selecting a customer
            li.addEventListener('click', function() {
                selectCustomer(client);
            });
            
            customerSearchResults.appendChild(li);
        });
    }

    // Select Customer Function
    function selectCustomer(client) {
        // Fill in the customer details
        customerIdInput.value = client.id;
        customerNameInput.value = client.name;
        customerPhoneInput.value = client.phone;
        
        // Clear and hide search results
        customerSearchInput.value = client.name + ' - ' + client.phone;
        customerSearchResults.classList.add('d-none');
        customerSearchResults.innerHTML = '';
        
        // Visual feedback
        customerNameInput.classList.add('border-success');
        customerPhoneInput.classList.add('border-success');
        setTimeout(() => {
            customerNameInput.classList.remove('border-success');
            customerPhoneInput.classList.remove('border-success');
        }, 1500);
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!customerSearchInput.contains(e.target) && !customerSearchResults.contains(e.target)) {
            customerSearchResults.classList.add('d-none');
        }
    });

    // Clear customer ID if name or phone is manually changed
    customerNameInput.addEventListener('input', function() {
        if (customerIdInput.value) {
            customerIdInput.value = '';
        }
    });

    customerPhoneInput.addEventListener('input', function() {
        if (customerIdInput.value) {
            customerIdInput.value = '';
        }
    });

    // Quantity Management
    document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.order-item-row');
            const input = row.querySelector('.quantity-input');
            input.value = parseInt(input.value) + 1;
            updateRowTotal(row);
            updateOrderSummary();
        });
    });

    document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.order-item-row');
            const input = row.querySelector('.quantity-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateRowTotal(row);
                updateOrderSummary();
            }
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('.order-item-row');
            if (parseInt(this.value) < 1) this.value = 1;
            updateRowTotal(row);
            updateOrderSummary();
        });
    });

    // Remove Item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل تريد حذف هذا المنتج من الطلب؟')) {
                const row = this.closest('.order-item-row');
                const productId = row.dataset.productId;
                orderItems.delete(productId);
                row.remove();
                
                // Show empty message if no items left
                if (document.querySelectorAll('.order-item-row').length === 0) {
                    document.getElementById('orderItemsTable').innerHTML = `
                        <tr id="emptyOrderRow">
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                لا توجد منتجات في الطلب
                            </td>
                        </tr>
                    `;
                }
                
                updateOrderSummary();
            }
        });
    });

    // Update Row Total
    function updateRowTotal(row) {
        const quantity = parseInt(row.querySelector('.quantity-input').value);
        const price = parseFloat(row.querySelector('.quantity-input').dataset.price);
        const total = quantity * price;
        row.querySelector('.item-total').textContent = total.toFixed(2) + ' ريال';
        
        // Update orderItems map
        const productId = row.dataset.productId;
        if (orderItems.has(productId)) {
            orderItems.get(productId).quantity = quantity;
        }
    }

    // Update Order Summary
    function updateOrderSummary() {
        let subtotal = 0;
        
        document.querySelectorAll('.order-item-row').forEach(row => {
            const quantity = parseInt(row.querySelector('.quantity-input').value);
            const price = parseFloat(row.querySelector('.quantity-input').dataset.price);
            subtotal += quantity * price;
        });
        
        const discount = parseFloat(discountInput.value) || 0;
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
    }

    // Discount change handler
    discountInput.addEventListener('input', updateOrderSummary);

    // Payment Method Change Handler
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

    // Form Validation
    document.getElementById('updateOrderForm').addEventListener('submit', function(e) {
        const remainingItems = document.querySelectorAll('.order-item-row').length;
        
        if (remainingItems === 0) {
            e.preventDefault();
            alert('يجب أن يحتوي الطلب على منتج واحد على الأقل');
            return false;
        }
        
        // Add items data to form
        const itemsData = [];
        document.querySelectorAll('.order-item-row').forEach(row => {
            itemsData.push({
                item_id: row.dataset.itemId,
                product_id: row.dataset.productId,
                quantity: parseInt(row.querySelector('.quantity-input').value),
                price: parseFloat(row.querySelector('.quantity-input').dataset.price)
            });
        });
        
        // Add items as hidden field
        const itemsInput = document.createElement('input');
        itemsInput.type = 'hidden';
        itemsInput.name = 'items';
        itemsInput.value = JSON.stringify(itemsData);
        this.appendChild(itemsInput);
    });
});
</script>

<style>
.order-item-row {
    transition: background-color 0.2s;
}

.order-item-row:hover {
    background-color: #f8f9fa;
}

.customer-result-item {
    transition: all 0.2s;
}

.customer-result-item:hover {
    background-color: #f0f8ff;
    transform: translateX(-5px);
}

#customerSearchResults {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
    margin-top: 2px;
}

.border-success {
    animation: successPulse 0.5s ease-in-out;
}

@keyframes successPulse {
    0%, 100% { border-color: #198754; }
    50% { border-color: #28a745; box-shadow: 0 0 10px rgba(40, 167, 69, 0.3); }
}
</style>
@endpush

@endsection
