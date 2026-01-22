<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->when(request('search'), function ($query) {
                $query->where('name_ar', 'like', '%' . request('search') . '%')
                    ->orWhere('name_en', 'like', '%' . request('search') . '%')
                    ->orWhere('barcode', 'like', '%' . request('search') . '%')
                    ->orWhere('sku', 'like', '%' . request('search') . '%');
            })
            ->when(request('category_id'), function ($query) {
                $query->where('category_id', request('category_id'));
            })
            ->when(request('status'), function ($query) {
                $query->where('is_active', request('status') === '1');
            })
            ->orderBy('s_number', 'asc')
            ->paginate(20);

        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        // return $request->all();
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }

            // Set boolean values
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_active'] = $request->boolean('is_active');
            $data['is_seasonal'] = $request->boolean('is_seasonal');

            // Set defaults for optional fields
            $data['stock_quantity'] = $data['stock_quantity'] ?? 0;
            $data['min_stock_alert'] = $data['min_stock_alert'] ?? 10;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            Product::create($data);

            return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'inventoryTransactions' => function ($query) {
            $query->with('user')->latest()->take(20);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }

            // Set boolean values
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_active'] = $request->boolean('is_active');
            $data['is_seasonal'] = $request->boolean('is_seasonal');

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المنتج بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Check if product has related orders
            $hasOrders = $product->orderItems()->exists();

            if ($hasOrders) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف المنتج لوجود طلبات مرتبطة به'
                ], 400);
            }

            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage()
            ], 400);
        }
    }

    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => $product->is_active ? 'تم تفعيل المنتج' : 'تم إلغاء تفعيل المنتج'
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();

        return response()->json($products);
    }

    public function lowStock()
    {
        $products = Product::where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->with('category')
            ->get();

        return view('products.low-stock', compact('products'));
    }
}
