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

        .order-types {
            display: flex;
            justify-content: start;
            gap: 2px;
        }

        .order-types .btn {
            margin: 0 0.1px;
            border-radius: 0.6rem 0.6rem 0 0;
        }

        .order-types .btn:first-child {
            margin-inline-start: 1rem;
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

        

        @media (min-width: 991px) {

            .input-group input,
            .input-group button,
            .input-group select,
            .input-group label {

                font: normal 14px/1.2rem Cairo;
            }
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
    <form action="{{route('orders.update', $order->id)}}" method="POST">
        @csrf
        @method('PUT')
        <div class="order-types">
            <button type="button" class="btn btn-outline-primary">Away</button>
            <button type="button" class="btn btn-outline-primary">Local</button>
            <button type="button" class="btn btn-outline-primary">Delivery</button>
            <button type="button" class="btn btn-outline-primary">Feast</button>
        </div>
        <div class="inputs border border-primary p-3" 
            style="margin-top: -2px; background-color: #e07aee; border-radius: 0.6rem;">
            <!-- Customer Search with Autocomplete -->
            <div class="mb-2">
                <label for="customerSearch" class="form-label fw-bold text-white">
                    <i class="bi bi-search"></i> البحث عن العميل
                </label>
                <div class="position-relative">
                    <input type="text" 
                           class="form-control" 
                           id="customerSearch" 
                           placeholder="ابحث بالاسم أو رقم الهاتف..."
                           autocomplete="off"
                           style="height: 3rem; border-radius: 0.6rem;">
                    <div class="position-absolute w-100" style="z-index: 1050;">
                        <ul id="customerSearchResults" class="list-group d-none shadow-lg" style="max-height: 300px; overflow-y: auto;"></ul>
                    </div>
                </div>
                <small class="text-white"><i class="bi bi-info-circle"></i> ابدأ بالكتابة للبحث (حرفين على الأقل)</small>
            </div>

            <div class="input-group mb-1">
                <label for="client_name" class="input-group-text">Client</label>
                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                <input type="text" name="client_name" id="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="اسم العميل">
            </div>
            <div class="input-group mb-1">
                <label for="phone" class="input-group-text">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="رقم الهاتف">
            </div>
            <div class="input-group mb-1">
                <label for="address" class="input-group-text">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}">
            </div>
            <div class="input-group mb-1">
                <label for="room_or_table" class="input-group-text">Address</label>
                <input type="text" name="room_or_table" id="room_or_table" class="form-control" value="{{ old('room_or_table') }}">
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
});
</script>

<style>
.customer-result-item {
    transition: all 0.2s ease;
}

.customer-result-item:hover {
    background-color: #e3f2fd !important;
    transform: translateX(-5px);
    border-right: 4px solid #2196F3;
}

#customerSearchResults {
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    border-radius: 0.6rem;
    margin-top: 2px;
    animation: fadeIn 0.2s ease-out;
}

.border-success {
    animation: successPulse 0.6s ease-in-out;
}

@keyframes successPulse {
    0%, 100% { 
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
@endsection