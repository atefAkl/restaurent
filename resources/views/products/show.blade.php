@extends('layouts.app')

@section('title', 'عرض المنتج')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">عرض المنتج</h1>
                <div class="btn-group">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i>
                        تعديل
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right"></i>
                        العودة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name_ar }}"
                                class="img-fluid rounded mb-3" style="max-height: 300px;">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                style="height: 300px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                            @endif

                            <h4>{{ $product->name_ar }}</h4>
                            <p class="text-muted">{{ $product->name_en }}</p>

                            <div class="d-flex justify-content-center gap-2">
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                @if($product->is_seasonal)
                                <span class="badge bg-info">موسمي</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">معلومات المنتج</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>الفئة:</strong>
                                    <p>{{ $product->category->name_ar ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>الباركود:</strong>
                                    <p><code>{{ $product->barcode ?? '-' }}</code></p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>رمز المنتج (SKU):</strong>
                                    <p><code>{{ $product->sku ?? '-' }}</code></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>تتبع المخزون:</strong>
                                    <p>
                                        <span class="badge {{ $product->track_inventory ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->track_inventory ? 'مفعل' : 'معطل' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="card-title">المعلومات المالية</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>سعر البيع:</strong>
                                    <p class="fs-4 text-success">{{ number_format($product->price, 2) }} ريال</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>التكلفة:</strong>
                                    <p class="fs-4 text-danger">{{ number_format($product->cost, 2) }} ريال</p>
                                </div>
                            </div>

                            @if($product->cost > 0)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>هامش الربح:</strong>
                                    <p class="fs-4 text-primary">
                                        {{ number_format(($product->price - $product->cost) / $product->cost * 100, 2) }}%
                                    </p>
                                </div>
                            </div>
                            @endif

                            <hr>

                            <h5 class="card-title">المخزون</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>الكمية الحالية:</strong>
                                    <p>
                                        <span class="badge fs-5 {{ $product->isLowStock() ? 'bg-danger' : 'bg-primary' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        @if($product->isLowStock())
                                        <br>
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            منخفض (أقل من {{ $product->min_stock_alert }})
                                        </small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>حد التنبيه:</strong>
                                    <p>{{ $product->min_stock_alert }}</p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="card-title">الوصف</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الوصف (عربي):</strong>
                                    <p>{{ $product->description_ar ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>الوصف (إنجليزي):</strong>
                                    <p>{{ $product->description_en ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales History -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">سجل المبيعات</h5>

                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>رقم الطلب</th>
                                            <th>التاريخ</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->orderItems()->with('order')->latest()->take(10)->get() as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('orders.show', $item->order->id) }}">
                                                    #{{ $item->order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $item->order->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                لا توجد مبيعات سابقة
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection