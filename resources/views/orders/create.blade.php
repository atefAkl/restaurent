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

        .order-meals-item .card {
            width: 130px;
            height: 100px;
            position: relative;
            overflow: hidden;
        }

        .order-meals-item .card p.meal-price,
        .order-meals-item .card p.meal-name {
            display: block;
            position: absolute;
            top: 0;
            color: #fff;
            height: 100%;
            width: 100%;
            font-weight: bold;
        }

        .order-meals-item .card p.meal-name {
            background-color: #3d3e3dcc;
            text-align: center;
            padding: 1rem 0.5rem;
        }

        .order-meals-item .card p.meal-price {
            padding: 4rem 0.5rem;
            text-align: end;
        }


        .update-order-item-form input,
        .update-order-item-form button,
        .update-order-item-form select,
        .update-order-item-form label {
            height: 3rem;
            text-align: center;
            margin: 0;
            font: normal 10px/1.2rem Cairo;
            border-radius: 0.6rem
        }

        .order-types {
            display: flex;
            justify-content: start;
            gap: 2px;
        }

        .order-types .btn {
            margin: 1px 1px 0.6rem;
            border-radius: 0.6rem;
            padding: 0.25rem 1rem;
        }

        .order-types .btn:first-of-type {
            margin-inline-start: 1rem;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"]:checked+label {
            background-color: #007bff;
            color: white;
        }


        @media (min-width: 991px) {

            .input-group input,
            .input-group button,
            .input-group select,
            .input-group label {

                font: normal 14px/1.2rem Cairo;
            }
        }

        .customer-result-item {
            transition: all 0.2s ease;
        }

        .customer-result-item:hover {
            background-color: #e3f2fd !important;
            transform: translateX(-5px);
            border-right: 4px solid #2196F3;
        }

        #customerSearchResults {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 0.6rem;
            margin-top: 2px;
            animation: fadeIn 0.2s ease-out;
        }

        .border-success {
            animation: successPulse 0.6s ease-in-out;
        }

        @keyframes successPulse {

            0%,
            100% {
                border-color: #198754;
            }

            50% {
                border-color: #28a745;
                box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }

            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }

        /* Make the search input stand out */
        #customerSearch:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
    {{-- Top Devisions --}}
    <div class="row">
        <div class="col-2 p-0">
            <h4 class="bg-secondary text-white text-center py-2">{{__('orders.titles.categories')}}</h4>
            <div id="order_cats" style="background-color: #3d3e3dcc">
                <div class="border-bottom {{$active_category == '' ? 'active' : ''}}">
                    <a href="?active_category=" class="btn btn-block w-100 text-start text-white">
                        {{__('orders.all_categories')}}
                    </a>
                </div>
                @foreach($categories as $category)
                <div class="border-bottom {{$active_category == $category->id ? 'active' : ''}}">
                    <a href="?active_category={{ $category->id }}" class="btn btn-block w-100 text-start text-white">
                        {{ $category->name }} {{$active_category == $category->id ? '(Active)' : ''}}
                    </a>
                </div>
                @endforeach
                <div class="border-bottom">
                    <a href="{{route('orders.index')}}" class="btn btn-block w-100 text-start text-white">
                        {{ __('orders.back_to_orders') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-10 col-lg-5 p-0">
            <h4 class="bg-secondary text-white text-center py-2">{{__('orders.titles.products')}}</h4>
            <div class="d-flex p-3 justify-content-start flex-wrap">
                {{-- Products --}}
                {{-- Loop through products --}}
                @foreach($products as $product)
                <div class="order-meals-item p-1">
                    <form method="POST" action="{{route('orders.items.store')}}" class="text-decoration-none">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="border-0 bg-transparent p-0 m-0 w-100 h-100">
                            <div class="card" style="">
                                @if ($product->image)
                                <img role="logo" src="{{asset('storage/'.$product->image)}}" class="p-1 card-img-top" alt="{{$product->description_ar}}">
                                @else
                                <img role="icon" src="{{asset('storage/products/default.meal.icon.png')}}" class="p-1 card-img-top" alt="{{$product->description_ar}}">
                                @endif
                                <p class="meal-name" style="">
                                    {{$product->name}}
                                </p>

                                <p class="meal-price">{{$product->price}}</p>

                            </div>
                        </button>
                    </form>
                </div>
                @endforeach
                {{-- <div class="order-meals-item p-1">
                    <div class="card " style="">
                        <a href="{{route('products.create', ['category_id' => $active_category])}}" class="w-100 h-100 d-flex flex-column justify-content-center align-items-center" style="background-color: #333c; color: #fff; text-decoration: none;">
                <h1 class="text-center text-xl p-0 m-0">+</h1>
                <h3 class="text-center p-0 m-0">Add</h3>
                </a>
            </div>
        </div> --}}
    </div>
</div>
<div class="col-md-12 col-lg-5 p-0">
    <h4 class="bg-secondary text-white text-center py-2">{{ __('orders.titles.order_items') }}</h4>
    @csrf
    @forelse( $orderItems as $item )
    <form class="update-order-item-form" method="POST" action="{{route('orders.items.update', $item->id)}}">
        @csrf
        @method('PUT')
        <div class="input-group mb-1">
            <label for="" class="input-group-text px-3">{{$loop->iteration}}</label>
            <input type="text" class="form-control" name="product_name" value="{{$item->product->name}}" id="item_name_{{$item->id}}">
            <input type="number" class="input-group-text" style="width: 80px;" name="unit_price" value="{{$item->unit_price}}" id="item_unit_price_{{$item->id}}">
            <input type="number" class="input-group-text" style="width: 50px;" name="quantity" value="{{$item->quantity}}" id="item_quantity_{{$item->id}}">
            <input type="number" class="input-group-text" style="width: 110px;" name="total_price" value="{{$item->total_price}}" id="item_total_price_{{$item->id}}">
            <button type="submit" class="input-group-text btn btn-secondary"><i class="bi bi-send"></i></button>
            <button type="button" class="input-group-text btn btn-danger p-0">
                <a class="d-block text-white text-decoration-none w-100 p-3" href="{{route('orders.items.destroy', $item->id)}}">
                    <i class="bi bi-trash"></i>
                </a>
            </button>
        </div>
    </form>
    @empty
    No items added yest, please add some items
    @endforelse
    @if ($orderItems->count() > 0)
    <h4 class="bg-secondary text-white text-center py-1">{{ __('orders.titles.client_information') }}</h4>
    <form action="{{route('orders.update', $order->id)}}" method="POST" id="orderForm">
        @csrf
        @method('PUT')
        <div class="order-types">
            <input type="radio" name="order_type" id="away" value="away" style="display: none;" checked>
            <label for="away" class="btn btn-outline-primary">{{ __('orders.labels.order_type_away') }}</label>
            <input type="radio" name="order_type" id="local" value="local" style="display: none;">
            <label for="local" class="btn btn-outline-primary">{{ __('orders.labels.order_type_local') }}</label>
            <input type="radio" name="order_type" id="delivery" value="delivery" style="display: none;">
            <label for="delivery" class="btn btn-outline-primary">{{ __('orders.labels.order_type_delivery') }}</label>
            <input type="radio" name="order_type" id="feast" value="feast" style="display: none;">
            <label for="feast" class="btn btn-outline-primary">{{ __('orders.labels.order_type_feast')}}</label>
        </div>

        <div class="inputs border border-primary p-3"
            style="margin-top: -2px; background-color: #ebebebff; border-radius: 0.6rem;">
            <!-- Customer Search with Autocomplete -->
            <div class="mb-2">
                <label for="customerSearch" class="form-label fw-bold">
                    <i class="bi bi-search"></i> {{__('orders.labels.customer_search')}}
                </label>
                <div class="position-relative">
                    <input type="text"
                        class="form-control"
                        id="customerSearch"
                        placeholder="{{__('orders.hints.customer_search_hint')}}"
                        autocomplete="off"
                        style="height: 3rem; border-radius: 0.6rem;">
                    <div class="position-absolute w-100" style="z-index: 1050;">
                        <ul id="customerSearchResults" class="list-group d-none shadow-lg" style="max-height: 300px; overflow-y: auto;"></ul>
                    </div>
                </div>
                <small class=""><i class="bi bi-info-circle"></i> {{__('orders.hints.customer_search_hint')}}</small>
            </div>

            <div class="input-group mb-1" id="client_name_group">
                <label for="client_name" class="input-group-text">{{__('orders.labels.customer_name')}}</label>
                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                <input type="text" name="client_name" id="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="اسم العميل">
            </div>

            <div class="input-group mb-1" id="phone_group">
                <label for="phone" class="input-group-text">{{__('orders.labels.phone')}}</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="رقم الهاتف">
            </div>

            <div class="input-group mb-1" id="address_group">
                <label for="address" class="input-group-text">{{__('orders.labels.address')}}</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}">
            </div>

            <div class="input-group mb-1" id="table_id_group">
                <label for="table_id" class="input-group-text">{{__('orders.labels.table_or_room')}}</label>
                <input type="text" name="table_id" id="table_id" class="form-control" value="{{ old('table_id') }}">
            </div>
        </div>

        <!-- Payment Methods Section -->
        <div class="payment-section mt-3">
            <h4 class="bg-secondary text-white text-center py-1">{{ __('orders.titles.payment_methods') }}</h4>
            <div class="order-types">
                <input type="radio" name="payment_method" id="payment_cash" value="cash" style="display: none;">
                <label for="payment_cash" class="btn btn-outline-success">{{ __('orders.labels.payment_method_cash') }}</label>
                <input type="radio" name="payment_method" id="payment_pos" value="pos" style="display: none;">
                <label for="payment_pos" class="btn btn-outline-success">{{ __('orders.labels.payment_method_pos') }}</label>
                <input type="radio" name="payment_method" id="payment_bank_transfer" value="bank_transfer" style="display: none;">
                <label for="payment_bank_transfer" class="btn btn-outline-success">{{ __('orders.labels.payment_method_bank_transfer') }}</label>
                <input type="radio" name="payment_method" id="payment_account" value="account" style="display: none;">
                <label for="payment_account" class="btn btn-outline-success">{{ __('orders.labels.payment_method_account') }}</label>
            </div>
            <div class="inputs border border-success p-3" style="margin-top: -2px; background-color: #f8f9fa; border-radius: 0.6rem;">

                <!-- Common Payment Fields (Shown for all payment methods) -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <label for="subtotal" class="input-group-text">{{ __('orders.labels.subtotal') }}</label>
                            <input type="number" id="subtotal" class="form-control" value="0.00" readonly step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <label for="tax" class="input-group-text">{{ __('orders.labels.tax') }}</label>
                            <input type="number" id="tax" class="form-control" value="0.00" readonly step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <label for="total_amount" class="input-group-text">{{ __('orders.labels.total') }}</label>
                            <input type="number" id="total_amount" class="form-control" value="0.00" readonly step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Cash Payment Fields -->
                <div id="cash_fields" style="display: none;">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="paid_amount" class="input-group-text">{{ __('orders.labels.paid_amount') }}</label>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="remaining_amount" class="input-group-text">{{ __('orders.labels.remaining_amount') }}</label>
                                <input type="number" id="remaining_amount" class="form-control" value="0.00" readonly step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Fields -->
                <div id="bank_transfer_fields" style="display: none;">
                    <div class="input-group mb-2">
                        <label for="bank_account" class="input-group-text">{{ __('orders.labels.bank_account') }}</label>
                        <select name="bank_account" id="bank_account" class="form-control">
                            <option value="">{{ __('orders.labels.select_bank_account') }}</option>
                            <option value="account1">البنك الأهلي - 123456789</option>
                            <option value="account2">بنك الراجحي - 987654321</option>
                            <option value="account3">البنك السعودي - 456789123</option>
                        </select>
                    </div>
                    <div class="input-group mb-2">
                        <label for="transfer_receipt" class="input-group-text">{{ __('orders.labels.transfer_receipt') }}</label>
                        <input type="file" name="transfer_receipt" id="transfer_receipt" class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- POS Fields -->
                <div id="pos_fields" style="display: none;">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="pos_device" class="input-group-text">{{ __('orders.labels.pos_device') }}</label>
                                <select name="pos_device" id="pos_device" class="form-control">
                                    <option value="">{{ __('orders.labels.select_pos_device') }}</option>
                                    <option value="pos1">جهاز الصرافة 1 - الكاشير الرئيسي</option>
                                    <option value="pos2">جهاز الصرافة 2 - الطابق الأول</option>
                                    <option value="pos3">جهاز الصرافة 3 - الخارجي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" id="send_to_pos_btn" class="btn btn-primary flex-fill">
                                    <i class="bi bi-send"></i> {{ __('orders.labels.send_to_pos') }}
                                </button>
                                <button type="button" id="manual_complete_btn" class="btn btn-warning">
                                    <i class="bi bi-hand-thumbs-up"></i> {{ __('orders.labels.manual_complete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="pos_status" class="alert alert-info mb-2" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <span>جاري إرسال المبلغ لجهاز الصرافة...</span>
                        </div>
                    </div>
                </div>

                <!-- Account Fields -->
                <div id="account_fields" style="display: none;">
                    <div class="alert alert-info mb-2">
                        <i class="bi bi-info-circle"></i>
                        <span id="account_balance_info">{{ __('orders.labels.account_balance_info') }}</span>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <div class="input-group">
                                <label for="account_amount" class="input-group-text">{{ __('orders.labels.total') }}</label>
                                <input type="number" name="account_amount" id="account_amount" class="form-control" value="0.00" readonly step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="add_to_account_btn" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle"></i> {{ __('orders.labels.add_to_account') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="button" id="save_print_order_btn" class="btn btn-outline-primary w-100">
                            <i class="bi bi-save"></i> {{ __('orders.labels.save_print_order') }}
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" id="complete_print_invoice_btn" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> {{ __('orders.labels.complete_print_invoice') }}
                        </button>
                    </div>
                </div>

            </div>
    </form>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerSearchInput = document.getElementById('customerSearch');
        const customerSearchResults = document.getElementById('customerSearchResults');
        const customerNameInput = document.getElementById('client_name');
        const customerPhoneInput = document.getElementById('phone');
        const customerIdInput = document.getElementById('client_id');

        let searchTimeout;

        // Advanced Customer Search with Autocomplete
        if (customerSearchInput) {
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
        }

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
                        <strong class="d-block"><i class="bi bi-person-fill text-primary"></i> ${client.name}</strong>
                        <small class="text-muted"><i class="bi bi-telephone-fill"></i> ${client.phone}</small>
                    </div>
                    <i class="bi bi-arrow-left-circle text-primary fs-5"></i>
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

            // Visual feedback - Add success animation
            customerNameInput.classList.add('border-success', 'border-3');
            customerPhoneInput.classList.add('border-success', 'border-3');

            // Show success notification
            showNotification('✅ تم اختيار العميل بنجاح!', 'success');

            setTimeout(() => {
                customerNameInput.classList.remove('border-success', 'border-3');
                customerPhoneInput.classList.remove('border-success', 'border-3');
            }, 2000);
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} position-fixed shadow-lg`;
            notification.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px; animation: slideDown 0.3s ease-out;';
            notification.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <span>${message}</span>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (customerSearchInput && !customerSearchInput.contains(e.target) && !customerSearchResults.contains(e.target)) {
                customerSearchResults.classList.add('d-none');
            }
        });

        // Clear customer ID if name or phone is manually changed
        if (customerNameInput) {
            customerNameInput.addEventListener('input', function() {
                if (customerIdInput.value) {
                    customerIdInput.value = '';
                }
            });
        }

        if (customerPhoneInput) {
            customerPhoneInput.addEventListener('input', function() {
                if (customerIdInput.value) {
                    customerIdInput.value = '';
                }
            });
        }

        // Order Type Field Management
        const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
        const clientNameGroup = document.getElementById('client_name_group');
        const phoneGroup = document.getElementById('phone_group');
        const addressGroup = document.getElementById('address_group');
        const tableIdGroup = document.getElementById('table_id_group');
        const customerSearchGroup = document.querySelector('#customerSearch').closest('.mb-2');

        let localCustomerCounter = 1;

        // Function to update fields based on order type
        function updateOrderFields(orderType) {
            // Hide all optional fields first
            phoneGroup.style.display = 'none';
            addressGroup.style.display = 'none';
            tableIdGroup.style.display = 'none';

            // Remove required attributes
            document.getElementById('phone').removeAttribute('required');
            document.getElementById('address').removeAttribute('required');
            document.getElementById('table_id').removeAttribute('required');

            // Show customer search for all types except local
            if (orderType === 'local') {
                customerSearchGroup.style.display = 'none';
                // Generate automatic local customer name
                const localCustomerName = `عميل محلي ${localCustomerCounter++}`;
                document.getElementById('client_name').value = localCustomerName;
                document.getElementById('client_name').setAttribute('readonly', true);

                // Show table_id field for local
                tableIdGroup.style.display = 'flex';
                document.getElementById('table_id').setAttribute('required', 'required');
            } else {
                customerSearchGroup.style.display = 'block';
                document.getElementById('client_name').removeAttribute('readonly');
                document.getElementById('client_name').value = '';

                // For takeaway (away)
                if (orderType === 'away') {
                    phoneGroup.style.display = 'flex';
                    document.getElementById('phone').setAttribute('required', 'required');
                }

                // For delivery and feast
                if (orderType === 'delivery' || orderType === 'feast') {
                    phoneGroup.style.display = 'flex';
                    addressGroup.style.display = 'flex';
                    document.getElementById('phone').setAttribute('required', 'required');
                    document.getElementById('address').setAttribute('required', 'required');
                }
            }
        }

        // Add event listeners to order type radios
        orderTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    updateOrderFields(this.value);
                }
            });
        });

        // Initialize with default state (no fields shown)
        updateOrderFields(null);

        // Payment Method Field Management
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const cashFields = document.getElementById('cash_fields');
        const bankTransferFields = document.getElementById('bank_transfer_fields');
        const posFields = document.getElementById('pos_fields');
        const accountFields = document.getElementById('account_fields');
        const posStatus = document.getElementById('pos_status');

        // Function to update payment fields based on payment method
        function updatePaymentFields(paymentMethod) {
            // Hide all payment fields first
            cashFields.style.display = 'none';
            bankTransferFields.style.display = 'none';
            posFields.style.display = 'none';
            accountFields.style.display = 'none';
            posStatus.style.display = 'none';

            // Remove required attributes
            document.getElementById('paid_amount').removeAttribute('required');
            document.getElementById('bank_account').removeAttribute('required');
            document.getElementById('transfer_receipt').removeAttribute('required');
            document.getElementById('pos_device').removeAttribute('required');

            // Show fields based on payment method
            if (paymentMethod === 'cash') {
                cashFields.style.display = 'block';
                document.getElementById('paid_amount').setAttribute('required', 'required');
            } else if (paymentMethod === 'bank_transfer') {
                bankTransferFields.style.display = 'block';
                document.getElementById('bank_account').setAttribute('required', 'required');
                document.getElementById('transfer_receipt').setAttribute('required', 'required');
            } else if (paymentMethod === 'pos') {
                posFields.style.display = 'block';
                document.getElementById('pos_device').setAttribute('required', 'required');
            } else if (paymentMethod === 'account') {
                accountFields.style.display = 'block';
                loadCustomerAccountBalance();
            }
        }

        // Calculate order totals
        function calculateOrderTotals() {
            let subtotal = 0;
            // Calculate total from order items
            document.querySelectorAll('[name="total_price"]').forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });

            const tax = subtotal * 0.15; // 15% tax
            const total = subtotal + tax;

            // Update the display fields
            document.getElementById('subtotal').value = subtotal.toFixed(2);
            document.getElementById('tax').value = tax.toFixed(2);
            document.getElementById('total_amount').value = total.toFixed(2);
            document.getElementById('account_amount').value = total.toFixed(2);

            // Calculate remaining amount for cash payment
            const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
            const remaining = Math.max(0, total - paidAmount);
            document.getElementById('remaining_amount').value = remaining.toFixed(2);

            return total;
        }

        // Load customer account balance
        function loadCustomerAccountBalance() {
            const customerId = document.getElementById('client_id').value;
            if (customerId) {
                fetch(`/api/customers/${customerId}/balance`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('account_balance_info').textContent =
                                `{{ __('orders.labels.account_balance_info') }}`.replace('0', data.balance);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading account balance:', error);
                    });
            }
        }

        // Add event listeners to payment method radios
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    updatePaymentFields(this.value);
                    calculateOrderTotals(); // Recalculate when payment method changes
                }
            });
        });

        // Event listener for paid amount (cash payment)
        document.getElementById('paid_amount').addEventListener('input', function() {
            calculateOrderTotals();
        });

        // Event listeners for POS buttons
        document.getElementById('send_to_pos_btn').addEventListener('click', function() {
            const posDevice = document.getElementById('pos_device').value;
            const totalAmount = parseFloat(document.getElementById('total_amount').value);

            if (!posDevice) {
                showNotification('⚠️ يجب اختيار جهاز الصرافة أولاً!', 'warning');
                return;
            }

            // Show processing status
            posStatus.style.display = 'block';
            posStatus.className = 'alert alert-info mb-2';
            posStatus.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <span>جاري إرسال المبلغ (${totalAmount.toFixed(2)}) لجهاز الصرافة...</span>
                </div>
            `;

            // Simulate POS communication
            setTimeout(() => {
                posStatus.className = 'alert alert-success mb-2';
                posStatus.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2"></i>
                        <span>تم إرسال المبلغ بنجاح لجهاز الصرافة!</span>
                    </div>
                `;
            }, 2000);
        });

        document.getElementById('manual_complete_btn').addEventListener('click', function() {
            posStatus.style.display = 'block';
            posStatus.className = 'alert alert-warning mb-2';
            posStatus.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <span>تم إتمام العملية يدوياً بنجاح!</span>
                </div>
            `;
        });

        // Event listener for account button
        document.getElementById('add_to_account_btn').addEventListener('click', function() {
            const customerId = document.getElementById('client_id').value;
            const amount = parseFloat(document.getElementById('account_amount').value);

            if (!customerId) {
                showNotification('⚠️ يجب اختيار العميل أولاً!', 'warning');
                return;
            }

            if (amount <= 0) {
                showNotification('⚠️ المبلغ غير صحيح!', 'warning');
                return;
            }

            showNotification('✅ تم إضافة المبلغ لحساب العميل بنجاح!', 'success');
        });

        // Event listener for save and print order
        document.getElementById('save_print_order_btn').addEventListener('click', function() {
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');

            if (!selectedPaymentMethod) {
                showNotification('⚠️ يجب اختيار طريقة الدفع أولاً!', 'warning');
                return;
            }

            showNotification('🔄 جاري حفظ الطلب وطباعته...', 'info');

            // Simulate save and print
            setTimeout(() => {
                showNotification('✅ تم حفظ الطلب وطباعته بنجاح!', 'success');
                window.print(); // Open print dialog
            }, 1500);
        });

        // Initialize with default state (no payment fields shown)
        updatePaymentFields(null);

        // Calculate initial totals
        calculateOrderTotals();

        // Form validation before submission
        const orderForm = document.getElementById('orderForm');
        if (orderForm) {
            orderForm.addEventListener('submit', function(e) {
                const selectedOrderType = document.querySelector('input[name="order_type"]:checked');
                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');

                if (!selectedOrderType) {
                    e.preventDefault();
                    showNotification('⚠️ يجب اختيار نوع الطلب أولاً!', 'warning');
                    return;
                }

                if (!selectedPaymentMethod) {
                    e.preventDefault();
                    showNotification('⚠️ يجب اختيار طريقة الدفع أولاً!', 'warning');
                    return;
                }

                const orderType = selectedOrderType.value;
                const paymentMethod = selectedPaymentMethod.value;

                // Validate order type fields
                if (orderType === 'local') {
                    const tableId = document.getElementById('table_id').value.trim();
                    if (!tableId) {
                        e.preventDefault();
                        showNotification('⚠️ رقم الغرفة مطلوب للطلبات المحلية!', 'warning');
                        document.getElementById('table_id').focus();
                        return;
                    }
                }

                if (orderType === 'away') {
                    const phone = document.getElementById('phone').value.trim();
                    if (!phone) {
                        e.preventDefault();
                        showNotification('⚠️ رقم الهاتف مطلوب للطلبات السفرية!', 'warning');
                        document.getElementById('phone').focus();
                        return;
                    }
                }

                if (orderType === 'delivery' || orderType === 'feast') {
                    const phone = document.getElementById('phone').value.trim();
                    const address = document.getElementById('address').value.trim();

                    if (!phone) {
                        e.preventDefault();
                        showNotification('⚠️ رقم الهاتف مطلوب لهذا النوع من الطلبات!', 'warning');
                        document.getElementById('phone').focus();
                        return;
                    }

                    if (!address) {
                        e.preventDefault();
                        showNotification('⚠️ العنوان مطلوب لهذا النوع من الطلبات!', 'warning');
                        document.getElementById('address').focus();
                        return;
                    }
                }

                // Validate customer name for non-local orders
                if (orderType !== 'local') {
                    const customerName = document.getElementById('client_name').value.trim();
                    if (!customerName) {
                        e.preventDefault();
                        showNotification('⚠️ اسم العميل مطلوب!', 'warning');
                        document.getElementById('client_name').focus();
                        return;
                    }
                }

                // Validate payment method fields
                if (paymentMethod === 'bank_transfer') {
                    const receipt = document.getElementById('transfer_receipt').files.length;
                    const transferNumber = document.getElementById('transfer_number').value.trim();

                    if (!receipt) {
                        e.preventDefault();
                        showNotification('⚠️ يجب رفع صورة الإيصال!', 'warning');
                        return;
                    }

                    if (!transferNumber) {
                        e.preventDefault();
                        showNotification('⚠️ رقم التحويل مطلوب!', 'warning');
                        document.getElementById('transfer_number').focus();
                        return;
                    }
                }

                if (paymentMethod === 'account') {
                    const balance = document.getElementById('account_balance').value;
                    const confirmDeduction = document.getElementById('confirm_account_deduction').checked;

                    if (!balance || balance <= 0) {
                        e.preventDefault();
                        showNotification('⚠️ يجب تحديد المبلغ المراد خصمه!', 'warning');
                        document.getElementById('account_balance').focus();
                        return;
                    }

                    if (!confirmDeduction) {
                        e.preventDefault();
                        showNotification('⚠️ يجب تأكيد الخصم من حساب العميل!', 'warning');
                        return;
                    }
                }

                if (paymentMethod === 'pos') {
                    e.preventDefault();
                    processPOSPayment();
                    return;
                }
            });
        }

        // POS Payment Processing Function
        function processPOSPayment() {
            const orderTotal = calculateOrderTotal(); // You'll need to implement this function

            showNotification('🔄 جاري الاتصال بجهاز الصرافة...', 'info');

            // Simulate POS processing (replace with actual POS integration)
            setTimeout(() => {
                // This is where you'll integrate with the actual POS device
                // For now, we'll simulate a successful payment
                showNotification('✅ تم الدفع بنجاح عبر جهاز الصرافة!', 'success');

                // Submit the form after successful POS payment
                document.getElementById('orderForm').submit();
            }, 3000);
        }

        // Calculate order total (placeholder function)
        function calculateOrderTotal() {
            let total = 0;
            // Calculate total from order items
            document.querySelectorAll('[name="total_price"]').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            return total;
        }
    });
</script>

<style>

</style>
@endsection