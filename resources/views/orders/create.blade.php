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
            margin-inline-start: 0.6rem;
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



        [dir=rtl] .input-group input:first-child,
        [dir=rtl] .input-group label:first-child,
        [dir=rtl] .input-group button:first-child,
        [dir=rtl] .input-group select:first-child {
            border-radius: 0 0.6rem 0.6rem 0 !important;
        }

        @media (min-width: 991px) {

            .input-group input,
            .input-group button,
            .input-group select,
            .input-group label {

                font: normal 14px/1.2rem Cairo;
            }
        }

        [dir=rtl] .input-group input:last-child,
        [dir=rtl] .input-group label:last-child,
        [dir=rtl] .input-group select:last-child,
        [dir=rtl] .input-group button:last-child {
            border-radius: 0.6rem 0 0 0.6rem !important;
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
    <h4 class="bg-secondary text-white text-center py-1">{{ __('orders.titles.order_items') }}</h4>
    <form action="{{route('orders.update', $order->id)}}" method="POST">
        @csrf
        @method('PUT')
        <div class="order-types">
            <button type="button" class="btn btn-outline-primary">Away</button>
            <button type="button" class="btn btn-outline-primary">Local</button>
            <button type="button" class="btn btn-outline-primary">Delivery</button>
            <button type="button" class="btn btn-outline-primary">Feast</button>
        </div>
        <div class="inputs border border-primary p-3" style="margin-top: -1">
            <div class="input-group mb-1">
                <label for="customer_name" class="input-group-text">Client</label>
                <input type="text" name="client_name" id="client_name" class="form-control" value="{{ old('client_name') }}">
                <select type="text" name="client_id" id="client_id" class="form-select" value="{{ old('client_id') }}">
                    <option value="">اختر العميل</option>
                    @foreach($clients as $client)
                    <option value="{{$client->id}}">{{$client->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group mb-1">
                <label for="phone" class="input-group-text">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
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
    ['client_name', 'phone'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            if (this.value.length >= 2) searchClient(this.value, 'client_id');
        });
    })

    function searchClient(value, id) {
        $.ajax({
            url: `/clients/search/by/name/or/phone`,
            type: 'GET',
            data: {
                search: value
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success && data.clients.length > 0) {
                    var clientSelect = document.getElementById('client_id');
                    // Add new options from search results
                    var options = data.clients.map(function(client) {
                        return `<option value="${client.id}">${client.name}</option>`;
                    });

                    console.log(options);
                    clientSelect.innerHTML = options.join('');
                }
            },
        });
    }
</script>
@endsection