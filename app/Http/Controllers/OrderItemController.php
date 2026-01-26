<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    //
    public function store(Request $request)
    {
        $orderId = $request->input('order_id');
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        $orderItem = OrderItem::where(['order_id' => $orderId, 'product_id' => $productId])->first();

        // return null == $orderItem;
        try {
            if (null == $orderItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'unit_price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price,
                ]);
            } else {
                $qty = $orderItem->quantity + 1;
                $orderItem->update(['quantity' => $qty, 'total_price' => $qty * $orderItem->unit_price]);
            }
            return redirect()->route('orders.create', ['order' => $orderId])->with('success', 'Item added to order successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Error adding item to order');

            // Logic to add the product to the order
            // This is a placeholder; actual implementation may vary
            // For example, you might create a new OrderItem model instance here
            // 'order_id' => request,
            // 'product_id' => request,     
            // 'unit_price' => $product->price,
            // 'quantity' => 1,
            // 'total_price' => $product->price  * quantity,
            // Redirect back to the order creation page or wherever appropriate
        }
    }
}
