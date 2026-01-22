<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $categories = Category::with('activeProducts')->get();
        $products = Product::where('is_active', true)->get();
        return view('orders.create', compact('categories', 'products'));
    }

    public function store(OrderRequest $request)
    {
        $data = OrderRequest::validate($request->all());
        $data['user_id'] = Auth::id();

        try {
            DB::beginTransaction();
            $order = Order::create($data);

            // Add Order Items
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->track_inventory && $product->stock_quantity < $item['quantity']) {
                    throw new Exception("المنتج '{$product->name_ar}' غير متوفر بالكمية المطلوبة");
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->notes = $item['notes'] ?? null;
                $orderItem->save();

                $subtotal += $orderItem->total_price;
            }

            // Apply Coupon
            $discountAmount = 0;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $coupon->incrementUsage();
                }
            }

            // Calculate Totals
            $order->subtotal = $subtotal;
            $order->discount_amount = $discountAmount;
            $order->tax_amount = ($subtotal - $discountAmount) * 0.15; // Saudi VAT
            $order->total_amount = $order->subtotal + $order->tax_amount - $order->discount_amount;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->load('orderItems.product'),
                'message' => 'تم إنشاء الطلب بنجاح'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return redirect()->back()->with('error', 'لا يمكن تعديل طلب مكتمل أو ملغي');
        }

        $order->load('orderItems.product');
        $categories = \App\Models\Category::with('activeProducts')->get();

        return view('orders.edit', compact('order', 'categories'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل طلب مكتمل أو ملغي'
            ], 400);
        }

        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'type' => 'required|in:dine_in,takeaway,delivery,catering',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,visa,mixed',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Restore stock for old items
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->track_inventory) {
                    $item->product->stock_quantity += $item->quantity;
                    $item->product->save();
                }
            }

            // Delete old items
            $order->orderItems()->delete();

            // Add new items
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->track_inventory && $product->stock_quantity < $item['quantity']) {
                    throw new Exception("المنتج '{$product->name_ar}' غير متوفر بالكمية المطلوبة");
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->notes = $item['notes'] ?? null;
                $orderItem->save();

                $subtotal += $orderItem->total_price;
            }

            // Update Order
            $order->customer_name = $request->customer_name;
            $order->customer_phone = $request->customer_phone;
            $order->type = $request->type;
            $order->payment_method = $request->payment_method;
            $order->notes = $request->notes;
            $order->subtotal = $subtotal;
            $order->tax_amount = $subtotal * 0.15;
            $order->total_amount = $order->subtotal + $order->tax_amount - $order->discount_amount;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->load('orderItems.product'),
                'message' => 'تم تحديث الطلب بنجاح'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {

        $order->status = $request->status;

        if ($request->status === 'completed') {
            $order->markAsCompleted();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح'
        ]);
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن حذف طلب مكتمل');
        }

        try {
            DB::beginTransaction();

            // Restore stock
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->track_inventory) {
                    $item->product->stock_quantity += $item->quantity;
                    $item->product->save();
                }
            }

            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الطلب بنجاح'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الطلب'
            ], 400);
        }
    }

    public function print(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('orders.print', compact('order'));
    }

    public function kitchenOrders()
    {
        $orders = Order::with(['orderItems.product'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at')
            ->get();

        return view('orders.kitchen', compact('orders'));
    }
}
