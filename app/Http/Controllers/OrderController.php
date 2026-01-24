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
        $order = Order::where(['status' => 'in_progress', 'user_id' => Auth::id()])->latest()->first();
        if (!$order) {
            $order = Order::create([
                'order_number' => 'TEMP-' . time() . '-' . Auth::id(),
                'user_id' => Auth::id(),
                'status' => 'in_progress',
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
            ]);
        }
        return view('orders.create', compact('categories', 'products', 'order'));
    }

    public function store(OrderRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generate order number
            $lastOrder = Order::latest()->first();
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(($lastOrder ? $lastOrder->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create order
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'room_number' => $request->room_number,
                'type' => $request->type,
                'status' => 'pending',
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount ?? 0,
                'remaining_amount' => $request->total_amount - ($request->paid_amount ?? 0),
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
            ];

            $order = Order::create($orderData);

            // Add Order Items
            if ($request->has('products') && is_array($request->products)) {
                foreach ($request->products as $index => $productId) {
                    $quantity = $request->quantities[$index];
                    $price = $request->prices[$index];
                    $product = Product::findOrFail($productId);

                    // Check stock
                    if ($product->track_inventory && $product->stock_quantity < $quantity) {
                        throw new Exception("المنتج '{$product->name_ar}' غير متوفر بالكمية المطلوبة");
                    }

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_price' => $price * $quantity,
                    ]);

                    // Update stock
                    if ($product->track_inventory) {
                        $product->stock_quantity -= $quantity;
                        $product->save();
                    }
                }
            }

            DB::commit();

            // Check if print is requested
            if ($request->has('print_receipt') && $request->print_receipt == '1') {
                return redirect()->route('orders.print', $order->id);
            }

            return redirect()->route('orders.index')->with('success', 'تم إنشاء الطلب بنجاح');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
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
